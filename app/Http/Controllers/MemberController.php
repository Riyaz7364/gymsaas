<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberPlan;
use App\Models\Plan;
use App\Models\DietPlan;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $gymId = auth()->user()->gym_id;

        $query = Member::where('gym_id', $gymId)->with('activePlan.plan');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('member_no', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($goal = $request->get('goal')) {
            $query->where('goal', $goal);
        }

        $members = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $counts = [
            'all'      => Member::where('gym_id', $gymId)->count(),
            'active'   => Member::where('gym_id', $gymId)->where('status', 'active')->count(),
            'inactive' => Member::where('gym_id', $gymId)->where('status', 'inactive')->count(),
            'frozen'   => Member::where('gym_id', $gymId)->where('status', 'frozen')->count(),
            'expired'  => Member::where('gym_id', $gymId)->where('status', 'expired')->count(),
        ];

        return view('members.index', compact('members', 'counts'));
    }

    public function create()
    {
        $gymId   = auth()->user()->gym_id;
        $plans   = Plan::where('gym_id', $gymId)->where('is_active', true)->get();
        $trainers = Trainer::where('gym_id', $gymId)->where('status', 'active')->get();
        return view('members.create', compact('plans', 'trainers'));
    }

    public function store(Request $request)
    {
        $gymId = auth()->user()->gym_id;

        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'phone'                   => 'required|string|max:20',
            'email'                   => 'nullable|email|max:255',
            'gender'                  => 'nullable|in:male,female,other',
            'dob'                     => 'nullable|date',
            'address'                 => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'goal'                    => 'nullable|in:weight_loss,muscle_gain,maintain,endurance',
            'blood_group'             => 'nullable|string|max:10',
            'occupation'              => 'nullable|string|max:255',
            'joined_at'               => 'nullable|date',
            'notes'                   => 'nullable|string',
            'whatsapp_optin'          => 'boolean',
            'avatar'                  => 'nullable|image|max:2048',
            // plan assignment
            'plan_id'                 => 'nullable|exists:plans,id',
            'plan_start_date'         => 'nullable|date',
            'trainer_id'              => 'nullable|exists:trainers,id',
        ]);

        // dd($gymId);

        // Generate member number
        $lastNo = Member::where('gym_id', $gymId)->max('id') ?? 0;
        $memberNo = 'GH-' . str_pad($lastNo + 1, 4, '0', STR_PAD_LEFT);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/members', 'public');
        }

        $member = Member::create([
            'gym_id'                  => $gymId,
            'member_no'               => $memberNo,
            'name'                    => $validated['name'],
            'phone'                   => $validated['phone'],
            'email'                   => $validated['email'] ?? null,
            'gender'                  => $validated['gender'] ?? null,
            'dob'                     => $validated['dob'] ?? null,
            'address'                 => $validated['address'] ?? null,
            'emergency_contact_name'  => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'goal'                    => $validated['goal'] ?? 'maintain',
            'blood_group'             => $validated['blood_group'] ?? null,
            'occupation'              => $validated['occupation'] ?? null,
            'joined_at'               => $validated['joined_at'] ?? today(),
            'notes'                   => $validated['notes'] ?? null,
            'whatsapp_optin'          => $request->boolean('whatsapp_optin', true),
            'avatar'                  => $avatarPath,
            'status'                  => 'active',
        ]);

        // Assign plan if selected
        if (!empty($validated['plan_id'])) {
            $plan = Plan::findOrFail($validated['plan_id']);
            $start = $validated['plan_start_date'] ?? today();
            MemberPlan::create([
                'gym_id'     => $gymId,
                'member_id'  => $member->id,
                'plan_id'    => $plan->id,
                'start_date' => $start,
                'end_date'   => \Carbon\Carbon::parse($start)->addDays($plan->duration_days),
                'price_paid' => $plan->price,
                'status'     => 'active',
            ]);
        }

        // dd($validated);
        // Assign trainer if selected
        if (!empty($validated['trainer_id'])) {
            $member->trainer()->sync([$validated['trainer_id'] => ['assigned_at' => now(), 'gym_id' => $gymId]]);
        }

        return redirect()->route('members.show', $member)
                         ->with('success', "Member {$member->name} added successfully.");
    }

    public function show(Member $member)
    {
        $this->authorizeGym($member);
        $gymId = auth()->user()->gym_id;

        $member->load([
            'activePlan.plan',
            'memberPlans.plan',
            'trainer',
            'trainerSchedules.trainer',
            'bodyStats',
            'attendances'    => fn($q) => $q->latest()->limit(20),
            'latestBodyStat',
            'dietPlan.meals',
            'workoutPlan.items.activity',
            'workoutProgress',
            'aiMessages'     => fn($q) => $q->latest()->limit(15),
        ]);

        $attendanceCount = $member->attendances()->whereMonth('check_in', now()->month)->count();
        $allTrainers     = Trainer::where('gym_id', $gymId)->where('status', 'active')->orderBy('name')->get();
        $allDietPlans    = DietPlan::where('gym_id', $gymId)->orderByDesc('created_at')->get();

        // For schedule assignment — load all trainer schedules for the gym, with member count
        $allTrainerSchedules = \App\Models\TrainerSchedule::with(['trainer', 'members'])
            ->where('gym_id', $gymId)
            ->where('is_active', true)
            ->orderByRaw("FIELD(day_of_week,'mon','tue','wed','thu','fri','sat','sun')")
            ->orderBy('start_time')
            ->get();

        return view('members.show', compact('member', 'attendanceCount', 'allTrainers', 'allDietPlans', 'allTrainerSchedules'));
    }

    public function edit(Member $member)
    {
        $this->authorizeGym($member);
        $gymId   = auth()->user()->gym_id;
        $plans   = Plan::where('gym_id', $gymId)->where('is_active', true)->get();
        $trainers = Trainer::where('gym_id', $gymId)->where('status', 'active')->get();
        return view('members.edit', compact('member', 'plans', 'trainers'));
    }

    public function update(Request $request, Member $member)
    {
        $this->authorizeGym($member);

        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'phone'                   => 'required|string|max:20',
            'email'                   => 'nullable|email|max:255',
            'gender'                  => 'nullable|in:male,female,other',
            'dob'                     => 'nullable|date',
            'address'                 => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'goal'                    => 'nullable|in:weight_loss,muscle_gain,maintain,endurance',
            'blood_group'             => 'nullable|string|max:10',
            'occupation'              => 'nullable|string|max:255',
            'joined_at'               => 'nullable|date',
            'notes'                   => 'nullable|string',
            'status'                  => 'required|in:active,inactive,frozen,expired',
            'whatsapp_optin'          => 'boolean',
            'avatar'                  => 'nullable|image|max:2048',
        ]);

        $avatarPath = $member->avatar;
        if ($request->hasFile('avatar')) {
            if ($avatarPath) Storage::disk('public')->delete($avatarPath);
            $avatarPath = $request->file('avatar')->store('avatars/members', 'public');
        }

        $member->update(array_merge($validated, ['avatar' => $avatarPath, 'whatsapp_optin' => $request->boolean('whatsapp_optin')]));

        return redirect()->route('members.show', $member)
                         ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $this->authorizeGym($member);
        if ($member->avatar) Storage::disk('public')->delete($member->avatar);
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deleted.');
    }

    public function plans(Member $member)
    {
        $this->authorizeGym($member);
        $gymId   = auth()->user()->gym_id;
        $plans   = Plan::where('gym_id', $gymId)->where('is_active', true)->get();
        $history = $member->memberPlans()->with('plan')->orderByDesc('start_date')->get();
        return view('members.plans', compact('member', 'plans', 'history'));
    }

    public function assignPlan(Request $request, Member $member)
    {
        $this->authorizeGym($member);
        $gymId = auth()->user()->gym_id;

        $request->validate([
            'plan_id'    => 'required|exists:plans,id',
            'start_date' => 'required|date',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        // Deactivate existing active plan
        MemberPlan::where('member_id', $member->id)->where('status', 'active')->update(['status' => 'expired']);

        MemberPlan::create([
            'gym_id'     => $gymId,
            'member_id'  => $member->id,
            'plan_id'    => $plan->id,
            'start_date' => $request->start_date,
            'end_date'   => \Carbon\Carbon::parse($request->start_date)->addDays($plan->duration_days),
            'price_paid' => $plan->price,
            'status'     => 'active',
        ]);

        $member->update(['status' => 'active']);

        return back()->with('success', "Plan \"{$plan->name}\" assigned successfully.");
    }

    public function freeze(Request $request, Member $member)
    {
        $this->authorizeGym($member);

        $newStatus = $member->status === 'frozen' ? 'active' : 'frozen';
        $member->update(['status' => $newStatus]);

        $msg = $newStatus === 'frozen' ? 'Member account frozen.' : 'Member account unfrozen.';
        return back()->with('success', $msg);
    }

    public function assignTrainer(Request $request, Member $member)
    {
        $this->authorizeGym($member);
        $gymId = auth()->user()->gym_id;

        $request->validate([
            'trainer_id' => 'nullable|exists:trainers,id',
        ]);

        if ($request->filled('trainer_id')) {
            $member->trainer()->sync([
                $request->trainer_id => ['assigned_at' => now(), 'gym_id' => $gymId],
            ]);
            return back()->with('success', 'Trainer assigned successfully.');
        }

        $member->trainer()->detach();
        // Also remove member from any schedule slots belonging to those trainers
        $member->trainerSchedules()->detach();
        return back()->with('success', 'Trainer removed.');
    }

    public function assignDietPlan(Request $request, Member $member)
    {
        $this->authorizeGym($member);

        $request->validate([
            'diet_plan_id' => 'required|exists:diet_plans,id',
        ]);

        // Deactivate other diet plans for this member
        DietPlan::where('member_id', $member->id)->update(['is_active' => false]);

        $plan = DietPlan::findOrFail($request->diet_plan_id);
        $plan->update(['member_id' => $member->id, 'is_active' => true]);

        return back()->with('success', "Diet plan \"{$plan->name}\" activated for {$member->name}.");
    }

    public function assignTrainerSchedule(Request $request, Member $member)
    {
        $this->authorizeGym($member);
        $gymId = auth()->user()->gym_id;

        $request->validate([
            'trainer_id'  => 'required|exists:trainers,id',
            'schedule_id' => 'nullable|exists:trainer_schedules,id',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $trainerId  = $request->trainer_id;
        $scheduleId = $request->schedule_id;

        // 1. Sync trainer_members pivot
        $member->trainer()->sync([
            $trainerId => ['assigned_at' => now()->toDateString(), 'gym_id' => $gymId],
        ]);

        // 2. Detach from any previous schedule slots belonging to this trainer
        $oldSlotIds = \App\Models\TrainerSchedule::where('trainer_id', $trainerId)
                        ->where('gym_id', $gymId)
                        ->pluck('id');
        if ($oldSlotIds->isNotEmpty()) {
            $member->trainerSchedules()->detach($oldSlotIds);
        }

        // 3. Attach to new slot (allow joining even if "full" — allows friends sharing same slot)
        if ($scheduleId) {
            $schedule = \App\Models\TrainerSchedule::where('id', $scheduleId)
                            ->where('gym_id', $gymId)
                            ->firstOrFail();

            $pivotData = ['assigned_at' => now()->toDateString()];
            if ($request->filled('start_date')) $pivotData['start_date'] = $request->start_date;
            if ($request->filled('end_date'))   $pivotData['end_date']   = $request->end_date;

            $schedule->members()->syncWithoutDetaching([$member->id => $pivotData]);

            $currentCount = $schedule->members()->count();
            if ($currentCount > $schedule->max_members) {
                return back()->with('success', "Trainer assigned to \"{$schedule->title}\" ({$currentCount}/{$schedule->max_members} sharing).");
            }
            return back()->with('success', "Trainer assigned to \"{$schedule->title}\" slot.");
        }

        return back()->with('success', 'Trainer assigned successfully.');
    }

    private function authorizeGym(Member $member): void
    {
        if (!auth()->user()->isSuperAdmin() && $member->gym_id !== auth()->user()->gym_id) {
            abort(403);
        }
    }
}
