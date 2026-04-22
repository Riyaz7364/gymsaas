<x-layouts.app>
    <x-slot:title>New Diet Plan — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>New Diet Plan</x-slot:header>
    <x-slot:topbarTitle>New Diet Plan</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('diet-plans.index') }}">Diet Plans</a> / New</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Plan Details</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('diet-plans.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Plan Name *</label>
                        <input type="text" name="name" class="gh-input" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Goal</label>
                        <select name="goal" class="gh-input">
                            <option value="">— Select goal —</option>
                            @foreach(['weight_loss' => 'Weight Loss', 'muscle_gain' => 'Muscle Gain', 'maintain' => 'Maintain', 'endurance' => 'Endurance'] as $val => $label)
                            <option value="{{ $val }}" {{ old('goal') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Member (optional)</label>
                        <select name="member_id" class="gh-input">
                            <option value="">— Default Plan (no member) —</option>
                            @foreach($members as $m)<option value="{{ $m->id }}" {{ old('member_id')==$m->id?'selected':'' }}>{{ $m->name }}</option>@endforeach
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <span class="gh-label" style="margin:0;">Mark as Default Plan</span>
                        </label>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Create Plan</button>
                    <a href="{{ route('diet-plans.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>