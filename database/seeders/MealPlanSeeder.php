<?php

namespace Database\Seeders;

use App\Models\Meal;
use App\Models\MealPlan;
use App\Models\User;
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

        if (! $demoUser) {
            $this->command->error('User not found. Run UserSeeder first.');

            return;
        }

        // Get some foods to attach to meals
        $foods = \App\Models\Food::all();
        if ($foods->isEmpty()) {
            $this->command->error('No foods found. Run FoodSeeder first.');

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
                ['day' => 0, 'type' => 'breakfast', 'name' => 'Oatmeal with Whey & Banana', 'serving_size' => '80g oats, 30g whey protein, 1 medium banana (120g)', 'calories' => 450, 'protein' => 35, 'carbs' => 65, 'fat' => 8],
                ['day' => 0, 'type' => 'lunch', 'name' => 'Grilled Chicken, Rice & Vegetables', 'serving_size' => '200g chicken breast, 150g white rice (cooked), 150g mixed vegetables', 'calories' => 550, 'protein' => 45, 'carbs' => 60, 'fat' => 12],
                ['day' => 0, 'type' => 'dinner', 'name' => 'Salmon, Sweet Potato & Broccoli', 'serving_size' => '180g salmon fillet, 200g sweet potato, 150g broccoli', 'calories' => 600, 'protein' => 42, 'carbs' => 50, 'fat' => 20],
                ['day' => 0, 'type' => 'snack', 'name' => 'Protein Shake with Peanut Butter', 'serving_size' => '30g whey protein, 300ml milk, 20g peanut butter', 'calories' => 320, 'protein' => 30, 'carbs' => 25, 'fat' => 12],
            ],
            // Tuesday (1)
            [
                ['day' => 1, 'type' => 'breakfast', 'name' => 'Greek Yogurt with Berries & Granola', 'serving_size' => '300g Greek yogurt (0% fat), 100g mixed berries, 40g granola', 'calories' => 380, 'protein' => 30, 'carbs' => 45, 'fat' => 10],
                ['day' => 1, 'type' => 'lunch', 'name' => 'Turkey Wrap with Mixed Greens', 'serving_size' => '150g turkey breast, 1 large whole wheat tortilla, 100g mixed greens, 20g hummus', 'calories' => 480, 'protein' => 38, 'carbs' => 42, 'fat' => 15],
                ['day' => 1, 'type' => 'dinner', 'name' => 'Lean Beef Mince, Rice & Vegetables', 'serving_size' => '180g lean beef mince (5% fat), 140g basmati rice (cooked), 200g mixed vegetables', 'calories' => 620, 'protein' => 48, 'carbs' => 55, 'fat' => 18],
                ['day' => 1, 'type' => 'snack', 'name' => 'Cottage Cheese with Apple', 'serving_size' => '200g cottage cheese (low fat), 1 medium apple (180g)', 'calories' => 220, 'protein' => 20, 'carbs' => 25, 'fat' => 4],
            ],
            // Wednesday (2)
            [
                ['day' => 2, 'type' => 'breakfast', 'name' => 'Scrambled Eggs, Toast & Avocado', 'serving_size' => '3 whole eggs, 2 slices whole grain bread, 1/2 avocado (50g)', 'calories' => 420, 'protein' => 25, 'carbs' => 35, 'fat' => 22],
                ['day' => 2, 'type' => 'lunch', 'name' => 'Tuna Salad with Quinoa', 'serving_size' => '150g tuna (in water), 120g quinoa (cooked), 150g mixed salad, 10ml olive oil', 'calories' => 500, 'protein' => 40, 'carbs' => 48, 'fat' => 14],
                ['day' => 2, 'type' => 'dinner', 'name' => 'Chicken Stir-Fry with Noodles', 'serving_size' => '180g chicken breast, 150g rice noodles, 200g stir-fry vegetables, 15ml sesame oil', 'calories' => 580, 'protein' => 42, 'carbs' => 62, 'fat' => 16],
                ['day' => 2, 'type' => 'snack', 'name' => 'Mixed Nuts & Banana', 'serving_size' => '30g mixed nuts (almonds, cashews, walnuts), 1 medium banana (120g)', 'calories' => 280, 'protein' => 8, 'carbs' => 32, 'fat' => 16],
            ],
            // Thursday (3)
            [
                ['day' => 3, 'type' => 'breakfast', 'name' => 'Protein Pancakes with Syrup', 'serving_size' => '3 pancakes (100g total, made with 30g protein powder), 30ml maple syrup, 100g berries', 'calories' => 440, 'protein' => 32, 'carbs' => 52, 'fat' => 10],
                ['day' => 3, 'type' => 'lunch', 'name' => 'Grilled Chicken Caesar Salad', 'serving_size' => '200g chicken breast, 150g romaine lettuce, 30g parmesan, 40ml Caesar dressing', 'calories' => 520, 'protein' => 44, 'carbs' => 28, 'fat' => 24],
                ['day' => 3, 'type' => 'dinner', 'name' => 'Baked Cod, Potatoes & Green Beans', 'serving_size' => '200g cod fillet, 250g baby potatoes, 150g green beans, 10ml olive oil', 'calories' => 560, 'protein' => 46, 'carbs' => 58, 'fat' => 12],
                ['day' => 3, 'type' => 'snack', 'name' => 'Rice Cakes with Peanut Butter', 'serving_size' => '3 rice cakes, 25g peanut butter', 'calories' => 240, 'protein' => 10, 'carbs' => 28, 'fat' => 12],
            ],
            // Friday (4)
            [
                ['day' => 4, 'type' => 'breakfast', 'name' => 'Omelette with Vegetables & Toast', 'serving_size' => '3 eggs, 100g mushrooms, 50g spinach, 50g tomatoes, 2 slices whole grain bread', 'calories' => 400, 'protein' => 28, 'carbs' => 35, 'fat' => 18],
                ['day' => 4, 'type' => 'lunch', 'name' => 'Chicken Burrito Bowl', 'serving_size' => '180g chicken breast, 150g brown rice, 80g black beans, 50g salsa, 30g cheese', 'calories' => 580, 'protein' => 42, 'carbs' => 65, 'fat' => 16],
                ['day' => 4, 'type' => 'dinner', 'name' => 'Turkey Meatballs with Pasta & Sauce', 'serving_size' => '200g turkey meatballs, 120g whole wheat pasta, 150g marinara sauce', 'calories' => 640, 'protein' => 48, 'carbs' => 68, 'fat' => 18],
                ['day' => 4, 'type' => 'snack', 'name' => 'Protein Bar', 'serving_size' => '1 protein bar (60g)', 'calories' => 220, 'protein' => 20, 'carbs' => 22, 'fat' => 8],
            ],
            // Saturday (5)
            [
                ['day' => 5, 'type' => 'breakfast', 'name' => 'French Toast with Protein & Berries', 'serving_size' => '3 slices bread, 2 eggs, 15g protein powder, 100g mixed berries, 20ml maple syrup', 'calories' => 460, 'protein' => 30, 'carbs' => 58, 'fat' => 12],
                ['day' => 5, 'type' => 'lunch', 'name' => 'Beef & Black Bean Tacos', 'serving_size' => '150g lean beef, 3 soft tortillas, 80g black beans, 30g cheese, 50g salsa', 'calories' => 560, 'protein' => 38, 'carbs' => 55, 'fat' => 20],
                ['day' => 5, 'type' => 'dinner', 'name' => 'Pork Tenderloin, Rice & Asparagus', 'serving_size' => '180g pork tenderloin, 150g jasmine rice (cooked), 200g asparagus', 'calories' => 590, 'protein' => 45, 'carbs' => 52, 'fat' => 18],
                ['day' => 5, 'type' => 'snack', 'name' => 'Greek Yogurt with Honey', 'serving_size' => '250g Greek yogurt (0% fat), 20g honey', 'calories' => 200, 'protein' => 18, 'carbs' => 28, 'fat' => 3],
            ],
            // Sunday (6)
            [
                ['day' => 6, 'type' => 'breakfast', 'name' => 'Smoothie Bowl with Protein & Granola', 'serving_size' => '200ml almond milk, 1 banana, 100g frozen berries, 30g protein powder, 30g granola', 'calories' => 420, 'protein' => 28, 'carbs' => 52, 'fat' => 12],
                ['day' => 6, 'type' => 'lunch', 'name' => 'Pulled Chicken Sandwich with Sweet Potato', 'serving_size' => '180g pulled chicken, 1 whole grain bun, 200g sweet potato wedges, 20g BBQ sauce', 'calories' => 540, 'protein' => 40, 'carbs' => 62, 'fat' => 14],
                ['day' => 6, 'type' => 'dinner', 'name' => 'Shrimp Fried Rice with Vegetables', 'serving_size' => '200g shrimp, 150g jasmine rice, 150g mixed vegetables, 2 eggs, 15ml soy sauce', 'calories' => 580, 'protein' => 38, 'carbs' => 70, 'fat' => 16],
                ['day' => 6, 'type' => 'snack', 'name' => 'Apple with Almond Butter', 'serving_size' => '1 large apple (200g), 25g almond butter', 'calories' => 240, 'protein' => 8, 'carbs' => 28, 'fat' => 14],
            ],
        ];

        // Insert all meals and attach random foods
        foreach ($mealsData as $dayMeals) {
            foreach ($dayMeals as $mealData) {
                $meal = Meal::create([
                    'meal_plan_id' => $mealPlan->id,
                    'day_of_week' => $mealData['day'],
                    'type' => $mealData['type'],
                    'name' => $mealData['name'],
                    'serving_size' => $mealData['serving_size'],
                    'calories' => $mealData['calories'],
                    'protein' => $mealData['protein'],
                    'carbs' => $mealData['carbs'],
                    'fat' => $mealData['fat'],
                ]);

                // Attach 2-4 random foods to each meal for the grocery list
                $randomFoods = $foods->random(rand(2, 4));
                foreach ($randomFoods as $food) {
                    $meal->foods()->attach($food->id, [
                        'servings' => rand(1, 3),
                        'grams' => rand(50, 200),
                    ]);
                }
            }
        }

        $this->command->info("Meal plan seeded for week starting {$weekStart->toDateString()}");
        $this->command->info('Total meals created: '.(7 * 4).' (7 days × 4 meals)');
        $this->command->info('Foods attached to meals for grocery list');
    }
}
