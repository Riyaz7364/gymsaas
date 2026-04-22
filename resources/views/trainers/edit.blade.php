<x-layouts.app>
    <x-slot:title>Edit Trainer — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Trainer</x-slot:header>
    <x-slot:topbarTitle>Trainers</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        Home / <a href="{{ route('trainers.index') }}" style="color:var(--gh-primary);text-decoration:none;">Trainers</a> / {{ $trainer->name }}
    </x-slot:breadcrumb>

    <div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; max-width:1000px;">

        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">{{ $trainer->name }}</h3>
                <a href="{{ route('trainers.index') }}" class="gh-btn gh-btn-outline gh-btn-sm">← Back</a>
            </div>
            <div class="gh-card-body">

                @if($errors->any())
                <div class="gh-alert gh-alert-danger" style="margin-bottom:16px;">{{ $errors->first() }}</div>
                @endif
                @if(session('success'))
                <div class="gh-alert gh-alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('trainers.update', $trainer) }}" enctype="multipart/form-data" id="trainerForm">
                    @csrf @method('PUT')

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Full Name <span style="color:red;">*</span></label>
                            <input type="text" name="name" class="gh-input" value="{{ old('name', $trainer->name) }}" required>
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Phone <span style="color:red;">*</span></label>
                            <input type="text" name="phone" class="gh-input" value="{{ old('phone', $trainer->phone) }}" required>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Email</label>
                            <input type="email" name="email" class="gh-input" value="{{ old('email', $trainer->email) }}">
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Specialization</label>
                            <input type="text" name="specialization" class="gh-input" value="{{ old('specialization', $trainer->specialization) }}">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Experience (years)</label>
                            <input type="number" name="experience_years" class="gh-input" min="0" value="{{ old('experience_years', $trainer->experience_years) }}">
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Salary ({{ auth()->user()->gym?->currency ?? '₹' }})</label>
                            <input type="number" name="salary" class="gh-input" min="0" step="0.01" value="{{ old('salary', $trainer->salary) }}">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="gh-form-group">
                            <label class="gh-label">Joined Date</label>
                            <input type="date" name="joined_at" class="gh-input" value="{{ old('joined_at', $trainer->joined_at?->format('Y-m-d')) }}">
                        </div>
                        <div class="gh-form-group">
                            <label class="gh-label">Status</label>
                            <select name="status" class="gh-input">
                                <option value="active" {{ old('status', $trainer->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $trainer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="gh-form-group">
                        <label class="gh-label">Bio / Notes</label>
                        <textarea name="bio" class="gh-input" rows="3">{{ old('bio', $trainer->bio) }}</textarea>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                        <form method="POST" action="{{ route('trainers.destroy', $trainer) }}" onsubmit="return confirm('Remove this trainer?');" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="gh-btn" style="background:#fee2e2; color:#dc2626; border:none; cursor:pointer; border-radius:6px; padding:9px 16px; font-size:13px; font-weight:500;">Delete Trainer</button>
                        </form>
                    </div>
                </form>
            </div>
        </div>

        {{-- Avatar --}}
        <div class="gh-card">
            <div class="gh-card-header"><h3 class="gh-card-title">Profile Photo</h3></div>
            <div class="gh-card-body" style="text-align:center;" x-data="avatarPreview('{{ $trainer->avatar ? asset('storage/'.$trainer->avatar) : '' }}')">
                <label for="avatarInput" style="cursor:pointer; display:block;">
                    <img :src="preview || '{{ $trainer->avatar ? asset('storage/'.$trainer->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($trainer->name).'&color=fff&background=0abf8e&size=100&bold=true' }}'"
                         style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb; margin-bottom:12px;">
                </label>
                <div style="font-size:12px; color:#9ca3af; margin-bottom:12px;">Click to change photo</div>
                <input type="file" id="avatarInput" name="avatar" form="trainerForm"
                       accept="image/*" class="gh-input" @change="onFileChange" style="font-size:12px;">
                <div style="margin-top:16px; padding-top:16px; border-top:1px solid #f3f4f6;">
                    <div style="font-size:13px; color:#6b7280; margin-bottom:4px;"><strong>{{ $trainer->members_count }}</strong> assigned members</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function avatarPreview(existing) {
        return {
            preview: existing || null,
            onFileChange(e) {
                const file = e.target.files[0];
                if (file) this.preview = URL.createObjectURL(file);
            }
        };
    }
    </script>
    @endpush
</x-layouts.app>
