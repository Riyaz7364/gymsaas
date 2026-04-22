<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\WorkoutActivity;
use App\Models\WorkoutSequence;
use App\Models\WorkoutSequenceDay;
use App\Models\WorkoutSequenceExercise;
use Illuminate\Database\Seeder;

class WorkoutSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sequence_data = [
            1 => [
                'label'         => 'Chest Day',
                'icon'          => '💪',
                'color'         => '#3b82f6',
                'bg'            => '#eff6ff',
                'border'        => '#bfdbfe',
                'muscle_groups' => ['Chest', 'Triceps'],
                'exercises'     => ['Bench Press', 'Incline Dumbbell Press', 'Cable Flyes', 'Tricep Dips', 'Push-ups'],
            ],
            2 => [
                'label'         => 'Back Day',
                'icon'          => '🏋️',
                'color'         => '#8b5cf6',
                'bg'            => '#f5f3ff',
                'border'        => '#ddd6fe',
                'muscle_groups' => ['Back', 'Biceps'],
                'exercises'     => ['Pull-ups', 'Bent-over Barbell Row', 'Lat Pulldown', 'Seated Cable Row', 'Bicep Curls'],
            ],
            3 => [
                'label'         => 'Leg Day',
                'icon'          => '🦵',
                'color'         => '#10b981',
                'bg'            => '#f0fdf4',
                'border'        => '#bbf7d0',
                'muscle_groups' => ['Quads', 'Hamstrings', 'Glutes', 'Calves'],
                'exercises'     => ['Barbell Squat', 'Leg Press', 'Romanian Deadlift', 'Lunges', 'Calf Raises'],
            ],
            4 => [
                'label'         => 'Shoulder & Arms',
                'icon'          => '🤸',
                'color'         => '#f59e0b',
                'bg'            => '#fffbeb',
                'border'        => '#fde68a',
                'muscle_groups' => ['Shoulders', 'Biceps', 'Triceps'],
                'exercises'     => ['Overhead Press', 'Lateral Raises', 'Front Raises', 'Hammer Curls', 'Skull Crushers'],
            ],
            5 => [
                'label'         => 'Core & Cardio',
                'icon'          => '🏃',
                'color'         => '#ef4444',
                'bg'            => '#fef2f2',
                'border'        => '#fecaca',
                'muscle_groups' => ['Core', 'Cardio'],
                'exercises'     => ['Plank', 'Crunches', 'Hanging Leg Raises', 'Russian Twists', 'Treadmill'],
            ],
        ];

        // Get all gyms
        $gyms = Gym::all();

        foreach ($gyms as $gym) {
            // Create the default 5-day sequence for this gym
            $sequence = WorkoutSequence::firstOrCreate(
                [
                    'gym_id' => $gym->id,
                    'name' => '5-Day Full Body Split',
                ],
                [
                    'description' => 'Default 5-day workout split: Chest, Back, Legs, Shoulders & Arms, Core & Cardio, with Recovery',
                    'total_days' => 5,
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            // Create days and exercises for this sequence
            foreach ($sequence_data as $day_num => $data) {
                $day = WorkoutSequenceDay::firstOrCreate(
                    [
                        'sequence_id' => $sequence->id,
                        'day_number' => $day_num,
                    ],
                    [
                        'label' => $data['label'],
                        'icon' => $data['icon'],
                        'color' => $data['color'],
                        'bg' => $data['bg'],
                        'border' => $data['border'],
                        'muscle_groups' => $data['muscle_groups'],
                    ]
                );

                // Add exercises - try to link to activities if they exist
                foreach ($data['exercises'] as $index => $exercise_name) {
                    // Try to find matching activity
                    $activity = WorkoutActivity::where('gym_id', $gym->id)
                        ->where('name', 'like', '%' . $exercise_name . '%')
                        ->first();

                    WorkoutSequenceExercise::firstOrCreate(
                        [
                            'day_id' => $day->id,
                            'activity_id' => $activity?->id,
                        ],
                        [
                            'sort_order' => $index,
                        ]
                    );
                }
            }
        }
    }
}
