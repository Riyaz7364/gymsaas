<x-layouts.super-admin>
    <x-slot:title>Pricing Plans — Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Pricing Plans</x-slot:header>
    <x-slot:topbarTitle>Manage Subscription Plans</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / Pricing Plans</x-slot:breadcrumb>

    @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    {{-- Header bar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <h2 style="font-size:20px;font-weight:700;color:#0f172a;margin:0 0 4px;">Subscription Plans</h2>
            <p style="font-size:13px;color:#64748b;margin:0;">Manage the pricing tiers gyms subscribe to</p>
        </div>
        <a href="{{ route('super-admin.pricing.create') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#0abf8e;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Plan
        </a>
    </div>

    {{-- Plans Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
        @forelse($plans as $plan)
        @php
            $colorMap = ['basic' => ['#3b82f6','#dbeafe','#eff6ff'], 'pro' => ['#0abf8e','#d1fae5','#ecfdf5'], 'enterprise' => ['#a855f7','#f3e8ff','#fdf4ff']];
            $c = $colorMap[$plan->name] ?? ['#64748b','#f1f5f9','#f8fafc'];
        @endphp
        <div style="background:#fff;border-radius:14px;border:2px solid {{ $plan->is_active ? $c[0] : '#e2e8f0' }};overflow:hidden;position:relative;">

            {{-- Top accent --}}
            <div style="height:4px;background:{{ $plan->is_active ? $c[0] : '#e2e8f0' }};"></div>

            <div style="padding:22px 24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <div>
                        <div style="font-size:18px;font-weight:700;color:#0f172a;">{{ $plan->display_name }}</div>
                        <div style="font-size:12px;color:#94a3b8;margin-top:2px;">{{ $plan->name }}</div>
                    </div>
                    <span style="padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $plan->is_active ? $c[1] : '#f1f5f9' }};color:{{ $plan->is_active ? $c[0] : '#94a3b8' }};">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                {{-- Pricing --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px;">
                    <div style="background:{{ $c[2] }};border-radius:10px;padding:12px 14px;text-align:center;">
                        <div style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Monthly</div>
                        <div style="font-size:22px;font-weight:800;color:{{ $c[0] }};margin:4px 0;">₹{{ number_format($plan->monthly_price) }}</div>
                        <div style="font-size:11px;color:#94a3b8;">/month</div>
                    </div>
                    <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;text-align:center;">
                        <div style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Annual</div>
                        <div style="font-size:22px;font-weight:800;color:#0f172a;margin:4px 0;">₹{{ number_format($plan->annual_price) }}</div>
                        <div style="font-size:11px;color:#94a3b8;">/year</div>
                    </div>
                </div>

                {{-- Limits --}}
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
                    <span style="padding:4px 10px;background:#f1f5f9;border-radius:6px;font-size:12px;color:#475569;">
                        {{ $plan->max_members == -1 ? '∞' : $plan->max_members }} members
                    </span>
                    <span style="padding:4px 10px;background:#f1f5f9;border-radius:6px;font-size:12px;color:#475569;">
                        {{ $plan->max_trainers == -1 ? '∞' : $plan->max_trainers }} trainers
                    </span>
                    <span style="padding:4px 10px;background:#f1f5f9;border-radius:6px;font-size:12px;color:#475569;">
                        {{ $plan->max_classes == -1 ? '∞' : $plan->max_classes }} classes
                    </span>
                </div>

                {{-- Features --}}
                @if($plan->features)
                <ul style="list-style:none;padding:0;margin:0 0 18px;display:flex;flex-direction:column;gap:6px;">

                    @foreach($plan->modules as $module)
                    <li style="display:flex;align-items:center;gap:8px;font-size:13px;color:#4b5563;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="{{ $c[0] }}" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $module->label }}
                    </li>
                    @endforeach
                    @foreach(array_slice($plan->features, 0, 4) as $feature)
                    <li style="display:flex;align-items:center;gap:8px;font-size:13px;color:#4b5563;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="{{ $c[0] }}" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                    @if(count($plan->features) > 4)
                    <li style="font-size:12px;color:#94a3b8;padding-left:22px;">+{{ count($plan->features) - 4 }} more features</li>
                    @endif
                </ul>
                @endif

                {{-- Actions --}}
                <div style="display:flex;gap:8px;padding-top:14px;border-top:1px solid #f1f5f9;">
                    <a href="{{ route('super-admin.pricing.edit', $plan) }}"
                       style="flex:1;text-align:center;padding:8px 14px;background:{{ $c[1] }};color:{{ $c[0] }};border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">Edit</a>
                    <form method="POST" action="{{ route('super-admin.pricing.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?');" style="flex:1;">
                        @csrf @method('DELETE')
                        <button type="submit" style="width:100%;padding:8px 14px;background:#fee2e2;color:#991b1b;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;background:#fff;border-radius:14px;border:2px dashed #e2e8f0;">
            <div style="font-size:40px;margin-bottom:12px;">💳</div>
            <h3 style="font-size:16px;font-weight:600;color:#0f172a;margin:0 0 8px;">No pricing plans yet</h3>
            <p style="font-size:14px;color:#64748b;margin:0 0 16px;">Create your first subscription plan to start onboarding gyms.</p>
            <a href="{{ route('super-admin.pricing.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#0abf8e;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">Create First Plan</a>
        </div>
        @endforelse
    </div>

</x-layouts.super-admin>
