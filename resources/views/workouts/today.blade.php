@extends('layouts.app')

@section('title', "Today's Workout")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4">
                <i class="bi bi-calendar-day"></i> Today's Workout
            </h1>

            @if($template)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $template->name }}</h5>
                    </div>
                    <div class="card-body">
                        @if($template->description)
                            <p class="mb-3">{{ $template->description }}</p>
                        @endif

                        @if($template->workoutTemplateExercises->count() > 0)
                            <h6>Planned Exercises:</h6>
                            <ol>
                                @foreach($template->workoutTemplateExercises as $exercise)
                                    <li>
                                        {{ $exercise->exercise->name }}
                                        @if($exercise->target_sets && $exercise->target_reps)
                                            - {{ $exercise->target_sets }}×{{ $exercise->target_reps }}
                                        @endif
                                        @if($exercise->target_weight)
                                            @ {{ $exercise->target_weight }}kg
                                        @endif
                                    </li>
                                @endforeach
                            </ol>

                            @if($session)
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i> You already started today's workout!
                                </div>
                                <a href="{{ route('workouts.session', $session) }}" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-play-circle"></i> Continue Workout
                                </a>
                            @else
                                <form action="{{ route('workouts.start') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{ $template->id }}">
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="bi bi-play-circle"></i> Start Workout
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> This template has no exercises yet.
                            </div>
                            <a href="{{ route('workout-templates.edit', $template) }}" class="btn btn-primary">
                                Add Exercises
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No workout planned for today. Check your <a href="{{ route('planner.workouts') }}">weekly plan</a>!
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Start a Custom Workout</h5>
                    </div>
                    <div class="card-body">
                        <p>Want to workout anyway? Start a free-form session:</p>
                        <form action="{{ route('workouts.start') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-play-circle"></i> Start Free Workout
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

