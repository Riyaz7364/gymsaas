<x-layouts.app>
    <x-slot:title>Add Locker — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Locker</x-slot:header>
    <x-slot:topbarTitle>Add Locker</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('lockers.index') }}">Lockers</a> / Add</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Locker Details</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('lockers.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label class="gh-label">Locker Number *</label>
                        <input type="text" name="locker_no" class="gh-input" value="{{ old('locker_no') }}" required placeholder="e.g. A-101">
                    </div>
                    <div>
                        <label class="gh-label">Status *</label>
                        <select name="status" class="gh-input" required>
                            <option value="available" {{ old('status','available')=='available'?'selected':'' }}>Available</option>
                            <option value="occupied" {{ old('status')=='occupied'?'selected':'' }}>Occupied</option>
                            <option value="maintenance" {{ old('status')=='maintenance'?'selected':'' }}>Maintenance</option>
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Assign to Member</label>
                        <select name="member_id" class="gh-input">
                            <option value="">— Unassigned —</option>
                            @foreach($members as $m)<option value="{{ $m->id }}" {{ old('member_id')==$m->id?'selected':'' }}>{{ $m->name }} ({{ $m->member_no }})</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Assigned Date</label>
                        <input type="date" name="assigned_at" class="gh-input" value="{{ old('assigned_at') }}">
                    </div>
                    <div>
                        <label class="gh-label">Expires Date</label>
                        <input type="date" name="expires_at" class="gh-input" value="{{ old('expires_at') }}">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Notes</label>
                        <textarea name="notes" class="gh-input" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Create Locker</button>
                    <a href="{{ route('lockers.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>