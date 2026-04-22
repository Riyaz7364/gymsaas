<x-layouts.app>
    <x-slot:title>Create Plan — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Create Membership Plan</x-slot:header>
    <x-slot:topbarTitle>Plans</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        Home / <a href="{{ route('plans.index') }}" style="color:var(--gh-primary);text-decoration:none;">Plans</a> / Create
    </x-slot:breadcrumb>

    <div style="max-width:640px;">
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">New Plan</h3>
                <a href="{{ route('plans.index') }}" class="gh-btn gh-btn-outline gh-btn-sm">← Back</a>
            </div>
            <div class="gh-card-body">

                @if($errors->any())
                <div class="gh-alert gh-alert-danger" style="margin-bottom:16px;">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('plans.store') }}">
                    @csrf

                    <div class="gh-form-group">
                        <label class="gh-label">Plan Name <span style="color:red;">*</span></label>
                        <input type="text" name="name" class="gh-input" value="{{ old('name') }}"
                               placeholder="e.g. Gold Monthly" required>
                    </div>

                    @php $initType = old('type', 'monthly'); $initDays = old('duration_days', 30); @endphp
                    <div x-data="{
                        type: '{{ $initType }}',
                        presets: { monthly: 30, quarterly: 90, half_yearly: 180, yearly: 365 },
                        customDays: {{ $initType === 'custom' ? $initDays : 30 }},
                        get isCustom() { return this.type === 'custom'; },
                        get days() { return this.isCustom ? this.customDays : this.presets[this.type]; },
                        get label() { return this.isCustom ? '' : this.presets[this.type] + ' days'; },
                    }">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; align-items:start;">
                            <div class="gh-form-group">
                                <label class="gh-label">Type <span style="color:red;">*</span></label>
                                <select name="type" class="gh-input" x-model="type" required>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="half_yearly">Half Yearly</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="gh-form-group">
                                <template x-if="isCustom">
                                    <div>
                                        <label class="gh-label">Duration (days) <span style="color:red;">*</span></label>
                                        <input type="number" name="duration_days" class="gh-input" min="1"
                                               x-model="customDays" placeholder="e.g. 45" required>
                                    </div>
                                </template>
                                <template x-if="!isCustom">
                                    <div>
                                        <label class="gh-label">Duration</label>
                                        <input type="hidden" name="duration_days" :value="days">
                                        <div class="gh-input" style="background:#f8fafc;color:#64748b;cursor:default;" x-text="label"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="gh-form-group">
                        <label class="gh-label">Price ({{ auth()->user()->gym?->currency ?? '₹' }}) <span style="color:red;">*</span></label>
                        <input type="number" name="price" class="gh-input" min="0" step="0.01"
                               value="{{ old('price') }}" placeholder="0.00" required>
                    </div>

                    <div class="gh-form-group">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="3"
                                  placeholder="Optional description of plan features…">{{ old('description') }}</textarea>
                    </div>

                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active"
                               {{ old('is_active', '1') ? 'checked' : '' }}
                               style="width:16px; height:16px;">
                        <label for="is_active" class="gh-label" style="margin:0; cursor:pointer;">Active (visible for member assignment)</label>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="gh-btn gh-btn-primary">Create Plan</button>
                        <a href="{{ route('plans.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
