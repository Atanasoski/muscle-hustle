<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'atanasoski992@gmail.com')->first();

        if (! $user) {
            $this->command->error('User not found. Run UserSeeder first.');

            return;
        }

        // Clear existing recipes
        Recipe::where('user_id', $user->id)->delete();

        // Define recipes with their ingredients
        $recipes = [
            // 1. Classic Chicken & Rice
            [
                'name' => 'Grilled Chicken with Brown Rice & Broccoli',
                'description' => 'A classic bodybuilder meal - lean protein, complex carbs, and fiber',
                'instructions' => "1. Season chicken breast with salt, pepper, and garlic powder\n2. Grill chicken for 6-7 minutes per side until internal temp reaches 165°F\n3. Steam broccoli for 5-6 minutes until tender-crisp\n4. Serve chicken over brown rice with broccoli on the side",
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 25,
                'servings' => 1,
                'meal_type' => 'lunch',
                'is_favorite' => true,
                'ingredients' => [
                    ['food' => 'Chicken Breast', 'quantity' => 200, 'unit' => 'g'],
                    ['food' => 'Brown Rice (cooked)', 'quantity' => 150, 'unit' => 'g'],
                    ['food' => 'Broccoli', 'quantity' => 150, 'unit' => 'g'],
                    ['food' => 'Olive Oil', 'quantity' => 10, 'unit' => 'ml'],
                ],
            ],

            // 2. Protein Pancakes
            [
                'name' => 'High-Protein Pancakes with Berries',
                'description' => 'Delicious breakfast packed with protein and complex carbs',
                'instructions' => "1. Mix oats, eggs, banana, and protein powder in a blender\n2. Heat a non-stick pan over medium heat\n3. Pour batter to make 3-4 pancakes\n4. Cook 2-3 minutes per side until golden\n5. Top with Greek yogurt and mixed berries",
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 10,
                'servings' => 1,
                'meal_type' => 'breakfast',
                'is_favorite' => true,
                'ingredients' => [
                    ['food' => 'Oatmeal (dry)', 'quantity' => 60, 'unit' => 'g'],
                    ['food' => 'Whole Eggs', 'quantity' => 2, 'unit' => 'whole'],
                    ['food' => 'Banana', 'quantity' => 1, 'unit' => 'medium'],
                    ['food' => 'Whey Protein Powder', 'quantity' => 30, 'unit' => 'g'],
                    ['food' => 'Greek Yogurt (non-fat)', 'quantity' => 100, 'unit' => 'g'],
                    ['food' => 'Blueberries', 'quantity' => 50, 'unit' => 'g'],
                ],
            ],

            // 3. Salmon Power Bowl
            [
                'name' => 'Baked Salmon with Sweet Potato & Asparagus',
                'description' => 'Omega-3 rich meal with complex carbs and vegetables',
                'instructions' => "1. Preheat oven to 200°C\n2. Season salmon with lemon, dill, salt and pepper\n3. Cut sweet potato into cubes and toss with olive oil\n4. Bake salmon and sweet potato for 20 minutes\n5. Steam asparagus for last 5 minutes\n6. Serve together with lemon wedges",
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 20,
                'servings' => 1,
                'meal_type' => 'dinner',
                'is_favorite' => true,
                'ingredients' => [
                    ['food' => 'Salmon', 'quantity' => 180, 'unit' => 'g'],
                    ['food' => 'Sweet Potato', 'quantity' => 200, 'unit' => 'g'],
                    ['food' => 'Asparagus', 'quantity' => 150, 'unit' => 'g'],
                    ['food' => 'Olive Oil', 'quantity' => 15, 'unit' => 'ml'],
                ],
            ],

            // 4. Greek Yogurt Parfait
            [
                'name' => 'Greek Yogurt Parfait with Granola & Berries',
                'description' => 'Perfect pre or post-workout snack with protein and quick carbs',
                'instructions' => "1. Layer Greek yogurt in a bowl or glass\n2. Add a layer of mixed berries\n3. Sprinkle granola on top\n4. Drizzle with honey\n5. Repeat layers if desired",
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 0,
                'servings' => 1,
                'meal_type' => 'snack',
                'ingredients' => [
                    ['food' => 'Greek Yogurt (non-fat)', 'quantity' => 250, 'unit' => 'g'],
                    ['food' => 'Oatmeal (dry)', 'quantity' => 40, 'unit' => 'g'],
                    ['food' => 'Strawberries', 'quantity' => 50, 'unit' => 'g'],
                    ['food' => 'Blueberries', 'quantity' => 50, 'unit' => 'g'],
                    ['food' => 'Peanut Butter', 'quantity' => 15, 'unit' => 'g'],
                ],
            ],

            // 5. Beef Stir-Fry
            [
                'name' => 'Lean Beef Stir-Fry with Vegetables & Rice',
                'description' => 'High-protein meal with plenty of vegetables and carbs for energy',
                'instructions' => "1. Cut beef into thin strips and marinate with soy sauce\n2. Heat wok or large pan with sesame oil on high heat\n3. Stir-fry beef for 3-4 minutes, remove and set aside\n4. Stir-fry mixed vegetables for 4-5 minutes\n5. Add beef back, toss everything together\n6. Serve over cooked rice",
                'prep_time_minutes' => 15,
                'cook_time_minutes' => 15,
                'servings' => 1,
                'meal_type' => 'dinner',
                'ingredients' => [
                    ['food' => 'Ground Beef (90% lean)', 'quantity' => 180, 'unit' => 'g'],
                    ['food' => 'White Rice (cooked)', 'quantity' => 150, 'unit' => 'g'],
                    ['food' => 'Broccoli', 'quantity' => 100, 'unit' => 'g'],
                    ['food' => 'Bell Pepper', 'quantity' => 80, 'unit' => 'g'],
                    ['food' => 'Spinach', 'quantity' => 60, 'unit' => 'g'],
                    ['food' => 'Olive Oil', 'quantity' => 10, 'unit' => 'ml'],
                ],
            ],

            // 6. Tuna Protein Bowl
            [
                'name' => 'Tuna Quinoa Power Bowl',
                'description' => 'Light but filling meal with lean protein and superfoods',
                'instructions' => "1. Cook quinoa according to package directions\n2. Drain tuna and flake into chunks\n3. Chop vegetables into bite-sized pieces\n4. Mix olive oil, lemon juice, salt and pepper for dressing\n5. Combine all ingredients in a bowl\n6. Toss with dressing and serve",
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 15,
                'servings' => 1,
                'meal_type' => 'lunch',
                'ingredients' => [
                    ['food' => 'Tuna (canned in water)', 'quantity' => 150, 'unit' => 'g'],
                    ['food' => 'Quinoa (cooked)', 'quantity' => 120, 'unit' => 'g'],
                    ['food' => 'Spinach', 'quantity' => 50, 'unit' => 'g'],
                    ['food' => 'Bell Pepper', 'quantity' => 80, 'unit' => 'g'],
                    ['food' => 'Lettuce (Mixed Greens)', 'quantity' => 80, 'unit' => 'g'],
                    ['food' => 'Olive Oil', 'quantity' => 15, 'unit' => 'ml'],
                ],
            ],

            // 7. Egg White Omelette
            [
                'name' => 'Veggie Egg White Omelette with Toast',
                'description' => 'Low-fat, high-protein breakfast to start your day',
                'instructions' => "1. Whisk egg whites with a pinch of salt\n2. Sauté vegetables in a non-stick pan\n3. Pour egg whites over vegetables\n4. Cook until set, fold omelette in half\n5. Toast whole grain bread\n6. Serve omelette with toast and avocado",
                'prep_time_minutes' => 5,
                'cook_time_minutes' => 10,
                'servings' => 1,
                'meal_type' => 'breakfast',
                'ingredients' => [
                    ['food' => 'Egg Whites', 'quantity' => 200, 'unit' => 'ml'],
                    ['food' => 'Spinach', 'quantity' => 30, 'unit' => 'g'],
                    ['food' => 'Bell Pepper', 'quantity' => 50, 'unit' => 'g'],
                    ['food' => 'Broccoli', 'quantity' => 40, 'unit' => 'g'],
                    ['food' => 'Whole Wheat Bread', 'quantity' => 60, 'unit' => 'g'],
                    ['food' => 'Avocado', 'quantity' => 50, 'unit' => 'g'],
                ],
            ],

            // 8. Protein Shake
            [
                'name' => 'Post-Workout Protein Shake',
                'description' => 'Quick and easy shake to fuel muscle recovery',
                'instructions' => "1. Add all ingredients to a blender\n2. Blend on high for 30-60 seconds until smooth\n3. Add ice if desired for a colder shake\n4. Pour into a glass and drink immediately",
                'prep_time_minutes' => 2,
                'cook_time_minutes' => 0,
                'servings' => 1,
                'meal_type' => 'snack',
                'is_favorite' => true,
                'ingredients' => [
                    ['food' => 'Whey Protein Powder', 'quantity' => 30, 'unit' => 'g'],
                    ['food' => 'Cottage Cheese (low-fat)', 'quantity' => 150, 'unit' => 'g'],
                    ['food' => 'Banana', 'quantity' => 1, 'unit' => 'medium'],
                    ['food' => 'Peanut Butter', 'quantity' => 20, 'unit' => 'g'],
                    ['food' => 'Oatmeal (dry)', 'quantity' => 30, 'unit' => 'g'],
                ],
            ],

            // 9. Turkey Chili
            [
                'name' => 'Lean Turkey Chili with Black Beans',
                'description' => 'High-protein, high-fiber meal prep favorite',
                'instructions' => "1. Brown ground turkey in a large pot\n2. Add diced onions, peppers, and garlic, cook until soft\n3. Add crushed tomatoes, black beans, and spices\n4. Simmer for 30 minutes, stirring occasionally\n5. Serve with brown rice or eat as is\n6. Stores great for meal prep!",
                'prep_time_minutes' => 15,
                'cook_time_minutes' => 45,
                'servings' => 4,
                'meal_type' => 'dinner',
                'is_favorite' => true,
                'ingredients' => [
                    ['food' => 'Ground Turkey', 'quantity' => 500, 'unit' => 'g'],
                    ['food' => 'Quinoa (cooked)', 'quantity' => 400, 'unit' => 'g'],
                    ['food' => 'Bell Pepper', 'quantity' => 200, 'unit' => 'g'],
                    ['food' => 'Spinach', 'quantity' => 150, 'unit' => 'g'],
                    ['food' => 'Brown Rice (cooked)', 'quantity' => 200, 'unit' => 'g'],
                    ['food' => 'Olive Oil', 'quantity' => 15, 'unit' => 'ml'],
                ],
            ],

            // 10. Chicken Fajita Bowl
            [
                'name' => 'Chicken Fajita Burrito Bowl',
                'description' => 'Mexican-inspired high-protein bowl with balanced macros',
                'instructions' => "1. Season chicken with fajita seasoning\n2. Grill or pan-fry chicken until cooked through\n3. Sauté bell peppers and onions until softened\n4. Assemble bowl with rice, chicken, vegetables\n5. Top with salsa, guacamole, and Greek yogurt\n6. Optional: add cheese for extra protein",
                'prep_time_minutes' => 10,
                'cook_time_minutes' => 20,
                'servings' => 1,
                'meal_type' => 'lunch',
                'ingredients' => [
                    ['food' => 'Chicken Breast', 'quantity' => 180, 'unit' => 'g'],
                    ['food' => 'Brown Rice (cooked)', 'quantity' => 150, 'unit' => 'g'],
                    ['food' => 'Bell Pepper', 'quantity' => 100, 'unit' => 'g'],
                    ['food' => 'Lettuce (Mixed Greens)', 'quantity' => 60, 'unit' => 'g'],
                    ['food' => 'Avocado', 'quantity' => 50, 'unit' => 'g'],
                    ['food' => 'Greek Yogurt (non-fat)', 'quantity' => 50, 'unit' => 'g'],
                ],
            ],
        ];

        // Create recipes and their ingredients
        $recipeCount = 0;
        $ingredientCount = 0;

        foreach ($recipes as $recipeData) {
            $recipe = Recipe::create([
                'user_id' => $user->id,
                'name' => $recipeData['name'],
                'description' => $recipeData['description'],
                'instructions' => $recipeData['instructions'],
                'prep_time_minutes' => $recipeData['prep_time_minutes'],
                'cook_time_minutes' => $recipeData['cook_time_minutes'],
                'servings' => $recipeData['servings'],
                'meal_type' => $recipeData['meal_type'],
                'is_favorite' => $recipeData['is_favorite'] ?? false,
            ]);

            $recipeCount++;

            // Add ingredients
            foreach ($recipeData['ingredients'] as $index => $ingredientData) {
                // Find the food by name
                $food = Food::whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($ingredientData['food']).'%'])->first();

                if ($food) {
                    RecipeIngredient::create([
                        'recipe_id' => $recipe->id,
                        'food_id' => $food->id,
                        'quantity' => $ingredientData['quantity'],
                        'unit' => $ingredientData['unit'],
                        'order' => $index,
                    ]);
                    $ingredientCount++;
                } else {
                    $this->command->warn("Food not found: {$ingredientData['food']}");
                }
            }
        }

        $this->command->info("✅ Fitness recipes seeded successfully!");
        $this->command->info("📖 Created {$recipeCount} recipes");
        $this->command->info("🥗 Added {$ingredientCount} ingredients");
    }
}
