@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="fw-bold mb-1">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Welcome back, {{ Auth::user()->name }}!
            </h2>
            <p class="text-muted">Let's crush your fitness goals today</p>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card bg-warning text-white border-0 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-light bg-opacity-25 rounded p-2 me-3">
                        <i class="bi bi-calendar-check fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $weekWorkouts->count() }}</h4>
                        <small class="opacity-75">Workouts This Week</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-dark text-white border-0 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-light bg-opacity-25 rounded p-2 me-3">
                        <i class="bi bi-fire fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $streak }}</h4>
                        <small class="opacity-75">Day Streak</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-success text-white border-0 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-light bg-opacity-25 rounded p-2 me-3">
                        <i class="bi bi-trophy fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $weekWorkouts->sum(function($w) { return $w->exercises->count(); }) }}</h4>
                        <small class="opacity-75">Exercises Planned</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-info text-white border-0 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-light bg-opacity-25 rounded p-2 me-3">
                        <i class="bi bi-journal-text fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ Auth::user()->workoutTemplates->count() }}</h4>
                        <small class="opacity-75">Templates</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Today's Workout -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-warning text-white border-0 py-2">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-day-fill me-2"></i> Today's Workout
                    </h5>
                </div>
                <div class="card-body p-3">
                    @if($todayWorkout)
                        <div class="workout-card">
                            <h5 class="fw-bold mb-1">{{ $todayWorkout->name }}</h5>
                            <p class="text-muted mb-2 small">{{ $todayWorkout->description }}</p>
                            
                            @if($todayWorkout->exercises->count() > 0)
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="badge bg-light text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-list-check me-1"></i>
                                        <strong>{{ $todayWorkout->exercises->count() }}</strong> exercises
                                    </div>
                                    <div class="badge bg-light text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-clock me-1"></i>
                                        ~{{ $todayWorkout->exercises->count() * 5 }} min
                                    </div>
                                </div>
                                
                                <!-- Exercise List -->
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-2 text-muted small">
                                        <i class="bi bi-list-ul"></i> Exercise Plan
                                    </h6>
                                    <div class="exercise-list">
                                        @foreach($todayWorkout->workoutTemplateExercises->sortBy('order') as $index => $templateExercise)
                                            <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded" style="background: rgba(0,0,0,0.02);">
                                                <div class="fw-bold" style="min-width: 25px; color: #ff6b35; font-size: 1rem;">{{ $index + 1 }}</div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold">{{ $templateExercise->exercise->name }}</div>
                                                    <small class="text-muted">
                                                        <span class="fw-bold" style="color: #ff6b35;">{{ $templateExercise->target_sets }}×{{ $templateExercise->target_reps }}</span>
                                                        @if($templateExercise->target_weight)
                                                            @ <span class="fw-bold" style="color: #ff6b35;">{{ $templateExercise->target_weight }}kg</span>
                                                        @endif
                                                    </small>
                                                </div>
                                                <span class="badge bg-secondary">{{ $templateExercise->exercise->category->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                @if(!$todayWorkoutCompleted)
                                    <form action="{{ route('workouts.start') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="template_id" value="{{ $todayWorkout->id }}">
                                        <button type="submit" class="btn btn-success w-100 py-2 shadow-sm">
                                            <i class="bi bi-play-circle-fill me-2"></i> Start Workout
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-success border-0 shadow-sm mb-0 py-2">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <strong>Workout Completed!</strong> Great job today! 💪
                                    </div>
                                @endif
                                
                                @if($recentWorkouts->count() > 0)
                                    <div class="mt-3">
                                        <h6 class="fw-bold mb-2 small">
                                            <i class="bi bi-clock-history text-muted"></i> Recent Workouts
                                        </h6>
                                        @foreach($recentWorkouts as $workout)
                                            <div class="p-2 bg-light border rounded-3 mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <p class="mb-1 fw-bold">{{ $workout->workoutTemplate ? $workout->workoutTemplate->name : 'Free Workout' }}</p>
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar3"></i> {{ $workout->performed_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-success">
                                                            {{ $workout->setLogs->count() }} sets
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning border-0 mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    No exercises added yet to this template.
                                </div>
                                <a href="{{ route('workout-templates.edit', $todayWorkout) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle me-2"></i> Add Exercises
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x fs-1 text-muted mb-2"></i>
                            <h6 class="text-muted mb-2">No workout scheduled for today</h6>
                            <p class="text-muted small mb-3">Rest day or time to plan your week?</p>
                            <a href="{{ route('planner.workouts') }}" class="btn btn-primary">
                                <i class="bi bi-calendar-plus me-2"></i> Plan Your Week
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Today's Meals -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-success text-white border-0 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-egg-fried me-2"></i> Today's Nutrition
                        </h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#quickLogMealModal">
                            <i class="bi bi-plus-circle me-1"></i> Quick Log
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($todayMeals->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($todayMeals as $meal)
                                <div class="p-2 rounded-3 border bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-success mb-2">
                                                {{ ucfirst($meal->type) }}
                                            </span>
                                            <h6 class="fw-bold mb-0">{{ $meal->name }}</h6>
                                        </div>
                                    </div>
                                    
                                    @if($meal->serving_size)
                                        <div class="mb-2">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="bi bi-rulers text-muted" style="font-size: 0.85rem;"></i>
                                                <div class="text-muted small lh-sm">{{ $meal->serving_size }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex gap-2 flex-wrap mt-2">
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-fire"></i> {{ $meal->calories }} cal
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-lightning"></i> {{ $meal->protein }}g protein
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-droplet"></i> {{ $meal->carbs }}g carbs
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-circle"></i> {{ $meal->fat }}g fat
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="alert alert-success border-0 mb-0">
                                <strong>Daily Totals:</strong>
                                {{ $todayMeals->sum('calories') }} cal • {{ $todayMeals->sum('protein') }}g protein • {{ $todayMeals->sum('carbs') }}g carbs • {{ $todayMeals->sum('fat') }}g fat
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-egg fs-1 text-muted mb-2"></i>
                            <h6 class="text-muted mb-3">No meals planned for today</h6>
                            <a href="{{ route('planner.meals') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i> Plan Your Meals
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-range-fill me-2"></i> This Week's Training Plan
                    </h5>
                </div>
                <div class="card-body p-3">
                    @if($weekWorkouts->count() > 0)
                        <div class="row g-3">
                            @php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            @endphp
                            @foreach($days as $index => $day)
                                @php
                                    $workout = $weekWorkouts->firstWhere('day_of_week', $index);
                                    $isToday = $index === $dayOfWeek;
                                @endphp
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    @if($workout)
                                        <a href="{{ route('workout-templates.edit', $workout) }}" class="text-decoration-none">
                                            <div class="card h-100 border-2 {{ $isToday ? 'border-warning' : 'border-secondary' }}">
                                                <div class="card-header bg-warning text-white p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div class="fw-bold">{{ $day }}</div>
                                                    @if($isToday)
                                                        <span class="badge bg-primary">Today</span>
                                                    @endif
                                                </div>
                                                <div class="card-body p-3">
                                                    <h6 class="fw-semibold text-dark mb-1">{{ $workout->name }}</h6>
                                                    @if($workout->description)
                                                        <p class="text-muted small mb-2">{{ $workout->description }}</p>
                                                    @endif
                                                    <div class="text-secondary small">
                                                        <i class="bi bi-list-check"></i> {{ $workout->exercises->count() }} exercises
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <div class="card h-100 border-2 {{ $isToday ? 'border-warning' : '' }}">
                                            <div class="card-header bg-secondary text-white p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="fw-bold">{{ $day }}</div>
                                                @if($isToday)
                                                    <span class="badge bg-primary">Today</span>
                                                @endif
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="text-secondary fst-italic small">
                                                    <i class="bi bi-moon-stars-fill"></i> Rest & Recovery
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x fs-1 text-muted mb-2"></i>
                            <h6 class="text-muted mb-2">No workouts planned this week</h6>
                            <p class="text-muted small mb-3">Get started by planning your weekly training schedule</p>
                            <a href="{{ route('planner.workouts') }}" class="btn btn-primary">
                                <i class="bi bi-calendar-plus me-2"></i> Plan Your Week
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Quick Log Meal Modal -->
<div class="modal fade" id="quickLogMealModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('planner.meals.store') }}" method="POST">
                @csrf
                <input type="hidden" name="day_of_week" value="{{ $dayOfWeek }}">
                
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-lightning-charge-fill me-2"></i>
                        Quick Log Meal for Today
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Recipe Quick-Select (if user has recipes) -->
                    @if($userRecipes->isNotEmpty())
                        <div class="mb-4 p-3 rounded-3" style="background: linear-gradient(135deg, rgba(40,167,69,0.05) 0%, rgba(32,201,151,0.05) 100%); border: 1px solid rgba(40,167,69,0.2);">
                            <label class="form-label fw-bold">
                                <i class="bi bi-book-fill text-success"></i> Select from Saved Recipes
                            </label>
                            <select class="form-select" id="quickLogRecipeSelect">
                                <option value="">Choose a recipe...</option>
                                @foreach($userRecipes as $recipe)
                                    @php $nutrition = $recipe->getNutritionPerServing(); @endphp
                                    <option value="{{ $recipe->id }}" 
                                            data-name="{{ $recipe->name }}"
                                            data-servings="{{ $recipe->servings }}"
                                            data-calories="{{ round($nutrition['calories']) }}"
                                            data-protein="{{ round($nutrition['protein']) }}"
                                            data-carbs="{{ round($nutrition['carbs']) }}"
                                            data-fat="{{ round($nutrition['fat']) }}"
                                            data-ingredients="{{ $recipe->recipeIngredients->pluck('food.name')->implode(', ') }}"
                                            data-meal-type="{{ $recipe->meal_type }}">
                                        {{ $recipe->is_favorite ? '⭐ ' : '' }}{{ $recipe->name }} ({{ round($nutrition['calories']) }} cal)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <!-- AI Nutrition Calculator -->
                    <div class="mb-4 p-3 rounded-3" style="background: linear-gradient(135deg, rgba(255,107,53,0.05) 0%, rgba(255,140,97,0.05) 100%); border: 1px solid rgba(255,107,53,0.2);">
                        <label class="form-label fw-bold">
                            <i class="bi bi-stars text-warning"></i> AI Nutrition Calculator
                        </label>
                        <p class="text-muted small mb-3">Describe what you ate and let AI calculate the nutrition!</p>
                        <textarea class="form-control mb-2" id="quickLogAiInput" rows="3" 
                                  placeholder="Example: 200g grilled chicken breast, 1 cup brown rice, steamed broccoli, 1 tbsp olive oil"></textarea>
                        <button type="button" class="btn btn-warning btn-sm w-100" id="quickLogAiBtn">
                            <i class="bi bi-magic me-2"></i> Calculate with AI
                        </button>
                        <div class="mt-2" id="quickLogAiLoading" style="display: none;">
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="ms-2 small">Analyzing...</span>
                            </div>
                        </div>
                        <div class="alert alert-success mt-2" id="quickLogAiSuccess" style="display: none;">
                            <i class="bi bi-check-circle me-2"></i> Nutrition calculated and filled!
                        </div>
                        <div class="alert alert-danger mt-2" id="quickLogAiError" style="display: none;">
                            <i class="bi bi-exclamation-triangle me-2"></i> <span class="error-text"></span>
                        </div>
                    </div>
                    
                    <!-- Meal Type -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Meal Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" id="quickLogMealType" required>
                            <option value="">Select type...</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>
                    
                    <!-- Meal Name -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Meal Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="quickLogMealName" 
                               placeholder="e.g., Chicken & Rice Bowl" required>
                    </div>
                    
                    <!-- Serving Size -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Serving / Portions</label>
                        <input type="text" class="form-control" name="serving_size" id="quickLogServingSize"
                               placeholder="e.g., 1 bowl, 300g total">
                    </div>
                    
                    <!-- Nutrition -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Calories</label>
                        <input type="number" class="form-control" name="calories" id="quickLogCalories" min="0">
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label fw-bold small">Protein (g)</label>
                            <input type="number" class="form-control" name="protein" id="quickLogProtein" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold small">Carbs (g)</label>
                            <input type="number" class="form-control" name="carbs" id="quickLogCarbs" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold small">Fat (g)</label>
                            <input type="number" class="form-control" name="fat" id="quickLogFat" min="0">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Log Meal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Recipe Quick-Select for Dashboard
document.getElementById('quickLogRecipeSelect')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    
    if (!selected.value) {
        return;
    }
    
    // Fill in the form
    document.getElementById('quickLogMealName').value = selected.dataset.name;
    document.getElementById('quickLogServingSize').value = `1 serving (of ${selected.dataset.servings} total)`;
    document.getElementById('quickLogCalories').value = selected.dataset.calories;
    document.getElementById('quickLogProtein').value = selected.dataset.protein;
    document.getElementById('quickLogCarbs').value = selected.dataset.carbs;
    document.getElementById('quickLogFat').value = selected.dataset.fat;
    
    // Set meal type if available
    if (selected.dataset.mealType) {
        document.getElementById('quickLogMealType').value = selected.dataset.mealType;
    }
});

// AI Nutrition Calculator for Dashboard
document.getElementById('quickLogAiBtn')?.addEventListener('click', function() {
    const text = document.getElementById('quickLogAiInput').value.trim();
    const loadingDiv = document.getElementById('quickLogAiLoading');
    const successDiv = document.getElementById('quickLogAiSuccess');
    const errorDiv = document.getElementById('quickLogAiError');
    
    if (!text) {
        errorDiv.querySelector('.error-text').textContent = 'Please enter what you ate';
        errorDiv.style.display = 'block';
        return;
    }
    
    // Reset states
    successDiv.style.display = 'none';
    errorDiv.style.display = 'none';
    loadingDiv.style.display = 'block';
    this.disabled = true;
    
    // Call API
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
        if (data.totals) {
            // Fill form
            document.getElementById('quickLogCalories').value = Math.round(data.totals.calories);
            document.getElementById('quickLogProtein').value = Math.round(data.totals.protein);
            document.getElementById('quickLogCarbs').value = Math.round(data.totals.carbs);
            document.getElementById('quickLogFat').value = Math.round(data.totals.fat);
            
            // Auto-fill meal name if empty
            if (!document.getElementById('quickLogMealName').value && data.items && data.items.length > 0) {
                const itemNames = data.items.map(item => item.food).slice(0, 3).join(', ');
                document.getElementById('quickLogMealName').value = itemNames;
            }
            
            // Auto-fill serving size if empty
            if (!document.getElementById('quickLogServingSize').value) {
                document.getElementById('quickLogServingSize').value = text;
            }
            
            successDiv.style.display = 'block';
            
            // Auto-hide success after 3s
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 3000);
        } else if (data.message) {
            errorDiv.querySelector('.error-text').textContent = data.message;
            errorDiv.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.querySelector('.error-text').textContent = 'An error occurred. Please try again.';
        errorDiv.style.display = 'block';
    })
    .finally(() => {
        loadingDiv.style.display = 'none';
        this.disabled = false;
    });
});
</script>
@endpush

@endsection
