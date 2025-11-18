@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="bi bi-speedometer2"></i> Dashboard
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Today's Workout -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-day"></i> Today's Workout</h5>
                </div>
                <div class="card-body">
                    @if($todayWorkout)
                        <h6 class="card-title">{{ $todayWorkout->name }}</h6>
                        <p class="card-text text-muted">{{ $todayWorkout->description }}</p>
                        
                        @if($todayWorkout->exercises->count() > 0)
                            <p class="mb-2"><strong>{{ $todayWorkout->exercises->count() }}</strong> exercises planned</p>
                            <a href="{{ route('workouts.today') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-play-circle"></i> Start Workout
                            </a>
                        @else
                            <p class="text-muted">No exercises added yet.</p>
                            <a href="{{ route('workout-templates.edit', $todayWorkout) }}" class="btn btn-primary">
                                Add Exercises
                            </a>
                        @endif
                    @else
                        <p class="text-muted">No workout scheduled for today.</p>
                        <a href="{{ route('planner.workouts') }}" class="btn btn-primary">
                            <i class="bi bi-calendar-plus"></i> Plan Week
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('workout-templates.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> New Template
                        </a>
                        <a href="{{ route('planner.workouts') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-calendar-week"></i> Plan Workouts
                        </a>
                        <a href="{{ route('planner.meals') }}" class="btn btn-outline-success">
                            <i class="bi bi-egg-fried"></i> Plan Meals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Overview -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-week"></i> This Week's Plan</h5>
                </div>
                <div class="card-body">
                    @if($weekWorkouts->count() > 0)
                        <div class="row">
                            @php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            @endphp
                            @foreach($days as $index => $day)
                                @php
                                    $workout = $weekWorkouts->firstWhere('day_of_week', $index);
                                @endphp
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card {{ $index === $dayOfWeek ? 'border-primary' : '' }}">
                                        <div class="card-body p-2">
                                            <h6 class="card-subtitle mb-1 {{ $index === $dayOfWeek ? 'text-primary fw-bold' : 'text-muted' }}">
                                                {{ $day }}
                                                @if($index === $dayOfWeek)
                                                    <span class="badge bg-primary">Today</span>
                                                @endif
                                            </h6>
                                            @if($workout)
                                                <p class="card-text small mb-0">{{ $workout->name }}</p>
                                            @else
                                                <p class="card-text small text-muted mb-0"><em>Rest day</em></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No workouts planned this week. <a href="{{ route('planner.workouts') }}">Plan your week</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
