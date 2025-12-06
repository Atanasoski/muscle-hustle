<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Food::query()
            ->with('category')
            ->where(function ($q) {
                $q->whereNull('user_id')
                    ->orWhere('user_id', auth()->id());
            });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by ownership
        if ($request->filled('ownership')) {
            if ($request->ownership === 'custom') {
                $query->where('user_id', auth()->id());
            } elseif ($request->ownership === 'global') {
                $query->whereNull('user_id');
            }
        }

        $foods = $query->orderBy('name')->paginate(50);

        // Get food categories for filter
        $categories = Category::food()
            ->orderBy('display_order')
            ->get();

        return view('foods.index', compact('foods', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::food()
            ->orderBy('display_order')
            ->get();

        return view('foods.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'calories' => 'nullable|integer|min:0',
            'protein' => 'nullable|integer|min:0',
            'carbs' => 'nullable|integer|min:0',
            'fat' => 'nullable|integer|min:0',
            'fiber' => 'nullable|integer|min:0',
            'sugar' => 'nullable|integer|min:0',
            'default_serving_unit' => 'nullable|string|max:50',
            'default_serving_size' => 'nullable|numeric|min:0',
        ]);

        Food::create([
            'user_id' => auth()->id(), // Custom food for this user
            'name' => $request->name,
            'category_id' => $request->category_id,
            'calories' => $request->calories,
            'protein' => $request->protein,
            'carbs' => $request->carbs,
            'fat' => $request->fat,
            'fiber' => $request->fiber,
            'sugar' => $request->sugar,
            'default_serving_unit' => $request->default_serving_unit,
            'default_serving_size' => $request->default_serving_size,
        ]);

        return redirect()->route('foods.index')
            ->with('success', 'Food added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Food $food)
    {
        // Authorization check - can only view global foods or own custom foods
        if ($food->user_id && $food->user_id !== auth()->id()) {
            abort(403);
        }

        return view('foods.show', compact('food'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Food $food)
    {
        // Authorization check - can only edit own custom foods
        if ($food->user_id !== auth()->id()) {
            abort(403, 'You can only edit your own custom foods.');
        }

        $categories = Category::food()
            ->orderBy('display_order')
            ->get();

        return view('foods.edit', compact('food', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Food $food)
    {
        // Authorization check - can only edit own custom foods
        if ($food->user_id !== auth()->id()) {
            abort(403, 'You can only edit your own custom foods.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'calories' => 'nullable|integer|min:0',
            'protein' => 'nullable|integer|min:0',
            'carbs' => 'nullable|integer|min:0',
            'fat' => 'nullable|integer|min:0',
            'fiber' => 'nullable|integer|min:0',
            'sugar' => 'nullable|integer|min:0',
            'default_serving_unit' => 'nullable|string|max:50',
            'default_serving_size' => 'nullable|numeric|min:0',
        ]);

        $food->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'calories' => $request->calories,
            'protein' => $request->protein,
            'carbs' => $request->carbs,
            'fat' => $request->fat,
            'fiber' => $request->fiber,
            'sugar' => $request->sugar,
            'default_serving_unit' => $request->default_serving_unit,
            'default_serving_size' => $request->default_serving_size,
        ]);

        return redirect()->route('foods.index')
            ->with('success', 'Food updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Food $food)
    {
        // Authorization check - can only delete own custom foods
        if ($food->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own custom foods.');
        }

        $food->delete();

        return redirect()->route('foods.index')
            ->with('success', 'Food deleted successfully!');
    }
}
