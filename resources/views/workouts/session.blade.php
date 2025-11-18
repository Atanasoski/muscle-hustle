@extends('layouts.app')

@section('title', 'Active Workout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-play-circle-fill"></i> Active Workout</h1>
                <span class="badge bg-success fs-6">In Progress</span>
            </div>

            <!-- Rest Timer Card (Initially Hidden) -->
            <div id="timer-card" class="card shadow-lg border-warning mb-4" style="display: none;">
                <div class="card-body text-center">
                    <h4>Rest Timer</h4>
                    <div id="timer-display" class="display-1 fw-bold text-warning">00:00</div>
                    <div class="btn-group mt-3">
                        <button id="timer-stop" class="btn btn-danger">
                            <i class="bi bi-stop-circle"></i> Stop
                        </button>
                        <button id="timer-add-30" class="btn btn-secondary">+30s</button>
                    </div>
                </div>
            </div>

            @if($session->workoutTemplate && $exercises->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $session->workoutTemplate->name }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($exercises as $templateExercise)
                            <div class="exercise-section mb-4 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">{{ $templateExercise->exercise->name }}</h5>
                                        <small class="text-muted">
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
                                        <button class="btn btn-sm btn-outline-warning start-timer" data-seconds="{{ $templateExercise->rest_seconds }}">
                                            <i class="bi bi-clock"></i> {{ $templateExercise->rest_seconds }}s Rest
                                        </button>
                                    @endif
                                </div>

                                <!-- Sets Already Logged -->
                                @php
                                    $loggedSets = $session->setLogs->where('exercise_id', $templateExercise->exercise_id);
                                @endphp
                                @if($loggedSets->count() > 0)
                                    <div class="mb-3">
                                        <h6>Logged Sets:</h6>
                                        @foreach($loggedSets as $setLog)
                                            <span class="badge bg-success me-1">
                                                Set {{ $setLog->set_number }}: {{ $setLog->weight }}kg × {{ $setLog->reps }}
                                            </span>
                                        @endforeach
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
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('workouts.complete', $session) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="notes" class="form-label">Workout Notes (optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="How did it go? Any observations?">{{ $session->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100" onclick="return confirm('Complete this workout?')">
                            <i class="bi bi-check-circle-fill"></i> Complete Workout
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

