<x-layouts.app>
    <x-slot:title>Plans — {{ $member->name }} — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Membership Plans</x-slot:header>
    <x-slot:topbarTitle>Members</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        Home / <a href="{{ route('members.index') }}" style="color:var(--gh-primary);text-decoration:none;">Members</a>
        / <a href="{{ route('members.show', $member) }}" style="color:var(--gh-primary);text-decoration:none;">{{ $member->name }}</a>
        / Plans
    </x-slot:breadcrumb>

    <div style="display:grid; grid-template-columns:1fr 360px; gap:20px; align-items:start;">

        {{-- Plan history --}}
        <div>
            <div class="gh-card">
                <div class="gh-card-header">
                    <h3 class="gh-card-title">Plan History</h3>
                    <a href="{{ route('members.show', $member) }}" class="gh-btn gh-btn-outline gh-btn-sm">← Member Profile</a>
                </div>
                <div class="gh-card-body" style="padding:0;">
                    <table class="gh-table">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $mp)
                            <tr>
                                <td>
                                    <div style="font-weight:500;">{{ $mp->plan->name ?? '—' }}</div>
                                    <div style="font-size:12px; color:#9ca3af;">{{ $mp->plan->duration_days ?? '' }} days</div>
                                </td>
                                <td style="font-size:13px;">{{ optional($mp->start_date)->format('d M Y') }}</td>
                                <td style="font-size:13px; {{ $mp->end_date?->isPast() && $mp->status !== 'active' ? 'color:#ef4444;' : '' }}">
                                    {{ optional($mp->end_date)->format('d M Y') }}
                                </td>
                                <td style="font-size:13px; font-weight:500;">
                                    {{ auth()->user()->gym?->currency ?? '₹' }}{{ number_format($mp->price_paid) }}
                                </td>
                                <td>
                                    @php $sm = ['active'=>'gh-badge-success','expired'=>'gh-badge-danger','frozen'=>'gh-badge-info']; @endphp
                                    <span class="gh-badge {{ $sm[$mp->status] ?? 'gh-badge-muted' }}">{{ ucfirst($mp->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#9ca3af; padding:32px;">
                                    No membership plans assigned yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Assign plan form --}}
        <div>
            <div class="gh-card">
                <div class="gh-card-header">
                    <h3 class="gh-card-title">Assign New Plan</h3>
                </div>
                <div class="gh-card-body">

                    {{-- Member summary mini --}}
                    <div style="display:flex; align-items:center; gap:10px; padding:12px; background:#f8fafc; border-radius:8px; margin-bottom:20px;">
                        <img src="{{ $member->avatar ? asset('storage/'.$member->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&color=fff&background=0abf8e&size=40&bold=true' }}"
                             class="gh-avatar" alt="">
                        <div>
                            <div style="font-weight:600; font-size:14px;">{{ $member->name }}</div>
                            <div style="font-size:12px; color:#9ca3af;">{{ $member->member_no }} · {{ $member->phone }}</div>
                        </div>
                    </div>

                    @if($errors->any())
                    <div class="gh-alert gh-alert-danger" style="margin-bottom:16px;">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('members.plans.assign', $member) }}">
                        @csrf

                        <div class="gh-form-group">
                            <label class="gh-label">Membership Plan <span style="color:red;">*</span></label>
                            <select name="plan_id" class="gh-input" required
                                    x-data="{ selected: '' }" x-model="selected">
                                <option value="">Select a plan</option>
                                @foreach($plans as $plan)
                                <option value="{{ $plan->id }}"
                                        data-price="{{ $plan->price }}"
                                        data-days="{{ $plan->duration_days }}"
                                        {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                    ({{ $plan->duration_days }} days — {{ auth()->user()->gym?->currency ?? '₹' }}{{ number_format($plan->price) }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="gh-form-group">
                            <label class="gh-label">Start Date <span style="color:red;">*</span></label>
                            <input type="date" name="start_date" class="gh-input"
                                   value="{{ old('start_date', date('Y-m-d')) }}" required>
                        </div>

                        {{-- Plan preview --}}
                        <div x-data="planPreview()" x-init="init()" style="background:#f0fdf4; border-radius:8px; padding:14px; margin-bottom:20px;">
                            <div style="font-size:13px; font-weight:600; color:#166534; margin-bottom:8px;">Plan Summary</div>
                            <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;">
                                <span style="color:#6b7280;">Amount</span>
                                <span style="font-weight:600;" x-text="amount"></span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:13px;">
                                <span style="color:#6b7280;">Duration</span>
                                <span style="font-weight:600;" x-text="duration"></span>
                            </div>
                        </div>

                        <button type="submit" class="gh-btn gh-btn-primary" style="width:100%; justify-content:center; padding:11px;">
                            Assign Plan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function planPreview() {
        return {
            amount: '—',
            duration: '—',
            init() {
                const sel = document.querySelector('select[name=plan_id]');
                const updatePreview = () => {
                    const opt = sel.options[sel.selectedIndex];
                    const price = opt?.dataset?.price;
                    const days  = opt?.dataset?.days;
                    this.amount   = price ? '₹' + Number(price).toLocaleString('en-IN') : '—';
                    this.duration = days  ? days + ' days' : '—';
                };
                sel.addEventListener('change', updatePreview);
                updatePreview();
            }
        };
    }
    </script>
    @endpush
</x-layouts.app>
