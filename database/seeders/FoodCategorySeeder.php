<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use Illuminate\Database\Seeder;

class FoodCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Meat & Poultry', 'icon' => '🍗', 'description' => 'Beef, chicken, pork, lamb, and other meat products'],
            ['name' => 'Fish & Seafood', 'icon' => '🐟', 'description' => 'Fish, prawns, crabs, and other seafood'],
            ['name' => 'Dairy & Eggs', 'icon' => '🥛', 'description' => 'Milk, cheese, yogurt, eggs, and dairy products'],
            ['name' => 'Grains & Cereals', 'icon' => '🌾', 'description' => 'Rice, wheat, oats, bread, pasta, and grain products'],
            ['name' => 'Vegetables', 'icon' => '🥦', 'description' => 'Fresh and cooked vegetables'],
            ['name' => 'Fruits', 'icon' => '🍎', 'description' => 'Fresh and dried fruits'],
            ['name' => 'Legumes & Nuts', 'icon' => '🥜', 'description' => 'Beans, lentils, nuts, and seeds'],
            ['name' => 'Beverages', 'icon' => '☕', 'description' => 'Water, juice, tea, coffee, and other drinks'],
            ['name' => 'Snacks', 'icon' => '🍿', 'description' => 'Crackers, chips, cookies, and other snacks'],
            ['name' => 'Condiments & Oils', 'icon' => '🍯', 'description' => 'Oils, sauces, spices, and condiments'],
        ];

        // Get the first gym (assuming seeding for test/default gym)
        $gym = \App\Models\Gym::first();

        if ($gym) {
            foreach ($categories as $category) {
                FoodCategory::firstOrCreate(
                    ['gym_id' => $gym->id, 'name' => $category['name']],
                    ['icon' => $category['icon'], 'description' => $category['description']]
                );
            }
        }
    }
}
