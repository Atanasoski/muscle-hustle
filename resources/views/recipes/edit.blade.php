@extends('layouts.app')

@section('title', 'Edit Recipe')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <x-button variant="cancel" size="sm" href="{{ route('recipes.show', $recipe) }}" class="btn-outline-secondary me-3" icon="bi-arrow-left">
                    Back
                </x-button>
                <div>
                    <h1 class="h3 fw-bold mb-0">Edit Recipe</h1>
                    <p class="text-muted small mb-0">{{ $recipe->name }}</p>
                </div>
            </div>

            <form action="{{ route('recipes.update', $recipe) }}" method="POST" id="recipe-form">
                @csrf
                @method('PUT')

                <!-- Basic Info Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i> Basic Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-bold">Recipe Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $recipe->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="meal_type" class="form-label fw-bold">Meal Type</label>
                                <select class="form-select" id="meal_type" name="meal_type">
                                    <option value="">Select type...</option>
                                    <option value="breakfast" {{ old('meal_type', $recipe->meal_type) === 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                                    <option value="lunch" {{ old('meal_type', $recipe->meal_type) === 'lunch' ? 'selected' : '' }}>Lunch</option>
                                    <option value="dinner" {{ old('meal_type', $recipe->meal_type) === 'dinner' ? 'selected' : '' }}>Dinner</option>
                                    <option value="snack" {{ old('meal_type', $recipe->meal_type) === 'snack' ? 'selected' : '' }}>Snack</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $recipe->description) }}</textarea>
                            </div>

                            <div class="col-md-4">
                                <label for="servings" class="form-label fw-bold">Servings *</label>
                                <input type="number" step="0.5" min="0.5" class="form-control @error('servings') is-invalid @enderror" 
                                       id="servings" name="servings" value="{{ old('servings', $recipe->servings) }}" required>
                                @error('servings')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="prep_time_minutes" class="form-label fw-bold">Prep Time (min)</label>
                                <input type="number" min="0" class="form-control" id="prep_time_minutes" 
                                       name="prep_time_minutes" value="{{ old('prep_time_minutes', $recipe->prep_time_minutes) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="cook_time_minutes" class="form-label fw-bold">Cook Time (min)</label>
                                <input type="number" min="0" class="form-control" id="cook_time_minutes" 
                                       name="cook_time_minutes" value="{{ old('cook_time_minutes', $recipe->cook_time_minutes) }}">
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_favorite" name="is_favorite" value="1" 
                                           {{ old('is_favorite', $recipe->is_favorite) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_favorite">
                                        <i class="bi bi-heart-fill text-danger"></i> Mark as Favorite
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients List -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i> Ingredients</h5>
                            <x-button variant="create" size="sm" id="add-ingredient-btn" icon="bi-plus">
                                Add Ingredient
                            </x-button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="ingredients-container">
                            @foreach($recipe->recipeIngredients as $index => $ingredient)
                                <div class="ingredient-row mb-3 p-3 border rounded-3" data-index="{{ $index }}">
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <label class="form-label small">Food Item *</label>
                                            <select class="form-select form-select-sm" name="ingredients[{{ $index }}][food_id]" required>
                                                <option value="">Select food...</option>
                                                @foreach($foods as $category => $categoryFoods)
                                                    <optgroup label="{{ $category ?? 'Other' }}">
                                                        @foreach($categoryFoods as $food)
                                                            <option value="{{ $food->id }}" {{ $ingredient->food_id == $food->id ? 'selected' : '' }}>
                                                                {{ $food->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Quantity *</label>
                                            <input type="number" step="0.1" min="0" class="form-control form-control-sm" 
                                                   name="ingredients[{{ $index }}][quantity]" value="{{ $ingredient->quantity }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Unit *</label>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="ingredients[{{ $index }}][unit]" value="{{ $ingredient->unit }}" placeholder="g, ml, cup" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Notes</label>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="ingredients[{{ $index }}][notes]" value="{{ $ingredient->notes }}" placeholder="optional">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <x-button variant="delete" size="sm" class="btn-outline-danger remove-ingredient-btn w-100">
                                                <span class="visually-hidden">Remove</span>
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div id="no-ingredients-message" class="text-center text-muted py-4" style="display: {{ $recipe->recipeIngredients->count() > 0 ? 'none' : 'block' }};">
                            <i class="bi bi-basket3 display-4"></i>
                            <p class="mt-2">No ingredients added yet. Click "Add Ingredient" above.</p>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-card-text me-2"></i> Cooking Instructions</h5>
                    </div>
                    <div class="card-body p-4">
                        <textarea class="form-control" id="instructions" name="instructions" rows="6" 
                                  placeholder="1. Preheat oven to 180°C...">{{ old('instructions', $recipe->instructions) }}</textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-3">
                    <x-button variant="save" type="submit" size="lg" class="flex-fill">
                        Update Recipe
                    </x-button>
                    <x-button variant="cancel" size="lg" href="{{ route('recipes.show', $recipe) }}" class="btn-outline-secondary">Cancel</x-button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let ingredientIndex = {{ $recipe->recipeIngredients->count() }};

// Foods data for dropdowns (JSON)
const foodsData = @json($foods);

// Add ingredient manually
document.getElementById('add-ingredient-btn').addEventListener('click', function() {
    addIngredientRow();
});

// Remove ingredient
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-ingredient-btn')) {
        e.target.closest('.ingredient-row').remove();
        checkIfEmpty();
    }
});

function addIngredientRow(foodId = '', quantity = '', unit = '', notes = '') {
    const container = document.getElementById('ingredients-container');
    
    let foodOptions = '<option value="">Select food...</option>';
    for (const [category, foods] of Object.entries(foodsData)) {
        foodOptions += `<optgroup label="${category || 'Other'}">`;
        foods.forEach(food => {
            const selected = food.id == foodId ? 'selected' : '';
            foodOptions += `<option value="${food.id}" ${selected}>${food.name}</option>`;
        });
        foodOptions += '</optgroup>';
    }
    
    const html = `
        <div class="ingredient-row mb-3 p-3 border rounded-3" data-index="${ingredientIndex}">
            <div class="row g-2">
                <div class="col-md-5">
                    <label class="form-label small">Food Item *</label>
                    <select class="form-select form-select-sm" name="ingredients[${ingredientIndex}][food_id]" required>
                        ${foodOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Quantity *</label>
                    <input type="number" step="0.1" min="0" class="form-control form-control-sm" 
                           name="ingredients[${ingredientIndex}][quantity]" value="${quantity}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Unit *</label>
                    <input type="text" class="form-control form-control-sm" 
                           name="ingredients[${ingredientIndex}][unit]" value="${unit}" placeholder="g, ml, cup" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Notes</label>
                    <input type="text" class="form-control form-control-sm" 
                           name="ingredients[${ingredientIndex}][notes]" value="${notes}" placeholder="optional">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <x-button variant="delete" size="sm" class="btn-outline-danger remove-ingredient-btn w-100">
                        <span class="visually-hidden">Remove</span>
                    </x-button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    ingredientIndex++;
    checkIfEmpty();
}

function checkIfEmpty() {
    const container = document.getElementById('ingredients-container');
    const noIngredientsMsg = document.getElementById('no-ingredients-message');
    
    if (container.children.length === 0) {
        noIngredientsMsg.style.display = 'block';
    } else {
        noIngredientsMsg.style.display = 'none';
    }
}
</script>
@endpush
@endsection

