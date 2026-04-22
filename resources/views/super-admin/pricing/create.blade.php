<x-layouts.super-admin>
    <x-slot:title>Create Plan — Super Admin | {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Create Plan</x-slot:header>
    <x-slot:topbarTitle>New Subscription Plan</x-slot:topbarTitle>
    <x-slot:breadcrumb>Super Admin / <a href="{{ route('super-admin.pricing.index') }}" style="color:#0abf8e;text-decoration:none;">Pricing</a> / Create</x-slot:breadcrumb>

    <div style="max-width:700px;">
        <form method="POST" action="{{ route('super-admin.pricing.store') }}">
            @csrf

            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px;">
                <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;">
                    <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Plan Details</h3>
                </div>
                <div style="padding:24px;display:flex;flex-direction:column;gap:18px;">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Internal Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="basic / pro / enterprise"
                                   style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('name') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                            @error('name')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Display Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="display_name" value="{{ old('display_name') }}" placeholder="Basic Plan"
                                   style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('display_name') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                            @error('display_name')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Monthly Price (₹) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="monthly_price" value="{{ old('monthly_price') }}" min="0" step="1" placeholder="2999"
                                   style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('monthly_price') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                            @error('monthly_price')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Annual Price (₹) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="annual_price" value="{{ old('annual_price') }}" min="0" step="1" placeholder="29990"
                                   style="width:100%;padding:9px 12px;border:1px solid {{ $errors->has('annual_price') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                            @error('annual_price')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Max Members <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="max_members" value="{{ old('max_members', 150) }}" min="-1" placeholder="150 (-1 = unlimited)"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                            <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">-1 = unlimited</p>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Max Trainers <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="max_trainers" value="{{ old('max_trainers', 5) }}" min="-1"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Max Classes <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="max_classes" value="{{ old('max_classes', 10) }}" min="-1"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Features <span style="font-size:12px;font-weight:400;color:#94a3b8;">(one per line)</span></label>
                        <textarea name="features" rows="5" placeholder="Member management&#10;Attendance tracking&#10;Basic reports"
                                  style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;resize:vertical;box-sizing:border-box;">{{ old('features') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Sort Order <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 1) }}" min="0"
                                   style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#0f172a;outline:none;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                            <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;cursor:pointer;">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#0abf8e;">
                                <span style="font-size:14px;color:#374151;">Active (visible to gyms)</span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Premium Modules --}}
            @php
                $savedModuleIds = array_map('intval', old('modules', []));
                $groupData = $modules->map(fn($groupModules, $groupName) => [
                    'name'      => $groupName,
                    'moduleIds' => $groupModules->pluck('id')->map(fn($id) => (int) $id)->toArray(),
                ])->values();
            @endphp
            <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px;"
                 x-data="{
                     activeGroup: 0,
                     selected: {{ json_encode($savedModuleIds) }},
                     groups: {{ $groupData->toJson() }},
                     groupCount(idx) {
                         const ids = this.groups[idx].moduleIds;
                         const sel = ids.filter(id => this.selected.includes(id)).length;
                         return sel + '/' + ids.length;
                     },
                     totalSelected() { return this.selected.length; },
                     selectAll()  { this.groups.forEach(g => g.moduleIds.forEach(id => { if (!this.selected.includes(id)) this.selected.push(id); })); },
                     clearAll()   { this.selected = []; },
                     selectGroup(idx) { this.groups[idx].moduleIds.forEach(id => { if (!this.selected.includes(id)) this.selected.push(id); }); },
                     clearGroup(idx)  { const ids = this.groups[idx].moduleIds; this.selected = this.selected.filter(id => !ids.includes(id)); },
                 }">

                {{-- Header --}}
                <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Premium Add-on Modules</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">
                            Core features are always included. &nbsp;
                            <span style="color:#0abf8e;font-weight:600;"
                                  x-text="totalSelected() + ' module' + (totalSelected() !== 1 ? 's' : '') + ' selected'">0 modules selected</span>
                        </p>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button type="button" @click="selectAll()" style="font-size:12px;color:#0abf8e;background:none;border:none;cursor:pointer;font-weight:600;">Select All</button>
                        <button type="button" @click="clearAll()"  style="font-size:12px;color:#94a3b8;background:none;border:none;cursor:pointer;font-weight:600;">Clear All</button>
                    </div>
                </div>

                {{-- Group Tabs --}}
                <div style="padding:14px 24px 0;display:flex;gap:8px;flex-wrap:wrap;border-bottom:1px solid #f1f5f9;">
                    @foreach($modules as $groupName => $groupModules)
                    @php $idx = $loop->index; @endphp
                    <button type="button"
                            @click="activeGroup = {{ $idx }}"
                            :style="activeGroup === {{ $idx }}
                                ? 'background:#0abf8e;color:#fff;border-color:#0abf8e'
                                : 'background:#f8fafc;color:#64748b;border-color:#e2e8f0'"
                            style="display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1px solid;font-size:13px;font-weight:600;cursor:pointer;margin-bottom:12px;transition:all .15s;">
                        {{ $groupName }}
                        <span :style="activeGroup === {{ $idx }} ? 'background:rgba(255,255,255,0.25);color:#fff' : 'background:#e2e8f0;color:#64748b'"
                              style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:18px;padding:0 5px;border-radius:20px;font-size:11px;font-weight:700;"
                              x-text="groupCount({{ $idx }})">{{ $groupModules->filter(fn($m) => in_array($m->id, $savedModuleIds))->count() }}/{{ $groupModules->count() }}</span>
                    </button>
                    @endforeach
                </div>

                {{-- Module Panels --}}
                @foreach($modules as $groupName => $groupModules)
                @php $idx = $loop->index; @endphp
                <div x-show="activeGroup === {{ $idx }}" style="padding:20px 24px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                        <p style="font-size:12px;color:#94a3b8;margin:0;">{{ $groupModules->count() }} modules in this group</p>
                        <div style="display:flex;gap:12px;">
                            <button type="button" @click="selectGroup({{ $idx }})" style="font-size:12px;color:#0abf8e;background:none;border:none;cursor:pointer;font-weight:600;">All</button>
                            <button type="button" @click="clearGroup({{ $idx }})"  style="font-size:12px;color:#94a3b8;background:none;border:none;cursor:pointer;font-weight:600;">None</button>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;">
                        @foreach($groupModules as $mod)
                        <label :style="selected.includes({{ $mod->id }}) ? 'border:2px solid #0abf8e;background:#f0fdf9' : 'border:2px solid #e2e8f0;background:#fff'"
                               style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border-radius:10px;cursor:pointer;transition:all .15s;">
                            <input type="checkbox" name="modules[]" :value="{{ $mod->id }}" x-model="selected"
                                   style="width:16px;height:16px;accent-color:#0abf8e;flex-shrink:0;margin-top:3px;">
                            <div>
                                <span style="font-size:13px;font-weight:600;color:#0f172a;display:block;margin-bottom:3px;">{{ $mod->label }}</span>
                                <span style="font-size:11px;color:#94a3b8;line-height:1.4;">{{ $mod->description }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" style="padding:10px 24px;background:#0abf8e;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">Create Plan</button>
                <a href="{{ route('super-admin.pricing.index') }}" style="padding:10px 24px;background:#f1f5f9;color:#374151;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">Cancel</a>
            </div>
        </form>
    </div>

</x-layouts.super-admin>
