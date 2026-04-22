<x-layouts.app>
    <x-slot:title>{{ $trainer->name }} — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>{{ $trainer->name }}</x-slot:header>
    <x-slot:topbarTitle>Trainers</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        Home / <a href="{{ route('trainers.index') }}" style="color:var(--gh-primary);text-decoration:none;">Trainers</a> / {{ $trainer->name }}
    </x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="gh-alert gh-alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    @php
        $dayOrder = ['mon','tue','wed','thu','fri','sat','sun'];
        $days     = \App\Models\TrainerSchedule::DAY_LABELS;
        $currency = $currency ?? '₹';

        // Map member_id → schedule slot (for members tab)
        $memberScheduleMap = [];
        foreach ($trainer->activeSchedules as $slot) {
            foreach ($slot->members as $sm) {
                $memberScheduleMap[$sm->id] = ['slot' => $slot, 'pivot' => $sm->pivot];
            }
        }
    @endphp

    <div style="display:grid; grid-template-columns:290px 1fr; gap:20px; align-items:start;">

        {{-- LEFT sidebar --}}
        <div style="display:flex; flex-direction:column; gap:14px;">

            {{-- Profile card --}}
            <div class="gh-card">
                <div class="gh-card-body" style="text-align:center; padding:28px 20px 20px;">
                    <img src="{{ $trainer->avatar ? asset('storage/'.$trainer->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($trainer->name).'&color=fff&background=6366f1&size=96&bold=true' }}"
                         style="width:88px; height:88px; border-radius:50%; object-fit:cover; border:3px solid #e0e7ff; margin-bottom:12px;" alt="">
                    <h2 style="font-size:18px; font-weight:700; margin:0 0 4px;">{{ $trainer->name }}</h2>
                    <p style="font-size:13px; color:#6b7280; margin:0 0 10px;">{{ $trainer->specialization ?? 'Personal Trainer' }}</p>
                    <span class="gh-badge {{ $trainer->status === 'active' ? 'gh-badge-success' : 'gh-badge-muted' }}" style="font-size:13px; padding:4px 14px;">
                        {{ ucfirst($trainer->status) }}
                    </span>

                    {{-- Quick stats --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; margin-top:16px;">
                        <div style="background:#f8fafc; border-radius:8px; padding:10px 6px;">
                            <div style="font-size:18px; font-weight:700; color:var(--gh-primary);">{{ $trainer->members->count() }}</div>
                            <div style="font-size:10px; color:#9ca3af; margin-top:2px;">Members</div>
                        </div>
                        <div style="background:#f8fafc; border-radius:8px; padding:10px 6px;">
                            <div style="font-size:18px; font-weight:700; color:var(--gh-primary);">{{ $trainer->activeSchedules->count() }}</div>
                            <div style="font-size:10px; color:#9ca3af; margin-top:2px;">Slots</div>
                        </div>
                        <div style="background:#f8fafc; border-radius:8px; padding:10px 6px;">
                            <div style="font-size:18px; font-weight:700; color:var(--gh-primary);">{{ $trainer->experience_years ?? 0 }}</div>
                            <div style="font-size:10px; color:#9ca3af; margin-top:2px;">Yrs exp</div>
                        </div>
                    </div>

                    <div style="display:flex; gap:8px; margin-top:14px; justify-content:center;">
                        <a href="{{ route('trainers.edit', $trainer) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                    </div>
                </div>
            </div>

            {{-- Detail rows --}}
            <div class="gh-card">
                <div class="gh-card-header"><h3 class="gh-card-title">Details</h3></div>
                <div class="gh-card-body" style="padding:0;">
                    @foreach([
                        ['Phone',        $trainer->phone ?: '—'],
                        ['Email',        $trainer->email ?: '—'],
                        ['Specialization', $trainer->specialization ?: '—'],
                        ['Experience',   $trainer->experience_years ? $trainer->experience_years.' years' : '—'],
                        ['Salary',       $trainer->salary ? $currency.number_format($trainer->salary) : '—'],
                        ['Joined',       optional($trainer->joined_at)->format('d M Y') ?? '—'],
                    ] as [$label, $value])
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 16px; border-bottom:1px solid var(--gh-border);">
                        <span style="font-size:12px; color:#6b7280;">{{ $label }}</span>
                        <span style="font-size:13px; font-weight:500; text-align:right; max-width:160px; word-break:break-word;">{{ $value }}</span>
                    </div>
                    @endforeach
                    @if($trainer->bio)
                    <div style="padding:10px 16px;">
                        <span style="font-size:12px; color:#6b7280; display:block; margin-bottom:4px;">Bio</span>
                        <p style="font-size:13px; line-height:1.6; color:#374151; margin:0;">{{ $trainer->bio }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: Tabs --}}
        <div x-data="{ tab: 'schedule' }">

            {{-- Tab nav --}}
            <div style="display:flex; gap:2px; margin-bottom:16px; border-bottom:2px solid var(--gh-border); flex-wrap:wrap;">
                @php
                    $tabs = [
                        'schedule' => ['label' => 'Schedule',  'icon' => '📅'],
                        'members'  => ['label' => 'Members',   'icon' => '👥'],
                        'payments' => ['label' => 'Payments',  'icon' => '💳'],
                    ];
                @endphp
                @foreach($tabs as $key => $info)
                <button @click="tab = '{{ $key }}'"
                        :style="tab === '{{ $key }}' ? 'border-bottom:2px solid var(--gh-primary); color:var(--gh-primary); margin-bottom:-2px;' : ''"
                        style="display:flex; align-items:center; gap:5px; padding:10px 14px; font-size:13px; font-weight:500; background:none; border:none; border-bottom:2px solid transparent; cursor:pointer; color:#6b7280; transition:0.15s; white-space:nowrap;">
                    <span>{{ $info['icon'] }}</span> {{ $info['label'] }}
                </button>
                @endforeach
            </div>

            {{-- ─── SCHEDULE TAB ─── --}}
            <div x-show="tab === 'schedule'" x-transition>

                {{-- Existing slots grouped by day --}}
                @if($trainer->activeSchedules->count())
                <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:20px;">
                    @foreach($dayOrder as $d)
                    @php $daySlots = $trainer->activeSchedules->where('day_of_week', $d); @endphp
                    @if($daySlots->count())
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; padding-left:4px;">
                            {{ $days[$d] }}
                        </div>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            @foreach($daySlots as $slot)
                            @php $slotCount = $slot->members->count(); @endphp
                            <div class="gh-card" style="margin:0;">
                                <div class="gh-card-body" style="padding:14px 18px;">
                                    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">

                                        {{-- Time block --}}
                                        <div style="background:{{ $slotCount >= $slot->max_members && $slot->max_members === 1 ? '#fef2f2' : ($slotCount >= $slot->max_members ? '#fff7ed' : '#f0fdf9') }};
                                                    color:{{ $slotCount >= $slot->max_members && $slot->max_members === 1 ? '#ef4444' : ($slotCount >= $slot->max_members ? '#f97316' : '#0abf8e') }};
                                                    padding:8px 12px; border-radius:10px; text-align:center; min-width:80px; flex-shrink:0;">
                                            <div style="font-size:14px; font-weight:700;">{{ date('g:i A', strtotime($slot->start_time)) }}</div>
                                            <div style="font-size:11px; opacity:0.8;">{{ date('g:i A', strtotime($slot->end_time)) }}</div>
                                        </div>

                                        {{-- Slot info --}}
                                        <div style="flex:1; min-width:0;">
                                            <div style="font-size:15px; font-weight:700; color:#111827;">{{ $slot->title }}</div>
                                            @if($slot->price)
                                            <div style="font-size:13px; font-weight:700; color:#0abf8e; margin-top:2px;">{{ $currency }}{{ number_format($slot->price, 0) }} / session</div>
                                            @else
                                            <div style="font-size:12px; color:#9ca3af; margin-top:2px;">Free / included</div>
                                            @endif
                                            <div style="display:flex; align-items:center; gap:8px; margin-top:6px; flex-wrap:wrap;">
                                                @if($slot->max_members === 1)
                                                    @if($slotCount === 0)
                                                    <span class="gh-badge gh-badge-success">🟢 Free Slot</span>
                                                    @else
                                                    <span class="gh-badge gh-badge-danger">🔴 Booked</span>
                                                    @endif
                                                @else
                                                    @if($slotCount < $slot->max_members)
                                                    <span class="gh-badge gh-badge-primary">👥 {{ $slotCount }}/{{ $slot->max_members }} — {{ $slot->max_members - $slotCount }} spot(s) free</span>
                                                    @else
                                                    <span class="gh-badge gh-badge-warning" style="background:#fff7ed; color:#c2410c;">👥 {{ $slotCount }}/{{ $slot->max_members }} — Full</span>
                                                    @endif
                                                @endif

                                                {{-- Member pills --}}
                                                @foreach($slot->members as $sm)
                                                @php
                                                    $smPivotEnd = $sm->pivot->end_date ? \Carbon\Carbon::parse($sm->pivot->end_date) : null;
                                                    $smDaysLeft = $smPivotEnd ? max(0, (int) now()->diffInDays($smPivotEnd, false)) : null;
                                                @endphp
                                                <a href="{{ route('members.show', $sm) }}"
                                                   style="display:inline-flex; align-items:center; gap:4px; font-size:12px; background:#f5f3ff; color:#6d28d9; padding:3px 10px; border-radius:12px; text-decoration:none;">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($sm->name) }}&size=16&background=8b5cf6&color=fff&bold=true"
                                                         style="width:16px; height:16px; border-radius:50%;" alt="">
                                                    {{ $sm->name }}
                                                    @if($smDaysLeft !== null)
                                                    <span style="font-size:10px; color:#9ca3af; font-weight:400;">({{ $smDaysLeft }}d)</span>
                                                    @endif
                                                </a>
                                                @endforeach
                                            </div>

                                            @if($slot->notes)
                                            <div style="font-size:12px; color:#9ca3af; margin-top:4px;">{{ $slot->notes }}</div>
                                            @endif
                                        </div>

                                        {{-- Delete button --}}
                                        <form method="POST" action="{{ route('trainers.schedule.destroy', [$trainer, $slot]) }}" style="flex-shrink:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Remove this slot?')"
                                                    style="background:none; border:1px solid #e5e7eb; color:#9ca3af; cursor:pointer; border-radius:6px; padding:5px 8px; font-size:13px; transition:0.15s;"
                                                    onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444';"
                                                    onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#9ca3af';"
                                                    title="Remove slot">✕</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div style="text-align:center; padding:36px; background:#fff; border-radius:10px; border:1px dashed var(--gh-border); margin-bottom:20px;">
                    <div style="font-size:36px; margin-bottom:10px;">📅</div>
                    <p style="color:#9ca3af; font-weight:500; margin-bottom:4px;">No schedule slots yet.</p>
                    <p style="font-size:12px; color:#9ca3af;">Add slots below to manage personal training sessions.</p>
                </div>
                @endif

                {{-- Add slot form --}}
                <div class="gh-card">
                    <div class="gh-card-header"><h3 class="gh-card-title">Add Schedule Slot</h3></div>
                    <div class="gh-card-body">
                        <form method="POST" action="{{ route('trainers.schedule.store', $trainer) }}">
                            @csrf
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                                <div style="grid-column:1/-1;">
                                    <label class="gh-label">Slot Title <span style="color:#ef4444;">*</span></label>
                                    <input type="text" name="title" class="gh-input" value="{{ old('title') }}"
                                           placeholder="e.g. Strength Training, Morning Cardio, Fat Loss" required>
                                </div>
                                <div>
                                    <label class="gh-label">Day <span style="color:#ef4444;">*</span></label>
                                    <select name="day_of_week" class="gh-input" required>
                                        <option value="">— Select day —</option>
                                        @foreach($days as $val => $label)
                                        <option value="{{ $val }}" {{ old('day_of_week') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                    <div>
                                        <label class="gh-label">Start Time <span style="color:#ef4444;">*</span></label>
                                        <input type="time" name="start_time" class="gh-input" value="{{ old('start_time') }}" required>
                                    </div>
                                    <div>
                                        <label class="gh-label">End Time <span style="color:#ef4444;">*</span></label>
                                        <input type="time" name="end_time" class="gh-input" value="{{ old('end_time') }}" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="gh-label">
                                        Max Members <span style="font-weight:400; color:#9ca3af;">(1 = personal training)</span>
                                    </label>
                                    <input type="number" name="max_members" class="gh-input" value="{{ old('max_members', 1) }}" min="1" max="20" required>
                                    <p style="font-size:11px; color:#9ca3af; margin-top:4px;">Set &gt;1 if friends can train together in same slot.</p>
                                </div>
                                <div>
                                    <label class="gh-label">Session Fee <span style="font-weight:400; color:#9ca3af;">(optional — leave blank if included in membership)</span></label>
                                    <input type="number" name="price" class="gh-input" value="{{ old('price') }}" min="0" step="0.01" placeholder="e.g. 500">
                                </div>
                                <div style="grid-column:1/-1;">
                                    <label class="gh-label">Notes <span style="font-weight:400; color:#9ca3af;">(optional)</span></label>
                                    <input type="text" name="notes" class="gh-input" value="{{ old('notes') }}" placeholder="e.g. Bring own mat, outdoor session">
                                </div>
                            </div>
                            <button type="submit" class="gh-btn gh-btn-primary">Add Slot</button>
                        </form>
                    </div>
                </div>

            </div>{{-- /schedule --}}

            {{-- ─── MEMBERS TAB ─── --}}
            <div x-show="tab === 'members'" x-transition>
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Assigned Members</h3>
                        <span class="gh-badge gh-badge-primary">{{ $trainer->members->count() }} total</span>
                    </div>
                    <div class="gh-card-body" style="padding:0;">
                        @forelse($trainer->members as $m)
                        @php
                            $mInfo     = $memberScheduleMap[$m->id] ?? null;
                            $mSlot     = $mInfo['slot']  ?? null;
                            $mPivot    = $mInfo['pivot'] ?? null;
                            $mPtEnd    = $mPivot?->end_date   ? \Carbon\Carbon::parse($mPivot->end_date)   : null;
                            $mPtStart  = $mPivot?->start_date ? \Carbon\Carbon::parse($mPivot->start_date) : null;
                            $mDaysLeft = $mPtEnd ? max(0, (int) now()->diffInDays($mPtEnd, false)) : null;
                            $mTotal    = ($mPtStart && $mPtEnd) ? max(1, $mPtStart->diffInDays($mPtEnd)) : null;
                            $mProgress = $mTotal ? min(100, round(($mPtStart->diffInDays(now()) / $mTotal) * 100)) : null;
                            $mPlan     = $m->activePlan?->plan;
                            $statusMap = ['active'=>'gh-badge-success','frozen'=>'gh-badge-info','expired'=>'gh-badge-danger','inactive'=>'gh-badge-muted'];
                        @endphp
                        <div style="padding:14px 18px; border-bottom:1px solid var(--gh-border);">
                            <div style="display:flex; align-items:center; gap:14px;">
                                <img src="{{ $m->avatar ? asset('storage/'.$m->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($m->name).'&color=fff&background=0abf8e&size=44&bold=true' }}"
                                     style="width:46px; height:46px; border-radius:50%; object-fit:cover; flex-shrink:0; border:2px solid var(--gh-border);" alt="">
                                <div style="flex:1; min-width:0;">
                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                        <a href="{{ route('members.show', $m) }}" style="font-size:14px; font-weight:700; color:#111827; text-decoration:none;">{{ $m->name }}</a>
                                        <span class="gh-badge {{ $statusMap[$m->status] ?? 'gh-badge-muted' }}">{{ ucfirst($m->status) }}</span>
                                    </div>
                                    <div style="font-size:12px; color:#6b7280; margin-top:3px;">
                                        {{ $mPlan?->name ?? 'No active plan' }}
                                        @if($mSlot)
                                        · 📅 {{ \App\Models\TrainerSchedule::DAY_LABELS[$mSlot->day_of_week] }}
                                          {{ date('g:i A', strtotime($mSlot->start_time)) }}
                                          — {{ $mSlot->title }}
                                        @else
                                        · <span style="color:#f97316;">No slot assigned</span>
                                        @endif
                                    </div>
                                    @if($mPtStart || $mPtEnd)
                                    <div style="font-size:11px; color:#9ca3af; margin-top:3px;">
                                        @if($mPtStart) {{ $mPtStart->format('d M Y') }} @endif
                                        @if($mPtEnd) – {{ $mPtEnd->format('d M Y') }} @endif
                                    </div>
                                    @endif
                                </div>
                                @if($mDaysLeft !== null)
                                <div style="text-align:center; min-width:58px; background:{{ $mDaysLeft <= 7 ? '#fef2f2' : '#f0fdf9' }}; padding:8px 10px; border-radius:10px; flex-shrink:0;">
                                    <div style="font-size:18px; font-weight:700; color:{{ $mDaysLeft <= 7 ? '#ef4444' : '#0abf8e' }};">{{ $mDaysLeft }}</div>
                                    <div style="font-size:10px; color:#9ca3af;">days left</div>
                                </div>
                                @endif
                            </div>
                            @if($mProgress !== null)
                            <div style="margin-top:8px; padding-left:60px;">
                                <div style="display:flex; justify-content:space-between; font-size:11px; color:#9ca3af; margin-bottom:4px;">
                                    <span>Programme progress</span><span>{{ $mProgress }}%</span>
                                </div>
                                <div style="height:5px; background:#f1f5f9; border-radius:3px; overflow:hidden;">
                                    <div style="height:100%; width:{{ $mProgress }}%; background:{{ $mProgress >= 90 ? '#ef4444' : '#0abf8e' }}; border-radius:3px;"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div style="text-align:center; padding:48px 24px; color:#9ca3af;">
                            <div style="font-size:36px; margin-bottom:10px;">👥</div>
                            No members assigned to this trainer yet.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>{{-- /members --}}

            {{-- ─── PAYMENTS TAB ─── --}}
            <div x-show="tab === 'payments'" x-transition>
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Payment History</h3>
                        <span class="gh-badge gh-badge-success">Total {{ $currency }}{{ number_format($payments->sum('amount')) }}</span>
                    </div>
                    <div class="gh-card-body" style="padding:0;">
                        @forelse($payments as $pay)
                        <div style="display:flex; align-items:center; gap:14px; padding:13px 18px; border-bottom:1px solid var(--gh-border);">
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:13px; font-weight:600; color:#111827;">{{ $pay->member->name }}</div>
                                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:3px;">
                                    <span style="font-size:12px; color:#6b7280;">{{ $pay->invoice->invoice_no ?? '—' }}</span>
                                    <span class="gh-badge gh-badge-muted" style="font-size:11px;">{{ ucfirst($pay->method) }}</span>
                                    @if($pay->gateway_txn_id)
                                    <span style="font-size:11px; color:#9ca3af; font-family:monospace;">{{ $pay->gateway_txn_id }}</span>
                                    @endif
                                </div>
                                <div style="font-size:11px; color:#9ca3af; margin-top:2px;">
                                    {{ optional($pay->paid_at)->format('d M Y, h:i A') }}
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <div style="font-size:16px; font-weight:700; color:#0abf8e;">{{ $currency }}{{ number_format($pay->amount) }}</div>
                                <span class="gh-badge gh-badge-success" style="font-size:10px;">Paid</span>
                            </div>
                        </div>
                        @empty
                        <div style="text-align:center; padding:48px 24px; color:#9ca3af;">
                            <div style="font-size:36px; margin-bottom:10px;">💳</div>
                            <p style="font-weight:500; margin-bottom:4px;">No payments found.</p>
                            <p style="font-size:12px;">Payments from this trainer's members will appear here.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>{{-- /payments --}}

        </div>{{-- /tabs --}}
    </div>
</x-layouts.app>
