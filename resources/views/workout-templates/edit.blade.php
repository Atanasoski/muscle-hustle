@extends('layouts.app')

@section('title', 'Edit Workout Template')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="bi bi-pencil"></i> Edit: {{ $workoutTemplate->name }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('workout-templates.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Template Details -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Template Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('workout-templates.update', $workoutTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $workoutTemplate->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="day_of_week" class="form-label">Assigned Day</label>
                            <select class="form-select" id="day_of_week" name="day_of_week">
                                <option value="">Not assigned</option>
                                <option value="0" {{ old('day_of_week', $workoutTemplate->day_of_week) == '0' ? 'selected' : '' }}>Monday</option>
                                <option value="1" {{ old('day_of_week', $workoutTemplate->day_of_week) == '1' ? 'selected' : '' }}>Tuesday</option>
                                <option value="2" {{ old('day_of_week', $workoutTemplate->day_of_week) == '2' ? 'selected' : '' }}>Wednesday</option>
                                <option value="3" {{ old('day_of_week', $workoutTemplate->day_of_week) == '3' ? 'selected' : '' }}>Thursday</option>
                                <option value="4" {{ old('day_of_week', $workoutTemplate->day_of_week) == '4' ? 'selected' : '' }}>Friday</option>
                                <option value="5" {{ old('day_of_week', $workoutTemplate->day_of_week) == '5' ? 'selected' : '' }}>Saturday</option>
                                <option value="6" {{ old('day_of_week', $workoutTemplate->day_of_week) == '6' ? 'selected' : '' }}>Sunday</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $workoutTemplate->description) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Update Template
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Exercises List -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Exercises</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExerciseModal">
                        <i class="bi bi-plus-circle"></i> Add Exercise
                    </button>
                </div>
                <div class="card-body">
                    @if($workoutTemplate->workoutTemplateExercises->count() > 0)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> <strong>Tip:</strong> Drag exercises to reorder them
                        </div>
                        <ul id="exercise-list" class="list-group">
                            @foreach($workoutTemplate->workoutTemplateExercises as $templateExercise)
                                <li class="list-group-item" data-id="{{ $templateExercise->id }}">
                                    <div class="d-flex align-items-center">
                                        <span class="handle me-3" style="cursor: move;">
                                            <i class="bi bi-grip-vertical fs-4"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $templateExercise->exercise->name }}</h6>
                                            <small class="text-muted">
                                                @if($templateExercise->target_sets)
                                                    {{ $templateExercise->target_sets }} sets
                                                @endif
                                                @if($templateExercise->target_reps)
                                                    × {{ $templateExercise->target_reps }} reps
                                                @endif
                                                @if($templateExercise->target_weight)
                                                    @ {{ $templateExercise->target_weight }}kg
                                                @endif
                                                @if($templateExercise->rest_seconds)
                                                    | Rest: {{ $templateExercise->rest_seconds }}s
                                                @endif
                                            </small>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editExerciseModal{{ $templateExercise->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('workout-templates.remove-exercise', [$workoutTemplate, $templateExercise]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this exercise?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No exercises added yet. Click "Add Exercise" to get started!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Exercise Modals (moved outside the list for proper Bootstrap behavior) -->
@if($workoutTemplate->workoutTemplateExercises->count() > 0)
    @foreach($workoutTemplate->workoutTemplateExercises as $templateExercise)
        <div class="modal fade" id="editExerciseModal{{ $templateExercise->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('workout-templates.update-exercise', [$workoutTemplate, $templateExercise]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit {{ $templateExercise->exercise->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Video URL (YouTube)</label>
                                <input type="url" class="form-control" name="video_url" value="{{ $templateExercise->exercise->video_url }}" placeholder="https://www.youtube.com/watch?v=...">
                                <small class="text-muted">Add a YouTube video link for proper form demonstration</small>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Target Sets</label>
                                <input type="number" class="form-control" name="target_sets" value="{{ $templateExercise->target_sets }}" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Target Reps</label>
                                <input type="number" class="form-control" name="target_reps" value="{{ $templateExercise->target_reps }}" min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Target Weight (kg)</label>
                                <input type="number" class="form-control" name="target_weight" value="{{ $templateExercise->target_weight }}" step="0.5" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rest (seconds)</label>
                                <input type="number" class="form-control" name="rest_seconds" value="{{ $templateExercise->rest_seconds }}" min="0">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Add Exercise Modal -->
<div class="modal fade" id="addExerciseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('workout-templates.add-exercise', $workoutTemplate) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Exercise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Exercise <span class="text-danger">*</span></label>
                    <x-exercise-selector 
                        :exercises="$exercises" 
                        name="exercise_id"
                        id="workout-template-exercise-selector"
                        :required="true"
                        placeholder="Search exercises..."
                    />
                    <div class="mb-3">
                        <label class="form-label">Target Sets</label>
                        <input type="number" class="form-control" name="target_sets" min="1" placeholder="e.g., 3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Reps</label>
                        <input type="number" class="form-control" name="target_reps" min="1" placeholder="e.g., 10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Weight (kg)</label>
                        <input type="number" class="form-control" name="target_weight" step="0.5" min="0" placeholder="e.g., 60">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rest (seconds)</label>
                        <input type="number" class="form-control" name="rest_seconds" min="0" placeholder="e.g., 90">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Exercise</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reset exercise selector when modal closes
    const addExerciseModal = document.getElementById('addExerciseModal');
    if (addExerciseModal) {
        addExerciseModal.addEventListener('hidden.bs.modal', function() {
            const clearBtn = document.getElementById('workout-template-exercise-selector-clear');
            if (clearBtn) clearBtn.click();
        });
    }
    
    const list = document.getElementById('exercise-list');
    if (list) {
        let originalOrder = [];
        
        const sortable = new Sortable(list, {
            handle: '.handle',
            animation: 150,
            onStart: function(evt) {
                // Save the original order when drag starts
                originalOrder = Array.from(list.children)
                    .map(item => item.dataset.id)
                    .filter(id => id && id !== 'undefined' && id !== 'null')
                    .map(id => parseInt(id))
                    .filter(id => !isNaN(id) && id > 0);
            },
            onEnd: function(evt) {
                // Check if position actually changed
                if (evt.oldIndex === evt.newIndex) {
                    return; // No change, don't send request
                }
                
                // Get the new order - only include valid IDs
                const order = Array.from(list.children)
                    .map(item => item.dataset.id)
                    .filter(id => id && id !== 'undefined' && id !== 'null')
                    .map(id => parseInt(id))
                    .filter(id => !isNaN(id) && id > 0);
                
                // Check if the order actually changed
                const orderChanged = originalOrder.length !== order.length || 
                                    originalOrder.some((id, index) => id !== order[index]);
                
                if (!orderChanged) {
                    console.log('Order unchanged, skipping update');
                    return;
                }
                
                console.log('Sending order:', order);
                
                if (order.length === 0) {
                    console.error('No valid exercise IDs found');
                    return;
                }
                
                // Send AJAX request to update order
                fetch('{{ route('workout-templates.update-order', $workoutTemplate) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        console.error('Validation errors:', data);
                        throw new Error(data.message || 'Network response was not ok');
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        console.log('Order updated successfully');
                        showToast('Exercise order updated!', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Failed to update order: ' + error.message, 'error');
                });
            }
        });
    }
    
    // Simple toast notification function
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed bottom-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }
});
</script>
@endpush

