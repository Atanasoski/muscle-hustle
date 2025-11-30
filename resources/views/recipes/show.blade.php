@extends('layouts.app')

@section('title', $recipe->name)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <div>
                        <h1 class="h3 fw-bold mb-0">
                            {{ $recipe->name }}
                            @if($recipe->is_favorite)
                                <i class="bi bi-heart-fill text-danger ms-2"></i>
                            @endif
                        </h1>
                        @if($recipe->description)
                            <p class="text-muted small mb-0">{{ $recipe->description }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this recipe permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Column: Recipe Details -->
                <div class="col-lg-7">
                    <!-- Quick Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="row g-3 text-center">
                                <div class="col-4">
                                    <div class="fw-bold text-primary fs-4">{{ $recipe->servings }}</div>
                                    <small class="text-muted">Servings</small>
                                </div>
                                @if($recipe->prep_time_minutes)
                                    <div class="col-4">
                                        <div class="fw-bold text-success fs-4">{{ $recipe->prep_time_minutes }}m</div>
                                        <small class="text-muted">Prep Time</small>
                                    </div>
                                @endif
                                @if($recipe->cook_time_minutes)
                                    <div class="col-4">
                                        <div class="fw-bold text-warning fs-4">{{ $recipe->cook_time_minutes }}m</div>
                                        <small class="text-muted">Cook Time</small>
                                    </div>
                                @endif
                            </div>
                            
                            @if($recipe->meal_type || $recipe->tags)
                                <hr>
                                <div class="d-flex flex-wrap gap-2">
                                    @if($recipe->meal_type)
                                        <span class="badge bg-success">{{ ucfirst($recipe->meal_type) }}</span>
                                    @endif
                                    @if($recipe->tags)
                                        @foreach($recipe->tags as $tag)
                                            <span class="badge bg-secondary">{{ $tag }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ingredients -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i> Ingredients</h5>
                        </div>
                        <div class="card-body p-4">
                            @if($recipe->recipeIngredients->isEmpty())
                                <p class="text-muted mb-0">No ingredients added yet.</p>
                            @else
                                <ul class="list-unstyled mb-0">
                                    @foreach($recipe->recipeIngredients as $ingredient)
                                        <li class="mb-2 pb-2 border-bottom">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>{{ $ingredient->food->name }}</strong>
                                                    @if($ingredient->notes)
                                                        <small class="text-muted">({{ $ingredient->notes }})</small>
                                                    @endif
                                                </div>
                                                <span class="badge bg-light text-dark">{{ $ingredient->quantity }} {{ $ingredient->unit }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- Instructions -->
                    @if($recipe->instructions)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-bold mb-0"><i class="bi bi-card-text me-2"></i> Instructions</h5>
                            </div>
                            <div class="card-body p-4">
                                <div style="white-space: pre-wrap;">{{ $recipe->instructions }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Nutrition -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm mb-4 position-sticky" style="top: 20px;">
                        <div class="card-header bg-gradient text-white py-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill me-2"></i> Nutrition Per Serving</h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Calories -->
                            <div class="text-center mb-4 pb-4 border-bottom">
                                <div class="display-4 fw-bold text-success">{{ round($nutrition['calories']) }}</div>
                                <div class="text-muted">calories</div>
                            </div>

                            <!-- Macros -->
                            <div class="row g-3 mb-4">
                                <div class="col-4 text-center">
                                    <div class="h4 fw-bold text-primary mb-0">{{ round($nutrition['protein']) }}g</div>
                                    <small class="text-muted">Protein</small>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="h4 fw-bold text-warning mb-0">{{ round($nutrition['carbs']) }}g</div>
                                    <small class="text-muted">Carbs</small>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="h4 fw-bold text-danger mb-0">{{ round($nutrition['fat']) }}g</div>
                                    <small class="text-muted">Fat</small>
                                </div>
                            </div>

                            <!-- Fiber & Sugar -->
                            @if($nutrition['fiber'] > 0 || $nutrition['sugar'] > 0)
                                <div class="row g-3">
                                    @if($nutrition['fiber'] > 0)
                                        <div class="col-6">
                                            <div class="bg-light rounded-3 p-3 text-center">
                                                <div class="fw-bold">{{ round($nutrition['fiber']) }}g</div>
                                                <small class="text-muted">Fiber</small>
                                            </div>
                                        </div>
                                    @endif
                                    @if($nutrition['sugar'] > 0)
                                        <div class="col-6">
                                            <div class="bg-light rounded-3 p-3 text-center">
                                                <div class="fw-bold">{{ round($nutrition['sugar']) }}g</div>
                                                <small class="text-muted">Sugar</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Total Nutrition -->
                            <hr class="my-4">
                            <div class="text-center">
                                <small class="text-muted">Total recipe nutrition ({{ $recipe->servings }} servings)</small>
                                @php
                                    $totalNutrition = $recipe->getTotalNutrition();
                                @endphp
                                <div class="mt-2">
                                    <span class="badge bg-light text-dark me-2">{{ round($totalNutrition['calories']) }} cal</span>
                                    <span class="badge bg-light text-dark me-2">{{ round($totalNutrition['protein']) }}g protein</span>
                                    <span class="badge bg-light text-dark me-2">{{ round($totalNutrition['carbs']) }}g carbs</span>
                                    <span class="badge bg-light text-dark">{{ round($totalNutrition['fat']) }}g fat</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

