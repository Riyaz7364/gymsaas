<x-layouts.app>
    <x-slot:title>{{ $member->name }} — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>{{ $member->name }}</x-slot:header>
    <x-slot:topbarTitle>Members</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        Home / <a href="{{ gym_route('gym.members.index') }}" style="color:var(--gh-primary);text-decoration:none;">Members</a> / {{ $member->name }}
    </x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="gh-alert gh-alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    @php
        $goalMap = [
            'weight_loss'  => ['label' => 'Weight Loss',  'color' => '#ef4444', 'bg' => '#fef2f2', 'icon' => '🔥'],
            'muscle_gain'  => ['label' => 'Muscle Gain',  'color' => '#0abf8e', 'bg' => '#f0fdf9', 'icon' => '💪'],
            'maintain'     => ['label' => 'Maintain',     'color' => '#3b82f6', 'bg' => '#eff6ff', 'icon' => '⚖️'],
            'endurance'    => ['label' => 'Endurance',    'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'icon' => '🏃'],
        ];
        $goalInfo  = $goalMap[$member->goal] ?? ['label' => ucfirst($member->goal ?? 'Not set'), 'color' => '#9ca3af', 'bg' => '#f9fafb', 'icon' => '🎯'];
        $statusMap = ['active'=>'gh-badge-success','frozen'=>'gh-badge-info','expired'=>'gh-badge-danger','inactive'=>'gh-badge-muted'];
        $currency  = auth()->user()->gym?->currency ?? '₹';
        $ap        = $member->activePlan;
        $daysLeft  = $ap ? max(0, (int) ceil(now()->diffInDays($ap->end_date, false))) : 0;
    @endphp

    <div style="display:grid; grid-template-columns:320px 1fr; gap:20px; align-items:start;">

        {{-- LEFT: Profile card --}}
        <div style="display:flex; flex-direction:column; gap:14px;">

            {{-- Main profile --}}
            <div class="gh-card">
                <div class="gh-card-body" style="text-align:center; padding:28px 20px 20px;">
                    <img src="{{ $member->avatar ? asset('storage/'.$member->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&color=fff&background=0abf8e&size=96&bold=true' }}"
                         style="width:88px; height:88px; border-radius:50%; object-fit:cover; border:3px solid var(--gh-border); margin-bottom:12px;" alt="">
                    <h2 style="font-size:18px; font-weight:700; margin:0 0 2px;">{{ $member->name }}</h2>
                    <p style="font-size:12px; color:#9ca3af; margin:0 0 10px;">{{ $member->member_no }}</p>

                    <span class="gh-badge {{ $statusMap[$member->status] ?? 'gh-badge-muted' }}" style="font-size:13px; padding:4px 14px;">
                        {{ ucfirst($member->status) }}
                    </span>

                    {{-- Goal badge --}}
                    <div style="margin-top:10px; display:inline-flex; align-items:center; gap:6px; padding:6px 14px; background:{{ $goalInfo['bg'] }}; border-radius:20px; border:1px solid {{ $goalInfo['color'] }}30;">
                        <span style="font-size:15px;">{{ $goalInfo['icon'] }}</span>
                        <span style="font-size:12px; font-weight:700; color:{{ $goalInfo['color'] }};">{{ $goalInfo['label'] }}</span>
                    </div>

                    {{-- Quick stats --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:14px;">
                        <div style="background:#f8fafc; border-radius:8px; padding:10px;">
                            <div style="font-size:18px; font-weight:700; color:var(--gh-primary);">{{ $attendanceCount }}</div>
                            <div style="font-size:11px; color:#9ca3af;">visits this month</div>
                        </div>
                        <div style="background:#f8fafc; border-radius:8px; padding:10px;">
                            <div style="font-size:18px; font-weight:700; color:{{ $daysLeft <= 7 && $daysLeft > 0 ? '#f97316' : ($daysLeft == 0 && $ap ? '#ef4444' : 'var(--gh-primary)') }};">
                                {{ $ap ? $daysLeft : '—' }}
                            </div>
                            <div style="font-size:11px; color:#9ca3af;">days left</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex; gap:8px; margin-top:14px; justify-content:center; flex-wrap:wrap;">
                        <a href="{{ gym_route('gym.members.edit', [$member]) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ gym_route('gym.members.freeze', [$member]) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="gh-btn gh-btn-outline gh-btn-sm"
                                    style="{{ $member->status === 'frozen' ? 'color:#3b82f6;border-color:#3b82f6;' : '' }}">
                                {{ $member->status === 'frozen' ? 'Unfreeze' : 'Freeze' }}
                            </button>
                        </form>
                        <a href="{{ gym_route('gym.members.plans', [$member]) }}" class="gh-btn gh-btn-primary gh-btn-sm">+ Plan</a>
                    </div>
                </div>
            </div>

            {{-- Detail rows --}}
            <div class="gh-card">
                <div class="gh-card-header"><h3 class="gh-card-title">Profile</h3></div>
                <div class="gh-card-body" style="padding:0;">
                    @php
                        $rows = [
                            ['Phone',       $member->phone],
                            ['Email',       $member->email ?: '—'],
                            ['Gender',      ucfirst($member->gender ?? '—')],
                            ['DOB',         optional($member->dob)->format('d M Y') ?? '—'],
                            ['Blood Group', $member->blood_group ?: '—'],
                            ['Occupation',  $member->occupation ?: '—'],
                            ['Joined',      optional($member->joined_at)->format('d M Y') ?? '—'],
                        ];
                    @endphp
                    @foreach($rows as [$label, $value])
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 16px; border-bottom:1px solid var(--gh-border);">
                        <span style="font-size:12px; color:#6b7280;">{{ $label }}</span>
                        <span style="font-size:13px; font-weight:500;">{{ $value }}</span>
                    </div>
                    @endforeach
                    @if($member->address)
                    <div style="padding:9px 16px;">
                        <span style="font-size:12px; color:#6b7280; display:block; margin-bottom:3px;">Address</span>
                        <span style="font-size:13px;">{{ $member->address }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Emergency Contact --}}
            @if($member->emergency_contact_name || $member->emergency_contact_phone)
            <div class="gh-card">
                <div class="gh-card-header"><h3 class="gh-card-title">Emergency Contact</h3></div>
                <div class="gh-card-body">
                    <p style="font-size:14px; font-weight:500;">{{ $member->emergency_contact_name }}</p>
                    <p style="font-size:13px; color:#6b7280; margin-top:4px;">{{ $member->emergency_contact_phone }}</p>
                </div>
            </div>
            @endif

            {{-- WhatsApp AI opt-in --}}
            @if(auth()->user()->gymHasModule('whatsapp_updates'))
            <div class="gh-card">
                <div class="gh-card-body" style="display:flex; align-items:center; gap:12px;">
                    <span style="font-size:22px;">💬</span>
                    <div style="flex:1;">
                        <div style="font-size:13px; font-weight:600;">WhatsApp AI</div>
                        <div style="font-size:12px; color:#6b7280;">Messages on check-in</div>
                    </div>
                    <span class="gh-badge {{ $member->whatsapp_optin ? 'gh-badge-success' : 'gh-badge-muted' }}">
                        {{ $member->whatsapp_optin ? 'Opted In' : 'Opted Out' }}
                    </span>
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Tabs --}}
        <div x-data="{ tab: 'plan' }">

            {{-- Tab nav --}}
            <div style="display:flex; gap:2px; margin-bottom:16px; border-bottom:2px solid var(--gh-border); padding-bottom:0; flex-wrap:wrap;">
           
                @foreach(auth()->user()->membersTabs() as $key => $info)
                <button @click="tab = '{{ $key }}'"
                        :style="tab === '{{ $key }}' ? 'border-bottom:2px solid var(--gh-primary); color:var(--gh-primary); margin-bottom:-2px;' : ''"
                        class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded m-1">
                    <span>{{ $info['icon'] }}</span> {{ $info['label'] }}
                </button>
                @endforeach
            </div>

            {{-- ─── PLAN TAB ─── --}}
            <div x-show="tab === 'plan'" x-transition>
                @if($member->activePlan)
                @php $ap = $member->activePlan; @endphp
                <div class="gh-card" style="margin-bottom:16px;">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Active Plan</h3>
                        <span class="gh-badge gh-badge-success">Active</span>
                    </div>
                    <div class="gh-card-body">
                        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px;">
                            <div>
                                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Plan</div>
                                <div style="font-size:15px; font-weight:700;">{{ $ap->plan->name ?? '—' }}</div>
                            </div>
                            <div>
                                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Start</div>
                                <div style="font-size:15px; font-weight:700;">{{ optional($ap->start_date)->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Expiry</div>
                                <div style="font-size:15px; font-weight:700; {{ $ap->end_date?->isPast() ? 'color:#ef4444;' : '' }}">
                                    {{ optional($ap->end_date)->format('d M Y') }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Days Left</div>
                                <div style="font-size:15px; font-weight:700; {{ $daysLeft <= 7 ? 'color:#f97316;' : '' }}">{{ $daysLeft }} days</div>
                            </div>
                        </div>
                        @if($ap->start_date && $ap->end_date)
                        @php
                            $total    = max(1, $ap->start_date->diffInDays($ap->end_date));
                            $elapsed  = $ap->start_date->diffInDays(now());
                            $progress = min(100, round(($elapsed / $total) * 100));
                        @endphp
                        <div style="margin-top:16px;">
                            <div style="display:flex; justify-content:space-between; font-size:12px; color:#6b7280; margin-bottom:6px;">
                                <span>Plan progress</span><span>{{ $progress }}%</span>
                            </div>
                            <div style="height:8px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                                <div style="height:100%; width:{{ $progress }}%; background:{{ $progress >= 90 ? '#ef4444' : ($progress >= 75 ? '#f97316' : '#0abf8e') }}; border-radius:4px;"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div style="text-align:center; padding:40px; background:#fff; border-radius:10px; border:1px solid var(--gh-border); margin-bottom:16px;">
                    <p style="color:#9ca3af; margin-bottom:12px;">No active membership plan.</p>
                    <a href="{{ gym_route('gym.members.plans', [$member]) }}" class="gh-btn gh-btn-primary">Assign Plan</a>
                </div>
                @endif
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Plan History</h3>
                        <a href="{{ gym_route('gym.members.plans', [$member]) }}" class="gh-btn gh-btn-outline gh-btn-sm">Manage</a>
                    </div>
                    <div class="gh-card-body" style="padding:0;">
                        <table class="gh-table">
                            <thead><tr><th>Plan</th><th>Start</th><th>End</th><th>Amount</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($member->memberPlans->take(6) as $mp)
                                <tr>
                                    <td>{{ $mp->plan->name ?? '—' }}</td>
                                    <td style="font-size:13px;">{{ optional($mp->start_date)->format('d M Y') }}</td>
                                    <td style="font-size:13px;">{{ optional($mp->end_date)->format('d M Y') }}</td>
                                    <td style="font-size:13px;">{{ $currency }}{{ number_format($mp->price_paid) }}</td>
                                    <td>
                                        @php $sm = ['active'=>'gh-badge-success','expired'=>'gh-badge-danger','frozen'=>'gh-badge-info']; @endphp
                                        <span class="gh-badge {{ $sm[$mp->status] ?? 'gh-badge-muted' }}">{{ ucfirst($mp->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" style="text-align:center; color:#9ca3af; padding:20px;">No plan history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>{{-- /plan --}}

            {{-- ─── ATTENDANCE TAB ─── --}}
            <div x-show="tab === 'attendance'" x-transition>
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Attendance History</h3>
                        <span class="gh-badge gh-badge-primary">{{ $attendanceCount }} this month</span>
                    </div>
                    <div class="gh-card-body" style="padding:0;">
                        <table class="gh-table">
                            <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Duration</th></tr></thead>
                            <tbody>
                                @forelse($member->attendances as $att)
                                <tr>
                                    <td style="font-size:13px;">{{ \Carbon\Carbon::parse($att->check_in)->format('d M Y') }}</td>
                                    <td style="font-size:13px; color:#10b981;">{{ \Carbon\Carbon::parse($att->check_in)->format('h:i A') }}</td>
                                    <td style="font-size:13px; color:#6b7280;">
                                        @if($att->check_out)
                                            {{ \Carbon\Carbon::parse($att->check_out)->format('h:i A') }}
                                        @else
                                            <span style="color:#f97316;">Active</span>
                                        @endif
                                    </td>
                                    <td style="font-size:13px;">
                                        @if($att->check_out)
                                            @php $mins = \Carbon\Carbon::parse($att->check_in)->diffInMinutes($att->check_out); @endphp
                                            {{ floor($mins/60) }}h {{ $mins%60 }}m
                                        @else —
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" style="text-align:center; color:#9ca3af; padding:24px;">No attendance records.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>{{-- /attendance --}}

            {{-- ─── DIET PLAN TAB ─── --}}
            <div x-show="tab === 'diet'" x-transition>
                @if($member->dietPlan)
                @php
                    $dp         = $member->dietPlan;
                    $goalLabels = ['weight_loss'=>'Weight Loss','muscle_gain'=>'Muscle Gain','maintain'=>'Maintain','endurance'=>'Endurance'];
                @endphp

                {{-- Plan header card --}}
                <div class="gh-card" style="margin-bottom:16px;">
                    <div class="gh-card-header">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <h3 class="gh-card-title">{{ $dp->name }}</h3>
                            @if($dp->ai_generated)<span class="gh-badge gh-badge-info">AI</span>@endif
                            @if($dp->goal)<span class="gh-badge gh-badge-success" style="font-size:11px;">{{ $goalLabels[$dp->goal] ?? $dp->goal }}</span>@endif
                        </div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <a href="{{ gym_route('gym.diet-plans.edit', $dp) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit Plan</a>
                            <span class="gh-badge gh-badge-success">Active</span>
                        </div>
                    </div>
                    <div class="gh-card-body">
                        @if($dp->description)
                        <p style="font-size:13px; color:#6b7280; line-height:1.6; margin-bottom:14px;">{{ $dp->description }}</p>
                        @endif

                        {{-- Livewire: meals list + add form (no page reload) --}}
                        @livewire('diet-meals', ['dietPlan' => $dp], key('diet-meals-'.$dp->id))
                    </div>
                </div>
                @else
                <div style="text-align:center; padding:40px; background:#fff; border-radius:10px; border:1px solid var(--gh-border); margin-bottom:16px;">
                    <div style="font-size:40px; margin-bottom:12px;">🥗</div>
                    <p style="color:#9ca3af; font-weight:500; margin-bottom:4px;">No active diet plan.</p>
                    <p style="font-size:12px; color:#9ca3af; margin-bottom:16px;">Assign an existing plan or create one for this member.</p>
                    <a href="{{ gym_route('gym.diet-plans.create') }}?member_id={{ $member->id }}" class="gh-btn gh-btn-primary">Create New Diet Plan</a>
                </div>
                @endif

                {{-- Assign diet plan card --}}
                <div class="gh-card">
                    <div class="gh-card-header"><h3 class="gh-card-title">Assign Diet Plan</h3></div>
                    <div class="gh-card-body">
                        @if($allDietPlans->count())
                        <form method="POST" action="{{ gym_route('gym.members.assign-diet-plan', [$member]) }}" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                            @csrf @method('PATCH')
                            <div style="flex:1; min-width:220px;">
                                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Select plan</label>
                                <select name="diet_plan_id" class="gh-input" required>
                                    <option value="">— choose diet plan —</option>
                                    @foreach($allDietPlans as $dp)
                                    <option value="{{ $dp->id }}" {{ $member->dietPlan?->id === $dp->id ? 'selected' : '' }}>
                                        {{ $dp->name }}{{ $dp->is_default ? ' (Default)' : '' }}{{ $dp->ai_generated ? ' (AI)' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="gh-btn gh-btn-primary">Assign</button>
                            <a href="{{ gym_route('gym.diet-plans.create') }}?member_id={{ $member->id }}" class="gh-btn gh-btn-outline">New Plan</a>
                        </form>
                        @else
                        <p style="color:#9ca3af; font-size:13px; margin-bottom:12px;">No diet plans exist yet.</p>
                        <a href="{{ gym_route('gym.diet-plans.create') }}?member_id={{ $member->id }}" class="gh-btn gh-btn-primary gh-btn-sm">Create First Diet Plan</a>
                        @endif
                    </div>
                </div>
            </div>{{-- /diet --}}

            {{-- ─── TRAINER TAB ─── --}}
            <div x-show="tab === 'trainer'" x-transition>
                @livewire('member-trainer', ['member' => $member], key('member-trainer-'.$member->id))
            </div>{{-- /trainer --}}

            {{-- ─── WORKOUT PLAN TAB ─── --}}
            <div x-show="tab === 'workout'" x-transition>
            @php
                $wp = $member->workoutProgress;
                $sequence = $wp ? $wp->sequence : null;
                
                // If no sequence, try to get the default one
                if (!$sequence) {
                    $wp = \App\Models\MemberWorkoutProgress::forMember($member->id, $member->gym_id);
                    $sequence = $wp->sequence;
                }
                
                $totalSteps = ($wp && $wp->total_steps > 0) ? $wp->total_steps : 1;
                $currentStep = $wp ? $wp->current_step : 1;
                $currentWorkout = $wp ? $wp->currentWorkout() : null;
                $nextStep = ($currentStep % $totalSteps) + 1;
                $nextWorkout = $sequence ? $sequence->getDayByNumber($nextStep) : null;
                
                // Eager load activities for all exercises
                if ($currentWorkout) {
                    $currentWorkout->load('exercises.activity');
                }
                if ($sequence) {
                    $sequence->load('days.exercises.activity');
                }
            @endphp

            @if($sequence && (!isset($sequence->total_days) || $sequence->total_days == 0 || $sequence->days()->count() == 0))
            <div class="gh-card" style="text-align:center; padding:40px 20px; border:2px dashed #fca5a5;">
                <div style="font-size:36px; margin-bottom:12px;">⚠️</div>
                <p style="color:#ef4444; font-weight:bold; margin-bottom:8px;">Empty Workout Sequence</p>
                <p style="font-size:13px; color:#6b7280; margin-bottom:16px;">The assigned sequence "<strong>{{ $sequence->name }}</strong>" has no days or exercises configured.</p>
                <a href="{{ gym_route('gym.workout-sequences.index') }}" class="gh-btn gh-btn-outline">Manage Sequences</a>
            </div>
            @elseif($currentWorkout)
            {{-- Current day card --}}
            <div class="gh-card" style="margin-bottom:16px; border:2px solid {{ $currentWorkout->border }};">
                <div class="gh-card-header" style="background:{{ $currentWorkout->bg }};">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:28px;">{{ $currentWorkout->icon }}</span>
                        <div>
                            <div style="font-size:16px; font-weight:700; color:{{ $currentWorkout->color }};">{{ $currentWorkout->label }}</div>
                            <div style="font-size:12px; color:#6b7280;">Visit {{ $currentStep }} of {{ $totalSteps }}-day cycle</div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Next visit</div>
                        <div style="font-size:13px; font-weight:600; color:#374151;">{{ $nextWorkout ? $nextWorkout->icon . ' ' . $nextWorkout->label : '—' }}</div>
                    </div>
                </div>
                <div class="gh-card-body">

                    {{-- Cycle progress dots --}}
                    <div style="display:flex; gap:8px; align-items:center; margin-bottom:20px;">
                        @foreach($sequence->days() as $day)
                        <div style="flex:1; text-align:center;">
                            <div style="width:36px; height:36px; border-radius:50%; margin:0 auto 4px;
                                        display:flex; align-items:center; justify-content:center; font-size:16px;
                                        background:{{ $day->day_number == $currentStep ? $day->color : ($day->day_number < $currentStep ? '#d1fae5' : '#f3f4f6') }};
                                        border:2px solid {{ $day->day_number == $currentStep ? $day->color : ($day->day_number < $currentStep ? '#6ee7b7' : '#e5e7eb') }};
                                        color:{{ $day->day_number == $currentStep ? '#fff' : '#9ca3af' }};">
                                {{ $day->icon }}
                            </div>
                            <div style="font-size:10px; color:{{ $day->day_number == $currentStep ? $day->color : '#9ca3af' }}; font-weight:{{ $day->day_number == $currentStep ? '700' : '400' }};">
                                D{{ $day->day_number }}
                            </div>
                        </div>
                        @if(!$loop->last)
                        <div style="height:2px; flex:0.5; background:{{ $day->day_number < $currentStep ? '#6ee7b7' : '#e5e7eb' }}; margin-bottom:20px;"></div>
                        @endif
                        @endforeach
                    </div>

                    {{-- Muscle groups --}}
                    @if(!empty($currentWorkout->muscle_groups))
                    <div style="margin-bottom:16px;">
                        <div style="font-size:12px; font-weight:600; color:#374151; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.4px;">Muscle Groups</div>
                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                            @foreach($currentWorkout->muscle_groups as $mg)
                            <span style="background:{{ $currentWorkout->bg }}; border:1px solid {{ $currentWorkout->border }}; color:{{ $currentWorkout->color }}; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">
                                {{ $mg }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Exercise list --}}
                    @if($currentWorkout->exercises->count() > 0)
                    <div>
                        <div style="font-size:12px; font-weight:600; color:#374151; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.4px;">Exercises</div>
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            @foreach($currentWorkout->exercises as $exercise)
                            <div style="display:flex; align-items:center; gap:10px; background:#f8fafc; border-radius:8px; padding:10px 14px;">
                                <span style="width:22px; height:22px; border-radius:50%; background:{{ $currentWorkout->color }}; color:#fff; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $loop->iteration }}</span>
                                <div style="flex:1;">
                                    <div style="font-size:13px; font-weight:500; color:#374151;">{{ $exercise->activity->name ?? '—' }}</div>
                                    @if($exercise->activity)
                                    <div style="font-size:11px; color:#9ca3af;">{{ $exercise->activity->muscle_group }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($wp && $wp->last_checkin_date)
                    <div style="margin-top:14px; font-size:12px; color:#9ca3af;">
                        🕐 Last check-in: {{ $wp->last_checkin_date->format('d M Y') }} &nbsp;·&nbsp;
                        Step advances automatically on next check-in.
                    </div>
                    @else
                    <div style="margin-top:14px; font-size:12px; color:#9ca3af;">
                        ⚡ Step 1 ({{ $currentWorkout->label }}) is today's workout — will advance to the next day after first check-in.
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="gh-card" style="text-align:center; padding:40px 20px;">
                <div style="font-size:36px; margin-bottom:12px;">📋</div>
                <p style="color:#9ca3af; margin-bottom:12px;">No workout sequence assigned yet.</p>
                <p style="font-size:12px; color:#9ca3af;">The default sequence will be assigned automatically.</p>
            </div>
            @endif

            {{-- Full cycle overview --}}
            @if($sequence)
            <div class="gh-card">
                <div class="gh-card-header">
                    <h3 class="gh-card-title">Full Cycle ({{ $sequence->total_days }}-Days)</h3>
                    <span class="gh-badge" style="background:#f3f4f6; color:#6b7280;">{{ $sequence->name }}</span>
                </div>
                <div class="gh-card-body" style="padding:0;">
                    @forelse($sequence->days() as $day)
                    <div style="display:flex; align-items:center; gap:14px; padding:12px 18px; border-bottom:{{ !$loop->last ? '1px solid var(--gh-border)' : 'none' }}; background:{{ $day->day_number == $currentStep ? $day->bg : 'transparent' }};">
                        <div style="width:32px; height:32px; border-radius:50%; flex-shrink:0;
                                    display:flex; align-items:center; justify-content:center; font-size:16px;
                                    background:{{ $day->day_number == $currentStep ? $day->color : '#f3f4f6' }};">
                            {{ $day->icon }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:13px; font-weight:{{ $day->day_number == $currentStep ? '700' : '500' }}; color:{{ $day->day_number == $currentStep ? $day->color : '#374151' }};">
                                Day {{ $day->day_number }} — {{ $day->label }}
                                @if($day->day_number == $currentStep)<span style="font-size:11px; font-weight:600; background:{{ $day->color }}; color:#fff; padding:2px 8px; border-radius:12px; margin-left:6px;">Current</span>@endif
                            </div>
                            <div style="font-size:12px; color:#9ca3af; margin-top:2px;">{{ implode(' · ', $day->muscle_groups ?? []) }}</div>
                        </div>
                        <div style="font-size:12px; color:#9ca3af;">{{ $day->exercises->count() }} exercises</div>
                    </div>
                    @empty
                    <div style="padding: 24px; text-align: center; color: #9ca3af; font-size: 13px;">No days have been configured for this cycle.</div>
                    @endforelse
                </div>
            </div>
            @endif
            </div>{{-- /workout --}}

            {{-- ─── AI MESSAGES TAB ─── --}}
            <div x-show="tab === 'messages'" x-transition>
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">AI Messages Sent</h3>
                        <span class="gh-badge gh-badge-primary">{{ $member->aiMessages->count() }} messages</span>
                    </div>
                    <div class="gh-card-body" style="padding:0;">
                        @forelse($member->aiMessages as $msg)
                        <div style="padding:14px 18px; border-bottom:1px solid var(--gh-border);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <span style="font-size:11px; color:#9ca3af;">{{ $msg->created_at->format('d M Y h:i A') }}</span>
                                @if($msg->sent_via_whatsapp)
                                <span class="gh-badge gh-badge-success" style="font-size:10px;">WhatsApp ✓</span>
                                @endif
                            </div>
                            @if($msg->whatsapp_body)
                            <div style="background:#f0fdf9; border-left:3px solid #0abf8e; padding:10px 14px; border-radius:0 8px 8px 0; font-size:13px; line-height:1.6;">
                                {{ $msg->whatsapp_body }}
                            </div>
                            @elseif($msg->ai_response)
                            <div style="background:#f8fafc; border-left:3px solid #e2e8f0; padding:10px 14px; border-radius:0 8px 8px 0; font-size:13px; line-height:1.6; color:#374151;">
                                {{ $msg->ai_response }}
                            </div>
                            @endif
                        </div>
                        @empty
                        <div style="text-align:center; padding:48px 24px;">
                            <div style="font-size:36px; margin-bottom:12px;">🤖</div>
                            <p style="color:#9ca3af; font-weight:500; margin-bottom:4px;">No AI messages sent yet.</p>
                            <p style="font-size:12px; color:#9ca3af;">Messages are auto-sent when the member checks in.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>{{-- /messages --}}

            {{-- ─── BODY STATS TAB ─── --}}
            @php
                $latestBodyStat = $member->latestBodyStat;
                $latestPhotos = $latestBodyStat
                    ? $latestBodyStat->photos->sortByDesc('created_at')->unique('id')->values()
                    : collect();

                $galleryPhotos = $latestPhotos->map(function($photo) use ($latestBodyStat) {
                    return [
                        'id' => $photo->id,
                        'url' => asset('storage/' . $photo->photo_path),
                        'date' => optional($latestBodyStat->date)->format('d M Y'),
                        'label' => optional($latestBodyStat->date)->format('d M Y') . ' progress photo',
                    ];
                })->values()->toArray();

                $galleryPhotoIndexes = [];
                foreach ($galleryPhotos as $index => $photo) {
                    $galleryPhotoIndexes[$photo['id']] = $index;
                }
            @endphp
            <div x-show="tab === 'health'" x-transition x-data="bodyStatGallery(@js($galleryPhotos))">
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Latest Body Stats</h3>
                        <div style="display:flex; gap:8px;">
                            @if($member->latestBodyStat)
                            <a href="{{ gym_route('gym.progress-photos.index', ['gym' => $gym, 'member_id' => $member->id]) }}" class="gh-btn gh-btn-outline gh-btn-sm">View Uploads</a>
                            <a href="{{ gym_route('gym.body-stats.index', ['gym' => $gym, 'member_id' => $member->id]) }}" class="gh-btn gh-btn-outline gh-btn-sm">View All</a>
                            @endif
                            <a href="{{ gym_route('gym.body-stats.create') }}?member_id={{ $member->id }}" class="gh-btn gh-btn-primary gh-btn-sm">+ Add Stats</a>
                        </div>
                    </div>
                    <div class="gh-card-body">
                        @if($member->latestBodyStat)
                        @php $bs = $member->latestBodyStat; @endphp
                        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:16px;">
                            @foreach([['Weight','🏋️',$bs->weight ? $bs->weight.' kg' : '—'], ['Height','📏',$bs->height ? $bs->height.' cm' : '—'], ['BMI','⚖️',$bs->bmi ?? '—'], ['Body Fat','🔥',$bs->body_fat_pct ? $bs->body_fat_pct.'%' : '—']] as [$l, $icon, $v])
                            <div style="text-align:center; background:#f8fafc; border-radius:10px; padding:16px 8px;">
                                <div style="font-size:22px; margin-bottom:4px;">{{ $icon }}</div>
                                <div style="font-size:20px; font-weight:700; color:var(--gh-primary);">{{ $v }}</div>
                                <div style="font-size:11px; color:#6b7280; margin-top:4px;">{{ $l }}</div>
                            </div>
                            @endforeach
                        </div>
                        <p style="font-size:12px; color:#9ca3af;">Last updated: {{ optional($bs->date)->format('d M Y') }}</p>

                        @if($latestPhotos->count() > 0)
                        <div style="margin-top: 16px;">
                            <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:12px;">Latest Progress Photos</div>
                            <div class="bodystats-gallery" style="display:grid; grid-gap:10px; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); grid-auto-rows:250px 150px; grid-auto-flow:dense;">
                                @foreach($latestPhotos as $index => $photo)
                                <div class="item" style="overflow:hidden; border-radius:18px; box-shadow:0 16px 40px rgba(15,23,42,.12);">
                                    <button type="button" @click="open({{ $galleryPhotoIndexes[$photo->id] }})" style="all:unset; cursor:pointer; display:block; width:100%; height:100%;">
                                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Body stat photo" style="width:100%; height:100%; object-fit:cover; display:block;" />
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @else
                        <div style="text-align:center; padding:40px 0;">
                            <div style="font-size:36px; margin-bottom:12px;">📊</div>
                            <p style="color:#9ca3af; margin-bottom:16px;">No body stats recorded yet.</p>
                            <a href="{{ route('body-stats.create') }}?member_id={{ $member->id }}" class="gh-btn gh-btn-primary gh-btn-sm">Record First Stats</a>
                        </div>
                        @endif
                    </div>
                </div>

                <div x-show="activeIndex !== null" x-cloak style="position:fixed; inset:0; z-index:99999; background:rgba(15,23,42,.88); display:flex; align-items:center; justify-content:center; padding:24px;">
                    <div @click.away="close()" style="position:relative; max-width:920px; width:100%; max-height:calc(100vh - 48px); background:#111; border-radius:24px; overflow:hidden; box-shadow:0 32px 90px rgba(0,0,0,.45);">
                        <button type="button" @click="close()" style="position:absolute; top:14px; right:14px; z-index:10; width:42px; height:42px; border:none; border-radius:50%; background:rgba(255,255,255,.18); color:#fff; font-size:22px; cursor:pointer;">×</button>
                        <div style="padding:20px; display:flex; align-items:center; justify-content:center; min-height:360px;">
                            <img :src="activePhoto.url" :alt="activePhoto.label" draggable="false" style="max-width:100%; max-height:calc(100vh - 120px); object-fit:contain; border-radius:16px;" />
                        </div>
                        <div style="padding:14px 20px; background:#0f172a; color:#e2e8f0; font-size:14px; text-align:center;">{{-- placeholder --}}<span x-text="activePhoto.label"></span></div>
                    </div>
                </div>
            </div>{{-- /health --}}

            {{-- ─── NOTES TAB ─── --}}
            <div x-show="tab === 'notes'" x-transition>
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Member Notes</h3>
                        <a href="{{ gym_route('gym.members.edit', [$member]) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                    </div>
                    <div class="gh-card-body">
                        @if($member->notes)
                        <p style="font-size:14px; line-height:1.8; white-space:pre-wrap; color:#374151;">{{ $member->notes }}</p>
                        @else
                        <div style="text-align:center; padding:40px 0;">
                            <div style="font-size:36px; margin-bottom:12px;">📝</div>
                            <p style="color:#9ca3af; margin-bottom:16px;">No notes added.</p>
                            <a href="{{ gym_route('gym.members.edit', [$member]) }}" class="gh-btn gh-btn-outline gh-btn-sm">Add Notes</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>{{-- /notes --}}

        </div>{{-- /tabs --}}
    </div>

    @push('scripts')
    <script>
        function bodyStatGallery(initialPhotos) {
            return {
                photos: initialPhotos || [],
                activeIndex: null,
                open(index) {
                    if (this.photos.length === 0) return;
                    this.activeIndex = index;
                    document.body.style.overflow = 'hidden';
                },
                close() {
                    this.activeIndex = null;
                    document.body.style.overflow = '';
                },
                get activePhoto() {
                    return this.photos[this.activeIndex] || null;
                }
            }
        }
    </script>
    <style>
        .bodystats-gallery {
            display: grid;
            grid-gap: 10px;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            grid-auto-rows: 250px 150px;
            grid-auto-flow: dense;
        }
        .bodystats-gallery .item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bodystats-gallery .item:first-child {
            grid-row: span 2;
            grid-column: span 2;
        }
        @media (min-width: 480px) {
            .bodystats-gallery .item:nth-child(3n) {
                grid-column: span 2;
            }
        }
    </style>
    @endpush
</x-layouts.app>
