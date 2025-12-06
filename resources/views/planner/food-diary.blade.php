@extends('layouts.app')

@section('title', 'Food Diary')

@section('content')
<div class="container py-3 py-md-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 h2-md fw-bold mb-2">
                    <i class="bi bi-journal-text text-success"></i> Food Diary
                </h1>
                <p class="text-muted mb-0 small">What you ate this week ({{ $weekStart->format('M d') }} - {{ $weekStart->copy()->addDays(6)->format('M d, Y') }})</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('planner.meals') }}" class="btn btn-outline-success">
                    <i class="bi bi-calendar3 me-2"></i> Meal Planner
                </a>
            </div>
        </div>
    </div>

    @if($weeklyTotals['foods_logged'] == 0)
        <!-- Empty State -->
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-journal-x text-muted mb-3" style="font-size: 4rem;"></i>
                <h4 class="text-muted mb-3">No Foods Logged Yet</h4>
                <p class="text-muted mb-4">Start logging your meals to see what you've eaten this week!</p>
                <a href="{{ route('planner.meals') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i> Go to Meal Planner
                </a>
            </div>
        </div>
    @else
        <!-- Daily Logs -->
        <div class="d-flex flex-column gap-4">
            @foreach($logsByDay as $dayData)
                @if(count($dayData['meals']) > 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0 fw-bold text-white">
                                        <span class="me-2">{{ $dayData['emoji'] }}</span>
                                        {{ $dayData['name'] }}
                                    </h5>
                                    <small class="text-white-50">{{ $dayData['date']->format('M d, Y') }}</small>
                                </div>
                                <span class="badge bg-white text-success fs-6">
                                    {{ round($dayData['totals']['calories']) }} cal
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            @php
                                $typeIcons = [
                                    'breakfast' => 'sunrise',
                                    'lunch' => 'sun',
                                    'dinner' => 'moon-stars',
                                    'snack' => 'cup-straw'
                                ];
                            @endphp

                            @foreach($dayData['meals'] as $type => $mealData)
                                <div class="mb-4 pb-4 border-bottom last-child-no-border">
                                    <!-- Meal Type Header -->
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-{{ $typeIcons[$type] }} text-success fs-4"></i>
                                            <span class="text-uppercase fw-bold text-success" style="font-size: 0.9rem; letter-spacing: 1px;">{{ $type }}</span>
                                        </div>
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            {{ round($mealData['nutrition']['calories']) }} cal
                                        </span>
                                    </div>

                                    <!-- Logged Foods -->
                                    <div class="ms-4">
                                        @foreach($mealData['meal']->foods as $food)
                                            @php
                                                $grams = $food->pivot->grams;
                                                $multiplier = $grams / 100;
                                                $calories = round($food->calories * $multiplier);
                                                $protein = round($food->protein * $multiplier);
                                                $carbs = round($food->carbs * $multiplier);
                                                $fat = round($food->fat * $multiplier);
                                            @endphp
                                            
                                            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge rounded-pill" style="background-color: {{ $food->category->color ?? '#6c757d' }};">
                                                            {{ $food->category->icon ?? '🍽️' }}
                                                        </span>
                                                        <strong>{{ $food->name }}</strong>
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $food->pivot->servings }} × {{ $food->default_serving_size }}{{ $food->default_serving_unit }} 
                                                        <span class="text-muted">({{ number_format($grams, 0) }}g total)</span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-success">{{ $calories }} cal</div>
                                                    <div class="small text-muted">
                                                        P: {{ $protein }}g | C: {{ $carbs }}g | F: {{ $fat }}g
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Meal Macros Summary -->
                                    <div class="d-flex flex-wrap gap-2 ms-4 mt-2">
                                        <span class="badge rounded-pill border border-primary text-primary bg-white px-3 py-2">
                                            <i class="bi bi-lightning-charge-fill" style="font-size: 0.7rem;"></i> {{ round($mealData['nutrition']['protein']) }}g protein
                                        </span>
                                        <span class="badge rounded-pill border border-warning text-warning bg-white px-3 py-2">
                                            <i class="bi bi-droplet-fill" style="font-size: 0.7rem;"></i> {{ round($mealData['nutrition']['carbs']) }}g carbs
                                        </span>
                                        <span class="badge rounded-pill border border-danger text-danger bg-white px-3 py-2">
                                            <i class="bi bi-circle-fill" style="font-size: 0.7rem;"></i> {{ round($mealData['nutrition']['fat']) }}g fat
                                        </span>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Daily Totals -->
                            <div class="mt-3 pt-3 border-top">
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fw-bold text-success">{{ round($dayData['totals']['calories']) }}</div>
                                            <small class="text-muted">Calories</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fw-bold text-primary">{{ round($dayData['totals']['protein']) }}g</div>
                                            <small class="text-muted">Protein</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fw-bold text-warning">{{ round($dayData['totals']['carbs']) }}g</div>
                                            <small class="text-muted">Carbs</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fw-bold text-danger">{{ round($dayData['totals']['fat']) }}g</div>
                                            <small class="text-muted">Fat</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Weekly Summary -->
        <div class="card border-0 shadow-sm mt-4 bg-success text-white">
            <div class="card-body p-4">
                <h5 class="mb-4 text-white">
                    <i class="bi bi-graph-up-arrow me-2"></i> Weekly Summary
                </h5>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="h3 mb-1 fw-bold text-white">{{ number_format($weeklyTotals['calories']) }}</div>
                            <small class="text-white-50">Total Calories</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="h3 mb-1 fw-bold text-white">{{ number_format($weeklyTotals['avg_calories']) }}</div>
                            <small class="text-white-50">Avg/Day</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="h3 mb-1 fw-bold text-white">{{ round($weeklyTotals['protein']) }}g</div>
                            <small class="text-white-50">Protein</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="h3 mb-1 fw-bold text-white">{{ $weeklyTotals['foods_logged'] }}</div>
                            <small class="text-white-50">Foods Logged</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="h3 mb-1 fw-bold text-white">{{ round($weeklyTotals['carbs']) }}g</div>
                            <small class="text-white-50">Carbs</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="h3 mb-1 fw-bold text-white">{{ round($weeklyTotals['fat']) }}g</div>
                            <small class="text-white-50">Fat</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.last-child-no-border:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
}
</style>
@endsection

