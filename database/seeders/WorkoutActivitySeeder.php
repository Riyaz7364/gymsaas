<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\WorkoutCategory;
use App\Models\WorkoutActivity;
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
    }
}
