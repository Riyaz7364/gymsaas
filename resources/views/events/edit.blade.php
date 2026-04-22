<x-layouts.app>
    <x-slot:title>Edit Event — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Event</x-slot:header>
    <x-slot:topbarTitle>Edit Event</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('events.index') }}">Events</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:700px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit: {{ $event->title }}</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('events.update', $event) }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Title *</label>
                        <input type="text" name="title" class="gh-input" value="{{ old('title', $event->title) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Event Type</label>
                        <select name="event_type_id" class="gh-input">
                            <option value="">— Select Type —</option>
                            @foreach($eventTypes as $t)<option value="{{ $t->id }}" {{ old('event_type_id', $event->event_type_id)==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Location</label>
                        <input type="text" name="location" class="gh-input" value="{{ old('location', $event->location) }}">
                    </div>
                    <div>
                        <label class="gh-label">Start Date & Time *</label>
                        <input type="datetime-local" name="start_datetime" class="gh-input" value="{{ old('start_datetime', $event->start_datetime?->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" class="gh-input" value="{{ old('end_datetime', $event->end_datetime?->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div>
                        <label class="gh-label">Color</label>
                        <input type="color" name="color" class="gh-input" value="{{ old('color', $event->color ?? '#0abf8e') }}" style="height:42px;">
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;padding-top:24px;">
                        <input type="checkbox" name="all_day" value="1" id="all_day" {{ old('all_day', $event->all_day) ? 'checked' : '' }} style="accent-color:#0abf8e;">
                        <label for="all_day" class="gh-label" style="margin:0;">All Day Event</label>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="3">{{ old('description', $event->description) }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('events.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>