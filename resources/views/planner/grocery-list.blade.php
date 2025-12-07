@extends('layouts.app')

@section('title', 'Grocery List')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-2">
                        <i class="bi bi-cart3 text-success"></i> Grocery List
                    </h1>
                    <p class="text-muted mb-0">Week of {{ $weekStart->format('M d, Y') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('planner.meals') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Meals
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Print List
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($groceries->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                <h4 class="fw-bold mb-2">No Recipes with Ingredients</h4>
                <p class="text-muted mb-4">Add recipes to your meal plan to generate a grocery list.</p>
                <a href="{{ route('planner.meals') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle me-2"></i> Plan Your Meals
                </a>
            </div>
        </div>
    @else
        <!-- Summary Card -->
        <div class="card border-0 shadow-sm mb-4 bg-success text-white">
            <div class="card-body p-4">
                <div class="row g-3 text-center">
                    <div class="col-md-4">
                        <div class="h2 fw-bold mb-0">{{ $groceries->flatten(1)->count() }}</div>
                        <small class="opacity-75">Total Items</small>
                    </div>
                    <div class="col-md-4">
                        <div class="h2 fw-bold mb-0">{{ $groceries->count() }}</div>
                        <small class="opacity-75">Categories</small>
                    </div>
                    <div class="col-md-4">
                        <div class="h2 fw-bold mb-0">{{ $totalRecipes }}</div>
                        <small class="opacity-75">Recipes</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grocery Items by Category -->
        @foreach($groceries as $category => $items)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="fw-bold mb-0 text-success">
                        <i class="bi bi-tag-fill me-2"></i> {{ ucfirst($category) }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($items as $item)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="form-check mt-1">
                                        <input class="form-check-input grocery-checkbox" type="checkbox" id="item-{{ $loop->parent->index }}-{{ $loop->index }}">
                                    </div>
                                    <label class="flex-grow-1 grocery-item-label" for="item-{{ $loop->parent->index }}-{{ $loop->index }}" style="cursor: pointer;">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div>
                                                <strong class="fw-bold">{{ $item['food']->name }}</strong>
                                                <div class="text-muted small mt-1">
                                                    Used in: {{ implode(', ', array_unique($item['meals'])) }}
                                                </div>
                                            </div>
                                            <span class="badge bg-primary text-white px-3 py-2">
                                                {{ round($item['quantity'], 1) }} {{ $item['unit'] }}
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@push('styles')
<style>
/* Print Styles */
@media print {
    .navbar, .btn, .form-check-input {
        display: none !important;
    }
    
    .card {
        page-break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
    
    .card-header {
        background: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Checked item styling */
.grocery-checkbox:checked ~ .grocery-item-label {
    opacity: 0.5;
    text-decoration: line-through;
}

.list-group-item {
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: rgba(0,0,0,0.02);
}
</style>
@endpush

@push('scripts')
<script>
// Save checkbox states to localStorage
document.querySelectorAll('.grocery-checkbox').forEach(checkbox => {
    const storageKey = 'grocery_' + checkbox.id;
    
    // Restore state from localStorage
    if (localStorage.getItem(storageKey) === 'true') {
        checkbox.checked = true;
    }
    
    // Save state on change
    checkbox.addEventListener('change', function() {
        localStorage.setItem(storageKey, this.checked);
    });
});

// Clear localStorage when navigating away (optional)
window.addEventListener('beforeunload', function() {
    // Optionally clear grocery list state after a certain time
    // For now, we keep it persisted
});
</script>
@endpush
@endsection

