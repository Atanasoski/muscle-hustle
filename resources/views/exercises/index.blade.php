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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createExerciseModal">
            <i class="bi bi-plus-circle me-2"></i> Add Custom Exercise
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Exercises by Category -->
    @foreach($categories as $category)
        @if($category->exercises->count() > 0)
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
                                    <th class="text-center d-none d-lg-table-cell" style="width: 150px;">Video</th>
                                    <th class="text-center" style="width: 100px;">Type</th>
                                    <th class="text-end" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->exercises as $exercise)
                                    <tr>
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
                                        <td class="text-center d-none d-lg-table-cell">
                                            @if($exercise->video_url)
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#videoModal{{ $exercise->id }}">
                                                    <i class="bi bi-play-circle"></i> Watch
                                                </button>
                                            @else
                                                <span class="text-muted small">No video</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($exercise->user_id)
                                                <span class="badge bg-info">Custom</span>
                                            @else
                                                <span class="badge bg-secondary">Global</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editExerciseModal{{ $exercise->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($exercise->user_id)
                                                <form action="{{ route('exercises.destroy', $exercise) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Delete this exercise?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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
                        <select class="form-select" name="category_id" required>
                            <option value="">Select category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Video URL (YouTube)</label>
                        <input type="url" class="form-control" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                        <small class="text-muted">Optional: Add a YouTube tutorial for proper form</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Default Rest Time (seconds)</label>
                        <input type="number" class="form-control" name="default_rest_sec" value="90" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Create Exercise
                    </button>
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
                                <label class="form-label fw-bold">Exercise Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $exercise->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $exercise->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->icon }} {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Video URL (YouTube)</label>
                                <input type="url" class="form-control" name="video_url" value="{{ $exercise->video_url }}" placeholder="https://www.youtube.com/watch?v=...">
                                <small class="text-muted">Add or update YouTube tutorial</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Default Rest Time (seconds)</label>
                                <input type="number" class="form-control" name="default_rest_sec" value="{{ $exercise->default_rest_sec }}" min="0">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Video Preview Modal -->
        @if($exercise->video_url)
            <div class="modal fade" id="videoModal{{ $exercise->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $exercise->name }} - Form Tutorial</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ str_replace('watch?v=', 'embed/', $exercise->video_url) }}" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endforeach
@endsection

