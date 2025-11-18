@extends('layouts.app')

@section('title', 'Weekly Meal Planner')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="bi bi-egg-fried"></i> Weekly Meal Planner</h1>
    <p class="text-muted mb-4">Week starting: {{ $weekStart->format('M d, Y') }}</p>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th width="12%">Day</th>
                    <th width="22%">Breakfast</th>
                    <th width="22%">Lunch</th>
                    <th width="22%">Dinner</th>
                    <th width="22%">Snack</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $types = ['breakfast', 'lunch', 'dinner', 'snack'];
                @endphp
                
                @foreach($days as $dayIndex => $dayName)
                    <tr>
                        <td class="fw-bold">{{ $dayName }}</td>
                        @foreach($types as $type)
                            <td>
                                @if(isset($mealGrid[$dayIndex][$type]) && $mealGrid[$dayIndex][$type])
                                    @php $meal = $mealGrid[$dayIndex][$type]; @endphp
                                    <div class="meal-card">
                                        <strong>{{ $meal->name }}</strong>
                                        @if($meal->calories)
                                            <br><small class="text-muted">{{ $meal->calories }} cal</small>
                                        @endif
                                        @if($meal->protein || $meal->carbs || $meal->fat)
                                            <br><small class="text-muted">
                                                P:{{ $meal->protein }}g C:{{ $meal->carbs }}g F:{{ $meal->fat }}g
                                            </small>
                                        @endif
                                        <div class="mt-2">
                                            <button class="btn btn-xs btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editMealModal{{ $dayIndex }}{{ $type }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('planner.meals.destroy', $meal) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Delete this meal?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
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

                                <!-- Add/Edit Modal -->
                                <div class="modal fade" id="{{ isset($mealGrid[$dayIndex][$type]) && $mealGrid[$dayIndex][$type] ? 'editMealModal' : 'addMealModal' }}{{ $dayIndex }}{{ $type }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('planner.meals.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="day_of_week" value="{{ $dayIndex }}">
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ ucfirst($type) }} - {{ $dayName }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Meal Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name" 
                                                               value="{{ $mealGrid[$dayIndex][$type]->name ?? '' }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Calories</label>
                                                        <input type="number" class="form-control" name="calories" 
                                                               value="{{ $mealGrid[$dayIndex][$type]->calories ?? '' }}" min="0">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-4 mb-3">
                                                            <label class="form-label">Protein (g)</label>
                                                            <input type="number" class="form-control" name="protein" 
                                                                   value="{{ $mealGrid[$dayIndex][$type]->protein ?? '' }}" min="0">
                                                        </div>
                                                        <div class="col-4 mb-3">
                                                            <label class="form-label">Carbs (g)</label>
                                                            <input type="number" class="form-control" name="carbs" 
                                                                   value="{{ $mealGrid[$dayIndex][$type]->carbs ?? '' }}" min="0">
                                                        </div>
                                                        <div class="col-4 mb-3">
                                                            <label class="form-label">Fat (g)</label>
                                                            <input type="number" class="form-control" name="fat" 
                                                                   value="{{ $mealGrid[$dayIndex][$type]->fat ?? '' }}" min="0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Meal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
.meal-card {
    padding: 8px;
    background: #f8f9fa;
    border-radius: 4px;
}
.btn-xs {
    padding: 2px 6px;
    font-size: 0.75rem;
}
</style>
@endpush
@endsection

