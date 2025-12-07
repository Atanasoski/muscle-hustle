@extends('layouts.app')

@section('title', 'My Recipes')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-2">
                        <i class="bi bi-book text-success"></i> My Recipes
                    </h1>
                    <p class="text-muted mb-0">Create and manage your favorite meals</p>
                </div>
                <a href="{{ route('recipes.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i> Create Recipe
                </a>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    @if($recipes->isNotEmpty() || request()->has(['search', 'meal_type', 'favorites']))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('recipes.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search recipes...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="meal_type">
                                <option value="">All Types</option>
                                <option value="breakfast" {{ request('meal_type') === 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                                <option value="lunch" {{ request('meal_type') === 'lunch' ? 'selected' : '' }}>Lunch</option>
                                <option value="dinner" {{ request('meal_type') === 'dinner' ? 'selected' : '' }}>Dinner</option>
                                <option value="snack" {{ request('meal_type') === 'snack' ? 'selected' : '' }}>Snack</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="favorites" value="1" 
                                       id="favoritesFilter" {{ request('favorites') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="favoritesFilter">
                                    <i class="bi bi-heart-fill text-danger"></i> Favorites Only
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
                
                @if(request()->has(['search', 'meal_type', 'favorites']) && (request('search') || request('meal_type') || request('favorites')))
                    <div class="mt-3">
                        <a href="{{ route('recipes.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($recipes->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-book display-1 text-muted mb-3"></i>
                <h4 class="fw-bold mb-2">No Recipes Yet</h4>
                <p class="text-muted mb-4">Create your first recipe and start building your meal library!</p>
                <a href="{{ route('recipes.create') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle me-2"></i> Create Your First Recipe
                </a>
            </div>
        </div>
    @else
        <!-- Recipe Grid -->
        <div class="row g-4">
            @foreach($recipes as $recipe)
                @php
                    $nutrition = $recipe->getNutritionPerServing();
                @endphp
                
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-4">
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="fw-bold mb-0 flex-grow-1">{{ $recipe->name }}</h5>
                                <button class="btn btn-sm btn-link p-0 favorite-btn" 
                                        data-recipe-id="{{ $recipe->id }}"
                                        style="font-size: 1.5rem;">
                                    <i class="bi bi-{{ $recipe->is_favorite ? 'heart-fill text-danger' : 'heart' }}"></i>
                                </button>
                            </div>

                            @if($recipe->description)
                                <p class="text-muted small mb-3">{{ Str::limit($recipe->description, 80) }}</p>
                            @endif

                            <!-- Tags/Type -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($recipe->meal_type)
                                    <span class="badge bg-success text-white">{{ ucfirst($recipe->meal_type) }}</span>
                                @endif
                                @if($recipe->tags)
                                    @foreach($recipe->tags as $tag)
                                        <span class="badge bg-secondary">{{ $tag }}</span>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Time Info -->
                            @if($recipe->prep_time_minutes || $recipe->cook_time_minutes)
                                <div class="d-flex gap-3 mb-3 text-muted small">
                                    @if($recipe->prep_time_minutes)
                                        <div>
                                            <i class="bi bi-clock"></i> Prep: {{ $recipe->prep_time_minutes }}m
                                        </div>
                                    @endif
                                    @if($recipe->cook_time_minutes)
                                        <div>
                                            <i class="bi bi-fire"></i> Cook: {{ $recipe->cook_time_minutes }}m
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Nutrition Summary -->
                            <div class="bg-light rounded-3 p-3 mb-3">
                                <div class="small fw-bold text-muted mb-2">Per Serving ({{ $recipe->servings }} servings total)</div>
                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <div class="fw-bold text-success">{{ round($nutrition['calories']) }}</div>
                                        <small class="text-muted">calories</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-primary">{{ round($nutrition['protein']) }}g</div>
                                        <small class="text-muted">protein</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-warning">{{ round($nutrition['carbs']) }}g</div>
                                        <small class="text-muted">carbs</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-danger">{{ round($nutrition['fat']) }}g</div>
                                        <small class="text-muted">fat</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Ingredients Count -->
                            <div class="text-muted small mb-3">
                                <i class="bi bi-list-check"></i> {{ $recipe->recipeIngredients->count() }} ingredients
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this recipe?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
// Toggle favorite
document.querySelectorAll('.favorite-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const recipeId = this.dataset.recipeId;
        const icon = this.querySelector('i');
        
        fetch(`/recipes/${recipeId}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_favorite) {
                    icon.className = 'bi bi-heart-fill text-danger';
                } else {
                    icon.className = 'bi bi-heart';
                }
            }
        });
    });
});
</script>
@endpush
@endsection

