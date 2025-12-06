<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * NOTE: All nutrition values are stored PER 100 GRAMS
     * This is the standard used on food labels
     */
    public function run(): void
    {
        // Get food categories
        $proteinCategoryId = Category::food()->where('slug', 'protein')->value('id');
        $vegetablesCategoryId = Category::food()->where('slug', 'vegetables')->value('id');
        $fruitsCategoryId = Category::food()->where('slug', 'fruits')->value('id');
        $grainsCategoryId = Category::food()->where('slug', 'grains')->value('id');
        $fatsCategoryId = Category::food()->where('slug', 'fats-oils')->value('id');

        $foods = [
            // PROTEINS - MEAT & POULTRY
            ['name' => 'Chicken Breast', 'category_id' => $proteinCategoryId, 'calories' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.60, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 150],
            ['name' => 'Chicken Thigh', 'category_id' => $proteinCategoryId, 'calories' => 209, 'protein' => 26, 'carbs' => 0, 'fat' => 10.90, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 150],
            ['name' => 'Ground Beef (90% lean)', 'category_id' => $proteinCategoryId, 'calories' => 176, 'protein' => 20, 'carbs' => 0, 'fat' => 10, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Ground Turkey', 'category_id' => $proteinCategoryId, 'calories' => 150, 'protein' => 20, 'carbs' => 0, 'fat' => 8, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Lean Steak', 'category_id' => $proteinCategoryId, 'calories' => 250, 'protein' => 26, 'carbs' => 0, 'fat' => 15, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 150],

            // PROTEINS - FISH & SEAFOOD
            ['name' => 'Salmon', 'category_id' => $proteinCategoryId, 'calories' => 208, 'protein' => 20, 'carbs' => 0, 'fat' => 13, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 150],
            ['name' => 'Tuna (canned in water)', 'category_id' => $proteinCategoryId, 'calories' => 116.00, 'protein' => 26, 'carbs' => 0, 'fat' => 0.80, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Tilapia', 'category_id' => $proteinCategoryId, 'calories' => 96, 'protein' => 20, 'carbs' => 0, 'fat' => 1.70, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 150],
            ['name' => 'Shrimp', 'category_id' => $proteinCategoryId, 'calories' => 99, 'protein' => 24, 'carbs' => 0.20, 'fat' => 0.30, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'g', 'default_serving_size' => 100],

            // PROTEINS - EGGS & DAIRY
            ['name' => 'Whole Eggs', 'category_id' => $proteinCategoryId, 'calories' => 155, 'protein' => 13, 'carbs' => 1.10, 'fat' => 11, 'fiber' => 0, 'sugar' => 1.10, 'default_serving_unit' => 'piece', 'default_serving_size' => 2],
            ['name' => 'Egg Whites', 'category_id' => $proteinCategoryId, 'calories' => 52, 'protein' => 11, 'carbs' => 0.70, 'fat' => 0.20, 'fiber' => 0, 'sugar' => 0.70, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Greek Yogurt (non-fat)', 'category_id' => $proteinCategoryId, 'calories' => 59, 'protein' => 10, 'carbs' => 3.60, 'fat' => 0.40, 'fiber' => 0, 'sugar' => 3.20, 'default_serving_unit' => 'g', 'default_serving_size' => 170],
            ['name' => 'Cottage Cheese (low-fat)', 'category_id' => $proteinCategoryId, 'calories' => 72, 'protein' => 12, 'carbs' => 4.30, 'fat' => 1, 'fiber' => 0, 'sugar' => 4.10, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Whey Protein Powder', 'category_id' => $proteinCategoryId, 'calories' => 380, 'protein' => 80, 'carbs' => 10, 'fat' => 5, 'fiber' => 2, 'sugar' => 6, 'default_serving_unit' => 'scoop', 'default_serving_size' => 1],

            // GRAINS - COOKED
            ['name' => 'Brown Rice (cooked)', 'category_id' => $grainsCategoryId, 'calories' => 112.00, 'protein' => 2.60, 'carbs' => 24, 'fat' => 0.90, 'fiber' => 1.80, 'sugar' => 0.40, 'default_serving_unit' => 'cup', 'default_serving_size' => 1],
            ['name' => 'White Rice (cooked)', 'category_id' => $grainsCategoryId, 'calories' => 130, 'protein' => 2.70, 'carbs' => 28.00, 'fat' => 0.30, 'fiber' => 0.40, 'sugar' => 0.10, 'default_serving_unit' => 'cup', 'default_serving_size' => 1],
            ['name' => 'Oatmeal (dry)', 'category_id' => $grainsCategoryId, 'calories' => 389, 'protein' => 17, 'carbs' => 66, 'fat' => 7.00, 'fiber' => 11, 'sugar' => 1, 'default_serving_unit' => 'g', 'default_serving_size' => 40],
            ['name' => 'Quinoa (cooked)', 'category_id' => $grainsCategoryId, 'calories' => 120, 'protein' => 4.40, 'carbs' => 21, 'fat' => 1.90, 'fiber' => 2.80, 'sugar' => 0.90, 'default_serving_unit' => 'cup', 'default_serving_size' => 1],
            ['name' => 'Whole Wheat Pasta (cooked)', 'category_id' => $grainsCategoryId, 'calories' => 124, 'protein' => 5.30, 'carbs' => 26, 'fat' => 0.50, 'fiber' => 3.20, 'sugar' => 0.60, 'default_serving_unit' => 'g', 'default_serving_size' => 100],

            // GRAINS - BREAD & STARCHY VEGETABLES
            ['name' => 'Whole Wheat Bread', 'category_id' => $grainsCategoryId, 'calories' => 247.00, 'protein' => 13, 'carbs' => 41, 'fat' => 3.40, 'fiber' => 7.00, 'sugar' => 6, 'default_serving_unit' => 'slice', 'default_serving_size' => 2],
            ['name' => 'Sweet Potato', 'category_id' => $grainsCategoryId, 'calories' => 86, 'protein' => 1.60, 'carbs' => 20, 'fat' => 0.10, 'fiber' => 3, 'sugar' => 4.20, 'default_serving_unit' => 'g', 'default_serving_size' => 150],
            ['name' => 'White Potato', 'category_id' => $grainsCategoryId, 'calories' => 77, 'protein' => 2, 'carbs' => 17, 'fat' => 0.10, 'fiber' => 2.20, 'sugar' => 0.80, 'default_serving_unit' => 'g', 'default_serving_size' => 150],

            // VEGETABLES
            ['name' => 'Broccoli', 'category_id' => $vegetablesCategoryId, 'calories' => 34, 'protein' => 2.80, 'carbs' => 7.00, 'fat' => 0.40, 'fiber' => 2.60, 'sugar' => 1.70, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Spinach', 'category_id' => $vegetablesCategoryId, 'calories' => 23, 'protein' => 2.90, 'carbs' => 3.60, 'fat' => 0.40, 'fiber' => 2.20, 'sugar' => 0.40, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Asparagus', 'category_id' => $vegetablesCategoryId, 'calories' => 20, 'protein' => 2.20, 'carbs' => 3.90, 'fat' => 0.10, 'fiber' => 2.10, 'sugar' => 1.90, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Bell Pepper', 'category_id' => $vegetablesCategoryId, 'calories' => 31, 'protein' => 1, 'carbs' => 6, 'fat' => 0.30, 'fiber' => 2.10, 'sugar' => 4.20, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Green Beans', 'category_id' => $vegetablesCategoryId, 'calories' => 31, 'protein' => 1.80, 'carbs' => 7.00, 'fat' => 0.20, 'fiber' => 2.70, 'sugar' => 3.30, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Cauliflower', 'category_id' => $vegetablesCategoryId, 'calories' => 25, 'protein' => 1.90, 'carbs' => 5, 'fat' => 0.30, 'fiber' => 2, 'sugar' => 1.90, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Lettuce (Mixed Greens)', 'category_id' => $vegetablesCategoryId, 'calories' => 15, 'protein' => 1.40, 'carbs' => 2.90, 'fat' => 0.20, 'fiber' => 1.30, 'sugar' => 0.80, 'default_serving_unit' => 'g', 'default_serving_size' => 100],

            // FRUITS
            ['name' => 'Banana', 'category_id' => $fruitsCategoryId, 'calories' => 89, 'protein' => 1.10, 'carbs' => 23, 'fat' => 0.30, 'fiber' => 2.60, 'sugar' => 12, 'default_serving_unit' => 'piece', 'default_serving_size' => 1],
            ['name' => 'Apple', 'category_id' => $fruitsCategoryId, 'calories' => 52, 'protein' => 0.30, 'carbs' => 14.00, 'fat' => 0.20, 'fiber' => 2.40, 'sugar' => 10, 'default_serving_unit' => 'piece', 'default_serving_size' => 1],
            ['name' => 'Blueberries', 'category_id' => $fruitsCategoryId, 'calories' => 57.00, 'protein' => 0.70, 'carbs' => 14.00, 'fat' => 0.30, 'fiber' => 2.40, 'sugar' => 10, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Strawberries', 'category_id' => $fruitsCategoryId, 'calories' => 32, 'protein' => 0.70, 'carbs' => 7.70, 'fat' => 0.30, 'fiber' => 2, 'sugar' => 4.90, 'default_serving_unit' => 'g', 'default_serving_size' => 100],
            ['name' => 'Orange', 'category_id' => $fruitsCategoryId, 'calories' => 47, 'protein' => 0.90, 'carbs' => 12, 'fat' => 0.10, 'fiber' => 2.40, 'sugar' => 9, 'default_serving_unit' => 'piece', 'default_serving_size' => 1],

            // FATS & OILS
            ['name' => 'Olive Oil', 'category_id' => $fatsCategoryId, 'calories' => 884, 'protein' => 0, 'carbs' => 0, 'fat' => 100, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'tbsp', 'default_serving_size' => 1],
            ['name' => 'Coconut Oil', 'category_id' => $fatsCategoryId, 'calories' => 862.00, 'protein' => 0, 'carbs' => 0, 'fat' => 100, 'fiber' => 0, 'sugar' => 0, 'default_serving_unit' => 'tbsp', 'default_serving_size' => 1],
            ['name' => 'Avocado', 'category_id' => $fatsCategoryId, 'calories' => 160, 'protein' => 2, 'carbs' => 9, 'fat' => 15, 'fiber' => 7.00, 'sugar' => 0.70, 'default_serving_unit' => 'piece', 'default_serving_size' => 1],
            ['name' => 'Almonds', 'category_id' => $fatsCategoryId, 'calories' => 579, 'protein' => 21, 'carbs' => 22, 'fat' => 50, 'fiber' => 12, 'sugar' => 4.40, 'default_serving_unit' => 'g', 'default_serving_size' => 28],
            ['name' => 'Peanut Butter', 'category_id' => $fatsCategoryId, 'calories' => 588, 'protein' => 25, 'carbs' => 20, 'fat' => 50, 'fiber' => 8, 'sugar' => 9, 'default_serving_unit' => 'tbsp', 'default_serving_size' => 2],
            ['name' => 'Walnuts', 'category_id' => $fatsCategoryId, 'calories' => 654, 'protein' => 15, 'carbs' => 14.00, 'fat' => 65, 'fiber' => 7.00, 'sugar' => 2.60, 'default_serving_unit' => 'g', 'default_serving_size' => 28],
        ];

        // Insert all foods as global (user_id = null)
        foreach ($foods as $food) {
            Food::create(array_merge($food, ['user_id' => null]));
        }

        $this->command->info('Foods seeded successfully!');
    }
}
