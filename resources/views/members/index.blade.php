<x-layouts.app>
    <x-slot:title>Members — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Members</x-slot:header>
    <x-slot:topbarTitle>Members</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <span style="color:#0abf8e;">Members</span></x-slot:breadcrumb>

    {{-- Status tabs + actions --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        {{-- Status tabs --}}
        <div style="display:flex; gap:6px; flex-wrap:wrap;">
            @foreach(['all' => 'All', 'active' => 'Active', 'frozen' => 'Frozen', 'expired' => 'Expired', 'inactive' => 'Inactive'] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $key === 'all' ? '' : $key, 'page' => 1]) }}"
               style="padding:6px 14px; border-radius:20px; font-size:13px; font-weight:500; text-decoration:none; border:1px solid var(--gh-border);
                      {{ ($key === 'all' && !request('status')) || request('status') === $key
                          ? 'background:var(--gh-primary); color:#fff; border-color:var(--gh-primary);'
                          : 'background:#fff; color:var(--gh-text);' }}">
                {{ $label }}
                <span style="font-weight:600; margin-left:4px;">{{ $counts[$key === 'all' ? 'all' : $key] ?? 0 }}</span>
            </a>
            @endforeach
        </div>

        <a href="{{ gym_route('gym.members.create') }}" class="gh-btn gh-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Member
        </a>
    </div>

    {{-- Search + Filter bar --}}
    <div class="gh-card" style="margin-bottom:20px;">
        <div class="gh-card-body" style="padding:14px 20px;">
            <form method="GET" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div style="flex:1; min-width:200px; position:relative;">
                    <svg style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="gh-input" placeholder="Search name, phone, email, ID…"
                           style="padding-left:34px;">
                </div>
                <select name="goal" class="gh-input" style="width:auto;">
                    <option value="">All Goals</option>
                    <option value="weight_loss"  {{ request('goal') === 'weight_loss'  ? 'selected' : '' }}>Weight Loss</option>
                    <option value="muscle_gain"  {{ request('goal') === 'muscle_gain'  ? 'selected' : '' }}>Muscle Gain</option>
                    <option value="maintain"     {{ request('goal') === 'maintain'     ? 'selected' : '' }}>Maintain</option>
                    <option value="endurance"    {{ request('goal') === 'endurance'    ? 'selected' : '' }}>Endurance</option>
                </select>
                <button type="submit" class="gh-btn gh-btn-primary">Search</button>
                @if(request('search') || request('goal'))
                <a href="{{ gym_route('gym.members.index', request()->only('status')) }}" class="gh-btn gh-btn-outline">Clear</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="gh-card">
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Phone</th>
                        <th>Plan</th>
                        <th>Goal</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img src="{{ $member->avatar ? asset('storage/'.$member->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&color=fff&background=0abf8e&size=40&bold=true' }}"
                                     class="gh-avatar" alt="{{ $member->name }}">
                                <div>
                                    <a href="{{ gym_route('gym.members.show', [$member]) }}"
                                       style="font-weight:600; font-size:14px; color:var(--gh-text); text-decoration:none;">
                                        {{ $member->name }}
                                    </a>
                                    <div style="font-size:12px; color:#9ca3af;">{{ $member->member_no }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;">{{ $member->phone }}</td>
                        <td style="font-size:13px;">
                            @if($member->activePlan)
                                {{ $member->activePlan->plan->name ?? '—' }}
                                <div style="font-size:11px; color:#9ca3af;">
                                    Exp: {{ optional($member->activePlan->end_date)->format('d M Y') }}
                                </div>
                            @else
                                <span style="color:#9ca3af;">No Plan</span>
                            @endif
                        </td>
                        <td>
                            @php $goalMap = ['weight_loss'=>['Weight Loss','gh-badge-warning'], 'muscle_gain'=>['Muscle Gain','gh-badge-info'], 'maintain'=>['Maintain','gh-badge-muted'], 'endurance'=>['Endurance','gh-badge-primary']]; @endphp
                            <span class="gh-badge {{ $goalMap[$member->goal][1] ?? 'gh-badge-muted' }}">
                                {{ $goalMap[$member->goal][0] ?? ucfirst($member->goal) }}
                            </span>
                        </td>
                        <td style="font-size:13px; color:#6b7280;">
                            {{ optional($member->joined_at)->format('d M Y') ?? $member->created_at->format('d M Y') }}
                        </td>
                        <td>
                            @php $statusMap = ['active'=>'gh-badge-success','frozen'=>'gh-badge-info','expired'=>'gh-badge-danger','inactive'=>'gh-badge-muted']; @endphp
                            <span class="gh-badge {{ $statusMap[$member->status] ?? 'gh-badge-muted' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end; align-items:center;">
                                <a href="{{ gym_route('gym.members.show', [$member]) }}"
                                   class="gh-btn gh-btn-outline gh-btn-sm" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </a>
                                <a href="{{ gym_route('gym.members.edit', [$member]) }}"
                                   class="gh-btn gh-btn-outline gh-btn-sm" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                                </a>
                                <form method="POST" action="{{ gym_route('gym.members.freeze', [$member]) }}"
                                      style="display:inline;" x-data>
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="gh-btn gh-btn-outline gh-btn-sm"
                                            title="{{ $member->status === 'frozen' ? 'Unfreeze' : 'Freeze' }}"
                                            style="{{ $member->status === 'frozen' ? 'color:#3b82f6;border-color:#3b82f6;' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ gym_route('gym.members.destroy', [$member]) }}"
                                      style="display:inline;"
                                      x-data
                                      @submit.prevent="if(confirm('Delete {{ addslashes($member->name) }}? This cannot be undone.')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="gh-btn gh-btn-sm"
                                            style="border:1px solid #fee2e2; color:#ef4444; background:#fff;" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:48px; color:#9ca3af;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1" style="display:block; margin:0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            No members found.
                            @if(!request('search') && !request('status'))
                            <div style="margin-top:12px;">
                                <a href="{{ gym_route('gym.members.create') }}" class="gh-btn gh-btn-primary">Add First Member</a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
        <div style="padding:16px 20px; border-top:1px solid var(--gh-border); display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:13px; color:#6b7280;">
                Showing {{ $members->firstItem() }}–{{ $members->lastItem() }} of {{ $members->total() }} members
            </span>
            {{ $members->links() }}
        </div>
        @endif
    </div>
</x-layouts.app>
