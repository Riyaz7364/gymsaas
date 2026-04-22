<?php

namespace App\Livewire;

use App\Models\Member;
use App\Models\Trainer;
use App\Models\TrainerSchedule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MemberTrainer extends Component
{
    public Member $member;

    #[Validate('required|exists:trainers,id')]
    public ?int $trainerId = null;

    #[Validate('nullable|exists:trainer_schedules,id')]
    public ?int $scheduleId = null;

    #[Validate('nullable|date')]
    public ?string $startDate = null;

    #[Validate('nullable|date|after_or_equal:startDate')]
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->trainerId = $this->member->trainer->first()?->id;
    }

    public function assign(): void
    {
        $this->validate();

        $gymId     = auth()->user()->gym_id;
        $trainerId = $this->trainerId;

        // Sync trainer pivot
        $this->member->trainer()->sync([
            $trainerId => ['assigned_at' => now()->toDateString(), 'gym_id' => $gymId],
        ]);

        // Detach from old slots belonging to this trainer
        $oldSlotIds = TrainerSchedule::where('trainer_id', $trainerId)
                        ->where('gym_id', $gymId)
                        ->pluck('id');
        if ($oldSlotIds->isNotEmpty()) {
            $this->member->trainerSchedules()->detach($oldSlotIds);
        }

        // Attach to new slot
        if ($this->scheduleId) {
            $schedule = TrainerSchedule::where('id', $this->scheduleId)
                            ->where('gym_id', $gymId)
                            ->firstOrFail();

            $pivotData = ['assigned_at' => now()->toDateString()];
            if ($this->startDate) $pivotData['start_date'] = $this->startDate;
            if ($this->endDate)   $pivotData['end_date']   = $this->endDate;

            $schedule->members()->syncWithoutDetaching([$this->member->id => $pivotData]);
        }

        $this->reset(['scheduleId', 'startDate', 'endDate']);
        $this->member->refresh();
    }

    public function removeTrainer(): void
    {
        abort_if(
            $this->member->gym_id !== auth()->user()->gym_id,
            403
        );

        $this->member->trainer()->detach();
        $this->member->trainerSchedules()->detach();
        $this->trainerId  = null;
        $this->scheduleId = null;
        $this->member->refresh();
    }

    public function render()
    {
        $gymId = auth()->user()->gym_id;

        $allTrainers = Trainer::where('gym_id', $gymId)
                        ->where('status', 'active')
                        ->orderBy('name')
                        ->get();

        $allSchedules = TrainerSchedule::with('members')
                        ->where('gym_id', $gymId)
                        ->where('is_active', true)
                        ->orderByRaw("FIELD(day_of_week,'mon','tue','wed','thu','fri','sat','sun')")
                        ->orderBy('start_time')
                        ->get()
                        ->map(fn($s) => [
                            'id'         => $s->id,
                            'trainer_id' => $s->trainer_id,
                            'title'      => $s->title,
                            'day'        => TrainerSchedule::DAY_LABELS[$s->day_of_week] ?? $s->day_of_week,
                            'time'       => date('g:i A', strtotime($s->start_time)).' – '.date('g:i A', strtotime($s->end_time)),
                            'price'      => $s->price ? number_format((float)$s->price, 0) : null,
                            'spots_left' => max(0, $s->max_members - $s->members->count()),
                            'is_full'    => $s->members->count() >= $s->max_members,
                            'is_shared'  => $s->max_members > 1,
                            'max'        => $s->max_members,
                            'count'      => $s->members->count(),
                        ])
                        ->values();

        // Fresh member data with all needed relations
        $member = $this->member->load([
            'trainer',
            'workoutPlan.items.activity',
            'trainerSchedules.members',
        ]);

        $trainer     = $member->trainer->first();
        $workoutPlan = $member->workoutPlan;
        $memberSlots = $trainer
            ? $member->trainerSchedules->where('trainer_id', $trainer->id)
            : collect();

        return view('livewire.member-trainer', compact(
            'member', 'trainer', 'workoutPlan', 'memberSlots',
            'allTrainers', 'allSchedules'
        ));
    }
}
