<x-layouts.app>
    <x-slot:title>Edit Workout Plan — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Workout Plan</x-slot:header>
    <x-slot:topbarTitle>Edit Workout Plan</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ gym_route('gym.workout-plans.index') }}">Workout Plans</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit: {{ $plan->name }}</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ gym_route('gym.workout-plans.update', [$gym, $plan]) }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Plan Name *</label>
                        <input type="text" name="name" class="gh-input" value="{{ old('name', $plan->name) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Member</label>
                        <select name="member_id" class="gh-input">
                            <option value="">— No member (default plan) —</option>
                            @foreach($members as $m)<option value="{{ $m->id }}" {{ old('member_id',$plan->member_id)==$m->id?'selected':'' }}>{{ $m->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Trainer</label>
                        <select name="trainer_id" class="gh-input">
                            <option value="">— No trainer —</option>
                            @foreach($trainers as $t)<option value="{{ $t->id }}" {{ old('trainer_id',$plan->trainer_id)==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="3">{{ old('description', $plan->description) }}</textarea>
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default',$plan->is_default) ? 'checked' : '' }}>
                            <span class="gh-label" style="margin:0;">Mark as Default Plan</span>
                        </label>
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active',$plan->is_active) ? 'checked' : '' }}>
                            <span class="gh-label" style="margin:0;">Active</span>
                        </label>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ gym_route('gym.workout-plans.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>