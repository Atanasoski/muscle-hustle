@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4 fw-bold mb-2">
                <i class="bi bi-lightning-charge-fill"></i> Welcome back, {{ Auth::user()->name }}!
            </h1>
            <p class="text-muted fs-5">Let's crush your fitness goals today</p>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card h-100 border-0 shadow-lg">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-calendar-check display-4"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ $weekWorkouts->count() }}</h3>
                    <p class="mb-0 text-white-50">Workouts This Week</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card-secondary h-100 border-0 shadow-lg">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-fire display-4"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ $streak }}</h3>
                    <p class="mb-0 text-white-50">Day Streak</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card-success h-100 border-0 shadow-lg">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-trophy display-4"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ $weekWorkouts->sum(function($w) { return $w->exercises->count(); }) }}</h3>
                    <p class="mb-0 text-white-50">Exercises Planned</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card-info h-100 border-0 shadow-lg">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-journal-text display-4"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ Auth::user()->workoutTemplates->count() }}</h3>
                    <p class="mb-0 text-white-50">Templates</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Today's Workout -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-header bg-gradient-primary text-white border-0 py-3">
                    <h4 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-day-fill me-2"></i> Today's Workout
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($todayWorkout)
                        <div class="workout-card">
                            <h3 class="fw-bold mb-2">{{ $todayWorkout->name }}</h3>
                            <p class="text-muted mb-3 fs-6">{{ $todayWorkout->description }}</p>
                            
                            @if($todayWorkout->exercises->count() > 0)
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="badge-pill">
                                        <i class="bi bi-list-check me-1"></i>
                                        <strong>{{ $todayWorkout->exercises->count() }}</strong> exercises
                                    </div>
                                    <div class="badge-pill">
                                        <i class="bi bi-clock me-1"></i>
                                        ~{{ $todayWorkout->exercises->count() * 5 }} min
                                    </div>
                                </div>
                                
                                <a href="{{ route('workouts.today') }}" class="btn btn-success btn-lg w-100 py-3 shadow-sm">
                                    <i class="bi bi-play-circle-fill me-2"></i> Start Workout Now
                                </a>
                                
                                @if($recentWorkouts->count() > 0)
                                    <div class="mt-4">
                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-clock-history text-muted"></i> Recent Workouts
                                        </h6>
                                        @foreach($recentWorkouts as $workout)
                                            <div class="p-3 bg-light border rounded-3 mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <p class="mb-1 fw-bold">{{ $workout->workoutTemplate ? $workout->workoutTemplate->name : 'Free Workout' }}</p>
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar3"></i> {{ $workout->performed_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-success">
                                                            {{ $workout->setLogs->count() }} sets
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning border-0 mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    No exercises added yet to this template.
                                </div>
                                <a href="{{ route('workout-templates.edit', $todayWorkout) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle me-2"></i> Add Exercises
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                            <h5 class="text-muted mb-3">No workout scheduled for today</h5>
                            <p class="text-muted mb-4">Rest day or time to plan your week?</p>
                            <a href="{{ route('planner.workouts') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-calendar-plus me-2"></i> Plan Your Week
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Today's Meals -->
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-header bg-gradient-success text-white border-0 py-3">
                    <h4 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-egg-fried me-2"></i> Today's Nutrition
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($todayMeals->count() > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach($todayMeals as $meal)
                                <div class="meal-card p-3 rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-success mb-2">
                                                {{ ucfirst($meal->type) }}
                                            </span>
                                            <h6 class="fw-bold mb-0">{{ $meal->name }}</h6>
                                        </div>
                                    </div>
                                    
                                    @if($meal->serving_size)
                                        <div class="mb-2">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="bi bi-rulers text-muted" style="font-size: 0.85rem;"></i>
                                                <div class="text-muted small lh-sm">{{ $meal->serving_size }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex gap-2 flex-wrap mt-2">
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-fire"></i> {{ $meal->calories }} cal
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-lightning"></i> {{ $meal->protein }}g protein
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-droplet"></i> {{ $meal->carbs }}g carbs
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-circle"></i> {{ $meal->fat }}g fat
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="alert alert-success border-0 mb-0">
                                <strong>Daily Totals:</strong>
                                {{ $todayMeals->sum('calories') }} cal • {{ $todayMeals->sum('protein') }}g protein • {{ $todayMeals->sum('carbs') }}g carbs • {{ $todayMeals->sum('fat') }}g fat
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-egg display-1 text-muted mb-3"></i>
                            <h5 class="text-muted mb-3">No meals planned for today</h5>
                            <a href="{{ route('planner.meals') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i> Plan Your Meals
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-dark text-white border-0 py-3">
                    <h4 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-range-fill me-2"></i> This Week's Training Plan
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($weekWorkouts->count() > 0)
                        <div class="row g-3">
                            @php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            @endphp
                            @foreach($days as $index => $day)
                                @php
                                    $workout = $weekWorkouts->firstWhere('day_of_week', $index);
                                    $isToday = $index === $dayOfWeek;
                                @endphp
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="day-card {{ $isToday ? 'is-today' : '' }} {{ $workout ? 'has-workout' : 'rest-day' }}">
                                        <div class="day-card-header">
                                            <div class="day-name">{{ $day }}</div>
                                            @if($isToday)
                                                <span class="badge bg-primary today-badge">Today</span>
                                            @endif
                                        </div>
                                        <div class="day-card-body">
                                            @if($workout)
                                                <div class="workout-name">{{ $workout->name }}</div>
                                                <div class="workout-meta">
                                                    <i class="bi bi-list-check"></i> {{ $workout->exercises->count() }} exercises
                                                </div>
                                            @else
                                                <div class="rest-day-text">
                                                    <i class="bi bi-moon-stars-fill"></i> Rest & Recovery
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                            <h5 class="text-muted mb-3">No workouts planned this week</h5>
                            <p class="text-muted mb-4">Get started by planning your weekly training schedule</p>
                            <a href="{{ route('planner.workouts') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-calendar-plus me-2"></i> Plan Your Week
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Stat Cards - Signature Colors */
    .stat-card {
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c61 100%); /* Orange */
        color: white;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(255, 107, 53, 0.4) !important;
    }
    
    .stat-card-secondary {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); /* Navy */
        color: white;
    }
    
    .stat-card-secondary:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(44, 62, 80, 0.4) !important;
    }
    
    .stat-card-success {
        background: linear-gradient(135deg, #44bd32 0%, #4cd137 100%); /* Green */
        color: white;
    }
    
    .stat-card-success:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(68, 189, 50, 0.4) !important;
    }
    
    .stat-card-info {
        background: linear-gradient(135deg, #4ecdc4 0%, #5dd9d1 100%); /* Teal */
        color: white;
    }
    
    .stat-card-info:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(78, 205, 196, 0.4) !important;
    }

    .stat-icon i {
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    /* Card Headers - Signature Colors */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c61 100%); /* Orange */
    }
    
    .bg-gradient-secondary {
        background: linear-gradient(135deg, #4ecdc4 0%, #5dd9d1 100%); /* Teal */
    }
    
    .bg-gradient-dark {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); /* Navy */
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #44bd32 0%, #4cd137 100%); /* Green */
    }

    /* Hover Lift Effect */
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }

    /* Badge Pills */
    .badge-pill {
        background: var(--bg-tertiary);
        color: var(--text-primary);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Action Buttons */
    .btn-action {
        border-width: 2px;
        padding: 1rem;
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: translateX(5px);
    }
    
    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Day Cards */
    .day-card {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .day-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .day-card.is-today {
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.2);
    }
    
    .day-card.has-workout .day-card-header {
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c61 100%);
        color: white;
    }
    
    .day-card.rest-day .day-card-header {
        background: var(--bg-tertiary);
        color: var(--text-secondary);
    }
    
    .day-card-header {
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .day-name {
        font-weight: 700;
        font-size: 1rem;
        flex: 1;
    }
    
    .today-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    
    .day-card-body {
        padding: 1rem;
    }
    
    .workout-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .workout-meta {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }
    
    .rest-day-text {
        color: var(--text-secondary);
        font-style: italic;
        font-size: 0.9rem;
    }
    
    /* Meal Cards */
    .meal-card {
        background: var(--bg-secondary);
        transition: all 0.3s ease;
    }
    
    .meal-card:hover {
        background: var(--bg-tertiary);
        transform: translateX(3px);
    }
</style>
@endpush
@endsection
