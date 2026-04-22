<x-layouts.app>
    <x-slot:title>Membership Plans — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Membership Plans</x-slot:header>
    <x-slot:topbarTitle>Plans</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Membership Plans</x-slot:breadcrumb>

    @if(session('error'))
    <div class="gh-alert gh-alert-danger">{{ session('error') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">All Plans <span style="color:#9ca3af; font-weight:400; font-size:13px;">({{ $plans->count() }})</span></h3>
            <a href="{{ route('plans.create') }}" class="gh-btn gh-btn-primary gh-btn-sm">+ New Plan</a>
        </div>

        @if($plans->isEmpty())
        <div class="gh-card-body" style="text-align:center; padding:60px;">
            <div style="font-size:40px; margin-bottom:12px;">💳</div>
            <h4 style="font-weight:600; margin-bottom:6px;">No plans yet</h4>
            <p style="color:#9ca3af; font-size:14px; margin-bottom:16px;">Create your first membership plan to start assigning members.</p>
            <a href="{{ route('plans.create') }}" class="gh-btn gh-btn-primary">+ Create Plan</a>
        </div>
        @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px; padding:20px;">
            @foreach($plans as $plan)
            <div style="border:{{ $plan->is_active ? '2px solid #0abf8e' : '1px solid #e5e7eb' }}; border-radius:12px; padding:24px; background:#fff; position:relative;">
                @if(!$plan->is_active)
                <div style="position:absolute; top:12px; right:12px;">
                    <span class="gh-badge gh-badge-muted" style="font-size:10px;">Inactive</span>
                </div>
                @endif

                <div style="font-size:12px; color:#9ca3af; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">
                    {{ str_replace('_', ' ', $plan->type) }}
                </div>
                <h3 style="font-size:18px; font-weight:700; margin-bottom:12px;">{{ $plan->name }}</h3>

                <div style="font-size:32px; font-weight:800; color:#0abf8e; margin-bottom:4px;">
                    {{ auth()->user()->gym?->currency ?? '₹' }}{{ number_format($plan->price) }}
                </div>
                <div style="font-size:13px; color:#6b7280; margin-bottom:16px;">{{ $plan->duration_days }} days</div>

                @if($plan->description)
                <p style="font-size:13px; color:#6b7280; margin-bottom:16px; line-height:1.5;">{{ $plan->description }}</p>
                @endif

                <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid #f3f4f6;">
                    <div style="font-size:12px; color:#9ca3af;">
                        <strong style="color:#374151;">{{ $plan->member_plans_count }}</strong> assignments
                    </div>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('plans.edit', $plan) }}" class="gh-btn gh-btn-outline gh-btn-sm" style="font-size:11px; padding:4px 10px;">Edit</a>
                        <form method="POST" action="{{ route('plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?');" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="gh-btn gh-btn-sm" style="font-size:11px; padding:4px 10px; background:#fee2e2; color:#dc2626; border:none; cursor:pointer; border-radius:6px;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</x-layouts.app>

