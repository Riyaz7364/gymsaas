<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\WorkoutCategory;
use App\Models\WorkoutActivity;
use App\Models\WorkoutSequence;
use App\Models\WorkoutSequenceDay;
use App\Models\WorkoutSequenceExercise;
use Illuminate\Database\Seeder;

class WorkoutActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Strength Training',
                'icon' => '💪',
                'activities' => [
                    // Chest
                    ['name' => 'Bench Press', 'muscle_group' => 'Chest', 'equipment' => 'Barbell', 'difficulty' => 'Intermediate'],
                    ['name' => 'Incline Dumbbell Press', 'muscle_group' => 'Chest', 'equipment' => 'Dumbbells', 'difficulty' => 'Intermediate'],
                    ['name' => 'Cable Flyes', 'muscle_group' => 'Chest', 'equipment' => 'Cable Machine', 'difficulty' => 'Beginner'],
                    ['name' => 'Push-ups', 'muscle_group' => 'Chest', 'equipment' => 'Bodyweight', 'difficulty' => 'Beginner'],
                    ['name' => 'Tricep Dips', 'muscle_group' => 'Triceps', 'equipment' => 'Bodyweight', 'difficulty' => 'Intermediate'],

                    // Back
                    ['name' => 'Pull-ups', 'muscle_group' => 'Back', 'equipment' => 'Pull-up Bar', 'difficulty' => 'Advanced'],
                    ['name' => 'Bent-over Barbell Row', 'muscle_group' => 'Back', 'equipment' => 'Barbell', 'difficulty' => 'Intermediate'],
                    ['name' => 'Lat Pulldown', 'muscle_group' => 'Back', 'equipment' => 'Cable Machine', 'difficulty' => 'Beginner'],
                    ['name' => 'Seated Cable Row', 'muscle_group' => 'Back', 'equipment' => 'Cable Machine', 'difficulty' => 'Beginner'],
                    ['name' => 'Bicep Curls', 'muscle_group' => 'Biceps', 'equipment' => 'Dumbbells', 'difficulty' => 'Beginner'],

                    // Legs
                    ['name' => 'Barbell Squat', 'muscle_group' => 'Quads', 'equipment' => 'Barbell', 'difficulty' => 'Intermediate'],
                    ['name' => 'Leg Press', 'muscle_group' => 'Quads', 'equipment' => 'Leg Press Machine', 'difficulty' => 'Beginner'],
                    ['name' => 'Romanian Deadlift', 'muscle_group' => 'Hamstrings', 'equipment' => 'Barbell', 'difficulty' => 'Intermediate'],
                    ['name' => 'Lunges', 'muscle_group' => 'Quads', 'equipment' => 'Bodyweight', 'difficulty' => 'Beginner'],
                    ['name' => 'Calf Raises', 'muscle_group' => 'Calves', 'equipment' => 'Bodyweight', 'difficulty' => 'Beginner'],

                    // Shoulders & Arms
                    ['name' => 'Overhead Press', 'muscle_group' => 'Shoulders', 'equipment' => 'Barbell', 'difficulty' => 'Intermediate'],
                    ['name' => 'Lateral Raises', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'Beginner'],
                    ['name' => 'Front Raises', 'muscle_group' => 'Shoulders', 'equipment' => 'Dumbbells', 'difficulty' => 'Beginner'],
                    ['name' => 'Hammer Curls', 'muscle_group' => 'Biceps', 'equipment' => 'Dumbbells', 'difficulty' => 'Beginner'],
                    ['name' => 'Skull Crushers', 'muscle_group' => 'Triceps', 'equipment' => 'Dumbbells', 'difficulty' => 'Intermediate'],
                ]
            ],
            [
                'name' => 'Core & Cardio',
                'icon' => '🏃',
                'activities' => [
                    // Core
                    ['name' => 'Plank', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'Beginner'],
                    ['name' => 'Crunches', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'Beginner'],
                    ['name' => 'Hanging Leg Raises', 'muscle_group' => 'Core', 'equipment' => 'Pull-up Bar', 'difficulty' => 'Advanced'],
                    ['name' => 'Russian Twists', 'muscle_group' => 'Core', 'equipment' => 'Bodyweight', 'difficulty' => 'Beginner'],

                    // Cardio
                    ['name' => 'Treadmill Running', 'muscle_group' => 'Cardio', 'equipment' => 'Treadmill', 'difficulty' => 'Beginner'],
                    ['name' => 'Stationary Bike', 'muscle_group' => 'Cardio', 'equipment' => 'Exercise Bike', 'difficulty' => 'Beginner'],
                    ['name' => 'Elliptical', 'muscle_group' => 'Cardio', 'equipment' => 'Elliptical Machine', 'difficulty' => 'Beginner'],
                    ['name' => 'Rowing Machine', 'muscle_group' => 'Cardio', 'equipment' => 'Rowing Machine', 'difficulty' => 'Beginner'],
                    ['name' => 'Jump Rope', 'muscle_group' => 'Cardio', 'equipment' => 'Jump Rope', 'difficulty' => 'Beginner'],
                ]
            ],
            [
                'name' => 'Recovery & Mobility',
                'icon' => '🛌',
                'activities' => [
                    ['name' => 'Foam Rolling', 'muscle_group' => 'Full Body', 'equipment' => 'Foam Roller', 'difficulty' => 'Beginner'],
                    ['name' => 'Stretching', 'muscle_group' => 'Full Body', 'equipment' => 'Bodyweight', 'difficulty' => 'Beginner'],
                    ['name' => 'Yoga', 'muscle_group' => 'Full Body', 'equipment' => 'Yoga Mat', 'difficulty' => 'Beginner'],
                    ['name' => 'Light Walking', 'muscle_group' => 'Cardio', 'equipment' => 'Bodyweight', 'difficulty' => 'Beginner'],
                ]
            ]
        ];

        // Get all gyms
        $gyms = Gym::all();

        foreach ($gyms as $gym) {
            foreach ($categories as $categoryData) {
                // Create category
                $category = WorkoutCategory::firstOrCreate(
                    [
                        'gym_id' => $gym->id,
                        'name' => $categoryData['name'],
                    ],
                    [
                        'icon' => $categoryData['icon'],
                    ]
                );

                // Create activities for this category
                foreach ($categoryData['activities'] as $activityData) {
                    WorkoutActivity::firstOrCreate(
                        [
                            'gym_id' => $gym->id,
                            'name' => $activityData['name'],
                        ],
                        [
                            'category_id' => $category->id,
                            'description' => null,
                            'muscle_group' => $activityData['muscle_group'],
                            'equipment' => $activityData['equipment'],
                            'difficulty' => $activityData['difficulty'],
                            'video_url' => null,
                            'image' => null,
                        ]
                    );
                }
            }
        }

        // Create default workout sequences
        $this->createDefaultSequences();
    }

    private function createDefaultSequences()
    {
        $gyms = Gym::all();

        foreach ($gyms as $gym) {
            // Beginner Full Body Sequence
            $sequence1 = WorkoutSequence::firstOrCreate(
                [
                    'gym_id' => $gym->id,
                    'name' => 'Beginner Full Body',
                ],
                [
                    'description' => 'A 3-day full body workout for beginners',
                    'total_days' => 3,
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            // Day 1
            $day1 = WorkoutSequenceDay::firstOrCreate(
                [
                    'sequence_id' => $sequence1->id,
                    'day_number' => 1,
                ],
                [
                    'label' => 'Upper Body Focus',
                    'icon' => '💪',
                    'color' => '#3b82f6',
                    'bg' => '#eff6ff',
                    'border' => '#bfdbfe',
                    'muscle_groups' => ['Chest', 'Back', 'Shoulders', 'Arms'],
                ]
            );

            $activities = WorkoutActivity::where('gym_id', $gym->id)->get();
            $exercises = [
                'Bench Press', 'Bent-over Barbell Row', 'Overhead Press', 'Bicep Curls', 'Plank'
            ];

            $order = 1;
            foreach ($exercises as $exerciseName) {
                $activity = $activities->where('name', $exerciseName)->first();
                if ($activity) {
                    WorkoutSequenceExercise::firstOrCreate(
                        [
                            'day_id' => $day1->id,
                            'activity_id' => $activity->id,
                        ],
                        [
                            'name' => $activity->name,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }

            // Day 2
            $day2 = WorkoutSequenceDay::firstOrCreate(
                [
                    'sequence_id' => $sequence1->id,
                    'day_number' => 2,
                ],
                [
                    'label' => 'Lower Body Focus',
                    'icon' => '🦵',
                    'color' => '#10b981',
                    'bg' => '#ecfdf5',
                    'border' => '#a7f3d0',
                    'muscle_groups' => ['Quads', 'Hamstrings', 'Calves'],
                ]
            );

            $exercises2 = [
                'Barbell Squat', 'Romanian Deadlift', 'Leg Press', 'Calf Raises', 'Crunches'
            ];

            $order = 1;
            foreach ($exercises2 as $exerciseName) {
                $activity = $activities->where('name', $exerciseName)->first();
                if ($activity) {
                    WorkoutSequenceExercise::firstOrCreate(
                        [
                            'day_id' => $day2->id,
                            'activity_id' => $activity->id,
                        ],
                        [
                            'name' => $activity->name,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }

            // Day 3
            $day3 = WorkoutSequenceDay::firstOrCreate(
                [
                    'sequence_id' => $sequence1->id,
                    'day_number' => 3,
                ],
                [
                    'label' => 'Full Body Circuit',
                    'icon' => '🏃',
                    'color' => '#f59e0b',
                    'bg' => '#fffbeb',
                    'border' => '#fde68a',
                    'muscle_groups' => ['Full Body'],
                ]
            );

            $exercises3 = [
                'Push-ups', 'Pull-ups', 'Lunges', 'Lateral Raises', 'Russian Twists'
            ];

            $order = 1;
            foreach ($exercises3 as $exerciseName) {
                $activity = $activities->where('name', $exerciseName)->first();
                if ($activity) {
                    WorkoutSequenceExercise::firstOrCreate(
                        [
                            'day_id' => $day3->id,
                            'activity_id' => $activity->id,
                        ],
                        [
                            'name' => $activity->name,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }

            // Intermediate Upper/Lower Split
            $sequence2 = WorkoutSequence::firstOrCreate(
                [
                    'gym_id' => $gym->id,
                    'name' => 'Intermediate Upper/Lower Split',
                ],
                [
                    'description' => 'A 4-day upper/lower split for intermediate lifters',
                    'total_days' => 4,
                    'is_default' => false,
                    'is_active' => true,
                ]
            );

            // Upper Day 1
            $upper1 = WorkoutSequenceDay::firstOrCreate(
                [
                    'sequence_id' => $sequence2->id,
                    'day_number' => 1,
                ],
                [
                    'label' => 'Upper Body Push',
                    'icon' => '💪',
                    'color' => '#3b82f6',
                    'bg' => '#eff6ff',
                    'border' => '#bfdbfe',
                    'muscle_groups' => ['Chest', 'Shoulders', 'Triceps'],
                ]
            );

            $upperPush = [
                'Bench Press', 'Incline Dumbbell Press', 'Overhead Press', 'Lateral Raises', 'Skull Crushers'
            ];

            $order = 1;
            foreach ($upperPush as $exerciseName) {
                $activity = $activities->where('name', $exerciseName)->first();
                if ($activity) {
                    WorkoutSequenceExercise::firstOrCreate(
                        [
                            'day_id' => $upper1->id,
                            'activity_id' => $activity->id,
                        ],
                        [
                            'name' => $activity->name,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }

            // Lower Day 1
            $lower1 = WorkoutSequenceDay::firstOrCreate(
                [
                    'sequence_id' => $sequence2->id,
                    'day_number' => 2,
                ],
                [
                    'label' => 'Lower Body',
                    'icon' => '🦵',
                    'color' => '#10b981',
                    'bg' => '#ecfdf5',
                    'border' => '#a7f3d0',
                    'muscle_groups' => ['Quads', 'Hamstrings', 'Calves'],
                ]
            );

            $lowerExercises = [
                'Barbell Squat', 'Romanian Deadlift', 'Leg Press', 'Calf Raises'
            ];

            $order = 1;
            foreach ($lowerExercises as $exerciseName) {
                $activity = $activities->where('name', $exerciseName)->first();
                if ($activity) {
                    WorkoutSequenceExercise::firstOrCreate(
                        [
                            'day_id' => $lower1->id,
                            'activity_id' => $activity->id,
                        ],
                        [
                            'name' => $activity->name,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }

            // Upper Day 2
            $upper2 = WorkoutSequenceDay::firstOrCreate(
                [
                    'sequence_id' => $sequence2->id,
                    'day_number' => 3,
                ],
                [
                    'label' => 'Upper Body Pull',
                    'icon' => '🏋️',
                    'color' => '#8b5cf6',
                    'bg' => '#f3e8ff',
                    'border' => '#d8b4fe',
                    'muscle_groups' => ['Back', 'Biceps'],
                ]
            );

            $upperPull = [
                'Bent-over Barbell Row', 'Lat Pulldown', 'Seated Cable Row', 'Bicep Curls', 'Hammer Curls'
            ];

            $order = 1;
            foreach ($upperPull as $exerciseName) {
                $activity = $activities->where('name', $exerciseName)->first();
                if ($activity) {
                    WorkoutSequenceExercise::firstOrCreate(
                        [
                            'day_id' => $upper2->id,
                            'activity_id' => $activity->id,
                        ],
                        [
                            'name' => $activity->name,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }

            // Lower Day 2
            $lower2 = WorkoutSequenceDay::firstOrCreate(
                [
                    'sequence_id' => $sequence2->id,
                    'day_number' => 4,
                ],
                [
                    'label' => 'Lower Body & Core',
                    'icon' => '🏃',
                    'color' => '#f59e0b',
                    'bg' => '#fffbeb',
                    'border' => '#fde68a',
                    'muscle_groups' => ['Quads', 'Hamstrings', 'Calves', 'Core'],
                ]
            );

            $lowerCore = [
                'Barbell Squat', 'Lunges', 'Calf Raises', 'Plank', 'Hanging Leg Raises'
            ];

            $order = 1;
            foreach ($lowerCore as $exerciseName) {
                $activity = $activities->where('name', $exerciseName)->first();
                if ($activity) {
                    WorkoutSequenceExercise::firstOrCreate(
                        [
                            'day_id' => $lower2->id,
                            'activity_id' => $activity->id,
                        ],
                        [
                            'name' => $activity->name,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }
        }
    }
}
