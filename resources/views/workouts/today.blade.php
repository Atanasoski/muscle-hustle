@extends('layouts.app')

@section('title', "Today's Workout")

@section('content')
<div class="container py-4">
    @if($template)
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-danger text-white rounded-3 p-3 shadow" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-calendar-day-fill fs-1"></i>
                    </div>
                    <div>
                        <h1 class="display-5 fw-bold mb-1">{{ $template->name }}</h1>
                        <p class="text-muted mb-0 fs-5">
                            <i class="bi bi-clock"></i> Today's Training Session
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Workout Card -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="bi bi-fire"></i> Workout Details
                            </h4>
                            @if($template->workoutTemplateExercises->count() > 0)
                                <span class="badge bg-white text-danger fs-6 px-3 py-2">
                                    {{ $template->workoutTemplateExercises->count() }} Exercises
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if($template->description)
                            <div class="alert alert-light border mb-4">
                                <i class="bi bi-info-circle text-danger me-2"></i>
                                <strong>Goal:</strong> {{ $template->description }}
                            </div>
                        @endif

                        @if($template->workoutTemplateExercises->count() > 0)
                            <h5 class="mb-3 fw-bold">
                                <i class="bi bi-list-check"></i> Exercise Plan
                            </h5>
                            
                            <div class="d-flex flex-column gap-3">
                                @foreach($template->workoutTemplateExercises as $index => $exercise)
                                    <div class="card border-2 {{ $loop->first ? 'border-danger' : '' }}">
                                        <div class="card-body p-3">
                                            <div class="d-flex gap-3">
                                                <div class="bg-danger text-white rounded-3 fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                                    {{ $index + 1 }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-0 fw-bold">
                                                            <i class="bi bi-{{ getExerciseIcon($exercise->exercise->category) }} text-danger me-2"></i>
                                                            {{ $exercise->exercise->name }}
                                                        </h6>
                                                        <span class="badge bg-light text-dark">
                                                            {{ ucfirst($exercise->exercise->category) }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                                        @if($exercise->target_sets)
                                                            <span class="badge bg-secondary">
                                                                <i class="bi bi-layers"></i> {{ $exercise->target_sets }} sets
                                                            </span>
                                                        @endif
                                                        @if($exercise->target_reps)
                                                            <span class="badge bg-secondary">
                                                                <i class="bi bi-arrow-repeat"></i> {{ $exercise->target_reps }} reps
                                                            </span>
                                                        @endif
                                                        @if($exercise->target_weight)
                                                            <span class="badge bg-secondary">
                                                                <i class="bi bi-speedometer2"></i> {{ $exercise->target_weight }}kg
                                                            </span>
                                                        @endif
                                                        @if($exercise->rest_seconds)
                                                            <span class="badge bg-secondary">
                                                                <i class="bi bi-stopwatch"></i> {{ $exercise->rest_seconds }}s rest
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($session)
                                <div class="alert alert-success border-0 mt-4 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill fs-1 me-3"></i>
                                        <div>
                                            <strong>Workout In Progress!</strong>
                                            <p class="mb-0">You've already started this workout. Let's finish strong!</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('workouts.session', $session) }}" class="btn btn-success btn-lg w-100 py-3 shadow">
                                    <i class="bi bi-play-circle-fill me-2"></i> Continue Workout
                                </a>
                            @else
                                <form action="{{ route('workouts.start') }}" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{ $template->id }}">
                                    <button type="submit" class="btn btn-success btn-lg w-100 py-3 shadow">
                                        <i class="bi bi-play-circle-fill me-2 fs-4"></i> 
                                        <span class="fs-5">Start Workout Now</span>
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                <h5 class="text-muted mb-3">No Exercises Added</h5>
                                <p class="text-muted mb-4">This template doesn't have any exercises yet. Let's add some!</p>
                                <a href="{{ route('workout-templates.edit', $template) }}" class="btn btn-danger btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i> Add Exercises
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mb-4">
                <!-- Quick Stats -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase text-muted mb-3 fw-bold small">
                            <i class="bi bi-graph-up"></i> Workout Stats
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 fw-bold text-danger">{{ $template->workoutTemplateExercises->count() }}</div>
                                    <div class="small text-muted mt-1">Exercises</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 fw-bold text-success">{{ $template->workoutTemplateExercises->sum('target_sets') }}</div>
                                    <div class="small text-muted mt-1">Total Sets</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 fw-bold text-warning">~{{ $template->workoutTemplateExercises->count() * 5 }}</div>
                                    <div class="small text-muted mt-1">Minutes</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="display-6 fw-bold text-info">{{ $template->workoutTemplateExercises->avg('rest_seconds') ?? 90 }}s</div>
                                    <div class="small text-muted mt-1">Avg Rest</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-lightbulb-fill text-warning"></i> Pro Tips
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Warm up for 5-10 minutes</span>
                            </li>
                            <li class="mb-2 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Focus on proper form</span>
                            </li>
                            <li class="mb-2 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Stay hydrated</span>
                            </li>
                            <li class="mb-0 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Control your breathing</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Workout Today -->
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="text-center py-5 mb-4">
                    <i class="bi bi-calendar-x display-1 text-muted opacity-50 mb-4"></i>
                    <h2 class="mb-3">No Workout Scheduled</h2>
                    <p class="text-muted fs-5 mb-0">
                        You don't have a workout planned for today. Would you like to plan your week or start a free workout?
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                    <i class="bi bi-calendar-week-fill fs-1"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Plan Your Week</h5>
                                <p class="text-muted mb-3">Schedule your workouts for the entire week</p>
                                <a href="{{ route('planner.workouts') }}" class="btn btn-danger">
                                    <i class="bi bi-calendar-plus me-2"></i> Go to Planner
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                    <i class="bi bi-play-circle-fill fs-1"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Free Workout</h5>
                                <p class="text-muted mb-3">Start a workout without a template</p>
                                <form action="{{ route('workouts.start') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-play-circle me-2"></i> Start Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@php
function getExerciseIcon($category) {
    $icons = [
        'chest' => 'heart-pulse',
        'back' => 'arrow-bar-up',
        'legs' => 'bicycle',
        'shoulders' => 'person-arms-up',
        'arms' => 'lightning',
        'core' => 'bullseye',
    ];
    return $icons[strtolower($category)] ?? 'lightning-charge';
}
@endphp
