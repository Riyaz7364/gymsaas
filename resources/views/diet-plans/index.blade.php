<x-layouts.app>
    <x-slot:title>Diet Plans — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Diet Plans</x-slot:header>
    <x-slot:topbarTitle>Diet Plans</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Diet Plans</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    @if($showAiPlans)
    <div class="gh-card" style="margin-bottom:16px;">
        <div class="gh-card-header">
            <h3 class="gh-card-title">AI Diet Generator</h3>
        </div>
        <div class="gh-card-body">
            <form method="POST" action="{{ gym_route('gym.diet-plans.generate-ai') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
                @csrf
                <div style="min-width:280px;">
                    <label class="gh-label">Select Member</label>
                    <select name="member_id" class="gh-input" required>
                        <option value="">-- Choose member --</option>
                        @foreach($membersForAi as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} ({{ str_replace('_', ' ', $member->goal ?? 'maintain') }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="gh-btn gh-btn-primary">Generate AI Diet Plan</button>
            </form>
        </div>
    </div>
    @endif

    {{-- Filter tabs --}}
    <div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ gym_route('gym.diet-plans.index', ['type'=>'default']) }}" class="gh-btn {{ request('type')==='default' || !request('type') ? 'gh-btn-primary' : 'gh-btn-outline' }}">Plans</a>
        @if($showAiPlans)
        <a href="{{ gym_route('gym.diet-plans.index', ['type'=>'ai']) }}" class="gh-btn {{ request('type')==='ai' ? 'gh-btn-primary' : 'gh-btn-outline' }}">AI Plans</a>
        @endif
        <a href="{{ gym_route('gym.diet-plans.index', ['type'=>'custom']) }}" class="gh-btn {{ request('type')==='custom' ? 'gh-btn-primary' : 'gh-btn-outline' }}">Custom Plans</a>
        <a href="{{ gym_route('gym.food-items.index') }}" class="gh-btn gh-btn-outline">Food & Drinks</a>
    </div>

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Diet Plans</h3>
            <a href="{{ gym_route('gym.diet-plans.create') }}" class="gh-btn gh-btn-primary">+ New Plan</a>
        </div>
        @if($plans->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">🥗</div>
            <h4>No diet plans yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Create nutrition plans for your members.</p>
            <a href="{{ gym_route('gym.diet-plans.create') }}" class="gh-btn gh-btn-primary">+ New Plan</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Name</th><th>Goal</th><th>Member</th><th>Default</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($plans as $plan)
                <tr>
                    <td style="font-weight:500;">{{ $plan->name }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $plan->goal ?? '—' }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $plan->member?->name ?? '—' }}</td>
                    <td>@if($plan->is_default)<span class="gh-badge gh-badge-info">Default</span>@endif</td>
                    <td>
                        @if($plan->is_active)<span class="gh-badge gh-badge-success">Active</span>
                        @else<span class="gh-badge gh-badge-muted">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ gym_route('gym.diet-plans.edit', $plan) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ gym_route('gym.diet-plans.destroy', [$gym, $plan]) }}" style="display:inline;" onsubmit="return confirm('Delete plan?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $plans->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>
