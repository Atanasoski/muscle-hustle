@extends('layouts.app')

@section('title', 'Food Database')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-database text-success"></i> Food Database
            </h1>
            <p class="text-muted mb-0 small">Browse and manage nutrition information</p>
        </div>
        <a href="{{ route('foods.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i> Add Custom Food
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('foods.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Search</label>
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name...">
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
            
            @if(request()->has(['search', 'ownership']) && (request('search') || request('ownership')))
                <div class="mt-3">
                    <a href="{{ route('foods.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Foods by Category -->
    @foreach($categories as $category)
        @if($category->foods->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3" style="background: {{ $category->color }}; color: white;">
                    <h5 class="mb-0 fw-bold">
                        {{ $category->icon }} {{ $category->name }}
                        <span class="badge bg-white text-dark ms-2">{{ $category->foods->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">Food Name</th>
                                    <th class="text-center" style="width: 12%;">Calories</th>
                                    <th class="text-center" style="width: 12%;">Protein</th>
                                    <th class="text-center" style="width: 12%;">Carbs</th>
                                    <th class="text-center" style="width: 12%;">Fat</th>
                                    <th class="text-center" style="width: 12%;">Type</th>
                                    <th class="text-end" style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->foods as $food)
                                    <tr>
                                        <td>
                                            <strong>{{ $food->name }}</strong>
                                            @if($food->brand)
                                                <br><small class="text-muted">{{ $food->brand }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ number_format($food->calories, 0) }}</span>
                                            <br><small class="text-muted">per 100g</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary text-white">{{ number_format($food->protein, 1) }}g</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">{{ number_format($food->carbs, 1) }}g</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger text-white">{{ number_format($food->fat, 1) }}g</span>
                                        </td>
                                        <td class="text-center">
                                            @if($food->user_id)
                                                <span class="badge bg-success text-white"><i class="bi bi-person-fill"></i> Custom</span>
                                            @else
                                                <span class="badge bg-info text-white"><i class="bi bi-globe"></i> Global</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
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
                </div>
            </div>
        @endif
    @endforeach

    @if($categories->sum(fn($cat) => $cat->foods->count()) === 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h5 class="text-muted mt-3">No foods found</h5>
                <p class="text-muted">Try adjusting your filters or add a new custom food</p>
            </div>
        </div>
    @endif
</div>
@endsection

