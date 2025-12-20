@extends('layouts.app')

@section('title', 'Exercise Library')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-list-ul text-primary"></i> Exercise Library
            </h1>
            <p class="text-muted mb-0 small">Manage your exercises and video tutorials</p>
        </div>
        <x-button variant="create" data-bs-toggle="modal" data-bs-target="#createExerciseModal">
            Add Custom Exercise
        </x-button>
    </div>

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="input-group input-group-lg">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="exercise-search" class="form-control" 
                       placeholder="Search exercises by name..." autocomplete="off">
            </div>
        </div>
    </div>

    <!-- Exercises by Category -->
    @foreach($categories as $category)
        @if($category->exercises->count() > 0)
        <div class="exercise-category" data-category="{{ $category->name }}">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3" style="background: {{ $category->color }}; color: white;">
                    <h5 class="mb-0 fw-bold">
                        {{ $category->icon }} {{ $category->name }}
                        <span class="badge bg-white text-dark ms-2">{{ $category->exercises->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50%;">Exercise Name</th>
                                    <th class="text-center d-none d-md-table-cell" style="width: 120px;">Rest Time</th>
                                    <th class="text-center" style="width: 100px;">Type</th>
                                    <th class="text-end" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->exercises as $exercise)
                                    <tr class="exercise-row" data-name="{{ strtolower($exercise->name) }}">
                                        <td>
                                            <strong>{{ $exercise->name }}</strong>
                                            @if($exercise->user_id)
                                                <span class="badge bg-info-subtle text-info ms-2">Custom</span>
                                            @endif
                                        </td>
                                        <td class="text-center d-none d-md-table-cell">
                                            <span class="badge bg-secondary">
                                                {{ $exercise->default_rest_sec }}s
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($exercise->user_id)
                                                <span class="badge bg-info text-white">Custom</span>
                                            @else
                                                <span class="badge bg-secondary">Global</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <x-button variant="edit" 
                                                    size="sm"
                                                    class="btn-outline-primary"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editExerciseModal{{ $exercise->id }}">
                                                <span class="visually-hidden">Edit</span>
                                            </x-button>
                                            @if($exercise->user_id)
                                                <form action="{{ route('exercises.destroy', $exercise) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Delete this exercise?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-button variant="delete" size="sm" type="submit" class="btn-outline-danger">
                                                        <span class="visually-hidden">Delete</span>
                                                    </x-button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>

<!-- Create Exercise Modal -->
<div class="modal fade" id="createExerciseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('exercises.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Custom Exercise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Exercise Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., Dumbbell Curl">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <input type="text" id="category-search-create" class="form-control mb-2" placeholder="🔍 Search categories..." autocomplete="off">
                        <select class="form-select" id="category-select-create" name="category_id" required size="6">
                            <option value="">Select category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Default Rest Time (seconds)</label>
                        <input type="number" class="form-control" name="default_rest_sec" value="90" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <x-button variant="cancel" data-bs-dismiss="modal">Cancel</x-button>
                    <x-button variant="create" type="submit">
                        Create Exercise
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Exercise Modals -->
@foreach($categories as $category)
    @foreach($category->exercises as $exercise)
        <div class="modal fade" id="editExerciseModal{{ $exercise->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('exercises.update', $exercise) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Exercise</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit-name-{{ $exercise->id }}" class="form-label fw-bold">Exercise Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-name-{{ $exercise->id }}" name="name" value="{{ $exercise->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-category-{{ $exercise->id }}" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit-category-{{ $exercise->id }}" name="category_id" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $exercise->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->icon }} {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit-rest-{{ $exercise->id }}" class="form-label fw-bold">Default Rest Time (seconds)</label>
                                <input type="number" class="form-control" id="edit-rest-{{ $exercise->id }}" name="default_rest_sec" value="{{ $exercise->default_rest_sec }}" min="0">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <x-button variant="cancel" data-bs-dismiss="modal">Cancel</x-button>
                            <x-button variant="save" type="submit">
                                Save Changes
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach
@endsection

@push('scripts')
<script>
// Exercise search functionality
const exerciseSearchInput = document.getElementById('exercise-search');
if (exerciseSearchInput) {
    exerciseSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const exerciseRows = document.querySelectorAll('.exercise-row');
        const categories = document.querySelectorAll('.exercise-category');
        
        // Filter exercise rows
        exerciseRows.forEach(row => {
            const exerciseName = row.getAttribute('data-name');
            if (exerciseName.includes(searchTerm)) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });
        
        // Hide categories with no visible exercises
        categories.forEach(category => {
            const card = category.querySelector('.card');
            if (!card) return;
            
            const tbody = card.querySelector('tbody');
            if (!tbody) return;
            
            const visibleRows = tbody.querySelectorAll('.exercise-row:not(.d-none)');
            if (visibleRows.length === 0) {
                category.classList.add('d-none');
            } else {
                category.classList.remove('d-none');
            }
        });
    });
}

// Category search in Create Exercise modal
const categorySearchCreate = document.getElementById('category-search-create');
const categorySelectCreate = document.getElementById('category-select-create');

if (categorySearchCreate && categorySelectCreate) {
    categorySearchCreate.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = categorySelectCreate.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') return; // Keep placeholder
            
            const text = option.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    });
    
    // Reset on modal close
    const createModal = document.getElementById('createExerciseModal');
    if (createModal) {
        createModal.addEventListener('hidden.bs.modal', function() {
            categorySearchCreate.value = '';
            categorySelectCreate.querySelectorAll('option').forEach(opt => {
                opt.style.display = '';
            });
        });
    }
}
</script>
@endpush

