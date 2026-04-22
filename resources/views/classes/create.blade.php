<x-layouts.app>
    <x-slot:title>Add Class — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Gym Class</x-slot:header>
    <x-slot:topbarTitle>Add Class</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('classes.index') }}">Classes</a> / Add</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:700px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Class Details</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('classes.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Class Name *</label>
                        <input type="text" name="name" class="gh-input" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Trainer</label>
                        <select name="trainer_id" class="gh-input">
                            <option value="">— No Trainer —</option>
                            @foreach($trainers as $t)<option value="{{ $t->id }}" {{ old('trainer_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Room / Location</label>
                        <input type="text" name="room" class="gh-input" value="{{ old('room') }}">
                    </div>
                    <div>
                        <label class="gh-label">Start Time *</label>
                        <input type="time" name="start_time" class="gh-input" value="{{ old('start_time') }}" required>
                    </div>
                    <div>
                        <label class="gh-label">End Time *</label>
                        <input type="time" name="end_time" class="gh-input" value="{{ old('end_time') }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Capacity *</label>
                        <input type="number" name="capacity" class="gh-input" value="{{ old('capacity', 20) }}" min="1" required>
                    </div>
                    <div>
                        <label class="gh-label">Status *</label>
                        <select name="status" class="gh-input" required>
                            <option value="active" {{ old('status','active')=='active'?'selected':'' }}>Active</option>
                            <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>Inactive</option>
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Schedule Days *</label>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;">
                            @foreach(['mon','tue','wed','thu','fri','sat','sun'] as $day)
                            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;">
                                <input type="checkbox" name="schedule_days[]" value="{{ $day }}" {{ in_array($day, old('schedule_days', [])) ? 'checked' : '' }} style="accent-color:#0abf8e;">
                                <span style="text-transform:capitalize;font-size:14px;">{{ ucfirst($day) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Create Class</button>
                    <a href="{{ route('classes.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>