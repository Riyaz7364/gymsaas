<div x-data="{
    trainerId: @entangle('trainerId'),
    scheduleId: @entangle('scheduleId'),
    allSchedules: {{ $allSchedules->toJson() }},
    get filteredSchedules() {
        if (!this.trainerId) return [];
        return this.allSchedules.filter(s => s.trainer_id == this.trainerId);
    },
    selectedSlot() {
        return this.allSchedules.find(s => s.id == this.scheduleId) || null;
    }
}">

    {{-- ── Current trainer card ── --}}
    @if($trainer)
    {{-- Info banner: how to manage slots --}}
    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 16px; margin-bottom:14px; display:flex; align-items:center; gap:12px;">
        <span style="font-size:20px; flex-shrink:0;">📅</span>
        <div style="flex:1; font-size:13px; color:#1e40af;">
            <strong>To add training slots &amp; set session prices</strong> → go to the trainer's profile page.
        </div>
        <a href="{{ route('trainers.show', $trainer) }}"
           style="flex-shrink:0; background:#2563eb; color:#fff; padding:7px 14px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none; white-space:nowrap;">
            ➕ Manage Slots &amp; Pricing
        </a>
    </div>
    <div class="gh-card" style="margin-bottom:16px;">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Personal Trainer</h3>
            <div style="display:flex; gap:8px; align-items:center;">
                <a href="{{ route('trainers.show', $trainer) }}" class="gh-btn gh-btn-outline gh-btn-sm">View Profile →</a>
                <button wire:click="removeTrainer"
                        wire:confirm="Remove the assigned trainer and all their schedule slots for this member?"
                        wire:loading.attr="disabled"
                        wire:target="removeTrainer"
                        class="gh-btn gh-btn-sm"
                        style="color:#dc2626; border:1px solid #fca5a5; background:#fff;">
                    <span wire:loading.remove wire:target="removeTrainer">✕ Remove</span>
                    <span wire:loading.inline-flex wire:target="removeTrainer" style="align-items:center; gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                             style="animation:spin 0.8s linear infinite;">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                        Removing…
                    </span>
                </button>
            </div>
        </div>
        <div class="gh-card-body">
            {{-- Trainer bio --}}
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px;">
                <img src="{{ $trainer->avatar ? asset('storage/'.$trainer->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($trainer->name).'&color=fff&background=6366f1&size=64&bold=true' }}"
                     style="width:64px; height:64px; border-radius:50%; object-fit:cover; flex-shrink:0; border:3px solid #e0e7ff;" alt="">
                <div style="flex:1;">
                    <div style="font-size:17px; font-weight:700; color:#111827;">{{ $trainer->name }}</div>
                    <div style="font-size:13px; color:#6b7280;">{{ $trainer->specialization ?? 'Personal Trainer' }}</div>
                    @if($trainer->experience_years)
                    <div style="font-size:12px; color:#9ca3af; margin-top:2px;">{{ $trainer->experience_years }} yrs experience</div>
                    @endif
                    @if($trainer->pivot->assigned_at)
                    <div style="font-size:11px; color:#9ca3af; margin-top:3px;">
                        Assigned since {{ \Carbon\Carbon::parse($trainer->pivot->assigned_at)->format('d M Y') }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Workout programme --}}
            @if($workoutPlan)
            <div style="background:#f0fdf9; border:1px solid #bbf7d0; border-radius:10px; padding:14px 16px; margin-bottom:12px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                    <span style="font-size:16px;">💪</span>
                    <span style="font-size:14px; font-weight:700; color:#065f46;">{{ $workoutPlan->name }}</span>
                    @if($workoutPlan->description)
                    <span style="font-size:12px; color:#6b7280; margin-left:4px;">— {{ $workoutPlan->description }}</span>
                    @endif
                </div>
                @if($workoutPlan->items->count())
                @php
                    $dayOrder   = ['mon','tue','wed','thu','fri','sat','sun'];
                    $shortDay   = ['mon'=>'Mon','tue'=>'Tue','wed'=>'Wed','thu'=>'Thu','fri'=>'Fri','sat'=>'Sat','sun'=>'Sun'];
                    $activeDays = $workoutPlan->items->pluck('day_of_week')->unique()->toArray();
                @endphp
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    @foreach($dayOrder as $d)
                    <span style="padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;
                          background:{{ in_array($d,$activeDays) ? '#0abf8e' : '#e5e7eb' }};
                          color:{{ in_array($d,$activeDays) ? '#fff' : '#9ca3af' }};">
                        {{ $shortDay[$d] }}
                    </span>
                    @endforeach
                </div>
                <div style="font-size:12px; color:#6b7280; margin-top:8px;">
                    {{ $workoutPlan->items->pluck('day_of_week')->unique()->count() }} training days/week
                    · {{ $workoutPlan->items->count() }} exercises
                </div>
                @endif
            </div>
            @else
            <div style="background:#fafafa; border:1px dashed #d1d5db; border-radius:10px; padding:12px 16px; margin-bottom:12px; font-size:13px; color:#9ca3af;">
                💪 No workout programme assigned yet.
            </div>
            @endif

            {{-- PT sessions --}}
            @if($memberSlots->count())
            <div style="margin-top:4px;">
                <div style="font-size:12px; font-weight:600; color:#374151; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.4px;">PT Sessions</div>
                <div style="display:flex; flex-direction:column; gap:6px;">
                    @foreach($memberSlots as $slot)
                    @php
                        $pivotEnd     = $slot->pivot->end_date   ? \Carbon\Carbon::parse($slot->pivot->end_date)   : null;
                        $pivotStart   = $slot->pivot->start_date ? \Carbon\Carbon::parse($slot->pivot->start_date) : null;
                        $slotDaysLeft = $pivotEnd ? max(0, (int) now()->diffInDays($pivotEnd, false)) : null;
                        $slotTotal    = ($pivotStart && $pivotEnd) ? max(1, $pivotStart->diffInDays($pivotEnd)) : null;
                        $slotElapsed  = ($pivotStart && $slotTotal) ? min($slotTotal, $pivotStart->diffInDays(now())) : 0;
                        $slotProgress = $slotTotal ? min(100, round(($slotElapsed / $slotTotal) * 100)) : null;
                    @endphp
                    <div style="background:#f5f3ff; border-radius:10px; border-left:3px solid #8b5cf6; padding:12px 16px;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:{{ $slotDaysLeft !== null ? '10px' : '0' }};">
                            <span style="font-size:16px;">🗓️</span>
                            <div style="flex:1;">
                                <span style="font-size:13px; font-weight:700; color:#4c1d95;">{{ $slot->title }}</span>
                                <span style="font-size:12px; color:#7c3aed; margin-left:6px;">{{ \App\Models\TrainerSchedule::DAY_LABELS[$slot->day_of_week] ?? $slot->day_of_week }}</span>
                                <span style="font-size:12px; color:#6b7280; margin-left:6px;">🕐 {{ date('g:i A', strtotime($slot->start_time)) }} – {{ date('g:i A', strtotime($slot->end_time)) }}</span>
                            </div>
                            @if($slot->max_members > 1)
                            <span class="gh-badge gh-badge-info" style="font-size:11px;">👥 {{ $slot->members->count() }}/{{ $slot->max_members }}</span>
                            @else
                            <span class="gh-badge gh-badge-success" style="font-size:11px;">Personal</span>
                            @endif
                        </div>
                        @if($slotDaysLeft !== null)
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; margin-bottom:8px;">
                            <div style="background:rgba(255,255,255,0.7); border-radius:6px; padding:6px 10px;">
                                <div style="font-size:10px; color:#9ca3af;">Started</div>
                                <div style="font-size:12px; font-weight:600;">{{ $pivotStart?->format('d M Y') ?? '—' }}</div>
                            </div>
                            <div style="background:rgba(255,255,255,0.7); border-radius:6px; padding:6px 10px;">
                                <div style="font-size:10px; color:#9ca3af;">Ends</div>
                                <div style="font-size:12px; font-weight:600;">{{ $pivotEnd->format('d M Y') }}</div>
                            </div>
                            <div style="background:{{ $slotDaysLeft <= 7 ? '#fef2f2' : 'rgba(255,255,255,0.7)' }}; border-radius:6px; padding:6px 10px;">
                                <div style="font-size:10px; color:#9ca3af;">Days Left</div>
                                <div style="font-size:16px; font-weight:700; color:{{ $slotDaysLeft <= 7 ? '#ef4444' : '#0abf8e' }};">{{ $slotDaysLeft }}</div>
                            </div>
                        </div>
                        @if($slotProgress !== null)
                        <div style="height:6px; background:rgba(139,92,246,0.2); border-radius:3px; overflow:hidden;">
                            <div style="height:100%; width:{{ $slotProgress }}%; background:{{ $slotProgress >= 90 ? '#ef4444' : '#8b5cf6' }}; border-radius:3px;"></div>
                        </div>
                        @endif
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div style="font-size:12px; color:#9ca3af; margin-top:4px;">📅 No personal training sessions scheduled yet. Use the form below to assign a slot.</div>
            @endif
        </div>
    </div>
    @else
    <div style="text-align:center; padding:40px; background:#fff; border-radius:10px; border:1px solid var(--gh-border); margin-bottom:16px;">
        <div style="font-size:40px; margin-bottom:12px;">🏋️</div>
        <p style="color:#9ca3af; font-weight:500; margin-bottom:4px;">No trainer assigned.</p>
        <p style="font-size:12px; color:#9ca3af;">Use the form below to assign a trainer and a schedule slot.</p>
    </div>
    @endif

    {{-- ── Assign / change form ── --}}
    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">{{ $trainer ? 'Change Trainer / Schedule' : 'Assign Trainer & Schedule' }}</h3>
            @if($allTrainers->count() === 0)
            <a href="{{ route('trainers.create') }}" class="gh-btn gh-btn-primary gh-btn-sm">+ Add Trainer</a>
            @endif
        </div>
        <div class="gh-card-body">
            @if($allTrainers->count())

            @error('trainerId')<p style="font-size:12px; color:#ef4444; margin-bottom:8px;">{{ $message }}</p>@enderror

            <form wire:submit.prevent="assign">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                    {{-- Trainer --}}
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Trainer</label>
                        <select wire:model="trainerId" x-model="trainerId" @change="scheduleId=''" class="gh-input">
                            <option value="">— Select trainer —</option>
                            @foreach($allTrainers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}{{ $t->specialization ? ' — '.$t->specialization : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Schedule slot --}}
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">
                            Schedule slot <span style="font-weight:400; color:#9ca3af;">(optional)</span>
                        </label>
                        <select wire:model="scheduleId" x-model="scheduleId" class="gh-input">
                            <option value="">— No specific slot —</option>
                            <template x-for="s in filteredSchedules" :key="s.id">
                                <option :value="s.id" :disabled="s.is_full"
                                        x-text="s.day + ' ' + s.time + ' — ' + s.title + (s.is_shared ? ' [' + s.count + '/' + s.max + ']' : '') + (s.is_full ? ' FULL' : '')">
                                </option>
                            </template>
                        </select>
                        <template x-if="filteredSchedules.length === 0 && trainerId">
                            <div style="margin-top:8px; background:#fefce8; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:10px;">
                                <span style="font-size:15px;">⚠️</span>
                                <div style="flex:1; font-size:12px; color:#92400e;">
                                    This trainer has no schedule slots yet.
                                    <strong>Go to their profile to add slots and set pricing.</strong>
                                </div>
                                @if($trainer)
                                <a href="{{ route('trainers.show', $trainer) }}"
                                   style="background:#d97706; color:#fff; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; white-space:nowrap;">
                                    Add Slots →
                                </a>
                                @endif
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Selected slot detail --}}
                <template x-if="selectedSlot()">
                    <div style="background:#f5f3ff; border:1px solid #ddd6fe; border-radius:8px; padding:12px 16px; margin-bottom:14px; font-size:13px;">
                        <div x-text="'📅 ' + selectedSlot().day + '  🕐 ' + selectedSlot().time" style="font-weight:600; color:#4c1d95; margin-bottom:4px;"></div>
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; color:#6b7280;">
                            <span x-text="selectedSlot().title"></span>
                            <template x-if="selectedSlot().price">
                                <span style="font-weight:700; color:#0abf8e;" x-text="'💰 ' + selectedSlot().price + ' / session'"></span>
                            </template>
                            <template x-if="!selectedSlot().price">
                                <span style="color:#9ca3af;">Free / included in membership</span>
                            </template>
                            <span x-show="selectedSlot().is_shared" style="margin-left:4px;">
                                · 👥 <span x-text="selectedSlot().count"></span>/<span x-text="selectedSlot().max"></span> members
                                <span x-show="selectedSlot().spots_left > 0" style="color:#0abf8e; font-weight:600;" x-text="' · ' + selectedSlot().spots_left + ' spot(s) left'"></span>
                                <span x-show="selectedSlot().is_full" style="color:#ef4444; font-weight:600;"> · FULL</span>
                            </span>
                        </div>
                    </div>
                </template>

                {{-- Programme dates --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;" x-show="scheduleId">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Programme Start <span style="font-weight:400; color:#9ca3af;">(optional)</span></label>
                        <input wire:model="startDate" type="date" class="gh-input">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Programme End <span style="font-weight:400; color:#9ca3af;">(optional)</span></label>
                        <input wire:model="endDate" type="date" class="gh-input">
                    </div>
                </div>

                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="assign"
                        class="gh-btn gh-btn-primary">
                    <span wire:loading.remove wire:target="assign">Save Assignment</span>
                    <span wire:loading.inline-flex wire:target="assign" style="align-items:center; gap:5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                             style="animation:spin 0.8s linear infinite;">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                        Saving…
                    </span>
                </button>
            </form>
            @else
            <p style="color:#9ca3af; font-size:13px;">No trainers available. <a href="{{ route('trainers.create') }}" style="color:var(--gh-primary);">Add a trainer</a></p>
            @endif
        </div>
    </div>

    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</div>
