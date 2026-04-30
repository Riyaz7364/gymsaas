<x-layouts.app>
    <x-slot:title>Workout Sequences — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Workout Sequences</x-slot:header>
    <x-slot:topbarTitle>Manage Workout Sequences</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Workouts / Sequences</x-slot:breadcrumb>

    @if(session('success'))
    <div style="margin-bottom:20px;">
        <div class="gh-alert gh-alert-success">✓ {{ session('success') }}</div>
    </div>
    @endif

    @if($showAiGenerator ?? false)
    <div class="gh-card" style="margin-bottom:16px;">
        <div class="gh-card-header">
            <h3 class="gh-card-title">AI Workout Generator</h3>
        </div>
        <div class="gh-card-body">
            <form method="POST" action="{{ url('/' . auth()->user()->gym->slug . '/workout-sequences/generate-ai') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
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
                <button type="submit" class="gh-btn gh-btn-primary">Generate AI Workout Plan</button>
            </form>
        </div>
    </div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Workout Sequences</h3>
            <a href="{{ gym_route('gym.workout-sequences.create') }}" class="gh-btn gh-btn-primary">+ New Sequence</a>
        </div>
        <div class="gh-card-body" style="padding:0;">
            @forelse($sequences as $sequence)
            <div style="display:grid; grid-template-columns:1fr 200px 150px 100px; gap:16px; align-items:center; padding:16px 18px; border-bottom:1px solid var(--gh-border);">
                <div>
                    <div style="font-weight:600; color:#374151; margin-bottom:4px;">{{ $sequence->name }}</div>
                    <div style="font-size:12px; color:#9ca3af;">{{ $sequence->total_days }}-day split · {{ $sequence->days->count() }} days configured</div>
                    @if($sequence->description)
                    <div style="font-size:12px; color:#6b7280; margin-top:6px;">{{ Str::limit($sequence->description, 60) }}</div>
                    @endif
                </div>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    @if($sequence->is_default)
                    <span class="gh-badge" style="background:#d1fae5; color:#065f46;">Default</span>
                    @endif
                    @if(!$sequence->is_active)
                    <span class="gh-badge" style="background:#fee2e2; color:#991b1b;">Inactive</span>
                    @endif
                </div>
                <div style="display:flex; gap:6px;">
                    @foreach($sequence->days->take(3) as $day)
                    <div style="width:28px; height:28px; border-radius:4px; background:{{ $day->bg }}; border:1px solid {{ $day->border }}; display:flex; align-items:center; justify-content:center; font-size:12px; color:{{ $day->color }}; font-weight:600;">
                        {{ $day->day_number }}
                    </div>
                    @endforeach
                    @if($sequence->days->count() > 3)
                    <div style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:#9ca3af;">+{{ $sequence->days->count() - 3 }}</div>
                    @endif
                </div>
                <div style="text-align:right; display:flex; gap:6px; justify-content:flex-end;">
                    <a href="{{ gym_route('gym.workout-sequences.edit', [$sequence]) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                    <form method="POST" action="{{ gym_route('gym.workout-sequences.destroy', $sequence) }}" style="display:inline;" onsubmit="return confirm('Delete sequence? Members using it will be unaffected.');">
                        @csrf @method('DELETE')
                        <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:40px 18px; text-align:center;">
                <div style="font-size:36px; margin-bottom:12px;">🏋️</div>
                <p style="color:#9ca3af; margin-bottom:12px;">No sequences created yet</p>
                <a href="{{ gym_route('gym.workout-sequences.create') }}" class="gh-btn gh-btn-primary">Create First Sequence</a>
            </div>
            @endforelse
        </div>
    </div>

    @if($sequences->hasPages())
    <div style="margin-top:20px;">
        {{ $sequences->links() }}
    </div>
    @endif
</x-layouts.app>
