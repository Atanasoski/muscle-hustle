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
                                    <th class="text-center d-none d-lg-table-cell" style="width: 150px;">Video</th>
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
                                                <span class="badge bg-info text-white">Custom</span>
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
                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                                    <span>Background Video (Pixabay)</span>
                                    @if($exercise->pixabay_video_path)
                                        <span class="badge bg-success text-white">✓ Downloaded</span>
                                    @endif
                                </label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary flex-fill" 
                                            onclick="openPixabayModal({{ $exercise->id }}, '{{ $exercise->name }}')">
                                        <i class="bi bi-search"></i> Browse Videos
                                    </button>
                                    @if($exercise->pixabay_video_path)
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deletePixabayVideo({{ $exercise->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                                <small class="text-muted">Optional: Add a short background video clip from Pixabay</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi btn-check-circle me-1"></i> Save Changes
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

<!-- Pixabay Video Browser Modal -->
<div class="modal fade" id="pixabayModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Browse Pixabay Videos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="pixabay-search-input" class="form-control" placeholder="Search for videos...">
                        <button class="btn btn-primary" onclick="searchPixabayVideos()">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
                <div id="pixabay-results" class="row g-3">
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bi bi-search display-4 mb-3"></i>
                        <p>Search for exercise videos above</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Preview Modal -->
<div class="modal fade" id="videoPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <video id="preview-video" controls autoplay loop>
                        <source id="preview-source" src="" type="video/mp4">
                    </video>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="download-video-btn">
                    <i class="bi bi-download"></i> Download & Use This Video
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentExerciseId = null;
let currentVideoUrl = null;
const pixabayModal = new bootstrap.Modal(document.getElementById('pixabayModal'));
const previewModal = new bootstrap.Modal(document.getElementById('videoPreviewModal'));

function openPixabayModal(exerciseId, exerciseName) {
    currentExerciseId = exerciseId;
    document.getElementById('pixabay-search-input').value = exerciseName;
    pixabayModal.show();
    searchPixabayVideos();
}

async function searchPixabayVideos() {
    const query = document.getElementById('pixabay-search-input').value || 'fitness';
    const resultsDiv = document.getElementById('pixabay-results');
    
    resultsDiv.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Searching Pixabay...</p></div>';
    
    try {
        const response = await fetch(`{{ route('exercises.pixabay.search') }}?query=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.videos && data.videos.length > 0) {
            resultsDiv.innerHTML = data.videos.map(video => `
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative" style="cursor: pointer;" onclick="previewPixabayVideo('${video.video_url}')">
                            <img src="${video.image}" class="card-img-top" alt="Video thumbnail">
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="bg-white rounded-circle p-3 shadow">
                                    <i class="bi bi-play-fill fs-3 text-primary"></i>
                                </div>
                            </div>
                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark">${video.duration}s</span>
                        </div>
                        <div class="card-body p-2 text-center">
                            <small class="text-muted">${video.width}x${video.height}</small>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            resultsDiv.innerHTML = '<div class="col-12 text-center text-muted py-5"><i class="bi bi-exclamation-circle display-4 mb-3"></i><p>No videos found. Try a different search term.</p></div>';
        }
    } catch (error) {
        resultsDiv.innerHTML = '<div class="col-12 text-center text-danger py-5"><i class="bi bi-x-circle display-4 mb-3"></i><p>Error loading videos. Please try again.</p></div>';
    }
}

function previewPixabayVideo(videoUrl) {
    currentVideoUrl = videoUrl;
    document.getElementById('preview-source').src = videoUrl;
    document.getElementById('preview-video').load();
    pixabayModal.hide();
    previewModal.show();
}

document.getElementById('download-video-btn')?.addEventListener('click', async function() {
    if (!currentExerciseId || !currentVideoUrl) return;
    
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Downloading...';
    
    try {
        const response = await fetch(`/exercises/${currentExerciseId}/pixabay/download`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ video_url: currentVideoUrl })
        });
        
        const data = await response.json();
        
        if (data.success) {
            previewModal.hide();
            alert('Video downloaded successfully!');
            location.reload();
        } else {
            alert('Failed to download video: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    } catch (error) {
        alert('Error downloading video. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

async function deletePixabayVideo(exerciseId) {
    if (!confirm('Remove this background video?')) return;
    
    try {
        const response = await fetch(`/exercises/${exerciseId}/pixabay`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Video removed successfully!');
            location.reload();
        }
    } catch (error) {
        alert('Error removing video. Please try again.');
    }
}

// Allow Enter key to search
document.getElementById('pixabay-search-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchPixabayVideos();
    }
});

// Stop video when preview modal is closed
document.getElementById('videoPreviewModal')?.addEventListener('hidden.bs.modal', function() {
    const video = document.getElementById('preview-video');
    video.pause();
    video.currentTime = 0;
});

// Show browse modal again when preview modal is closed
document.getElementById('videoPreviewModal')?.addEventListener('hidden.bs.modal', function() {
    if (currentExerciseId) {
        pixabayModal.show();
    }
});

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

