<x-layouts.app>
    <x-slot:title>Add Trainer — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Add Trainer</x-slot:header>
    <x-slot:topbarTitle>Trainers</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        Home / <a href="{{ gym_route('gym.trainers.index') }}" style="color:var(--gh-primary);text-decoration:none;">Trainers</a> / Add
    </x-slot:breadcrumb>

    <div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; max-width:1000px;">

        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Trainer Info</h3>
                <a href="{{ gym_route('gym.trainers.index') }}" class="gh-btn gh-btn-outline gh-btn-sm">← Back</a>
            </div>
            <div class="gh-card-body">

                @if($errors->any())
                <div class="gh-alert gh-alert-danger" style="margin-bottom:16px;">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ gym_route('gym.trainers.store') }}" enctype="multipart/form-data" id="trainerForm">
                    @csrf

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Full Name <span style="color:red;">*</span></label>
                            <input type="text" name="name" class="gh-input" value="{{ old('name') }}" required>
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Phone <span style="color:red;">*</span></label>
                            <input type="text" name="phone" class="gh-input" value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Username <span style="color:red;">*</span></label>
                            <input type="text" name="username" class="gh-input" value="{{ old('username') }}" required>
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Email</label>
                            <input type="email" name="email" class="gh-input" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Password <span style="color:red;">*</span></label>
                            <input type="password" name="password" class="gh-input" required>
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Confirm Password <span style="color:red;">*</span></label>
                            <input type="password" name="password_confirmation" class="gh-input" required>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Experience (years)</label>
                            <input type="number" name="experience_years" class="gh-input" min="0" value="{{ old('experience_years') }}">
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Salary ({{ auth()->user()->gym?->currency ?? '₹' }})</label>
                            <input type="number" name="salary" class="gh-input" min="0" step="0.01" value="{{ old('salary') }}">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Joined Date</label>
                            <input type="date" name="joined_at" class="gh-input" value="{{ old('joined_at', date('Y-m-d')) }}">
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Status</label>
                            <select name="status" class="gh-input">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="gh-form-group">
                        <label class="gh-label">Bio / Notes</label>
                        <textarea name="bio" class="gh-input" rows="3" placeholder="Short bio or notes…">{{ old('bio') }}</textarea>
                    </div>

                    <button type="submit" class="gh-btn gh-btn-primary">Add Trainer</button>
                </form>
            </div>
        </div>

        {{-- Avatar upload --}}
        <div class="gh-card">
            <div class="gh-card-header"><h3 class="gh-card-title">Profile Photo</h3></div>
            <div class="gh-card-body" style="text-align:center;" x-data="avatarPreview()">
                <label for="avatarInput" style="cursor:pointer;">
                    <img :src="preview || 'https://ui-avatars.com/api/?name=Trainer&color=fff&background=0abf8e&size=100&bold=true'"
                         style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb; margin-bottom:12px; place-self:center;" alt="Click to upload">
                </label>
                <div style="font-size:12px; color:#9ca3af; margin-bottom:12px;">Click photo to change</div>
                <input type="file" id="avatarInput" name="avatar" form="trainerForm"
                       accept="image/*" class="gh-input" @change="onFileChange" style="font-size:12px;">
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function avatarPreview() {
        return {
            preview: null,
            onFileChange(e) {
                const file = e.target.files[0];
                if (file) this.preview = URL.createObjectURL(file);
            }
        };
    }
    </script>
    @endpush
</x-layouts.app>
