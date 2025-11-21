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
                    <div class="row g-3 g-md-4">
                        @foreach($types as $type)
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="meal-card-container">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-{{ $typeIcons[$type] }} text-success fs-5"></i>
                                        <strong class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #6c757d;">{{ $type }}</strong>
                                    </div>
                                    
                                    @if(isset($mealGrid[$dayIndex][$type]) && $mealGrid[$dayIndex][$type])
                                        @php $meal = $mealGrid[$dayIndex][$type]; @endphp
                                        <div class="meal-card p-2 rounded-3 bg-light border h-100">
                                            <div class="fw-bold small mb-1">{{ $meal->name }}</div>
                                            @if($meal->serving_size)
                                                <div class="small text-muted mb-1">
                                                    <i class="bi bi-rulers"></i> {{ $meal->serving_size }}
                                                </div>
                                            @endif
                                            @if($meal->calories)
                                                <div class="small text-muted mb-1">
                                                    <i class="bi bi-fire"></i> {{ $meal->calories }} cal
                                                </div>
                                            @endif
                                            @if($meal->protein || $meal->carbs || $meal->fat)
                                                <div class="small text-muted mb-2" style="font-size: 0.75rem;">
                                                    P:{{ $meal->protein }}g C:{{ $meal->carbs }}g F:{{ $meal->fat }}g
                                                </div>
                                            @endif
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-danger flex-fill" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editMealModal{{ $dayIndex }}{{ $type }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form action="{{ route('planner.meals.destroy', $meal) }}" 
                                                      method="POST" class="flex-fill" 
                                                      onsubmit="return confirm('Delete this meal?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <button class="btn btn-sm btn-outline-success w-100" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addMealModal{{ $dayIndex }}{{ $type }}">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
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
.meal-card-container {
    min-height: 100px;
}
.meal-card {
    transition: transform 0.2s;
}
.meal-card:hover {
    transform: translateY(-2px);
}
</style>
@endpush
@endsection

