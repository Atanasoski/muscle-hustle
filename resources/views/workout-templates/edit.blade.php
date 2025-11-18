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

    <!-- Template Details -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Template Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('workout-templates.update', $workoutTemplate) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $workoutTemplate->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
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
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $workoutTemplate->description) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Template
                </button>
            </form>
        </div>
    </div>

    <!-- Exercises List -->
    <div class="card shadow-sm mb-4">
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

                        <!-- Edit Exercise Modal -->
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
                </ul>
            @else
                <p class="text-muted mb-0">No exercises added yet. Click "Add Exercise" to get started!</p>
            @endif
        </div>
    </div>
</div>

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
                    <div class="mb-3">
                        <label class="form-label">Exercise <span class="text-danger">*</span></label>
                        <select class="form-select" name="exercise_id" required>
                            <option value="">Select exercise...</option>
                            @foreach($exercises as $exercise)
                                <option value="{{ $exercise->id }}">{{ $exercise->name }}</option>
                            @endforeach
                        </select>
                    </div>
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
    const list = document.getElementById('exercise-list');
    if (list) {
        new Sortable(list, {
            handle: '.handle',
            animation: 150,
            onEnd: function(evt) {
                // Get the new order
                const order = Array.from(list.children).map(item => item.dataset.id);
                
                // Send AJAX request to update order
                fetch('{{ route('workout-templates.update-order', $workoutTemplate) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Order updated successfully');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }
});
</script>
@endpush

