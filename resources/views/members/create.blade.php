<x-layouts.app>
    <x-slot:title>Add Member — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Member</x-slot:header>
    <x-slot:topbarTitle>Members</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('members.index') }}" style="color:var(--gh-primary);text-decoration:none;">Members</a> / Add</x-slot:breadcrumb>

    <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
        @csrf

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
                                <label class="gh-label" for="name">Full Name <span style="color:red;">*</span></label>
                                <input type="text" id="name" name="name" class="gh-input @error('name') border-red-400 @enderror"
                                       value="{{ old('name') }}" placeholder="John Doe" required>
                                @error('name')<p class="gh-form-error">{{ $message }}</p>@enderror
                            </div>

                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="phone">Phone <span style="color:red;">*</span></label>
                                <input type="tel" id="phone" name="phone" class="gh-input @error('phone') border-red-400 @enderror"
                                       value="{{ old('phone') }}" placeholder="+91 98765 43210" required>
                                @error('phone')<p class="gh-form-error">{{ $message }}</p>@enderror
                            </div>

                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="gh-input"
                                       value="{{ old('email') }}" placeholder="email@example.com">
                            </div>

                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="gender">Gender</label>
                                <select id="gender" name="gender" class="gh-input">
                                    <option value="">Select Gender</option>
                                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="dob">Date of Birth</label>
                                <input type="date" id="dob" name="dob" class="gh-input" value="{{ old('dob') }}">
                            </div>

                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="blood_group">Blood Group</label>
                                <select id="blood_group" name="blood_group" class="gh-input">
                                    <option value="">Select</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="occupation">Occupation</label>
                                <input type="text" id="occupation" name="occupation" class="gh-input"
                                       value="{{ old('occupation') }}" placeholder="Software Engineer">
                            </div>

                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="joined_at">Join Date</label>
                                <input type="date" id="joined_at" name="joined_at" class="gh-input"
                                       value="{{ old('joined_at', date('Y-m-d')) }}">
                            </div>

                            <div class="gh-form-group" style="margin:0; grid-column:1/-1;">
                                <label class="gh-label" for="address">Address</label>
                                <textarea id="address" name="address" class="gh-input" rows="2"
                                          placeholder="Full address…">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Emergency Contact --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Emergency Contact</h3>
                    </div>
                    <div class="gh-card-body">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="emergency_contact_name">Contact Name</label>
                                <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                       class="gh-input" value="{{ old('emergency_contact_name') }}" placeholder="Guardian / Spouse">
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="emergency_contact_phone">Contact Phone</label>
                                <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone"
                                       class="gh-input" value="{{ old('emergency_contact_phone') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fitness Info --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Fitness Information</h3>
                    </div>
                    <div class="gh-card-body">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="goal">Fitness Goal</label>
                                <select id="goal" name="goal" class="gh-input">
                                    <option value="maintain"    {{ old('goal','maintain') === 'maintain'    ? 'selected' : '' }}>Maintain Fitness</option>
                                    <option value="weight_loss" {{ old('goal') === 'weight_loss' ? 'selected' : '' }}>Weight Loss</option>
                                    <option value="muscle_gain" {{ old('goal') === 'muscle_gain' ? 'selected' : '' }}>Muscle Gain</option>
                                    <option value="endurance"   {{ old('goal') === 'endurance'   ? 'selected' : '' }}>Endurance</option>
                                </select>
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="trainer_id">Assign Trainer</label>
                                <select id="trainer_id" name="trainer_id" class="gh-input">
                                    <option value="">No Trainer Assigned</option>
                                    @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                        {{ $trainer->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="gh-form-group" style="margin-top:16px; margin-bottom:0;">
                            <label class="gh-label" for="notes">Notes</label>
                            <textarea id="notes" name="notes" class="gh-input" rows="3"
                                      placeholder="Any notes about the member…">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Membership Plan --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Assign Membership Plan</h3>
                        <span style="font-size:12px; color:#9ca3af;">Optional — can be done later</span>
                    </div>
                    <div class="gh-card-body">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="plan_id">Membership Plan</label>
                                <select id="plan_id" name="plan_id" class="gh-input">
                                    <option value="">Select Plan (optional)</option>
                                    @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} — {{ auth()->user()->gym?->currency ?? '₹' }}{{ number_format($plan->price) }}
                                        ({{ $plan->duration_days }} days)
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="gh-form-group" style="margin:0;">
                                <label class="gh-label" for="plan_start_date">Plan Start Date</label>
                                <input type="date" id="plan_start_date" name="plan_start_date"
                                       class="gh-input" value="{{ old('plan_start_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: avatar + settings --}}
            <div style="display:flex; flex-direction:column; gap:20px;">

                {{-- Avatar --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">Profile Photo</h3>
                    </div>
                    <div class="gh-card-body" style="text-align:center;">
                        <div x-data="{ preview: null }">
                            <div style="width:96px; height:96px; border-radius:50%; background:#f1f5f9; margin:0 auto 16px; overflow:hidden; border:3px solid var(--gh-border);">
                                <img :src="preview ?? 'https://ui-avatars.com/api/?name=Member&color=fff&background=0abf8e&size=96'"
                                     style="width:100%; height:100%; object-fit:cover;" alt="Preview">
                            </div>
                            <label for="avatar" class="gh-btn gh-btn-outline" style="cursor:pointer; display:inline-block;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                                Upload Photo
                            </label>
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden"
                                   @change="preview = URL.createObjectURL($event.target.files[0])">
                            <p style="font-size:12px; color:#9ca3af; margin-top:8px;">JPG, PNG up to 2MB</p>
                        </div>
                    </div>
                </div>

                {{-- WhatsApp --}}
                <div class="gh-card">
                    <div class="gh-card-header">
                        <h3 class="gh-card-title">WhatsApp AI</h3>
                    </div>
                    <div class="gh-card-body">
                        <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer;">
                            <input type="checkbox" name="whatsapp_optin" value="1"
                                   {{ old('whatsapp_optin', '1') ? 'checked' : '' }}
                                   style="width:18px; height:18px; margin-top:2px; accent-color:var(--gh-primary); flex-shrink:0;">
                            <div>
                                <div style="font-size:14px; font-weight:500; color:#111827;">Opt-in WhatsApp AI</div>
                                <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                    Member will receive AI-generated personalized diet &amp; workout plans on WhatsApp after every check-in.
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="gh-card">
                    <div class="gh-card-body">
                        <button type="submit" class="gh-btn gh-btn-primary" style="width:100%; justify-content:center; padding:11px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Add Member
                        </button>
                        <a href="{{ route('members.index') }}"
                           class="gh-btn gh-btn-outline" style="width:100%; justify-content:center; padding:11px; margin-top:10px; display:flex;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-layouts.app>
