@extends('layouts.app')

@section('title', 'Food Database')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-2">
                        <i class="bi bi-database text-success"></i> Food Database
                    </h1>
                    <p class="text-muted mb-0">Browse and manage nutrition information</p>
                </div>
                <a href="{{ route('foods.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i> Add Custom Food
                </a>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('foods.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Search</label>
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Category</label>
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Type</label>
                        <select class="form-select" name="ownership">
                            <option value="">All Foods</option>
                            <option value="global" {{ request('ownership') === 'global' ? 'selected' : '' }}>
                                Global Database
                            </option>
                            <option value="custom" {{ request('ownership') === 'custom' ? 'selected' : '' }}>
                                My Custom Foods
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
            
            @if(request()->has(['search', 'category', 'ownership']) && (request('search') || request('category') || request('ownership')))
                <div class="mt-3">
                    <a href="{{ route('foods.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-3">
        <p class="text-muted">
            Showing <strong>{{ $foods->count() }}</strong> of <strong>{{ $foods->total() }}</strong> foods
        </p>
    </div>

    <!-- Foods Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($foods->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No foods found</h5>
                    <p class="text-muted">Try adjusting your filters or add a new custom food</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Food Name</th>
                                <th>Category</th>
                                <th class="text-center">Calories</th>
                                <th class="text-center">Protein</th>
                                <th class="text-center">Carbs</th>
                                <th class="text-center">Fat</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($foods as $food)
                                <tr>
                                    <td class="fw-bold">{{ $food->name }}</td>
                                    <td>
                                        @if($food->category)
                                            <span class="badge bg-secondary">{{ ucfirst($food->category) }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $food->calories ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $food->protein ?? 0 }}g</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark">{{ $food->carbs ?? 0 }}g</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $food->fat ?? 0 }}g</span>
                                    </td>
                                    <td class="text-center">
                                        @if($food->user_id)
                                            <span class="badge bg-success">
                                                <i class="bi bi-person-fill"></i> Custom
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="bi bi-globe"></i> Global
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('foods.show', $food) }}" class="btn btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($food->user_id === auth()->id())
                                                <a href="{{ route('foods.edit', $food) }}" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('foods.destroy', $food) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this food?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($foods->hasPages())
                    <div class="d-flex justify-content-center p-4">
                        {{ $foods->appends(request()->query())->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

