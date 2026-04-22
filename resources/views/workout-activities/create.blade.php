<x-layouts.app>
    <x-slot:title>Add Exercise — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Exercise</x-slot:header>
    <x-slot:topbarTitle>Add Exercise</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('workout-activities.index') }}">Activities</a> / Add</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Exercise Details</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('workout-activities.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Exercise Name *</label>
                        <input type="text" name="name" class="gh-input" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Category</label>
                        <select name="category_id" class="gh-input">
                            <option value="">— None —</option>
                            @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Difficulty</label>
                        <select name="difficulty" class="gh-input">
                            <option value="">— None —</option>
                            <option value="beginner" {{ old('difficulty')==='beginner'?'selected':'' }}>Beginner</option>
                            <option value="intermediate" {{ old('difficulty')==='intermediate'?'selected':'' }}>Intermediate</option>
                            <option value="advanced" {{ old('difficulty')==='advanced'?'selected':'' }}>Advanced</option>
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Muscle Group</label>
                        <input type="text" name="muscle_group" class="gh-input" value="{{ old('muscle_group') }}" placeholder="e.g. Chest, Back">
                    </div>
                    <div>
                        <label class="gh-label">Equipment</label>
                        <input type="text" name="equipment" class="gh-input" value="{{ old('equipment') }}" placeholder="e.g. Barbell, Dumbbell">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Video URL</label>
                        <input type="url" name="video_url" class="gh-input" value="{{ old('video_url') }}" placeholder="https://youtube.com/...">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Add Exercise</button>
                    <a href="{{ route('workout-activities.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>