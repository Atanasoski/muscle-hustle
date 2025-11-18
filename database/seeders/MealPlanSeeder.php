<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MealPlan;
use App\Models\Meal;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoUser = User::where('email', 'atanasoski992@gmail.com')->first();

        if (!$demoUser) {
            $this->command->error('User not found. Run UserSeeder first.');
            return;
        }

        // Get next Monday as week start
        $weekStart = Carbon::now()->startOfWeek();

        // Create or find meal plan
        $mealPlan = MealPlan::firstOrCreate(
            [
                'user_id' => $demoUser->id,
                'week_start_date' => $weekStart->toDateString(),
            ]
        );

        // Clear existing meals
        $mealPlan->meals()->delete();

        // Define 7 days of meals (4 meals per day)
        $mealsData = [
            // Monday (0)
            [
                ['day' => 0, 'type' => 'breakfast', 'name' => 'Oatmeal with Whey & Banana', 'calories' => 450, 'protein' => 35, 'carbs' => 65, 'fat' => 8],
                ['day' => 0, 'type' => 'lunch', 'name' => 'Grilled Chicken, Rice & Vegetables', 'calories' => 550, 'protein' => 45, 'carbs' => 60, 'fat' => 12],
                ['day' => 0, 'type' => 'dinner', 'name' => 'Salmon, Sweet Potato & Broccoli', 'calories' => 600, 'protein' => 42, 'carbs' => 50, 'fat' => 20],
                ['day' => 0, 'type' => 'snack', 'name' => 'Protein Shake with Peanut Butter', 'calories' => 320, 'protein' => 30, 'carbs' => 25, 'fat' => 12],
            ],
            // Tuesday (1)
            [
                ['day' => 1, 'type' => 'breakfast', 'name' => 'Greek Yogurt with Berries & Granola', 'calories' => 380, 'protein' => 30, 'carbs' => 45, 'fat' => 10],
                ['day' => 1, 'type' => 'lunch', 'name' => 'Turkey Wrap with Mixed Greens', 'calories' => 480, 'protein' => 38, 'carbs' => 42, 'fat' => 15],
                ['day' => 1, 'type' => 'dinner', 'name' => 'Lean Beef Mince, Rice & Vegetables', 'calories' => 620, 'protein' => 48, 'carbs' => 55, 'fat' => 18],
                ['day' => 1, 'type' => 'snack', 'name' => 'Cottage Cheese with Apple', 'calories' => 220, 'protein' => 20, 'carbs' => 25, 'fat' => 4],
            ],
            // Wednesday (2)
            [
                ['day' => 2, 'type' => 'breakfast', 'name' => 'Scrambled Eggs, Toast & Avocado', 'calories' => 420, 'protein' => 25, 'carbs' => 35, 'fat' => 22],
                ['day' => 2, 'type' => 'lunch', 'name' => 'Tuna Salad with Quinoa', 'calories' => 500, 'protein' => 40, 'carbs' => 48, 'fat' => 14],
                ['day' => 2, 'type' => 'dinner', 'name' => 'Chicken Stir-Fry with Noodles', 'calories' => 580, 'protein' => 42, 'carbs' => 62, 'fat' => 16],
                ['day' => 2, 'type' => 'snack', 'name' => 'Mixed Nuts & Banana', 'calories' => 280, 'protein' => 8, 'carbs' => 32, 'fat' => 16],
            ],
            // Thursday (3)
            [
                ['day' => 3, 'type' => 'breakfast', 'name' => 'Protein Pancakes with Syrup', 'calories' => 440, 'protein' => 32, 'carbs' => 52, 'fat' => 10],
                ['day' => 3, 'type' => 'lunch', 'name' => 'Grilled Chicken Caesar Salad', 'calories' => 520, 'protein' => 44, 'carbs' => 28, 'fat' => 24],
                ['day' => 3, 'type' => 'dinner', 'name' => 'Baked Cod, Potatoes & Green Beans', 'calories' => 560, 'protein' => 46, 'carbs' => 58, 'fat' => 12],
                ['day' => 3, 'type' => 'snack', 'name' => 'Rice Cakes with Peanut Butter', 'calories' => 240, 'protein' => 10, 'carbs' => 28, 'fat' => 12],
            ],
            // Friday (4)
            [
                ['day' => 4, 'type' => 'breakfast', 'name' => 'Omelette with Vegetables & Toast', 'calories' => 400, 'protein' => 28, 'carbs' => 35, 'fat' => 18],
                ['day' => 4, 'type' => 'lunch', 'name' => 'Chicken Burrito Bowl', 'calories' => 580, 'protein' => 42, 'carbs' => 65, 'fat' => 16],
                ['day' => 4, 'type' => 'dinner', 'name' => 'Turkey Meatballs with Pasta & Sauce', 'calories' => 640, 'protein' => 48, 'carbs' => 68, 'fat' => 18],
                ['day' => 4, 'type' => 'snack', 'name' => 'Protein Bar', 'calories' => 220, 'protein' => 20, 'carbs' => 22, 'fat' => 8],
            ],
            // Saturday (5)
            [
                ['day' => 5, 'type' => 'breakfast', 'name' => 'French Toast with Protein & Berries', 'calories' => 460, 'protein' => 30, 'carbs' => 58, 'fat' => 12],
                ['day' => 5, 'type' => 'lunch', 'name' => 'Beef & Black Bean Tacos', 'calories' => 560, 'protein' => 38, 'carbs' => 55, 'fat' => 20],
                ['day' => 5, 'type' => 'dinner', 'name' => 'Pork Tenderloin, Rice & Asparagus', 'calories' => 590, 'protein' => 45, 'carbs' => 52, 'fat' => 18],
                ['day' => 5, 'type' => 'snack', 'name' => 'Greek Yogurt with Honey', 'calories' => 200, 'protein' => 18, 'carbs' => 28, 'fat' => 3],
            ],
            // Sunday (6)
            [
                ['day' => 6, 'type' => 'breakfast', 'name' => 'Smoothie Bowl with Protein & Granola', 'calories' => 420, 'protein' => 28, 'carbs' => 52, 'fat' => 12],
                ['day' => 6, 'type' => 'lunch', 'name' => 'Pulled Chicken Sandwich with Sweet Potato', 'calories' => 540, 'protein' => 40, 'carbs' => 62, 'fat' => 14],
                ['day' => 6, 'type' => 'dinner', 'name' => 'Shrimp Fried Rice with Vegetables', 'calories' => 580, 'protein' => 38, 'carbs' => 70, 'fat' => 16],
                ['day' => 6, 'type' => 'snack', 'name' => 'Apple with Almond Butter', 'calories' => 240, 'protein' => 8, 'carbs' => 28, 'fat' => 14],
            ],
        ];

        // Insert all meals
        foreach ($mealsData as $dayMeals) {
            foreach ($dayMeals as $mealData) {
                Meal::create([
                    'meal_plan_id' => $mealPlan->id,
                    'day_of_week' => $mealData['day'],
                    'type' => $mealData['type'],
                    'name' => $mealData['name'],
                    'calories' => $mealData['calories'],
                    'protein' => $mealData['protein'],
                    'carbs' => $mealData['carbs'],
                    'fat' => $mealData['fat'],
                ]);
            }
        }

        $this->command->info("Meal plan seeded for week starting {$weekStart->toDateString()}");
        $this->command->info('Total meals created: ' . (7 * 4) . ' (7 days × 4 meals)');
    }
}

