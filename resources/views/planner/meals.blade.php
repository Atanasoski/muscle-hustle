@extends('layouts.app')

@section('title', 'Weekly Meal Planner')

@section('content')
<div class="container py-3 py-md-4">
    <div class="mb-4">
        <h1 class="h3 h2-md fw-bold mb-2">
            <i class="bi bi-egg-fried text-success"></i> Weekly Meal Planner
        </h1>
        <p class="text-muted mb-0 small">Week starting: {{ $weekStart->format('M d, Y') }}</p>
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
                                <div class="meal-type-card border rounded-3 p-3 h-100 bg-white">
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
                                                <div class="macro-badge protein">
                                                    <i class="bi bi-lightning-charge-fill"></i> {{ $meal->protein }}g protein
                                                </div>
                                                <div class="macro-badge carbs">
                                                    <i class="bi bi-droplet-fill"></i> {{ $meal->carbs }}g carbs
                                                </div>
                                                <div class="macro-badge fat">
                                                    <i class="bi bi-circle-fill"></i> {{ $meal->fat }}g fat
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Actions -->
                                        <div class="d-flex gap-2 mt-auto">
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
                            <div class="mb-3">
                                <label class="form-label fw-bold">Meal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" 
                                       value="{{ $mealGrid[$dayIndex][$type]->name ?? '' }}" 
                                       placeholder="e.g., Grilled Chicken Salad" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Serving Size / Portions</label>
                                <input type="text" class="form-control" name="serving_size" 
                                       value="{{ $mealGrid[$dayIndex][$type]->serving_size ?? '' }}" 
                                       placeholder="e.g., 200g salmon, 150g sweet potato, 100g broccoli">
                                <small class="text-muted">Describe the portions/quantities</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Calories</label>
                                <input type="number" class="form-control" name="calories" 
                                       value="{{ $mealGrid[$dayIndex][$type]->calories ?? '' }}" 
                                       placeholder="e.g., 450" min="0">
                            </div>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label fw-bold small">Protein (g)</label>
                                    <input type="number" class="form-control" name="protein" 
                                           value="{{ $mealGrid[$dayIndex][$type]->protein ?? '' }}" 
                                           placeholder="35" min="0">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-bold small">Carbs (g)</label>
                                    <input type="number" class="form-control" name="carbs" 
                                           value="{{ $mealGrid[$dayIndex][$type]->carbs ?? '' }}" 
                                           placeholder="45" min="0">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-bold small">Fat (g)</label>
                                    <input type="number" class="form-control" name="fat" 
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

@push('styles')
<style>
.meal-type-card {
    transition: all 0.2s ease;
    min-height: 220px;
    display: flex;
    flex-direction: column;
}
.meal-type-card:hover {
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}
.macro-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1.5px solid;
    background: white;
    white-space: nowrap;
}
.macro-badge.protein {
    color: #0d6efd;
    border-color: #0d6efd;
}
.macro-badge.carbs {
    color: #ffc107;
    border-color: #ffc107;
}
.macro-badge.fat {
    color: #dc3545;
    border-color: #dc3545;
}
.macro-badge i {
    font-size: 0.65rem;
}
</style>
@endpush
@endsection

