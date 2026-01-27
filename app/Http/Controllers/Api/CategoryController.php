<?php

namespace App\Http\Controllers\Api;

use App\Enums\CategoryType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::query();

        // Filter by type if provided
        if ($request->has('type')) {
            $type = CategoryType::tryFrom($request->input('type'));
            if ($type) {
                $query->ofType($type);
            }
        }

        $categories = $query->orderBy('display_order')->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category with its exercises.
     */
    public function show(Category $category): CategoryResource
    {
        $category->load('exercises');

        return new CategoryResource($category);
    }
}
