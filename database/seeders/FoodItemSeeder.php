<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use App\Models\FoodItem;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    public function run(): void
    {
        $gym = \App\Models\Gym::first();
        if (!$gym) return;

        // Meat & Poultry
        $meatPoultry = FoodCategory::where('gym_id', $gym->id)->where('name', 'Meat & Poultry')->first();
        if ($meatPoultry) {
            $this->createFoodItems($gym->id, $meatPoultry->id, [
                ['Chicken Breast', '100', 'g', 165, 31, 0, 3.6, 0],
                ['Ground Chicken', '100', 'g', 165, 28.5, 0, 5.8, 0],
                ['Chicken Thigh', '100', 'g', 209, 26.2, 0, 11.1, 0],
                ['Beef Steak', '100', 'g', 271, 26.5, 0, 17.9, 0],
                ['Ground Beef (90/10)', '100', 'g', 173, 24.4, 0, 7.6, 0],
                ['Pork Chop', '100', 'g', 242, 27, 0, 14, 0],
                ['Turkey Breast', '100', 'g', 135, 29.9, 0, 0.7, 0],
                ['Lamb', '100', 'g', 294, 25.2, 0, 21.1, 0],
            ]);
        }

        // Fish & Seafood
        $seafood = FoodCategory::where('gym_id', $gym->id)->where('name', 'Fish & Seafood')->first();
        if ($seafood) {
            $this->createFoodItems($gym->id, $seafood->id, [
                ['Salmon', '100', 'g', 206, 22, 0, 13, 0],
                ['Tuna (canned in water)', '100', 'g', 132, 29.8, 0, 0.4, 0],
                ['White Fish (Cod)', '100', 'g', 82, 17.8, 0, 0.7, 0],
                ['Shrimp', '100', 'g', 99, 24, 0.2, 0.3, 0],
                ['Mackerel', '100', 'g', 305, 25, 0, 23.5, 0],
                ['Sardines', '100', 'g', 208, 25.4, 0, 11.5, 0],
            ]);
        }

        // Dairy & Eggs
        $dairy = FoodCategory::where('gym_id', $gym->id)->where('name', 'Dairy & Eggs')->first();
        if ($dairy) {
            $this->createFoodItems($gym->id, $dairy->id, [
                ['Egg (whole, large)', '1', 'piece', 70, 6, 0.4, 5, 0],
                ['Egg White', '1', 'piece', 17, 3.6, 0.2, 0, 0],
                ['Milk (whole)', '100', 'ml', 64, 3.3, 4.8, 3.6, 0],
                ['Milk (skim)', '100', 'ml', 35, 3.6, 4.7, 0.1, 0],
                ['Greek Yogurt (plain)', '100', 'g', 59, 10, 3.3, 0.4, 0],
                ['Cheddar Cheese', '30', 'g', 121, 7, 0.4, 10, 0],
                ['Cottage Cheese', '100', 'g', 98, 11, 3.9, 5, 0],
                ['Mozzarella', '30', 'g', 85, 6.3, 1, 6.3, 0],
            ]);
        }

        // Grains & Cereals
        $grains = FoodCategory::where('gym_id', $gym->id)->where('name', 'Grains & Cereals')->first();
        if ($grains) {
            $this->createFoodItems($gym->id, $grains->id, [
                ['White Rice (cooked)', '100', 'g', 130, 2.7, 28, 0.3, 0.4],
                ['Brown Rice (cooked)', '100', 'g', 111, 2.6, 23, 0.9, 1.8],
                ['Oats (dry)', '40', 'g', 150, 5, 27, 3, 4],
                ['Whole Wheat Bread', '30', 'g', 80, 4, 14, 1, 2.4],
                ['White Bread', '30', 'g', 79, 2.4, 14.8, 0.4, 0.8],
                ['Pasta (cooked)', '100', 'g', 131, 4.4, 25.2, 1.1, 1.8],
                ['Quinoa (cooked)', '100', 'g', 120, 4.4, 21.3, 1.9, 2.8],
                ['Sweet Potato (cooked)', '100', 'g', 86, 1.6, 20.1, 0.1, 3],
            ]);
        }

        // Vegetables
        $vegetables = FoodCategory::where('gym_id', $gym->id)->where('name', 'Vegetables')->first();
        if ($vegetables) {
            $this->createFoodItems($gym->id, $vegetables->id, [
                ['Broccoli (cooked)', '100', 'g', 34, 2.8, 7, 0.4, 2.4],
                ['Spinach (raw)', '100', 'g', 23, 2.7, 3.6, 0.4, 2.2],
                ['Carrots (raw)', '100', 'g', 41, 0.9, 10, 0.2, 2.8],
                ['Bell Pepper (raw)', '100', 'g', 31, 1, 7.3, 0.3, 2.2],
                ['Tomato (raw)', '100', 'g', 18, 0.9, 3.9, 0.2, 1.2],
                ['Lettuce (raw)', '100', 'g', 15, 1.4, 3, 0.2, 1.3],
                ['Cucumber (raw)', '100', 'g', 16, 0.7, 3.6, 0.1, 0.5],
                ['Onion (raw)', '100', 'g', 40, 1.1, 9, 0.1, 1.7],
            ]);
        }

        // Fruits
        $fruits = FoodCategory::where('gym_id', $gym->id)->where('name', 'Fruits')->first();
        if ($fruits) {
            $this->createFoodItems($gym->id, $fruits->id, [
                ['Apple (medium)', '1', 'piece', 95, 0.5, 25, 0.3, 4.4],
                ['Banana (medium)', '1', 'piece', 105, 1.3, 27, 0.3, 3.1],
                ['Orange (medium)', '1', 'piece', 62, 1.2, 15, 0.3, 3],
                ['Berries (mixed, raw)', '100', 'g', 57, 0.7, 14, 0.3, 2.4],
                ['Watermelon', '100', 'g', 30, 0.6, 7.6, 0.1, 0.4],
                ['Grapes', '100', 'g', 67, 0.6, 17, 0.2, 0.9],
                ['Mango', '100', 'g', 60, 0.8, 15, 0.4, 1.6],
                ['Pineapple', '100', 'g', 50, 0.5, 13, 0.1, 1.4],
            ]);
        }

        // Legumes & Nuts
        $legumes = FoodCategory::where('gym_id', $gym->id)->where('name', 'Legumes & Nuts')->first();
        if ($legumes) {
            $this->createFoodItems($gym->id, $legumes->id, [
                ['Almonds', '30', 'g', 164, 6, 6, 14, 3.5],
                ['Peanut Butter', '2', 'tbsp', 188, 8, 7, 16, 2.5],
                ['Lentils (cooked)', '100', 'g', 116, 9, 20, 0.4, 7.9],
                ['Black Beans (cooked)', '100', 'g', 132, 8.9, 24, 0.5, 6.4],
                ['Chickpeas (cooked)', '100', 'g', 134, 8.9, 23, 2.1, 6.5],
                ['Walnuts', '30', 'g', 185, 4.3, 3.9, 18.5, 1.9],
                ['Sunflower Seeds', '30', 'g', 165, 5.5, 6.5, 14, 2.4],
                ['Tofu (firm)', '100', 'g', 76, 8, 1.9, 4.8, 1.2],
            ]);
        }

        // Beverages
        $beverages = FoodCategory::where('gym_id', $gym->id)->where('name', 'Beverages')->first();
        if ($beverages) {
            $this->createFoodItems($gym->id, $beverages->id, [
                ['Water', '100', 'ml', 0, 0, 0, 0, 0],
                ['Green Tea (brewed)', '250', 'ml', 2, 0.3, 0.5, 0, 0],
                ['Black Tea (brewed)', '250', 'ml', 2, 0.3, 0.5, 0, 0],
                ['Coffee (black)', '250', 'ml', 2, 0.3, 0, 0, 0],
                ['Orange Juice (fresh)', '100', 'ml', 45, 0.7, 11, 0.2, 0.2],
                ['Milk (whole)', '200', 'ml', 128, 6.6, 9.6, 7.2, 0],
                ['Protein Shake', '1', 'cup', 180, 25, 6, 2.5, 1],
            ]);
        }

        // Condiments & Oils
        $condiments = FoodCategory::where('gym_id', $gym->id)->where('name', 'Condiments & Oils')->first();
        if ($condiments) {
            $this->createFoodItems($gym->id, $condiments->id, [
                ['Olive Oil', '1', 'tbsp', 119, 0, 0, 13.5, 0],
                ['Coconut Oil', '1', 'tbsp', 117, 0, 0, 13.5, 0],
                ['Salt', '1', 'tsp', 0, 0, 0, 0, 0],
                ['Honey', '1', 'tbsp', 64, 0.1, 17, 0, 0],
                ['Ketchup', '1', 'tbsp', 16, 0.4, 4, 0, 0],
                ['Mayonnaise', '1', 'tbsp', 94, 0.2, 0.1, 10.4, 0],
                ['Vinegar (white)', '1', 'tbsp', 3, 0, 0.1, 0, 0],
                ['Soy Sauce', '1', 'tbsp', 8, 1.3, 1.5, 0, 0],
            ]);
        }

        // Snacks
        $snacks = FoodCategory::where('gym_id', $gym->id)->where('name', 'Snacks')->first();
        if ($snacks) {
            $this->createFoodItems($gym->id, $snacks->id, [
                ['Rice Cakes', '10', 'g', 35, 0.8, 7.3, 0.3, 0.4],
                ['Popcorn (air-popped)', '30', 'g', 96, 3.6, 19.2, 1.1, 3.5],
                ['Dark Chocolate (70%)', '30', 'g', 172, 2, 13, 12, 3],
                ['Granola Bar', '30', 'g', 120, 3, 18, 5, 2],
                ['Crackers (whole wheat)', '30', 'g', 120, 3, 20, 2, 3],
                ['Protein Bar', '50', 'g', 200, 20, 12, 8, 5],
                ['Beef Jerky', '30', 'g', 82, 17, 2, 0.5, 0],
            ]);
        }
    }

    private function createFoodItems($gymId, $categoryId, $foods)
    {
        foreach ($foods as $food) {
            FoodItem::firstOrCreate(
                ['gym_id' => $gymId, 'name' => $food[0]],
                [
                    'category_id' => $categoryId,
                    'serving_size' => $food[1],
                    'serving_unit' => $food[2],
                    'calories' => $food[3],
                    'protein_g' => $food[4],
                    'carbs_g' => $food[5],
                    'fat_g' => $food[6],
                    'fiber_g' => $food[7] ?? 0,
                ]
            );
        }
    }
}
