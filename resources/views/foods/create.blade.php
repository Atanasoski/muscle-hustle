@extends('layouts.app')

@section('title', 'Add Custom Food')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('foods.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <div>
                    <h1 class="h3 fw-bold mb-0">Add Custom Food</h1>
                    <p class="text-muted small mb-0">Add a new food item to your personal database</p>
                </div>
            </div>

            <form action="{{ route('foods.store') }}" method="POST">
                @csrf

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
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="category_id" class="form-label fw-bold">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->icon }} {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nutrition (per 100g/ml) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-heart-pulse me-2"></i> Nutrition (per 100g/100ml)
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Enter nutritional values per 100 grams or 100 milliliters</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="calories" class="form-label fw-bold">Calories</label>
                                <input type="number" class="form-control" id="calories" name="calories" 
                                       value="{{ old('calories') }}" min="0" placeholder="e.g., 165">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="protein" class="form-label fw-bold">Protein (g)</label>
                                <input type="number" class="form-control" id="protein" name="protein" 
                                       value="{{ old('protein') }}" min="0" placeholder="e.g., 31">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="carbs" class="form-label fw-bold">Carbohydrates (g)</label>
                                <input type="number" class="form-control" id="carbs" name="carbs" 
                                       value="{{ old('carbs') }}" min="0" placeholder="e.g., 0">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fat" class="form-label fw-bold">Fat (g)</label>
                                <input type="number" class="form-control" id="fat" name="fat" 
                                       value="{{ old('fat') }}" min="0" placeholder="e.g., 3.6">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fiber" class="form-label fw-bold">Fiber (g)</label>
                                <input type="number" class="form-control" id="fiber" name="fiber" 
                                       value="{{ old('fiber') }}" min="0" placeholder="e.g., 0">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="sugar" class="form-label fw-bold">Sugar (g)</label>
                                <input type="number" class="form-control" id="sugar" name="sugar" 
                                       value="{{ old('sugar') }}" min="0" placeholder="e.g., 0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Default Serving -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-rulers me-2"></i> Default Serving (Optional)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="default_serving_size" class="form-label fw-bold">Serving Size</label>
                                <input type="number" step="0.01" class="form-control" id="default_serving_size" 
                                       name="default_serving_size" value="{{ old('default_serving_size') }}" 
                                       min="0" placeholder="e.g., 100">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="default_serving_unit" class="form-label fw-bold">Serving Unit</label>
                                <input type="text" class="form-control" id="default_serving_unit" 
                                       name="default_serving_unit" value="{{ old('default_serving_unit') }}" 
                                       placeholder="e.g., g, ml, cup, piece">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-success btn-lg flex-fill">
                        <i class="bi bi-check-circle me-2"></i> Add Food
                    </button>
                    <a href="{{ route('foods.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

