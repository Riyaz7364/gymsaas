<x-layouts.app>
    <x-slot:title>Edit Locker — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Locker</x-slot:header>
    <x-slot:topbarTitle>Edit Locker</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('lockers.index') }}">Lockers</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit Locker #{{ $locker->locker_no }}</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('lockers.update', $locker) }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label class="gh-label">Locker Number *</label>
                        <input type="text" name="locker_no" class="gh-input" value="{{ old('locker_no', $locker->locker_no) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Status *</label>
                        <select name="status" class="gh-input" required>
                            <option value="available" {{ old('status',$locker->status)=='available'?'selected':'' }}>Available</option>
                            <option value="occupied" {{ old('status',$locker->status)=='occupied'?'selected':'' }}>Occupied</option>
                            <option value="maintenance" {{ old('status',$locker->status)=='maintenance'?'selected':'' }}>Maintenance</option>
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Assign to Member</label>
                        <select name="member_id" class="gh-input">
                            <option value="">— Unassigned —</option>
                            @foreach($members as $m)<option value="{{ $m->id }}" {{ old('member_id',$locker->member_id)==$m->id?'selected':'' }}>{{ $m->name }} ({{ $m->member_no }})</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Assigned Date</label>
                        <input type="date" name="assigned_at" class="gh-input" value="{{ old('assigned_at', $locker->assigned_at?->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="gh-label">Expires Date</label>
                        <input type="date" name="expires_at" class="gh-input" value="{{ old('expires_at', $locker->expires_at?->format('Y-m-d')) }}">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Notes</label>
                        <textarea name="notes" class="gh-input" rows="2">{{ old('notes', $locker->notes) }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('lockers.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>