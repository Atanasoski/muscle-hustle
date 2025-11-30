@extends('layouts.app')

@section('title', 'Create Recipe')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <div>
                    <h1 class="h3 fw-bold mb-0">Create New Recipe</h1>
                    <p class="text-muted small mb-0">Build your recipe from scratch or use AI to parse ingredients</p>
                </div>
            </div>

            <form action="{{ route('recipes.store') }}" method="POST" id="recipe-form">
                @csrf

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
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="meal_type" class="form-label fw-bold">Meal Type</label>
                                <select class="form-select" id="meal_type" name="meal_type">
                                    <option value="">Select type...</option>
                                    <option value="breakfast" {{ old('meal_type') === 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                                    <option value="lunch" {{ old('meal_type') === 'lunch' ? 'selected' : '' }}>Lunch</option>
                                    <option value="dinner" {{ old('meal_type') === 'dinner' ? 'selected' : '' }}>Dinner</option>
                                    <option value="snack" {{ old('meal_type') === 'snack' ? 'selected' : '' }}>Snack</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-4">
                                <label for="servings" class="form-label fw-bold">Servings *</label>
                                <input type="number" step="0.5" min="0.5" class="form-control @error('servings') is-invalid @enderror" 
                                       id="servings" name="servings" value="{{ old('servings', 1) }}" required>
                                @error('servings')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="prep_time_minutes" class="form-label fw-bold">Prep Time (min)</label>
                                <input type="number" min="0" class="form-control" id="prep_time_minutes" 
                                       name="prep_time_minutes" value="{{ old('prep_time_minutes') }}">
                            </div>

                            <div class="col-md-4">
                                <label for="cook_time_minutes" class="form-label fw-bold">Cook Time (min)</label>
                                <input type="number" min="0" class="form-control" id="cook_time_minutes" 
                                       name="cook_time_minutes" value="{{ old('cook_time_minutes') }}">
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_favorite" name="is_favorite" value="1" {{ old('is_favorite') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_favorite">
                                        <i class="bi bi-heart-fill text-danger"></i> Mark as Favorite
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Ingredient Parser -->
                <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #ff6b35 0%, #ff8c61 100%);">
                    <div class="card-body p-4 text-white">
                        <h5 class="fw-bold mb-2"><i class="bi bi-robot me-2"></i> AI Ingredient Parser</h5>
                        <p class="small mb-3 opacity-75">Paste your ingredient list and let AI identify and add them automatically!</p>
                        
                        <div class="mb-3">
                            <textarea class="form-control" id="ai-ingredient-input" rows="4" 
                                      placeholder="Example:
200g chicken breast
1 cup brown rice  
100g broccoli
2 tbsp olive oil" 
                                      style="background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.3);">{{ old('ai_ingredient_text') }}</textarea>
                        </div>
                        
                        <button type="button" class="btn btn-light btn-lg w-100" id="parse-ai-ingredients-btn">
                            <i class="bi bi-magic me-2"></i> Parse Ingredients with AI
                        </button>
                        
                        <div class="ai-loading-spinner text-center mt-3" style="display: none;">
                            <div class="spinner-border text-light" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="mt-2">Analyzing ingredients...</div>
                        </div>
                        
                        <div class="alert alert-danger mt-3" id="ai-error-message" style="display: none;"></div>
                        <div class="alert alert-success mt-3" id="ai-success-message" style="display: none;"></div>
                    </div>
                </div>

                <!-- Ingredients List -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i> Ingredients</h5>
                            <button type="button" class="btn btn-sm btn-success" id="add-ingredient-btn">
                                <i class="bi bi-plus"></i> Add Ingredient
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="ingredients-container">
                            <!-- Ingredients will be added here dynamically -->
                            @if(old('ingredients'))
                                @foreach(old('ingredients') as $index => $ingredient)
                                    <div class="ingredient-row mb-3 p-3 border rounded-3" data-index="{{ $index }}">
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <label class="form-label small">Food Item *</label>
                                                <select class="form-select form-select-sm" name="ingredients[{{ $index }}][food_id]" required>
                                                    <option value="">Select food...</option>
                                                    @foreach($foods as $category => $categoryFoods)
                                                        <optgroup label="{{ $category ?? 'Other' }}">
                                                            @foreach($categoryFoods as $food)
                                                                <option value="{{ $food->id }}" {{ $ingredient['food_id'] == $food->id ? 'selected' : '' }}>
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
                                                       name="ingredients[{{ $index }}][quantity]" value="{{ $ingredient['quantity'] }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Unit *</label>
                                                <input type="text" class="form-control form-control-sm" 
                                                       name="ingredients[{{ $index }}][unit]" value="{{ $ingredient['unit'] }}" placeholder="g, ml, cup" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Notes</label>
                                                <input type="text" class="form-control form-control-sm" 
                                                       name="ingredients[{{ $index }}][notes]" value="{{ $ingredient['notes'] ?? '' }}" placeholder="optional">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-ingredient-btn w-100">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <div id="no-ingredients-message" class="text-center text-muted py-4" style="display: {{ old('ingredients') ? 'none' : 'block' }};">
                            <i class="bi bi-basket3 display-4"></i>
                            <p class="mt-2">No ingredients added yet. Use AI parser or add manually above.</p>
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
                                  placeholder="1. Preheat oven to 180°C
2. Season chicken breast with salt and pepper
3. Bake for 25-30 minutes...">{{ old('instructions') }}</textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-success btn-lg flex-fill">
                        <i class="bi bi-check-circle me-2"></i> Create Recipe
                    </button>
                    <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let ingredientIndex = {{ old('ingredients') ? count(old('ingredients')) : 0 }};

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

// AI Parse Ingredients
document.getElementById('parse-ai-ingredients-btn').addEventListener('click', function() {
    const text = document.getElementById('ai-ingredient-input').value.trim();
    
    if (!text) {
        showError('Please enter some ingredients to parse.');
        return;
    }
    
    const btn = this;
    const spinner = document.querySelector('.ai-loading-spinner');
    const errorMsg = document.getElementById('ai-error-message');
    const successMsg = document.getElementById('ai-success-message');
    
    btn.disabled = true;
    spinner.style.display = 'block';
    errorMsg.style.display = 'none';
    successMsg.style.display = 'none';
    
    fetch('{{ route('nutrition.parse') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ text })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            showError(data.message);
        } else if (data.items && data.items.length > 0) {
            // Parse each item and try to match to foods in database
            let added = 0;
            let notFound = [];
            
            data.items.forEach(item => {
                const matchedFood = findFoodByName(item.food);
                
                if (matchedFood) {
                    addIngredientRow(matchedFood.id, item.quantity, extractUnit(item.quantity), item.food);
                    added++;
                } else {
                    notFound.push(item.food);
                }
            });
            
            if (added > 0) {
                successMsg.textContent = `Added ${added} ingredient(s)!`;
                successMsg.style.display = 'block';
                document.getElementById('ai-ingredient-input').value = '';
            }
            
            if (notFound.length > 0) {
                showError(`Could not find these foods in database: ${notFound.join(', ')}. Please add them manually or create custom foods first.`);
            }
        } else {
            showError('No ingredients found in the text. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An unexpected error occurred. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        spinner.style.display = 'none';
    });
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
                    <button type="button" class="btn btn-sm btn-outline-danger remove-ingredient-btn w-100">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    ingredientIndex++;
    checkIfEmpty();
}

function findFoodByName(name) {
    const searchName = name.toLowerCase().trim();
    
    for (const [category, foods] of Object.entries(foodsData)) {
        for (const food of foods) {
            if (food.name.toLowerCase().includes(searchName) || searchName.includes(food.name.toLowerCase())) {
                return food;
            }
        }
    }
    
    return null;
}

function extractUnit(quantityString) {
    // Try to extract unit from quantity string like "200g", "1 cup", "2 tbsp"
    const match = quantityString.match(/[a-zA-Z]+/);
    return match ? match[0] : 'g';
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

function showError(message) {
    const errorMsg = document.getElementById('ai-error-message');
    errorMsg.textContent = message;
    errorMsg.style.display = 'block';
}
</script>
@endpush
@endsection

