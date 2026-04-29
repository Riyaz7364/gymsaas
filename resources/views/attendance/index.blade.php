<x-layouts.app>
    <x-slot:title>Attendance — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Attendance</x-slot:header>
    <x-slot:topbarTitle>Attendance</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Attendance</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="gh-alert gh-alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background:#e0f7f1;"><i class="fas fa-user-check" style="color:#0abf8e;"></i></div>
            <div class="gh-stat-body">
                <div class="gh-stat-number">{{ $todayCount }}</div>
                <div class="gh-stat-label">Today's Check-Ins</div>
            </div>
        </div>
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background:#fef3c7;"><i class="fas fa-door-open" style="color:#f59e0b;"></i></div>
            <div class="gh-stat-body">
                <div class="gh-stat-number">{{ $insideCount }}</div>
                <div class="gh-stat-label">Currently Inside</div>
            </div>
        </div>
        <div class="gh-stat-card">
            <div class="gh-stat-icon" style="background:#ede9fe;"><i class="fas fa-calendar-check" style="color:#8b5cf6;"></i></div>
            <div class="gh-stat-body">
                <div class="gh-stat-number">{{ $monthCount }}</div>
                <div class="gh-stat-label">This Month's Check-Ins</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-5 items-start">

        {{-- Attendance table --}}
        <div>
            <div class="gh-card">
                <div class="gh-card-header flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <h3 class="gh-card-title">Attendance Log</h3>
                    <div class="flex flex-wrap gap-2 items-center">
                        <a href="{{ route('attendance.generate-qr') }}" class="gh-btn gh-btn-primary gh-btn-sm">
                            <i class="fas fa-qrcode"></i> Generate Daily QR
                        </a>
                        {{-- Date filter --}}
                        <form method="GET" class="flex flex-wrap gap-2 items-center">
                            <input type="date" name="date" value="{{ $date }}" class="gh-input" style="width:auto; padding:6px 10px;">
                            <button type="submit" class="gh-btn gh-btn-outline gh-btn-sm">Filter</button>
                            @if($date !== today()->toDateString())
                        <a href="{{ route('attendance.index') }}" class="gh-btn gh-btn-sm" style="background:#f3f4f6; color:#6b7280;">Today</a>
                        @endif
                    </form>
                </div>
            </div>
                <div class="gh-card-body" style="padding:0;">
                    <div class="overflow-x-auto">
                    <table class="gh-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Duration</th>
                                <th>Method</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $rec)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <img src="{{ $rec->member->avatar ? asset('storage/'.$rec->member->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($rec->member->name).'&color=fff&background=0abf8e&size=32&bold=true' }}"
                                             class="gh-avatar-sm" alt="">
                                        <div>
                                            <div style="font-weight:500; font-size:13px;">{{ $rec->member->name }}</div>
                                            <div style="font-size:11px; color:#9ca3af;">{{ $rec->member->member_no }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:13px;">{{ $rec->check_in->format('h:i A') }}</td>
                                <td style="font-size:13px;">
                                    {{ $rec->check_out ? $rec->check_out->format('h:i A') : '—' }}
                                </td>
                                <td style="font-size:13px;">
                                    @if($rec->duration_minutes !== null)
                                        @php $h = intdiv($rec->duration_minutes, 60); $m = $rec->duration_minutes % 60; @endphp
                                        {{ $h > 0 ? "{$h}h " : '' }}{{ $m }}m
                                    @elseif(!$rec->check_out)
                                        <span class="gh-badge gh-badge-success" style="font-size:10px;">Inside</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="gh-badge {{ $rec->method === 'qr' ? 'gh-badge-info' : 'gh-badge-muted' }}" style="font-size:10px;">
                                        {{ strtoupper($rec->method) }}
                                    </span>
                                </td>
                                <td>
                                    @if(!$rec->check_out)
                                    <form method="POST" action="{{ route('attendance.check-out', $rec) }}" style="margin:0;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="gh-btn gh-btn-outline gh-btn-sm"
                                                style="font-size:11px; padding:4px 10px;">Check-Out</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center; color:#9ca3af; padding:40px;">
                                    No attendance records for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    @if($records->hasPages())
                    <div style="padding:16px 20px;">{{ $records->appends(['date' => $date])->links() }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Check-In panel --}}
        <div x-data="checkInPanel()" x-init="init()">
            <div class="gh-card">
                <div class="gh-card-header">
                    <h3 class="gh-card-title">Manual Check-In</h3>
                </div>
                <div class="gh-card-body">

                    {{-- Search --}}
                    <div class="gh-form-group" style="margin-bottom:12px;">
                        <label class="gh-label">Search Member</label>
                        <input type="text" x-model="query" @input.debounce.300ms="search"
                               class="gh-input" placeholder="Name, phone or member no…">
                    </div>

                    {{-- Search results --}}
                    <div x-show="results.length > 0 && !selected" style="margin-bottom:12px;">
                        <template x-for="m in results" :key="m.id">
                            <div @click="selectMember(m)"
                                 style="display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:6px; cursor:pointer; border:1px solid #e5e7eb; margin-bottom:6px; background:#fff;"
                                 @mouseenter="$el.style.background='#f0fdf4'"
                                 @mouseleave="$el.style.background='#fff'">
                                <img :src="m.avatar" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
                                <div>
                                    <div style="font-size:13px; font-weight:500;" x-text="m.name"></div>
                                    <div style="font-size:11px; color:#9ca3af;" x-text="m.member_no + ' · ' + m.phone"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Selected member + form --}}
                    <div x-show="selected">
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 12px; background:#f0fdf4; border-radius:8px; margin-bottom:16px; border:1px solid #86efac;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <img :src="selected?.avatar" style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
                                <div>
                                    <div style="font-size:13px; font-weight:600;" x-text="selected?.name"></div>
                                    <div style="font-size:11px; color:#6b7280;" x-text="selected?.member_no"></div>
                                </div>
                            </div>
                            <button type="button" @click="clearSelection()" style="color:#9ca3af; background:none; border:none; cursor:pointer; font-size:16px;">✕</button>
                        </div>

                        <form method="POST" action="{{ route('attendance.check-in') }}">
                            @csrf
                            <input type="hidden" name="member_id" :value="selected?.id">
                            <button type="submit" class="gh-btn gh-btn-primary" style="width:100%; justify-content:center; padding:11px;">
                                <i class="fas fa-sign-in-alt" style="margin-right:6px;"></i> Check In
                            </button>
                        </form>
                    </div>

                    <div x-show="!selected && results.length === 0 && query.length > 0 && !loading"
                         style="text-align:center; padding:20px; color:#9ca3af; font-size:13px;">
                        No active members found.
                    </div>
                    <div x-show="query.length === 0 && !selected"
                         style="text-align:center; padding:20px; color:#d1fae5; font-size:30px;">
                        <div style="color:#9ca3af; font-size:13px; margin-top:4px;">Type to search a member</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function checkInPanel() {
        return {
            query: '',
            results: [],
            selected: null,
            loading: false,

            init() {},

            async search() {
                if (this.query.length < 2) { this.results = []; return; }
                this.loading = true;
                try {
                    const resp = await fetch(`{{ route('attendance.search-members') }}?q=${encodeURIComponent(this.query)}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.results = await resp.json();
                } finally {
                    this.loading = false;
                }
            },

            selectMember(m) {
                this.selected = m;
                this.results  = [];
                this.query    = '';
            },

            clearSelection() {
                this.selected = null;
                this.query    = '';
                this.results  = [];
            }
        };
    }
    </script>
    @endpush
</x-layouts.app>
