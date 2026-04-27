<x-layouts.app>
    <x-slot:title>Edit Body Stats — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Body Stats</x-slot:header>
    <x-slot:topbarTitle>Edit Body Stats</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('body-stats.index') }}">Body Stats</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit Body Stats</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            @php
                $existingPhotos = isset($stat) && $stat->photos ? $stat->photos->map(function($photo) {
                    return ['id' => $photo->id, 'url' => asset('storage/' . $photo->photo_path)];
                })->values()->toArray() : [];
            @endphp

            <form method="POST" action="{{ route('body-stats.update', $stat) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Member *</label>
                        <select name="member_id" class="gh-input" required>
                            <option value="">— Select Member —</option>
                            @foreach($members as $m)<option value="{{ $m->id }}" {{ old('member_id',$stat->member_id)==$m->id?'selected':'' }}>{{ $m->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Date *</label>
                        <input type="date" name="date" class="gh-input" value="{{ old('date', $stat->date?->format('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Weight (kg)</label>
                        <input type="number" name="weight" class="gh-input" value="{{ old('weight', $stat->weight) }}" step="0.1" min="0">
                    </div>
                    <div>
                        <label class="gh-label">Height (cm)</label>
                        <input type="number" name="height" class="gh-input" value="{{ old('height', $stat->height) }}" step="0.1" min="0">
                    </div>
                    <div>
                        <label class="gh-label">Body Fat %</label>
                        <input type="number" name="body_fat_pct" class="gh-input" value="{{ old('body_fat_pct', $stat->body_fat_pct) }}" step="0.1" min="0" max="100">
                    </div>
                    <div>
                        <label class="gh-label">Waist (cm)</label>
                        <input type="number" name="waist" class="gh-input" value="{{ old('waist', $stat->waist) }}" step="0.1" min="0">
                    </div>
                    <div>
                        <label class="gh-label">Chest (cm)</label>
                        <input type="number" name="chest" class="gh-input" value="{{ old('chest', $stat->chest) }}" step="0.1" min="0">
                    </div>
                    <div>
                        <label class="gh-label">Arms (cm)</label>
                        <input type="number" name="arms" class="gh-input" value="{{ old('arms', $stat->arms) }}" step="0.1" min="0">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Notes</label>
                        <textarea name="notes" class="gh-input" rows="2">{{ old('notes', $stat->notes) }}</textarea>
                    </div>

                    <x-photo-uploader name="photos[]" label="Photos (Optional)" :existing="$existingPhotos" />
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('body-stats.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>