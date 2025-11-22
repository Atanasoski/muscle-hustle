@extends('layouts.app')

@section('title', "Today's Workout")

@section('content')
<div class="container py-4">
    @if($template)
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 gap-md-3 mb-3">
                    <div class="bg-danger text-white rounded-3 p-2 p-md-3 shadow" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-calendar-day-fill fs-3 fs-md-1"></i>
                    </div>
                    <div>
                        <h1 class="h3 h2-md fw-bold mb-1">{{ $template->name }}</h1>
                        <p class="text-muted mb-0 small">
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
                    <div class="card-body p-3 p-md-4">
                        @if($template->description)
                            <div class="alert alert-light border mb-3 mb-md-4">
                                <i class="bi bi-info-circle text-danger me-2"></i>
                                <strong>Goal:</strong> {{ $template->description }}
                            </div>
                        @endif

                        @if($template->workoutTemplateExercises->count() > 0)
                            <h5 class="mb-3 fw-bold">
                                <i class="bi bi-list-check"></i> Exercise Plan
                            </h5>
                            
                            <div class="d-flex flex-column gap-2 gap-md-3">
                                @foreach($template->workoutTemplateExercises as $index => $exercise)
                                    <div class="card border-2 {{ $loop->first ? 'border-danger' : '' }}">
                                        <div class="card-body p-2 p-md-3">
                                            <div class="d-flex gap-2 gap-md-3">
                                                <div class="bg-danger text-white rounded-3 fw-bold d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; min-width: 35px; font-size: 0.9rem;">
                                                    {{ $index + 1 }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                                        <h6 class="mb-0 fw-bold small">
                                                            @if($exercise->exercise->category)
                                                                <span class="me-1">{{ $exercise->exercise->category->icon }}</span>
                                                            @endif
                                                            {{ $exercise->exercise->name }}
                                                        </h6>
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if($exercise->exercise->video_url)
                                                                <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" 
                                                                        type="button" 
                                                                        data-bs-toggle="collapse" 
                                                                        data-bs-target="#video-preview-{{ $exercise->id }}"
                                                                        style="white-space: nowrap;">
                                                                    <i class="bi bi-play-circle"></i>
                                                                    <span class="d-none d-md-inline">Watch</span>
                                                                </button>
                                                            @endif
                                                            @if($exercise->exercise->category)
                                                                <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                                                    {{ $exercise->exercise->category->name }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Video Player (Collapsible) -->
                                                    @if($exercise->exercise->video_url)
                                                        <div class="collapse mb-3" id="video-preview-{{ $exercise->id }}">
                                                            <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                                                                <iframe src="{{ str_replace('watch?v=', 'embed/', $exercise->exercise->video_url) }}" 
                                                                        allowfullscreen></iframe>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="d-flex flex-wrap gap-1 gap-md-2 mt-2">
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
                                <div class="alert alert-success border-0 mt-3 mt-md-4 mb-3">
                                    <div class="d-flex align-items-center gap-2 gap-md-3">
                                        <i class="bi bi-check-circle-fill fs-3 fs-md-1"></i>
                                        <div>
                                            <strong>Workout In Progress!</strong>
                                            <p class="mb-0 small">You've already started this workout. Let's finish strong!</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('workouts.session', $session) }}" class="btn btn-success btn-lg w-100 py-2 py-md-3 shadow">
                                    <i class="bi bi-play-circle-fill me-2"></i> Continue Workout
                                </a>
                            @else
                                <form action="{{ route('workouts.start') }}" method="POST" class="mt-3 mt-md-4">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{ $template->id }}">
                                    <button type="submit" class="btn btn-success btn-lg w-100 py-2 py-md-3 shadow">
                                        <i class="bi bi-play-circle-fill me-2"></i> 
                                        <span>Start Workout Now</span>
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
                    <div class="card-body p-3 p-md-4">
                        <h6 class="text-uppercase text-muted mb-3 fw-bold small">
                            <i class="bi bi-graph-up"></i> Workout Stats
                        </h6>
                        <div class="row g-2 g-md-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 h3-md fw-bold text-danger">{{ $template->workoutTemplateExercises->count() }}</div>
                                    <div class="small text-muted mt-1">Exercises</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 h3-md fw-bold text-success">{{ $template->workoutTemplateExercises->sum('target_sets') }}</div>
                                    <div class="small text-muted mt-1">Total Sets</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 h3-md fw-bold text-warning">~{{ $template->workoutTemplateExercises->count() * 5 }}</div>
                                    <div class="small text-muted mt-1">Minutes</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 h3-md fw-bold text-info">{{ $template->workoutTemplateExercises->avg('rest_seconds') ?? 90 }}s</div>
                                    <div class="small text-muted mt-1">Avg Rest</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-3 p-md-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-lightbulb-fill text-warning"></i> Pro Tips
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i>
                                <span class="small">Warm up for 5-10 minutes</span>
                            </li>
                            <li class="mb-2 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i>
                                <span class="small">Focus on proper form</span>
                            </li>
                            <li class="mb-2 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i>
                                <span class="small">Stay hydrated</span>
                            </li>
                            <li class="mb-0 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i>
                                <span class="small">Control your breathing</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Workout Today -->
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="text-center py-5 mb-4">
                    <i class="bi bi-calendar-x display-1 text-muted opacity-50 mb-4"></i>
                    <h2 class="mb-3">No Workout Scheduled</h2>
                    <p class="text-muted fs-5 mb-4">
                        You don't have a workout planned for today. Let's set up your weekly workout schedule!
                    </p>
                </div>

                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="bi bi-calendar-week-fill fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Plan Your Training Week</h4>
                        <p class="text-muted mb-4">
                            Assign workout templates to specific days of the week and stay on track with your fitness goals.
                        </p>
                        <a href="{{ route('planner.workouts') }}" class="btn btn-danger btn-lg px-5">
                            <i class="bi bi-calendar-plus me-2"></i> Go to Workout Planner
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

