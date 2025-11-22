@extends('layouts.app')

@section('title', 'Active Workout')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #ff6b35 0%, #ff8c61 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div class="text-white">
                            <h1 class="h3 fw-bold mb-2 text-white">
                                <i class="bi bi-fire"></i> {{ $session->workoutTemplate->name }}
                            </h1>
                            <p class="mb-0 opacity-75 small">
                                <i class="bi bi-clock"></i> Started {{ $session->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-white text-success fs-6 px-3 py-2">
                                <i class="bi bi-lightning-fill"></i> In Progress
                            </span>
                            <form action="{{ route('workouts.cancel', $session) }}" method="POST" onsubmit="return confirm('Cancel workout? All logged sets will be deleted.')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    @php
                        $totalExercises = $exercises->count();
                        $completedExercises = $exercises->filter(function($ex) use ($session) {
                            return $session->setLogs->where('exercise_id', $ex->exercise_id)->count() > 0;
                        })->count();
                        $progressPercent = $totalExercises > 0 ? ($completedExercises / $totalExercises) * 100 : 0;
                    @endphp
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-white fw-semibold">Progress</small>
                            <small class="text-white">{{ $completedExercises }} / {{ $totalExercises }} exercises</small>
                        </div>
                        <div class="progress" style="height: 8px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar bg-white" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rest Timer (Hidden by default) -->
            <div id="timer-card" class="card border-0 shadow-lg mb-4 bg-info text-white" style="display: none;">
                <div class="card-body text-center py-4">
                    <h5 class="mb-3"><i class="bi bi-stopwatch"></i> Rest Timer</h5>
                    <div id="timer-display" class="display-2 fw-bold mb-3">00:00</div>
                    <div class="btn-group">
                        <button id="timer-stop" class="btn btn-light px-4">
                            <i class="bi bi-stop-circle"></i> Stop
                        </button>
                        <button id="timer-add-30" class="btn btn-outline-light px-4">
                            <i class="bi bi-plus-circle"></i> +30s
                        </button>
                    </div>
                </div>
            </div>

            <!-- Exercises -->
            @foreach($exercises as $index => $templateExercise)
                @php
                    $loggedSets = $session->setLogs->where('exercise_id', $templateExercise->exercise_id);
                    $isCompleted = $loggedSets->count() >= ($templateExercise->target_sets ?? 1);
                @endphp
                
                <div class="card border-0 shadow-sm mb-3 exercise-card {{ $isCompleted ? 'completed' : '' }}">
                    <!-- Exercise Header -->
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="exercise-number {{ $isCompleted ? 'bg-success' : 'bg-primary' }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                    @if($isCompleted)
                                        <i class="bi bi-check-lg"></i>
                                    @else
                                        <span class="fw-bold">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">{{ $templateExercise->exercise->name }}</h5>
                                    <small class="text-muted">
                                        @if($templateExercise->target_sets && $templateExercise->target_reps)
                                            Target: {{ $templateExercise->target_sets }}×{{ $templateExercise->target_reps }}
                                            @if($templateExercise->target_weight)
                                                @ {{ $templateExercise->target_weight }}kg
                                            @endif
                                        @endif
                                    </small>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                @if($templateExercise->exercise->video_url)
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#video-{{ $templateExercise->id }}">
                                        <i class="bi bi-play-circle"></i> <span class="d-none d-md-inline">Watch</span>
                                    </button>
                                @endif
                                @if($templateExercise->rest_seconds)
                                    <button class="btn btn-sm btn-info text-white start-timer" data-seconds="{{ $templateExercise->rest_seconds }}">
                                        <i class="bi bi-stopwatch"></i> {{ $templateExercise->rest_seconds }}s
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Background Video (Pixabay) -->
                        @if($templateExercise->exercise->pixabay_video_path)
                            <div class="mb-4 position-relative rounded-3 overflow-hidden" style="height: 200px;">
                                <video 
                                    src="{{ Storage::url($templateExercise->exercise->pixabay_video_path) }}" 
                                    autoplay 
                                    loop 
                                    muted 
                                    playsinline
                                    class="w-100 h-100 object-fit-cover">
                                </video>
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25"></div>
                            </div>
                        @endif

                        <!-- YouTube Tutorial Video -->
                        @if($templateExercise->exercise->video_url)
                            <div class="collapse mb-4" id="video-{{ $templateExercise->id }}">
                                <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm">
                                    <iframe src="{{ str_replace('watch?v=', 'embed/', $templateExercise->exercise->video_url) }}" allowfullscreen></iframe>
                                </div>
                            </div>
                        @endif

                        <!-- Last Workout Info -->
                        @if(isset($lastWorkouts[$templateExercise->exercise_id]))
                            <div class="alert alert-info border-0 mb-3 py-2">
                                <small>
                                    <i class="bi bi-info-circle-fill"></i> 
                                    <strong>Last workout:</strong> 
                                    {{ $lastWorkouts[$templateExercise->exercise_id]->weight }}kg × 
                                    {{ $lastWorkouts[$templateExercise->exercise_id]->reps }} reps
                                </small>
                            </div>
                        @endif

                        <!-- Logged Sets -->
                        @if($loggedSets->count() > 0)
                            <div class="mb-4">
                                <h6 class="text-success fw-semibold mb-3">
                                    <i class="bi bi-check-circle-fill"></i> Completed Sets
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 80px;">Set</th>
                                                <th class="text-center">Weight (kg)</th>
                                                <th class="text-center">Reps</th>
                                                <th style="width: 60px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($loggedSets as $setLog)
                                                <tr>
                                                    <td class="text-center fw-semibold">{{ $setLog->set_number }}</td>
                                                    <td class="text-center">{{ $setLog->weight }}</td>
                                                    <td class="text-center">{{ $setLog->reps }}</td>
                                                    <td class="text-center">
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Log New Set Form -->
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <h6 class="fw-semibold mb-3">
                                    <i class="bi bi-plus-circle text-primary"></i> Log Set {{ $loggedSets->count() + 1 }}
                                </h6>
                                <form class="log-set-form" data-exercise-id="{{ $templateExercise->exercise_id }}">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">Set Number</label>
                                            <input type="number" class="form-control" name="set_number" 
                                                   value="{{ $loggedSets->count() + 1 }}" min="1" required readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">Weight (kg)</label>
                                            <input type="number" class="form-control" name="weight" 
                                                   value="{{ $templateExercise->target_weight ?? ($lastWorkouts[$templateExercise->exercise_id]->weight ?? '') }}" 
                                                   step="0.5" min="0" required placeholder="0.0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">Reps</label>
                                            <input type="number" class="form-control" name="reps" 
                                                   value="{{ $templateExercise->target_reps ?? '' }}" 
                                                   min="0" required placeholder="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold d-none d-md-block">&nbsp;</label>
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="bi bi-check-circle"></i> Log Set
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Complete Workout Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('workouts.complete', $session) }}" method="POST">
                        @csrf
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-journal-text text-primary"></i> Workout Notes
                        </h5>
                        <textarea class="form-control mb-4" name="notes" rows="4" 
                                  placeholder="How did it go? Any observations or notes...">{{ $session->notes }}</textarea>
                        
                        <button type="submit" class="btn btn-danger btn-lg w-100 py-3 shadow" 
                                onclick="return confirm('Complete this workout?')">
                            <i class="bi bi-check-circle-fill me-2"></i> Complete Workout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.exercise-card {
    transition: all 0.3s ease;
}

.exercise-card.completed {
    opacity: 0.8;
    background: linear-gradient(to right, rgba(25, 135, 84, 0.05) 0%, transparent 10%);
}

.exercise-number {
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.exercise-card:hover {
    transform: translateY(-2px);
}
</style>
@endpush

@push('scripts')
<script>
let timerInterval = null;
let timerSeconds = 0;

// Timer Functions
function startTimer(seconds) {
    stopTimer();
    timerSeconds = seconds;
    document.getElementById('timer-card').style.display = 'block';
    updateTimerDisplay();
    
    timerInterval = setInterval(() => {
        timerSeconds--;
        updateTimerDisplay();
        
        if (timerSeconds <= 0) {
            stopTimer();
            alert('Rest time is up! 💪');
        }
    }, 1000);
    
    document.getElementById('timer-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function stopTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    document.getElementById('timer-card').style.display = 'none';
    timerSeconds = 0;
}

function updateTimerDisplay() {
    const minutes = Math.floor(timerSeconds / 60);
    const seconds = timerSeconds % 60;
    document.getElementById('timer-display').textContent = 
        `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

// Event Listeners
document.querySelectorAll('.start-timer').forEach(button => {
    button.addEventListener('click', function() {
        startTimer(parseInt(this.dataset.seconds));
    });
});

document.getElementById('timer-stop').addEventListener('click', stopTimer);
document.getElementById('timer-add-30').addEventListener('click', function() {
    timerSeconds += 30;
    updateTimerDisplay();
});

// Log Set Forms
document.querySelectorAll('.log-set-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging...';
        
        const exerciseId = this.dataset.exerciseId;
        const formData = new FormData(this);
        
        fetch('{{ route('workouts.log-set', $session) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                exercise_id: exerciseId,
                set_number: formData.get('set_number'),
                weight: formData.get('weight'),
                reps: formData.get('reps'),
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error logging set. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Log Set';
        });
    });
});
</script>
@endpush
