@extends('layouts.app')

@section('title', 'Active Workout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold">
                        <i class="bi bi-play-circle-fill text-danger"></i> Active Workout
                    </h1>
                    <p class="text-muted mb-0">Track your sets and progress</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <i class="bi bi-lightning-fill"></i> In Progress
                    </span>
                    <form action="{{ route('workouts.cancel', $session) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this workout? All logged sets will be deleted.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle"></i> Cancel Workout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Rest Timer Card (Initially Hidden) -->
            <div id="timer-card" class="card shadow-lg border-0 bg-info text-white mb-4" style="display: none;">
                <div class="card-body text-center py-4">
                    <h4 class="mb-3">
                        <i class="bi bi-stopwatch"></i> Rest Timer
                    </h4>
                    <div id="timer-display" class="display-1 fw-bold">00:00</div>
                    <div class="btn-group mt-3 shadow">
                        <button id="timer-stop" class="btn btn-light btn-lg">
                            <i class="bi bi-stop-circle"></i> Stop
                        </button>
                        <button id="timer-add-30" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-plus-circle"></i> +30s
                        </button>
                    </div>
                </div>
            </div>

            @if($session->workoutTemplate && $exercises->count() > 0)
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-danger text-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-fire"></i> {{ $session->workoutTemplate->name }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @foreach($exercises as $templateExercise)
                            <div class="exercise-section mb-4 p-4 border-2 border-start border-danger rounded-3 bg-light">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-2 fw-bold">
                                            <i class="bi bi-dumbbell text-danger"></i> {{ $templateExercise->exercise->name }}
                                        </h5>
                                        <small class="text-muted d-block">
                                            @if($templateExercise->target_sets && $templateExercise->target_reps)
                                                Target: {{ $templateExercise->target_sets }}×{{ $templateExercise->target_reps }}
                                            @endif
                                            @if($templateExercise->target_weight)
                                                @ {{ $templateExercise->target_weight }}kg
                                            @endif
                                        </small>
                                        @if(isset($lastWorkouts[$templateExercise->exercise_id]))
                                            <br>
                                            <small class="text-success">
                                                <i class="bi bi-info-circle"></i> Last time: 
                                                {{ $lastWorkouts[$templateExercise->exercise_id]->weight }}kg × 
                                                {{ $lastWorkouts[$templateExercise->exercise_id]->reps }} reps
                                            </small>
                                        @endif
                                    </div>
                                    @if($templateExercise->rest_seconds)
                                        <button class="btn btn-info text-white start-timer shadow-sm" data-seconds="{{ $templateExercise->rest_seconds }}">
                                            <i class="bi bi-stopwatch"></i> {{ $templateExercise->rest_seconds }}s Rest
                                        </button>
                                    @endif
                                </div>

                                <!-- Sets Already Logged -->
                                @php
                                    $loggedSets = $session->setLogs->where('exercise_id', $templateExercise->exercise_id);
                                @endphp
                                @if($loggedSets->count() > 0)
                                    <div class="mb-3 p-3 bg-white rounded">
                                        <h6 class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success"></i> Logged Sets:
                                        </h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($loggedSets as $setLog)
                                                <span class="badge bg-success fs-6 px-3 py-2">
                                                    Set {{ $setLog->set_number }}: {{ $setLog->weight }}kg × {{ $setLog->reps }} reps
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Log New Set Form -->
                                <form class="log-set-form row g-2" data-exercise-id="{{ $templateExercise->exercise_id }}">
                                    <div class="col-3">
                                        <label class="form-label small">Set #</label>
                                        <input type="number" class="form-control form-control-sm" name="set_number" 
                                               value="{{ $loggedSets->count() + 1 }}" min="1" required>
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label small">Weight (kg)</label>
                                        <input type="number" class="form-control form-control-sm" name="weight" 
                                               value="{{ $templateExercise->target_weight ?? ($lastWorkouts[$templateExercise->exercise_id]->weight ?? '') }}" 
                                               step="0.5" min="0" required>
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label small">Reps</label>
                                        <input type="number" class="form-control form-control-sm" name="reps" 
                                               value="{{ $templateExercise->target_reps ?? '' }}" 
                                               min="0" required>
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label small">&nbsp;</label>
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-check-circle"></i> Log Set
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> This is a free-form workout. You can log sets manually or add notes at the end.
                </div>
            @endif

            <!-- Complete Workout -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('workouts.complete', $session) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">
                                <i class="bi bi-journal-text"></i> Workout Notes (optional)
                            </label>
                            <textarea class="form-control form-control-lg" id="notes" name="notes" rows="3" 
                                      placeholder="How did it go? Any observations?">{{ $session->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg w-100 py-3 shadow" onclick="return confirm('Complete this workout?')">
                            <i class="bi bi-check-circle-fill me-2"></i> Complete Workout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let timerInterval = null;
let timerSeconds = 0;

// Timer Functions
function startTimer(seconds) {
    // Stop any existing timer
    stopTimer();
    
    timerSeconds = seconds;
    document.getElementById('timer-card').style.display = 'block';
    updateTimerDisplay();
    
    timerInterval = setInterval(() => {
        timerSeconds--;
        updateTimerDisplay();
        
        if (timerSeconds <= 0) {
            stopTimer();
            // Optional: Play sound or show notification
            alert('Rest time is up! 💪');
        }
    }, 1000);
    
    // Scroll to timer
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
        const seconds = parseInt(this.dataset.seconds);
        startTimer(seconds);
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
                // Reload page to show updated sets
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error logging set. Please try again.');
        });
    });
});
</script>
@endpush

