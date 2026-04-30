<x-layouts.app>
    <x-slot:title>{{ isset($workoutSequence) ? 'Edit' : 'Create' }} Workout Sequence — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>{{ isset($workoutSequence) ? 'Edit' : 'Create' }} Workout Sequence</x-slot:header>
    <x-slot:topbarTitle>{{ isset($workoutSequence) ? 'Edit' : 'Create' }} Sequence</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ gym_route('gym.workout-sequences.index') }}">Sequences</a> / {{ isset($workoutSequence) ? 'Edit' : 'Create' }}</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:900px;">
        <div class="gh-card-header"><h3 class="gh-card-title">{{ isset($workoutSequence) ? 'Edit' : 'New' }} Sequence</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger" style="margin-bottom:20px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ isset($workoutSequence) ? gym_route('gym.workout-sequences.update', [$workoutSequence]) : gym_route('gym.workout-sequences.store') }}" x-data="sequenceForm()">
                @csrf
                @if(isset($workoutSequence))
                @method('PUT')
                @endif

                {{-- Basic Info --}}
                <div style="margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid var(--gh-border);">
                    <h4 style="font-size:14px; font-weight:700; color:#374151; margin-bottom:16px;">Basic Information</h4>
                    <div style="display:grid; gap:16px; grid-template-columns:1fr 200px;">
                        <div>
                            <label class="gh-label">Sequence Name *</label>
                            <input type="text" name="name" class="gh-input" 
                                   value="{{ old('name', $workoutSequence->name ?? '') }}" 
                                   placeholder="e.g., 5-Day Push/Pull/Legs" required>
                        </div>
                        <div>
                            <label class="gh-label">Total Days *</label>
                            <input type="number" name="total_days" class="gh-input" 
                                   value="{{ old('total_days', $workoutSequence->total_days ?? 5) }}" 
                                   min="1" max="7" 
                                   x-model.number="totalDays"
                                   @change="updateDays()"
                                   required>
                            <div style="font-size:11px; color:#9ca3af; margin-top:4px;">1-7 days</div>
                        </div>
                    </div>
                    <div style="margin-top:16px;">
                        <label class="gh-label">Description</label>
                        <textarea name="description" class="gh-input" rows="2" placeholder="Optional description...">{{ old('description', $workoutSequence->description ?? '') }}</textarea>
                    </div>
                    <div style="margin-top:16px; display:flex; gap:16px;">
                        <label style="display:flex; align-items:center; gap:8px;">
                            <input type="checkbox" name="is_default" value="1" 
                                   {{ old('is_default', $workoutSequence->is_default ?? false) ? 'checked' : '' }}>
                            <span style="font-size:13px; color:#374151;">Set as default for new members</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px;">
                            <input type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $workoutSequence->is_active ?? true) ? 'checked' : '' }}>
                            <span style="font-size:13px; color:#374151;">Active</span>
                        </label>
                    </div>
                </div>

                {{-- Days Configuration --}}
                <h4 style="font-size:14px; font-weight:700; color:#374151; margin-bottom:16px;">Workout Days</h4>
                <template x-for="(day, index) in days" :key="index">
                    <div style="margin-bottom:24px; padding:16px; background:#f9fafb; border-radius:8px; border:1px solid var(--gh-border);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                            <h5 style="font-size:13px; font-weight:600; color:#374151;">Day <span x-text="index + 1"></span></h5>
                            <div style="display:flex; gap:8px; align-items:center;">
                                <input type="color" x-model="day.color" @change="day.bg = getLightColor(day.color)" 
                                       style="width:36px; height:36px; border:1px solid var(--gh-border); border-radius:4px; cursor:pointer;">
                                <span x-text="day.icon" style="font-size:20px;"></span>
                            </div>
                        </div>

                        <input type="hidden" :name="`days[${index}][color]`" :value="day.color">
                        <input type="hidden" :name="`days[${index}][bg]`" :value="day.bg">
                        <input type="hidden" :name="`days[${index}][border]`" :value="getLightBorder(day.color)">

                        <div style="display:grid; grid-template-columns:1.5fr 150px 80px; gap:12px; margin-bottom:16px;">
                            <div>
                                <label class="gh-label">Label *</label>
                                <input type="text" :name="`days[${index}][label]`" class="gh-input" 
                                       x-model="day.label" 
                                       placeholder="e.g., Chest Day" required>
                            </div>
                            <div>
                                <label class="gh-label">Icon</label>
                                <input type="text" :name="`days[${index}][icon]`" class="gh-input" 
                                       x-model="day.icon" 
                                       placeholder="e.g., 💪" 
                                       maxlength="3">
                            </div>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label class="gh-label">Muscle Groups</label>
                            <input type="text" :name="`days_ui[${index}][muscle_groups_text]`" class="gh-input" 
                                   placeholder="e.g., Chest, Triceps (comma-separated)"
                                   @input="updateMuscleGroups(index)"
                                   :value="day.muscle_groups.join(', ')">
                            <template x-for="(mg, mgIndex) in day.muscle_groups" :key="`mg-${index}-${mgIndex}`">
                                <input type="hidden" :name="`days[${index}][muscle_groups][${mgIndex}]`" :value="mg">
                            </template>
                        </div>

                        <div>
                            <label class="gh-label">Exercises</label>
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                <template x-for="(exercise, exIndex) in day.exercises" :key="exIndex">
                                    <div style="display:flex; gap:8px;">
                                        <select :name="`days[${index}][exercises][${exIndex}]`" class="gh-input"
                                                x-model="day.exercises[exIndex]">
                                            <option value="">— Select Activity —</option>
                                            @foreach($activities as $activity)
                                            <option value="{{ $activity->id }}">{{ $activity->name }} ({{ $activity->muscle_group }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" @click="day.exercises.splice(exIndex, 1)" 
                                                class="gh-btn gh-btn-sm" style="background:#fee2e2; color:#dc2626; border:none; flex-shrink:0;">×</button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="day.exercises.push('')" 
                                    class="gh-btn gh-btn-outline gh-btn-sm" style="margin-top:8px; width:100%;">+ Add Exercise</button>
                        </div>
                    </div>
                </template>

                <div style="margin-top:24px; display:flex; gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">{{ isset($workoutSequence) ? 'Update' : 'Create' }} Sequence</button>
                    <a href="{{ gym_route('gym.workout-sequences.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function sequenceForm() {
            return {
                totalDays: {{ isset($workoutSequence) ? $workoutSequence->total_days : 5 }},
                days: [
                    @if(isset($workoutSequence))
                        @foreach($workoutSequence->days as $day)
                        {
                            label: '{{ $day->label }}',
                            icon: '{{ $day->icon }}',
                            color: '{{ $day->color }}',
                            bg: '{{ $day->bg }}',
                            muscle_groups: {!! json_encode($day->muscle_groups ?? []) !!},
                            exercises: [
                                @foreach($day->exercises as $exercise)
                                '{{ $exercise->activity_id ?? '' }}',
                                @endforeach
                            ],
                        },
                        @endforeach
                    @else
                        { label: 'Chest Day', icon: '💪', color: '#3b82f6', bg: '#eff6ff', muscle_groups: ['Chest', 'Triceps'], exercises: [] },
                        { label: 'Back Day', icon: '🏋️', color: '#8b5cf6', bg: '#f5f3ff', muscle_groups: ['Back', 'Biceps'], exercises: [] },
                        { label: 'Leg Day', icon: '🦵', color: '#10b981', bg: '#f0fdf4', muscle_groups: ['Quads', 'Hamstrings'], exercises: [] },
                        { label: 'Shoulder & Arms', icon: '🤸', color: '#f59e0b', bg: '#fffbeb', muscle_groups: ['Shoulders', 'Arms'], exercises: [] },
                        { label: 'Core & Cardio', icon: '🏃', color: '#ef4444', bg: '#fef2f2', muscle_groups: ['Core', 'Cardio'], exercises: [] },
                    @endif
                ],
                updateDays() {
                    const newTotal = this.totalDays;
                    const currentTotal = this.days.length;

                    if (newTotal > currentTotal) {
                        for (let i = currentTotal; i < newTotal; i++) {
                            this.days.push({
                                label: `Day ${i + 1}`,
                                icon: '💪',
                                color: '#3b82f6',
                                bg: '#eff6ff',
                                muscle_groups: [],
                                exercises: [],
                            });
                        }
                    } else if (newTotal < currentTotal) {
                        this.days = this.days.slice(0, newTotal);
                    }
                },
                updateMuscleGroups(index) {
                    const input = document.querySelector(`input[name="days_ui[${index}][muscle_groups_text]"]`);
                    if (input) {
                        this.days[index].muscle_groups = input.value
                            .split(',')
                            .map(m => m.trim())
                            .filter(m => m);
                    }
                },
                getLightColor(hex) {
                    return hex + '1a';
                },
                getLightBorder(hex) {
                    return hex + '80';
                },
            };
        }
    </script>
</x-layouts.app>
