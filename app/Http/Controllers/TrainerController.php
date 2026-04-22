<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\TrainerSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::withCount('members')
            ->where('gym_id', auth()->user()->gym_id)
            ->orderBy('name')
            ->get();

        return view('trainers.index', compact('trainers'));
    }

    public function create()
    {
        return view('trainers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:120'],
            'email'            => ['nullable', 'email', 'max:120'],
            'phone'            => ['required', 'string', 'max:20'],
            'specialization'   => ['nullable', 'string', 'max:120'],
            'bio'              => ['nullable', 'string'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'salary'           => ['nullable', 'numeric', 'min:0'],
            'status'           => ['required', 'in:active,inactive'],
            'joined_at'        => ['nullable', 'date'],
            'avatar'           => ['nullable', 'image', 'max:2048'],
        ]);

        $data['gym_id'] = auth()->user()->gym_id;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars/trainers', 'public');
        }

        Trainer::create($data);

        return redirect()->route('trainers.index')->with('success', "Trainer {$data['name']} added.");
    }

    public function show(Trainer $trainer)
    {
        abort_if($trainer->gym_id !== auth()->user()->gym_id, 403);

        $gymId   = auth()->user()->gym_id;
        $currency = auth()->user()->gym?->currency ?? '₹';

        // Load schedule with member count
        $trainer->load([
            'activeSchedules.members',
            'members.activePlan.plan',
            'members.workoutPlan',
        ]);

        // Payment history: payments made by members assigned to this trainer
        $memberIds = $trainer->members->pluck('id');
        $payments  = \App\Models\Payment::with('member', 'invoice')
                        ->whereIn('member_id', $memberIds)
                        ->where('gym_id', $gymId)
                        ->where('status', 'success')
                        ->latest('paid_at')
                        ->limit(30)
                        ->get();

        return view('trainers.show', compact('trainer', 'currency', 'payments'));
    }

    public function edit(Trainer $trainer)
    {
        abort_if($trainer->gym_id !== auth()->user()->gym_id, 403);
        $trainer->loadCount('members');
        return view('trainers.edit', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        abort_if($trainer->gym_id !== auth()->user()->gym_id, 403);

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:120'],
            'email'            => ['nullable', 'email', 'max:120'],
            'phone'            => ['required', 'string', 'max:20'],
            'specialization'   => ['nullable', 'string', 'max:120'],
            'bio'              => ['nullable', 'string'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'salary'           => ['nullable', 'numeric', 'min:0'],
            'status'           => ['required', 'in:active,inactive'],
            'joined_at'        => ['nullable', 'date'],
            'avatar'           => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($trainer->avatar) {
                Storage::disk('public')->delete($trainer->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars/trainers', 'public');
        }

        $trainer->update($data);

        return redirect()->route('trainers.index')->with('success', "Trainer {$trainer->name} updated.");
    }

    public function destroy(Trainer $trainer)
    {
        abort_if($trainer->gym_id !== auth()->user()->gym_id, 403);

        if ($trainer->avatar) {
            Storage::disk('public')->delete($trainer->avatar);
        }

        $trainer->delete();

        return redirect()->route('trainers.index')->with('success', 'Trainer removed.');
    }

    // ─── Schedule Management ──────────────────────────────────────────

    public function scheduleStore(Request $request, Trainer $trainer)
    {
        abort_if($trainer->gym_id !== auth()->user()->gym_id, 403);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:120'],
            'day_of_week' => ['required', 'in:mon,tue,wed,thu,fri,sat,sun'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'max_members' => ['required', 'integer', 'min:1', 'max:20'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        $data['gym_id']     = auth()->user()->gym_id;
        $data['trainer_id'] = $trainer->id;
        $data['is_active']  = true;

        TrainerSchedule::create($data);

        return back()->with('success', 'Schedule slot added.');
    }

    public function scheduleDestroy(Trainer $trainer, TrainerSchedule $schedule)
    {
        abort_if($trainer->gym_id !== auth()->user()->gym_id, 403);
        abort_if($schedule->trainer_id !== $trainer->id, 403);

        $schedule->delete();

        return back()->with('success', 'Schedule slot removed.');
    }
}
