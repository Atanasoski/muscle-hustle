@extends('layouts.app')

@section('title', 'Edit Food')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <x-button variant="cancel" size="sm" href="{{ route('foods.index') }}" class="btn-outline-secondary me-3" icon="bi-arrow-left">
                    Back
                </x-button>
                <div>
                    <h1 class="h3 fw-bold mb-0">Edit Food</h1>
                    <p class="text-muted small mb-0">{{ $food->name }}</p>
                </div>
            </div>

            <form action="{{ route('foods.update', $food) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i> Basic Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-bold">Food Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $food->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="category_id" class="form-label fw-bold">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $food->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->icon }} {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nutrition -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-heart-pulse me-2"></i> Nutrition (per 100g/100ml)
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="calories" class="form-label fw-bold">Calories</label>
                                <input type="number" step="0.1" class="form-control" id="calories" name="calories" 
                                       value="{{ old('calories', $food->calories) }}" min="0">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="protein" class="form-label fw-bold">Protein (g)</label>
                                <input type="number" step="0.1" class="form-control" id="protein" name="protein" 
                                       value="{{ old('protein', $food->protein) }}" min="0">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="carbs" class="form-label fw-bold">Carbohydrates (g)</label>
                                <input type="number" step="0.1" class="form-control" id="carbs" name="carbs" 
                                       value="{{ old('carbs', $food->carbs) }}" min="0">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fat" class="form-label fw-bold">Fat (g)</label>
                                <input type="number" step="0.1" class="form-control" id="fat" name="fat" 
                                       value="{{ old('fat', $food->fat) }}" min="0">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fiber" class="form-label fw-bold">Fiber (g)</label>
                                <input type="number" step="0.1" class="form-control" id="fiber" name="fiber" 
                                       value="{{ old('fiber', $food->fiber) }}" min="0">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="sugar" class="form-label fw-bold">Sugar (g)</label>
                                <input type="number" step="0.1" class="form-control" id="sugar" name="sugar" 
                                       value="{{ old('sugar', $food->sugar) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Default Serving -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-rulers me-2"></i> Default Serving</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="default_serving_size" class="form-label fw-bold">Serving Size</label>
                                <input type="number" step="0.01" class="form-control" id="default_serving_size" 
                                       name="default_serving_size" value="{{ old('default_serving_size', $food->default_serving_size) }}" min="0">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="default_serving_unit" class="form-label fw-bold">Serving Unit</label>
                                <input type="text" class="form-control" id="default_serving_unit" 
                                       name="default_serving_unit" value="{{ old('default_serving_unit', $food->default_serving_unit) }}" 
                                       placeholder="e.g., g, ml, cup, piece">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-3">
                    <x-button variant="save" type="submit" size="lg" class="flex-fill">
                        Update Food
                    </x-button>
                    <x-button variant="cancel" size="lg" href="{{ route('foods.index') }}" class="btn-outline-secondary">Cancel</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

