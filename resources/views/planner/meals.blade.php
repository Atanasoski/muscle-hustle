@extends('layouts.app')

@section('title', 'Weekly Meal Planner')

@section('content')
<div class="container py-3 py-md-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 h2-md fw-bold mb-2">
                    <i class="bi bi-egg-fried text-success"></i> Weekly Meal Planner
                </h1>
                <p class="text-muted mb-0 small">Week starting: {{ $weekStart->format('M d, Y') }}</p>
            </div>
            <a href="{{ route('planner.grocery-list') }}" class="btn btn-primary">
                <i class="bi bi-cart3 me-2"></i> Generate Grocery List
            </a>
        </div>
    </div>

    @php
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dayEmojis = ['💪', '🔥', '⚡', '🎯', '🚀', '😎', '🎉'];
        $types = ['breakfast', 'lunch', 'dinner', 'snack'];
        $typeIcons = [
            'breakfast' => 'sunrise',
            'lunch' => 'sun',
            'dinner' => 'moon-stars',
            'snack' => 'cup-straw'
        ];
    @endphp

    <div class="d-flex flex-column gap-5">
        @foreach($days as $dayIndex => $dayName)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-white">
                            <span class="me-2">{{ $dayEmojis[$dayIndex] }}</span>
                            {{ $dayName }}
                        </h5>
                        @if($dailyTotals[$dayIndex]['calories'] > 0)
                            <span class="badge bg-white text-success">
                                {{ $dailyTotals[$dayIndex]['calories'] }} cal
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        @foreach($types as $type)
                            <div class="col-12 col-lg-6">
                                <div class="card border rounded-3 p-3 h-100 shadow-sm" style="min-height: 220px;">
                                    <!-- Header -->
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-{{ $typeIcons[$type] }} text-success fs-4"></i>
                                            <span class="text-uppercase fw-bold text-success" style="font-size: 0.9rem; letter-spacing: 1px;">{{ $type }}</span>
                                        </div>
                                        @if(isset($mealGrid[$dayIndex][$type]) && $mealGrid[$dayIndex][$type] && $mealGrid[$dayIndex][$type]->calories)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="bi bi-fire"></i> {{ $mealGrid[$dayIndex][$type]->calories }} cal
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($mealGrid[$dayIndex][$type]) && $mealGrid[$dayIndex][$type])
                                        @php $meal = $mealGrid[$dayIndex][$type]; @endphp
                                        
                                        <!-- Meal Name -->
                                        <h6 class="fw-bold mb-3">{{ $meal->name }}</h6>
                                        
                                        <!-- Foods in Meal -->
                                        @if($meal->foods->count() > 0)
                                            <div class="mb-3">
                                                @foreach($meal->foods as $food)
                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold">{{ $food->name }}</div>
                                                            <small class="text-muted">
                                                                {{ $food->pivot->servings }} × {{ $food->default_serving_size }}{{ $food->default_serving_unit }} 
                                                                ({{ $food->pivot->grams }}g)
                                                            </small>
                                                        </div>
                                                        <form action="{{ route('meals.foods.remove', [$meal, $food]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <!-- Serving Size -->
                                        @if($meal->serving_size)
                                            <div class="mb-3">
                                                <div class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-rulers text-muted mt-1"></i>
                                                    <div class="text-muted small lh-sm" style="flex: 1;">{{ $meal->serving_size }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Macros -->
                                        @if($meal->protein || $meal->carbs || $meal->fat)
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <span class="badge rounded-pill border border-primary text-primary bg-white d-inline-flex align-items-center gap-1 px-3 py-1">
                                                    <i class="bi bi-lightning-charge-fill" style="font-size: 0.65rem;"></i> {{ $meal->protein }}g protein
                                                </span>
                                                <span class="badge rounded-pill border border-warning text-warning bg-white d-inline-flex align-items-center gap-1 px-3 py-1">
                                                    <i class="bi bi-droplet-fill" style="font-size: 0.65rem;"></i> {{ $meal->carbs }}g carbs
                                                </span>
                                                <span class="badge rounded-pill border border-danger text-danger bg-white d-inline-flex align-items-center gap-1 px-3 py-1">
                                                    <i class="bi bi-circle-fill" style="font-size: 0.65rem;"></i> {{ $meal->fat }}g fat
                                                </span>
                                            </div>
                                        @endif
                                        
                                        <!-- Actions -->
                                        <div class="d-flex gap-2 mt-auto">
                                            <button class="btn btn-outline-success btn-sm flex-fill" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#addFoodModal{{ $meal->id }}">
                                                <i class="bi bi-plus-circle"></i> Add Food
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm flex-fill" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editMealModal{{ $dayIndex }}{{ $type }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form action="{{ route('planner.meals.destroy', $meal) }}" 
                                                  method="POST" class="flex-fill" 
                                                  onsubmit="return confirm('Delete this meal?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <!-- Empty State -->
                                        <div class="text-center py-4">
                                            <i class="bi bi-{{ $typeIcons[$type] }} text-muted mb-2" style="font-size: 2rem;"></i>
                                            <p class="text-muted mb-3 small">No meal planned</p>
                                            <button class="btn btn-outline-success btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#addMealModal{{ $dayIndex }}{{ $type }}">
                                                <i class="bi bi-plus-lg"></i> Add Meal
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Daily Summary -->
                    @if($dailyTotals[$dayIndex]['calories'] > 0)
                        <div class="mt-4 pt-3 border-top">
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-success">{{ $dailyTotals[$dayIndex]['calories'] }}</div>
                                        <small class="text-muted">Total Calories</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-primary">{{ $dailyTotals[$dayIndex]['protein'] }}g</div>
                                        <small class="text-muted">Protein</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-warning">{{ $dailyTotals[$dayIndex]['carbs'] }}g</div>
                                        <small class="text-muted">Carbs</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-danger">{{ $dailyTotals[$dayIndex]['fat'] }}g</div>
                                        <small class="text-muted">Fat</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Weekly Summary -->
    @if($weeklyTotals['calories'] > 0)
        <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <h5 class="text-white mb-3">
                    <i class="bi bi-graph-up me-2"></i>
                    Weekly Summary
                </h5>
                <div class="row g-3 text-white">
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="h2 mb-0 fw-bold">{{ number_format($weeklyTotals['calories']) }}</div>
                            <small class="opacity-75">Total Calories</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="h2 mb-0 fw-bold">{{ number_format($weeklyTotals['avg_calories']) }}</div>
                            <small class="opacity-75">Avg/Day</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-center">
                            <div class="h4 mb-0 fw-bold">{{ $weeklyTotals['protein'] }}g</div>
                            <small class="opacity-75">Protein</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-center">
                            <div class="h4 mb-0 fw-bold">{{ $weeklyTotals['carbs'] }}g</div>
                            <small class="opacity-75">Carbs</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-center">
                            <div class="h4 mb-0 fw-bold">{{ $weeklyTotals['fat'] }}g</div>
                            <small class="opacity-75">Fat</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Meal Modals (moved outside nested structure for proper Bootstrap behavior) -->
@foreach($days as $dayIndex => $dayName)
    @foreach($types as $type)
        <div class="modal fade" id="{{ isset($mealGrid[$dayIndex][$type]) && $mealGrid[$dayIndex][$type] ? 'editMealModal' : 'addMealModal' }}{{ $dayIndex }}{{ $type }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('planner.meals.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="day_of_week" value="{{ $dayIndex }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-{{ $typeIcons[$type] }} me-2"></i>
                                {{ ucfirst($type) }} - {{ $dayName }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Recipe Quick-Add -->
                            @if($recipes->isNotEmpty())
                                <div class="mb-4">
                                    <div class="border rounded-3 p-3" style="background: linear-gradient(135deg, rgba(40,167,69,0.05) 0%, rgba(32,201,151,0.05) 100%);">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-book-fill text-success fs-5"></i>
                                            <label class="form-label fw-bold mb-0">Quick Add from Recipes</label>
                                        </div>
                                        <p class="text-muted small mb-3">Select a saved recipe to auto-fill everything!</p>
                                        
                                        <select class="form-select recipe-selector" data-modal-id="{{ $dayIndex }}{{ $type }}">
                                            <option value="">Choose a recipe...</option>
                                            @foreach($recipes as $recipe)
                                                @php $nutrition = $recipe->getNutritionPerServing(); @endphp
                                                <option value="{{ $recipe->id }}" 
                                                        data-name="{{ $recipe->name }}"
                                                        data-servings="{{ $recipe->servings }}"
                                                        data-calories="{{ round($nutrition['calories']) }}"
                                                        data-protein="{{ round($nutrition['protein']) }}"
                                                        data-carbs="{{ round($nutrition['carbs']) }}"
                                                        data-fat="{{ round($nutrition['fat']) }}"
                                                        data-ingredients="{{ $recipe->recipeIngredients->pluck('food.name')->implode(', ') }}">
                                                    {{ $recipe->is_favorite ? '⭐ ' : '' }}{{ $recipe->name }} 
                                                    ({{ round($nutrition['calories']) }} cal)
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                        <div class="alert alert-success mt-3" style="display: none;" id="recipeApplied{{ $dayIndex }}{{ $type }}">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Recipe applied! Adjust servings if needed.
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Meal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control meal-name-input" name="name" 
                                       value="{{ $mealGrid[$dayIndex][$type]->name ?? '' }}" 
                                       placeholder="e.g., Grilled Chicken Salad" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Serving Size / Portions</label>
                                <input type="text" class="form-control meal-serving-input" name="serving_size" 
                                       value="{{ $mealGrid[$dayIndex][$type]->serving_size ?? '' }}" 
                                       placeholder="e.g., 200g salmon, 150g sweet potato, 100g broccoli">
                                <small class="text-muted">Describe the portions/quantities</small>
                            </div>
                            
                            <!-- AI Nutrition Parser -->
                            <div class="mb-4">
                                <div class="border rounded-3 p-3" style="background: linear-gradient(135deg, rgba(255,107,53,0.05) 0%, rgba(255,140,97,0.05) 100%);">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-stars text-warning fs-5"></i>
                                        <label class="form-label fw-bold mb-0">AI Nutrition Calculator</label>
                                    </div>
                                    <p class="text-muted small mb-3">Paste what you ate and let AI calculate nutrition for you!</p>
                                    
                                    <textarea class="form-control mb-2" 
                                              id="aiParseInput{{ $dayIndex }}{{ $type }}" 
                                              rows="3" 
                                              placeholder="Example: 2 chicken breasts, 1 cup brown rice, handful of broccoli, 1 tbsp olive oil"></textarea>
                                    
                                    <button type="button" 
                                            class="btn btn-warning btn-sm w-100 ai-parse-btn" 
                                            data-modal-id="{{ $dayIndex }}{{ $type }}">
                                        <i class="bi bi-magic"></i> Calculate Nutrition with AI
                                    </button>
                                    
                                    <div id="aiParseResult{{ $dayIndex }}{{ $type }}" class="mt-3" style="display: none;">
                                        <div class="alert alert-success mb-0">
                                            <i class="bi bi-check-circle me-2"></i>
                                            <strong>Parsed successfully!</strong> Nutrition values filled below.
                                        </div>
                                    </div>
                                    
                                    <div id="aiParseError{{ $dayIndex }}{{ $type }}" class="mt-3" style="display: none;">
                                        <div class="alert alert-danger mb-0">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <span class="error-message"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Calories</label>
                                <input type="number" class="form-control meal-calories-input" name="calories" 
                                       value="{{ $mealGrid[$dayIndex][$type]->calories ?? '' }}" 
                                       placeholder="e.g., 450" min="0">
                            </div>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label fw-bold small">Protein (g)</label>
                                    <input type="number" class="form-control meal-protein-input" name="protein" 
                                           value="{{ $mealGrid[$dayIndex][$type]->protein ?? '' }}" 
                                           placeholder="35" min="0">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-bold small">Carbs (g)</label>
                                    <input type="number" class="form-control meal-carbs-input" name="carbs" 
                                           value="{{ $mealGrid[$dayIndex][$type]->carbs ?? '' }}" 
                                           placeholder="45" min="0">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-bold small">Fat (g)</label>
                                    <input type="number" class="form-control meal-fat-input" name="fat" 
                                           value="{{ $mealGrid[$dayIndex][$type]->fat ?? '' }}" 
                                           placeholder="12" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Save Meal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

<!-- Add Food to Meal Modals -->
@foreach($days as $dayIndex => $dayName)
    @foreach($types as $type)
        @if(isset($mealGrid[$dayIndex][$type]) && $mealGrid[$dayIndex][$type])
            @php $meal = $mealGrid[$dayIndex][$type]; @endphp
            
            <div class="modal fade" id="addFoodModal{{ $meal->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-plus-circle text-success"></i> Add Food to {{ $dayName }} {{ ucfirst($type) }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('meals.foods.add', $meal) }}" method="POST" id="addFoodForm{{ $meal->id }}">
                                @csrf
                                
                                <!-- Food Selection -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Food</label>
                                    <select class="form-select food-selector" name="food_id" required data-meal-id="{{ $meal->id }}">
                                        <option value="">Choose a food...</option>
                                        @foreach($foodCategories as $category)
                                            @if($category->foods->count() > 0)
                                                <optgroup label="{{ $category->icon }} {{ $category->name }}">
                                                    @foreach($category->foods as $food)
                                                        <option value="{{ $food->id }}" 
                                                                data-default-size="{{ $food->default_serving_size }}"
                                                                data-default-unit="{{ $food->default_serving_unit }}"
                                                                data-calories="{{ $food->calories }}"
                                                                data-protein="{{ $food->protein }}"
                                                                data-carbs="{{ $food->carbs }}"
                                                                data-fat="{{ $food->fat }}">
                                                            {{ $food->name }} ({{ $food->calories }}cal/100g)
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Servings Input -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Number of Servings</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary" onclick="adjustServings('{{ $meal->id }}', -0.5)">-</button>
                                        <input type="number" 
                                               class="form-control text-center" 
                                               name="servings" 
                                               id="servings{{ $meal->id }}"
                                               value="1" 
                                               min="0.5" 
                                               step="0.5" 
                                               required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="adjustServings('{{ $meal->id }}', 0.5)">+</button>
                                    </div>
                                    <small class="text-muted" id="servingInfo{{ $meal->id }}">Select a food to see serving size</small>
                                </div>

                                <!-- Nutrition Preview -->
                                <div class="alert alert-info" id="nutritionPreview{{ $meal->id }}" style="display: none;">
                                    <strong>Nutrition for this portion:</strong>
                                    <div class="d-flex gap-3 mt-2 flex-wrap">
                                        <span><strong id="previewCalories{{ $meal->id }}">0</strong> cal</span>
                                        <span><strong id="previewProtein{{ $meal->id }}">0</strong>g protein</span>
                                        <span><strong id="previewCarbs{{ $meal->id }}">0</strong>g carbs</span>
                                        <span><strong id="previewFat{{ $meal->id }}">0</strong>g fat</span>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-plus-circle"></i> Add Food
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endforeach

@push('scripts')
<script>
// Recipe Quick-Add
document.querySelectorAll('.recipe-selector').forEach(select => {
    select.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        
        if (!selected.value) {
            return; // No recipe selected
        }
        
        const modalId = this.dataset.modalId;
        const modal = this.closest('.modal');
        
        // Get recipe data from option attributes
        const recipeName = selected.dataset.name;
        const servings = selected.dataset.servings;
        const calories = selected.dataset.calories;
        const protein = selected.dataset.protein;
        const carbs = selected.dataset.carbs;
        const fat = selected.dataset.fat;
        const ingredients = selected.dataset.ingredients;
        
        // Fill in the form fields
        modal.querySelector('.meal-name-input').value = recipeName;
        modal.querySelector('.meal-serving-input').value = `1 serving (of ${servings} total)`;
        modal.querySelector('.meal-calories-input').value = calories;
        modal.querySelector('.meal-protein-input').value = protein;
        modal.querySelector('.meal-carbs-input').value = carbs;
        modal.querySelector('.meal-fat-input').value = fat;
        
        // Show success message
        const successMsg = document.getElementById(`recipeApplied${modalId}`);
        if (successMsg) {
            successMsg.style.display = 'block';
            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 3000);
        }
        
        // Scroll to the form
        modal.querySelector('.meal-name-input').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
});

// AI Nutrition Parser
document.querySelectorAll('.ai-parse-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const modalId = this.dataset.modalId;
        const textArea = document.getElementById(`aiParseInput${modalId}`);
        const resultDiv = document.getElementById(`aiParseResult${modalId}`);
        const errorDiv = document.getElementById(`aiParseError${modalId}`);
        const text = textArea.value.trim();
        
        // Validate
        if (!text) {
            alert('Please enter some food items to parse');
            return;
        }
        
        // Hide previous results/errors
        resultDiv.style.display = 'none';
        errorDiv.style.display = 'none';
        
        // Show loading
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Calculating...';
        
        // Call API
        fetch('{{ route('nutrition.parse') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ text: text })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fill in the nutrition fields
                const modal = this.closest('.modal');
                const totals = data.data.totals;
                
                // Update form fields
                modal.querySelector('input[name="calories"]').value = Math.round(totals.calories);
                modal.querySelector('input[name="protein"]').value = Math.round(totals.protein);
                modal.querySelector('input[name="carbs"]').value = Math.round(totals.carbs);
                modal.querySelector('input[name="fat"]').value = Math.round(totals.fat);
                
                // Auto-fill serving size if empty
                const servingSizeInput = modal.querySelector('input[name="serving_size"]');
                if (!servingSizeInput.value) {
                    servingSizeInput.value = text;
                }
                
                // Show success message
                resultDiv.style.display = 'block';
                
                // Auto-hide success message after 3 seconds
                setTimeout(() => {
                    resultDiv.style.display = 'none';
                }, 3000);
            } else {
                // Show error
                errorDiv.querySelector('.error-message').textContent = data.message || 'Failed to parse nutrition data';
                errorDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorDiv.querySelector('.error-message').textContent = 'Network error. Please try again.';
            errorDiv.style.display = 'block';
        })
        .finally(() => {
            // Reset button
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-magic"></i> Calculate Nutrition with AI';
        });
    });
});

// Food Selection & Nutrition Preview
document.querySelectorAll('.food-selector').forEach(select => {
    select.addEventListener('change', function() {
        const mealId = this.dataset.mealId;
        const selected = this.options[this.selectedIndex];
        
        if (!selected.value) {
            document.getElementById(`servingInfo${mealId}`).textContent = 'Select a food to see serving size';
            document.getElementById(`nutritionPreview${mealId}`).style.display = 'none';
            return;
        }
        
        // Get food data
        const defaultSize = parseFloat(selected.dataset.defaultSize);
        const defaultUnit = selected.dataset.defaultUnit;
        const calories = parseFloat(selected.dataset.calories);
        const protein = parseFloat(selected.dataset.protein);
        const carbs = parseFloat(selected.dataset.carbs);
        const fat = parseFloat(selected.dataset.fat);
        
        // Update serving info
        document.getElementById(`servingInfo${mealId}`).textContent = `1 serving = ${defaultSize}${defaultUnit}`;
        
        // Store food data for calculation
        window[`foodData${mealId}`] = { defaultSize, calories, protein, carbs, fat };
        
        // Calculate and show nutrition
        updateNutritionPreview(mealId);
    });
});

// Servings adjustment
function adjustServings(mealId, change) {
    const input = document.getElementById(`servings${mealId}`);
    const newValue = Math.max(0.5, parseFloat(input.value) + change);
    input.value = newValue.toFixed(1);
    updateNutritionPreview(mealId);
}

// Update nutrition preview
function updateNutritionPreview(mealId) {
    const foodData = window[`foodData${mealId}`];
    if (!foodData) return;
    
    const servings = parseFloat(document.getElementById(`servings${mealId}`).value);
    const grams = foodData.defaultSize * servings;
    const multiplier = grams / 100; // Nutrition is per 100g
    
    // Calculate nutrition
    const calories = Math.round(foodData.calories * multiplier);
    const protein = Math.round(foodData.protein * multiplier);
    const carbs = Math.round(foodData.carbs * multiplier);
    const fat = Math.round(foodData.fat * multiplier);
    
    // Update preview
    document.getElementById(`previewCalories${mealId}`).textContent = calories;
    document.getElementById(`previewProtein${mealId}`).textContent = protein;
    document.getElementById(`previewCarbs${mealId}`).textContent = carbs;
    document.getElementById(`previewFat${mealId}`).textContent = fat;
    document.getElementById(`nutritionPreview${mealId}`).style.display = 'block';
}

// Update servings input on change
document.querySelectorAll('[id^="servings"]').forEach(input => {
    input.addEventListener('input', function() {
        const mealId = this.id.replace('servings', '');
        updateNutritionPreview(mealId);
    });
});
</script>
@endpush
@endsection

