<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Recipe::where('user_id', auth()->id())
            ->with('recipeIngredients.food');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by meal type
        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
        }

        // Filter by favorites
        if ($request->filled('favorites') && $request->favorites === '1') {
            $query->where('is_favorite', true);
        }

        $recipes = $query->latest()->get();

        return view('recipes.index', compact('recipes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $foods = Food::where(function ($query) {
            $query->whereNull('user_id')
                ->orWhere('user_id', auth()->id());
        })
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('recipes.create', compact('foods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'cook_time_minutes' => 'nullable|integer|min:0',
            'servings' => 'required|numeric|min:0.5',
            'meal_type' => 'nullable|string|in:breakfast,lunch,dinner,snack',
            'is_favorite' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'ingredients' => 'nullable|array',
            'ingredients.*.food_id' => 'required|exists:foods,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit' => 'required|string',
            'ingredients.*.notes' => 'nullable|string',
        ]);

        $recipe = Recipe::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'prep_time_minutes' => $request->prep_time_minutes,
            'cook_time_minutes' => $request->cook_time_minutes,
            'servings' => $request->servings,
            'meal_type' => $request->meal_type,
            'is_favorite' => $request->boolean('is_favorite'),
            'tags' => $request->tags,
        ]);

        // Add ingredients
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $index => $ingredient) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'food_id' => $ingredient['food_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                    'notes' => $ingredient['notes'] ?? null,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        // Authorization check
        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $recipe->load('recipeIngredients.food');
        $nutrition = $recipe->getNutritionPerServing();

        return view('recipes.show', compact('recipe', 'nutrition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        // Authorization check
        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $recipe->load('recipeIngredients.food');

        $foods = Food::where(function ($query) {
            $query->whereNull('user_id')
                ->orWhere('user_id', auth()->id());
        })
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('recipes.edit', compact('recipe', 'foods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        // Authorization check
        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'cook_time_minutes' => 'nullable|integer|min:0',
            'servings' => 'required|numeric|min:0.5',
            'meal_type' => 'nullable|string|in:breakfast,lunch,dinner,snack',
            'is_favorite' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'ingredients' => 'nullable|array',
            'ingredients.*.food_id' => 'required|exists:foods,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit' => 'required|string',
            'ingredients.*.notes' => 'nullable|string',
        ]);

        $recipe->update([
            'name' => $request->name,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'prep_time_minutes' => $request->prep_time_minutes,
            'cook_time_minutes' => $request->cook_time_minutes,
            'servings' => $request->servings,
            'meal_type' => $request->meal_type,
            'is_favorite' => $request->boolean('is_favorite'),
            'tags' => $request->tags,
        ]);

        // Delete existing ingredients and re-add
        $recipe->recipeIngredients()->delete();

        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $index => $ingredient) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'food_id' => $ingredient['food_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                    'notes' => $ingredient['notes'] ?? null,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        // Authorization check
        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $recipe->delete();

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe deleted successfully!');
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Recipe $recipe)
    {
        // Authorization check
        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $recipe->update(['is_favorite' => ! $recipe->is_favorite]);

        return response()->json(['success' => true, 'is_favorite' => $recipe->is_favorite]);
    }
}
