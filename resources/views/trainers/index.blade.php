<x-layouts.app>
    <x-slot:title>Trainers — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Trainers</x-slot:header>
    <x-slot:topbarTitle>Trainers</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Trainers</x-slot:breadcrumb>

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">All Trainers <span
                    style="color:#9ca3af; font-weight:400; font-size:13px;">({{ $trainers->count() }})</span></h3>
            <a href="{{ gym_route('gym.trainers.create') }}" class="gh-btn gh-btn-primary gh-btn-sm">+ Add Trainer</a>
        </div>

        @if ($trainers->isEmpty())
            <div class="gh-card-body" style="text-align:center; padding:60px;">
                <div style="font-size:40px; margin-bottom:12px;">🏋️</div>
                <h4 style="font-weight:600; margin-bottom:6px;">No trainers yet</h4>
                <p style="color:#9ca3af; font-size:14px; margin-bottom:16px;">Add your first trainer to get started.</p>
                <a href="{{ gym_route('gym.trainers.create') }}" class="gh-btn gh-btn-primary">+ Add Trainer</a>
            </div>
        @else
            <div style="padding:0;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                            <th
                                style="text-align:left; padding:11px 20px; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.4px;">
                                Trainer</th>
                            <th
                                style="text-align:left; padding:11px 16px; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.4px;">
                                Specialization</th>
                            <th
                                style="text-align:left; padding:11px 16px; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.4px;">
                                Phone</th>
                            <th
                                style="text-align:center; padding:11px 16px; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.4px;">
                                Members</th>
                            <th
                                style="text-align:center; padding:11px 16px; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.4px;">
                                Status</th>
                            <th
                                style="text-align:right; padding:11px 20px; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.4px;">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainers as $trainer)
                            <tr style="border-bottom:1px solid #f3f4f6; transition:background 0.1s;"
                                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                                {{-- Name + avatar --}}
                                <td style="padding:14px 20px;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <img src="{{ $trainer->avatar ? asset('storage/' . $trainer->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($trainer->name) . '&color=fff&background=0abf8e&size=40&bold=true' }}"
                                            style="width:40px; height:40px; border-radius:50%; object-fit:cover; flex-shrink:0; border:2px solid #e5e7eb;"
                                            alt="">
                                        <div>
                                            <div style="font-size:14px; font-weight:700; color:#111827;">
                                                {{ $trainer->name }}</div>
                                            @if ($trainer->experience_years)
                                                <div style="font-size:12px; color:#9ca3af;">
                                                    {{ $trainer->experience_years }}y exp</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                {{-- Specialization --}}
                                <td style="padding:14px 16px; font-size:13px; color:#6b7280;">
                                    {{ $trainer->specialization ?: '—' }}
                                </td>
                                {{-- Phone --}}
                                <td style="padding:14px 16px; font-size:13px; color:#374151;">
                                    {{ $trainer->phone ?: '—' }}
                                </td>
                                {{-- Members count --}}
                                <td style="padding:14px 16px; text-align:center;">
                                    <span
                                        style="font-size:14px; font-weight:700; color:#0abf8e;">{{ $trainer->members_count }}</span>
                                </td>
                                {{-- Status --}}
                                <td style="padding:14px 16px; text-align:center;">
                                    <span
                                        class="gh-badge {{ $trainer->status === 'active' ? 'gh-badge-success' : 'gh-badge-muted' }}"
                                        style="font-size:11px;">
                                        {{ ucfirst($trainer->status) }}
                                    </span>
                                </td>

                                <td style="padding:14px 20px; text-align:right; white-space:nowrap;">
                                    <div style="display:inline-flex; gap:6px; align-items:center;">
                                        <a href="{{ gym_route('gym.trainers.show', [$trainer]) }}"
                                            class="gh-btn gh-btn-primary gh-btn-sm" style="font-size:12px;">View</a>
                                        <a href="{{ gym_route('gym.trainers.edit', [$trainer]) }}"
                                            class="gh-btn gh-btn-outline gh-btn-sm" style="font-size:12px;">Edit</a>
                                        <button type="button"
                                            onclick="Livewire.dispatch('open-delete-dialog', {{ json_encode([
                                                'title' => 'Remove Trainer',
                                                'message' => 'Are you sure you want to remove this trainer? This action cannot be undone.',
                                                'itemName' => $trainer->name,
                                                'confirmText' => 'Remove',
                                                'cancelText' => 'Cancel',
                                                'formAction' => gym_route('gym.trainers.destroy', [$trainer]),
                                            ]) }})"
                                            class="gh-btn gh-btn-sm"
                                            style="font-size:12px; background:#fee2e2; color:#dc2626; border:none; cursor:pointer; border-radius:6px;">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @livewire('delete-dialog')
</x-layouts.app>
