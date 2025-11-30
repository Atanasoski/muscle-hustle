@extends('layouts.app')

@section('title', $food->name)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('foods.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <div>
                        <h1 class="h3 fw-bold mb-0">{{ $food->name }}</h1>
                        @if($food->category)
                            <span class="badge bg-secondary mt-2">{{ ucfirst($food->category) }}</span>
                        @endif
                    </div>
                </div>
                
                @if($food->user_id === auth()->id())
                    <div class="d-flex gap-2">
                        <a href="{{ route('foods.edit', $food) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('foods.destroy', $food) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this food?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Nutrition Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient text-white py-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill me-2"></i> Nutrition (per 100g/100ml)</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Main Macros -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3 text-center">
                            <div class="h2 fw-bold text-success mb-0">{{ $food->calories ?? 0 }}</div>
                            <small class="text-muted">Calories</small>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div class="h2 fw-bold text-primary mb-0">{{ $food->protein ?? 0 }}g</div>
                            <small class="text-muted">Protein</small>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div class="h2 fw-bold text-warning mb-0">{{ $food->carbs ?? 0 }}g</div>
                            <small class="text-muted">Carbs</small>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <div class="h2 fw-bold text-danger mb-0">{{ $food->fat ?? 0 }}g</div>
                            <small class="text-muted">Fat</small>
                        </div>
                    </div>

                    <!-- Additional Nutrients -->
                    @if($food->fiber || $food->sugar)
                        <hr class="my-4">
                        <div class="row g-3">
                            @if($food->fiber)
                                <div class="col-6 text-center">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="fw-bold">{{ $food->fiber }}g</div>
                                        <small class="text-muted">Fiber</small>
                                    </div>
                                </div>
                            @endif
                            @if($food->sugar)
                                <div class="col-6 text-center">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="fw-bold">{{ $food->sugar }}g</div>
                                        <small class="text-muted">Sugar</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Default Serving -->
            @if($food->default_serving_size)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-rulers me-2"></i> Default Serving</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-0 fs-5">
                            <strong>{{ $food->default_serving_size }}</strong> {{ $food->default_serving_unit ?? 'g' }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Type Badge -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-{{ $food->user_id ? 'person-fill' : 'globe' }} fs-3 text-{{ $food->user_id ? 'success' : 'info' }}"></i>
                        <div>
                            <h6 class="fw-bold mb-0">
                                {{ $food->user_id ? 'Custom Food' : 'Global Database' }}
                            </h6>
                            <small class="text-muted">
                                {{ $food->user_id ? 'Added by you' : 'Available to all users' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

