<x-layouts.app>
    <x-slot:title>Edit {{ $member->name }} — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Member</x-slot:header>
    <x-slot:topbarTitle>Members</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        Home / <a href="{{ gym_route('gym.members.index') }}"
            style="color:var(--gh-primary);text-decoration:none;">Members</a>
        / <a href="{{ gym_route('gym.members.show', [$member]) }}"
            style="color:var(--gh-primary);text-decoration:none;">{{ $member->name }}</a>
        / Edit
    </x-slot:breadcrumb>

    <form method="POST" action="{{ gym_route('gym.members.update', [$member]) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div style="display:grid; grid-template-columns: 1fr 340px; gap:20px; align-items:start;">

            {{-- Left: main fields --}}
            <div style="display:flex; flex-direction:column; gap:20px;">

                {{-- Personal Info --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Personal Information</h3>
                    </div>
                    <div class="gh-card-body">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Full Name <span style="color:red;">*</span></label>
                                <input type="text" name="name"
                                    class="gh-input @error('name') border-red-400 @enderror"
                                    value="{{ old('name', $member->name) }}" required>
                                @error('name')
                                    <p class="gh-form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Phone <span style="color:red;">*</span></label>
                                <input type="tel" name="phone"
                                    class="gh-input @error('phone') border-red-400 @enderror"
                                    value="{{ old('phone', $member->phone) }}" required>
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Email</label>
                                <input type="email" name="email" class="gh-input"
                                    value="{{ old('email', $member->email) }}">
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="password">Password <span style="color:red;">(Leave blank to
                                        keep current password)</span></label>
                                <input type="text" id="password" name="password"
                                    class="gh-input @error('password') border-red-400 @enderror"
                                    placeholder="Enter password">
                                @error('password')
                                    <p class="gh-form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Gender</label>
                                <select name="gender" class="gh-input">
                                    <option value="">Select</option>
                                    @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $v => $l)
                                        <option value="{{ $v }}"
                                            {{ old('gender', $member->gender) === $v ? 'selected' : '' }}>
                                            {{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Date of Birth</label>
                                <input type="date" name="dob" class="gh-input"
                                    value="{{ old('dob', optional($member->dob)->format('Y-m-d')) }}">
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Blood Group</label>
                                <select name="blood_group" class="gh-input">
                                    <option value="">Select</option>
                                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}"
                                            {{ old('blood_group', $member->blood_group) === $bg ? 'selected' : '' }}>
                                            {{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Occupation</label>
                                <input type="text" name="occupation" class="gh-input"
                                    value="{{ old('occupation', $member->occupation) }}">
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Status</label>
                                <select name="status" class="gh-input">
                                    @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'frozen' => 'Frozen', 'expired' => 'Expired'] as $v => $l)
                                        <option value="{{ $v }}"
                                            {{ old('status', $member->status) === $v ? 'selected' : '' }}>
                                            {{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Joined Date</label>
                                <input type="date" name="joined_at" class="gh-input"
                                    value="{{ old('joined_at', optional($member->joined_at)->format('Y-m-d')) }}">
                            </div>
                            <div class="gh-form-group" style="margin:0; grid-column:1/-1;">
                                <label class="gh-label">Address</label>
                                <textarea name="address" class="gh-input" rows="2">{{ old('address', $member->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Emergency --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Emergency Contact</h3>
                    </div>
                    <div class="gh-card-body">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="gh-input"
                                    value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}">
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label">Contact Phone</label>
                                <input type="tel" name="emergency_contact_phone" class="gh-input"
                                    value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fitness --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Fitness Information</h3>
                    </div>
                    <div class="gh-card-body">
                        <div class="gh-form-group" style="margin:0 0 16px;">
                            <label class="gh-label">Fitness Goal</label>
                            <select name="goal" class="gh-input">
                                @foreach (['maintain' => 'Maintain Fitness', 'weight_loss' => 'Weight Loss', 'muscle_gain' => 'Muscle Gain', 'endurance' => 'Endurance'] as $v => $l)
                                    <option value="{{ $v }}"
                                        {{ old('goal', $member->goal) === $v ? 'selected' : '' }}>{{ $l }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="gh-form-group" style="margin:0 0 16px;">
                            <label class="gh-label">Workout Plan</label>
                            <select name="workout_plan_id" class="gh-input">
                                @php
                                    $selectedWorkout = old(
                                        'workout_plan_id',
                                        $member->workoutPlan
                                            ? $member->workoutPlan->id
                                            : ($workoutPlans->first()
                                                ? $workoutPlans->first()->id
                                                : ''),
                                    );
                                @endphp
                                @foreach ($workoutPlans as $plan)
                                    <option value="{{ $plan->id }}"
                                        {{ $selectedWorkout == $plan->id ? 'selected' : '' }}>{{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="gh-form-group" style="margin:0;">
                            <label class="gh-label">Notes</label>
                            <textarea name="notes" class="gh-input" rows="3">{{ old('notes', $member->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right --}}
            <div style="display:flex; flex-direction:column; gap:20px;">

                {{-- Avatar --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Profile Photo</h3>
                    </div>
                    <div class="gh-card-body" style="text-align:center;">
                        <div x-data="{ preview: '{{ $member->avatar ? asset('storage/' . $member->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&color=fff&background=0abf8e&size=96' }}' }">
                            <div
                                style="width:88px; height:88px; border-radius:50%; background:#f1f5f9; margin:0 auto 16px; overflow:hidden; border:3px solid var(--gh-border);">
                                <img :src="preview" style="width:100%; height:100%; object-fit:cover; "
                                    alt="">
                            </div>
                            <label for="avatar" class="gh-btn gh-btn-outline"
                                style="cursor:pointer; display:inline-flex;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                                Change Photo
                            </label>
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden"
                                @change="preview = URL.createObjectURL($event.target.files[0])">
                        </div>
                    </div>
                </div>

                {{-- WhatsApp --}}
                @if (auth()->user()->gymHasModule('whatsapp'))
                    <div class="gh-card">
                        <div class="gh-card-header">
                            <h3 class="gh-card-title">WhatsApp Updates</h3>
                        </div>
                        <div class="gh-card-body">
                            <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer;">
                                <input type="checkbox" name="whatsapp_optin" value="1"
                                    {{ old('whatsapp_optin', $member->whatsapp_optin) ? 'checked' : '' }}
                                    style="width:18px; height:18px; margin-top:2px; accent-color:var(--gh-primary); flex-shrink:0;">
                                <div>
                                    <div style="font-size:14px; font-weight:500;">Diet & Workout Updates</div>
                                    <div style="font-size:12px; color:#6b7280; margin-top:2px;">Send diet & workout
                                        information on check-in</div>
                                </div>
                            </label>
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="gh-card">
                    <div class="gh-card-body">
                        <button type="submit" class="gh-btn gh-btn-primary"
                            style="width:100%; justify-content:center; padding:11px;">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Cancel and Delete --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
        <a href="{{ gym_route('gym.members.show', [$member]) }}" class="gh-btn gh-btn-outline">
            Cancel
        </a>
        <form method="POST" action="{{ gym_route('gym.members.destroy', [$member]) }}" x-data
            @submit.prevent="if(confirm('Permanently delete {{ addslashes($member->name) }}?')) $el.submit()">
            @csrf @method('DELETE')
            <button type="submit" class="gh-btn gh-btn-danger">
                Delete Member
            </button>
        </form>
    </div>
</x-layouts.app>
