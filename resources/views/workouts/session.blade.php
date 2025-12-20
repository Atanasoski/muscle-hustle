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
                                <x-button variant="cancel" type="submit" size="sm" class="btn-light">
                                    Cancel
                            </x-button>
                        </form>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
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


            <!-- Exercises -->
            @foreach($exercisesData as $index => $exerciseData)
                <div id="exercise-{{ $exerciseData['template_exercise']->exercise_id }}" class="card border-0 shadow-sm mb-3 {{ $exerciseData['is_completed'] ? 'opacity-75 bg-success bg-opacity-10' : '' }}">
                    <!-- Exercise Header -->
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="fs-5 {{ $exerciseData['is_completed'] ? 'bg-success' : 'bg-primary' }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                    @if($exerciseData['is_completed'])
                                        <i class="bi bi-check-lg"></i>
                                    @else
                                        <span class="fw-bold">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">{{ $exerciseData['template_exercise']->exercise->name }}</h5>
                                    <small class="text-muted">
                                        @if($exerciseData['template_exercise']->target_sets && $exerciseData['template_exercise']->target_reps)
                                            Target: {{ $exerciseData['template_exercise']->target_sets }}×{{ $exerciseData['template_exercise']->target_reps }}
                                            @if($exerciseData['template_exercise']->target_weight)
                                                @ {{ $exerciseData['template_exercise']->target_weight }}kg
                                            @endif
                                        @endif
                                    </small>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                @if($exerciseData['rest_seconds'])
                                    <button class="btn btn-sm btn-info text-white start-timer" data-seconds="{{ $exerciseData['rest_seconds'] }}">
                                        <i class="bi bi-stopwatch"></i> {{ $exerciseData['rest_seconds'] }}s
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- All Sets Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 80px;">Set</th>
                                        <th class="text-center">Weight</th>
                                        <th class="text-center">Reps</th>
                                        <th class="text-center" style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Find the last completed set for this exercise
                                        $lastCompletedSet = null;
                                        foreach($exerciseData['sets'] as $s) {
                                            if ($s['is_completed']) {
                                                $lastCompletedSet = $s['set_number'];
                                            }
                                        }
                                    @endphp
                                    @foreach($exerciseData['sets'] as $set)
                                        <tr class="{{ $set['is_completed'] ? 'table-success' : ($set['is_active'] ? 'table-warning' : '') }}">
                                            <!-- Set Number -->
                                            <td class="text-center fw-bold">
                                                @if($set['is_completed'])
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @elseif($set['is_active'])
                                                    <i class="bi bi-arrow-right-circle-fill text-warning"></i>
                                                @else
                                                    <i class="bi bi-lock-fill text-muted"></i>
                                                @endif
                                                {{ $set['set_number'] }}
                                            </td>

                                            <!-- Weight Input or Display -->
                                            <td class="text-center">
                                                @if($set['is_completed'])
                                                    <strong>{{ number_format($set['current_weight'], 0) }}</strong>
                                                @elseif($set['is_active'])
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center weight-input-{{ $exerciseData['template_exercise']->exercise_id }}" 
                                                           value="{{ $set['default_weight'] ? number_format($set['default_weight'], 0) : '' }}" 
                                                           step="1" 
                                                           min="0" 
                                                           placeholder="Weight"
                                                           data-set="{{ $set['set_number'] }}">
                                                @else
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center" 
                                                           value="{{ $set['default_weight'] ? number_format($set['default_weight'], 0) : '' }}" 
                                                           disabled>
                                                @endif
                                            </td>

                                            <!-- Reps Input or Display -->
                                            <td class="text-center">
                                                @if($set['is_completed'])
                                                    <strong>{{ $set['current_reps'] }}</strong>
                                                @elseif($set['is_active'])
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center reps-input-{{ $exerciseData['template_exercise']->exercise_id }}" 
                                                           value="{{ $set['default_reps'] }}" 
                                                           min="0" 
                                                           placeholder="Reps"
                                                           data-set="{{ $set['set_number'] }}">
                                                @else
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center" 
                                                           value="{{ $set['default_reps'] }}" 
                                                           disabled>
                                                @endif
                                            </td>

                                            <!-- Action Button -->
                                            <td class="text-center">
                                                @if($set['is_completed'])
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <x-button variant="edit" size="sm" class="btn-outline-primary edit-set-btn" 
                                                                data-set-id="{{ $set['logged_set_id'] }}"
                                                                data-exercise-id="{{ $exerciseData['template_exercise']->exercise_id }}"
                                                                data-set-number="{{ $set['set_number'] }}"
                                                                data-weight="{{ $set['current_weight'] }}"
                                                                data-reps="{{ $set['current_reps'] }}">
                                                            <span class="visually-hidden">Edit</span>
                                                        </x-button>
                                                        @if($set['set_number'] === $lastCompletedSet)
                                                            <x-button variant="delete" size="sm" class="btn-outline-danger delete-set-btn" 
                                                                    data-set-id="{{ $set['logged_set_id'] }}"
                                                                    data-exercise-id="{{ $exerciseData['template_exercise']->exercise_id }}">
                                                                <span class="visually-hidden">Delete</span>
                                                            </x-button>
                                                        @endif
                                                    </div>
                                                @elseif($set['is_active'])
                                                    <x-button variant="save" size="sm" class="btn-success log-set-btn" 
                                                            data-exercise-id="{{ $exerciseData['template_exercise']->exercise_id }}"
                                                            data-set-number="{{ $set['set_number'] }}"
                                                            data-rest-seconds="{{ $exerciseData['rest_seconds'] }}" icon="bi-check-circle">
                                                        Log
                                                    </x-button>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-lock"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                        
                        <x-button variant="danger" type="submit" size="lg" class="w-100 py-3 shadow" 
                                confirm="Complete this workout?" icon="bi-check-circle-fill">
                            Complete Workout
                        </x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fixed Rest Timer at Bottom -->
<div id="timer-card" class="position-fixed bottom-0 start-0 end-0 bg-info shadow-lg" style="display: none; z-index: 1050;">
    <div class="container-fluid px-3 px-lg-4">
        <div class="row align-items-center">
            <div class="col-xl-10 offset-xl-1">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light bg-opacity-25 rounded d-flex align-items-center justify-content-center text-white fs-4" style="width: 50px; height: 50px;">
                            <i class="bi bi-stopwatch"></i>
                        </div>
                        <div>
                            <div class="small text-white-50 fw-semibold">REST TIMER</div>
                            <div id="timer-display" class="h2 mb-0 fw-bold text-white">00:00</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="timer-add-30" class="btn btn-light">
                            <i class="bi bi-plus-circle"></i> +30s
                        </button>
                        <button id="timer-stop" class="btn btn-outline-light">
                            <i class="bi bi-x-circle"></i> Stop
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Make input fields wide enough for 3-digit numbers */
.table input[type="number"] {
    min-width: 80px;
}

/* Add padding to body when timer is visible to prevent content overlap */
body.timer-active {
    padding-bottom: 100px;
}
</style>
@endpush

@push('scripts')
<script>
let timerInterval = null;
let timerSeconds = 0;

// Audio notification function - "Glass ding" sound
function playTimerSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // Create 3 "glass ding" sounds (like tapping a wine glass)
        for (let i = 0; i < 1; i++) {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            // High frequency for glass-like "ding" sound
            oscillator.frequency.value = 1200; // Higher = more glass-like
            oscillator.type = 'sine';
            
            const startTime = audioContext.currentTime + (i * 0.4);
            
            // Quick attack and fast decay (like glass resonance)
            gainNode.gain.setValueAtTime(0, startTime);
            gainNode.gain.linearRampToValueAtTime(0.4, startTime + 0.01); // Quick attack
            gainNode.gain.exponentialRampToValueAtTime(0.01, startTime + 0.8); // Slow fade like glass
            
            oscillator.start(startTime);
            oscillator.stop(startTime + 0.8);
        }
    } catch (e) {
        console.log('Audio not supported');
    }
}

// Timer Functions
function startTimer(seconds) {
    stopTimer();
    timerSeconds = seconds;
    
    // Store timer end time in localStorage
    const endTime = Date.now() + (seconds * 1000);
    localStorage.setItem('workout_timer_end', endTime);
    
    // Show timer bar and add padding to body
    document.getElementById('timer-card').style.display = 'block';
    document.body.classList.add('timer-active');
    updateTimerDisplay();
    
    timerInterval = setInterval(() => {
        timerSeconds--;
        updateTimerDisplay();
        
        if (timerSeconds <= 0) {
            stopTimer();
            playTimerSound();
        }
    }, 1000);
}

function stopTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    document.getElementById('timer-card').style.display = 'none';
    document.body.classList.remove('timer-active');
    timerSeconds = 0;
    localStorage.removeItem('workout_timer_end');
}

function updateTimerDisplay() {
    const minutes = Math.floor(timerSeconds / 60);
    const seconds = timerSeconds % 60;
    document.getElementById('timer-display').textContent = 
        `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

// Check if timer should resume on page load
function resumeTimerIfNeeded() {
    const timerEndTime = localStorage.getItem('workout_timer_end');
    if (timerEndTime) {
        const remainingMs = parseInt(timerEndTime) - Date.now();
        if (remainingMs > 0) {
            const remainingSeconds = Math.ceil(remainingMs / 1000);
            startTimer(remainingSeconds);
        } else {
            localStorage.removeItem('workout_timer_end');
        }
    }
}

// Resume timer on page load
resumeTimerIfNeeded();

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

// Handler function for logging sets (reusable)
function handleLogSetClick() {
    const btn = this;
        const exerciseId = this.dataset.exerciseId;
        const setNumber = this.dataset.setNumber;
        const restSeconds = parseInt(this.dataset.restSeconds);
        
        // Get input values
        const weightInput = document.querySelector(`.weight-input-${exerciseId}[data-set="${setNumber}"]`);
        const repsInput = document.querySelector(`.reps-input-${exerciseId}[data-set="${setNumber}"]`);
        
        const weight = parseFloat(weightInput.value);
        const reps = parseInt(repsInput.value);
        
        // Validate
        if (!weight || weight <= 0 || !reps || reps <= 0) {
            alert('Please enter valid weight and reps');
            return;
        }
        
        // Disable button and show loading
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        
        // Log the set
        fetch('{{ route('workouts.log-set', $session) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                exercise_id: exerciseId,
                set_number: setNumber,
                weight: weight,
                reps: reps,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find the current row (the one we just logged)
                const currentRow = btn.closest('tr');
                const tbody = currentRow.closest('tbody');
                
                // Remove delete button from previous last completed set (if any)
                const allCompletedRows = tbody.querySelectorAll('tr.table-success');
                allCompletedRows.forEach(row => {
                    const deleteBtn = row.querySelector('.delete-set-btn');
                    if (deleteBtn) {
                        deleteBtn.remove();
                    }
                });
                
                // Mark current set as completed
                currentRow.classList.remove('table-warning');
                currentRow.classList.add('table-success');
                
                // Update the row to show completed state
                const weightCell = currentRow.querySelector('td:nth-child(2)');
                const repsCell = currentRow.querySelector('td:nth-child(3)');
                const actionCell = currentRow.querySelector('td:nth-child(4)');
                
                weightCell.innerHTML = `<strong>${weight}</strong>`;
                repsCell.innerHTML = `<strong>${reps}</strong>`;
                actionCell.innerHTML = `<div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-outline-primary edit-set-btn" 
                                                    data-set-id="${data.set_log_id}"
                                                    data-exercise-id="${exerciseId}"
                                                    data-set-number="${setNumber}"
                                                    data-weight="${weight}"
                                                    data-reps="${reps}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-set-btn" 
                                                    data-set-id="${data.set_log_id}"
                                                    data-exercise-id="${exerciseId}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>`;
                
                // Update set number cell
                const setCell = currentRow.querySelector('td:nth-child(1)');
                setCell.innerHTML = `<i class="bi bi-check-circle-fill text-success"></i> ${setNumber}`;
                
                // Activate next set if there is one
                const nextRow = currentRow.nextElementSibling;
                if (nextRow && nextRow.tagName === 'TR') {
                    nextRow.classList.add('table-warning');
                    
                    // Update next row icons
                    const nextSetCell = nextRow.querySelector('td:nth-child(1)');
                    const nextSetNumber = parseInt(setNumber) + 1;
                    nextSetCell.innerHTML = `<i class="bi bi-arrow-right-circle-fill text-warning"></i> ${nextSetNumber}`;
                    
                    // Enable inputs and button in next row
                    const nextWeightCell = nextRow.querySelector('td:nth-child(2)');
                    const nextRepsCell = nextRow.querySelector('td:nth-child(3)');
                    const nextActionCell = nextRow.querySelector('td:nth-child(4)');
                    
                    const nextWeightInput = nextWeightCell?.querySelector('input[type="number"]');
                    const nextRepsInput = nextRepsCell?.querySelector('input[type="number"]');
                    const nextButton = nextActionCell?.querySelector('button');
                    
                    if (nextWeightInput) {
                        nextWeightInput.disabled = false;
                        nextWeightInput.classList.add(`weight-input-${exerciseId}`);
                        nextWeightInput.setAttribute('data-set', nextSetNumber);
                    }
                    if (nextRepsInput) {
                        nextRepsInput.disabled = false;
                        nextRepsInput.classList.add(`reps-input-${exerciseId}`);
                        nextRepsInput.setAttribute('data-set', nextSetNumber);
                    }
                    if (nextButton) {
                        nextButton.disabled = false;
                        nextButton.className = 'btn btn-sm btn-success log-set-btn';
                        nextButton.setAttribute('data-exercise-id', exerciseId);
                        nextButton.setAttribute('data-set-number', nextSetNumber);
                        nextButton.setAttribute('data-rest-seconds', restSeconds);
                        nextButton.innerHTML = '<i class="bi bi-check-circle"></i> Log';
                        
                        // Add event listener to new button
                        nextButton.addEventListener('click', handleLogSetClick);
                    }
                } else {
                    // All sets completed - mark exercise as complete
                    const exerciseCard = currentRow.closest('.card');
                    exerciseCard.classList.add('opacity-75', 'bg-success', 'bg-opacity-10');
                    
                    const exerciseNumber = exerciseCard.querySelector('.rounded-circle');
                    exerciseNumber.classList.remove('bg-primary');
                    exerciseNumber.classList.add('bg-success');
                    exerciseNumber.innerHTML = '<i class="bi bi-check-lg"></i>';
                }
                
                // Re-attach listeners to new buttons
                const editBtn = actionCell.querySelector('.edit-set-btn');
                const deleteBtn = actionCell.querySelector('.delete-set-btn');
                if (editBtn) {
                    editBtn.addEventListener('click', handleEditSetClick);
                }
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', handleDeleteSetClick);
                }
                
                // Start rest timer
                if (restSeconds > 0) {
                    startTimer(restSeconds);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error logging set. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Log';
        });
}

// Attach to all initial log set buttons
document.querySelectorAll('.log-set-btn').forEach(btn => {
    btn.addEventListener('click', handleLogSetClick);
});

// Handler function for deleting sets (reusable)
function handleDeleteSetClick() {
    const btn = this;
        if (!confirm('Delete this set?')) return;
        
    const setId = btn.dataset.setId;
        
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        
        fetch(`{{ route('workouts.session', $session) }}/sets/${setId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show the updated state
                window.location.reload();
            } else {
                // Show error message from backend
                alert(data.message || 'Error deleting set. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-trash"></i>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting set. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash"></i>';
        });
}

// Attach to all initial delete buttons
document.querySelectorAll('.delete-set-btn').forEach(btn => {
    btn.addEventListener('click', handleDeleteSetClick);
});

// Handler function for editing sets
function handleEditSetClick() {
    const btn = this;
    const setId = btn.dataset.setId;
    const exerciseId = btn.dataset.exerciseId;
    const setNumber = btn.dataset.setNumber;
    const currentWeight = btn.dataset.weight;
    const currentReps = btn.dataset.reps;
    
    const row = btn.closest('tr');
    const weightCell = row.querySelector('td:nth-child(2)');
    const repsCell = row.querySelector('td:nth-child(3)');
    const actionCell = row.querySelector('td:nth-child(4)');
    
    // Convert to edit mode
    row.classList.remove('table-success');
    row.classList.add('table-warning');
    
    // Replace weight display with input
    weightCell.innerHTML = `<input type="number" 
                                   class="form-control form-control-sm text-center edit-weight-input" 
                                   value="${currentWeight}" 
                                   step="1" 
                                   min="0">`;
    
    // Replace reps display with input
    repsCell.innerHTML = `<input type="number" 
                                 class="form-control form-control-sm text-center edit-reps-input" 
                                 value="${currentReps}" 
                                 min="0">`;
    
    // Replace buttons with save/cancel
    actionCell.innerHTML = `<div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-success save-edit-btn" 
                                        data-set-id="${setId}"
                                        data-exercise-id="${exerciseId}"
                                        data-set-number="${setNumber}">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-sm btn-secondary cancel-edit-btn"
                                        data-weight="${currentWeight}"
                                        data-reps="${currentReps}"
                                        data-set-id="${setId}"
                                        data-exercise-id="${exerciseId}"
                                        data-set-number="${setNumber}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>`;
    
    // Attach event listeners
    actionCell.querySelector('.save-edit-btn').addEventListener('click', handleSaveEditClick);
    actionCell.querySelector('.cancel-edit-btn').addEventListener('click', handleCancelEditClick);
}

// Save edited set
function handleSaveEditClick() {
    const btn = this;
    const setId = btn.dataset.setId;
    const exerciseId = btn.dataset.exerciseId;
    const setNumber = btn.dataset.setNumber;
    
    const row = btn.closest('tr');
    const weightInput = row.querySelector('.edit-weight-input');
    const repsInput = row.querySelector('.edit-reps-input');
    
    const weight = parseFloat(weightInput.value);
    const reps = parseInt(repsInput.value);
    
    // Validate
    if (!weight || weight <= 0 || !reps || reps <= 0) {
        alert('Please enter valid weight and reps');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    fetch(`{{ route('workouts.session', $session) }}/sets/${setId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            weight: weight,
            reps: reps,
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Convert back to completed state
            row.classList.remove('table-warning');
            row.classList.add('table-success');
            
            const weightCell = row.querySelector('td:nth-child(2)');
            const repsCell = row.querySelector('td:nth-child(3)');
            const actionCell = row.querySelector('td:nth-child(4)');
            
            weightCell.innerHTML = `<strong>${weight}</strong>`;
            repsCell.innerHTML = `<strong>${reps}</strong>`;
            actionCell.innerHTML = `<div class="d-flex gap-2 justify-content-center">
                                        <button class="btn btn-sm btn-outline-primary edit-set-btn" 
                                                data-set-id="${setId}"
                                                data-exercise-id="${exerciseId}"
                                                data-set-number="${setNumber}"
                                                data-weight="${weight}"
                                                data-reps="${reps}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-set-btn" 
                                                data-set-id="${setId}"
                                                data-exercise-id="${exerciseId}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>`;
            
            // Re-attach event listeners
            actionCell.querySelector('.edit-set-btn').addEventListener('click', handleEditSetClick);
            actionCell.querySelector('.delete-set-btn').addEventListener('click', handleDeleteSetClick);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating set. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg"></i>';
    });
}

// Cancel editing
function handleCancelEditClick() {
    const btn = this;
    const setId = btn.dataset.setId;
    const exerciseId = btn.dataset.exerciseId;
    const setNumber = btn.dataset.setNumber;
    const weight = btn.dataset.weight;
    const reps = btn.dataset.reps;
    
    const row = btn.closest('tr');
    
    // Convert back to completed state
    row.classList.remove('table-warning');
    row.classList.add('table-success');
    
    const weightCell = row.querySelector('td:nth-child(2)');
    const repsCell = row.querySelector('td:nth-child(3)');
    const actionCell = row.querySelector('td:nth-child(4)');
    
    weightCell.innerHTML = `<strong>${weight}</strong>`;
    repsCell.innerHTML = `<strong>${reps}</strong>`;
    actionCell.innerHTML = `<div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-outline-primary edit-set-btn" 
                                        data-set-id="${setId}"
                                        data-exercise-id="${exerciseId}"
                                        data-set-number="${setNumber}"
                                        data-weight="${weight}"
                                        data-reps="${reps}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-set-btn" 
                                        data-set-id="${setId}"
                                        data-exercise-id="${exerciseId}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>`;
    
    // Re-attach event listeners
    actionCell.querySelector('.edit-set-btn').addEventListener('click', handleEditSetClick);
    actionCell.querySelector('.delete-set-btn').addEventListener('click', handleDeleteSetClick);
}

// Attach to all initial edit buttons
document.querySelectorAll('.edit-set-btn').forEach(btn => {
    btn.addEventListener('click', handleEditSetClick);
});
</script>
@endpush
