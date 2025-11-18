@extends('layouts.app')

@section('title', 'Weekly Workout Planner')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="bi bi-calendar-week"></i> Weekly Workout Planner</h1>

    <div class="row">
        @php
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        @endphp
        
        @foreach($days as $index => $day)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $day }}</h5>
                    </div>
                    <div class="card-body">
                        @if($weeklyPlan[$index])
                            <h6 class="card-title">{{ $weeklyPlan[$index]->name }}</h6>
                            <p class="card-text text-muted">{{ $weeklyPlan[$index]->description ?: 'No description' }}</p>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('workout-templates.edit', $weeklyPlan[$index]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('planner.workouts.unassign') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{ $weeklyPlan[$index]->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle"></i> Remove
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="text-muted">No workout assigned</p>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#assignModal{{ $index }}">
                                <i class="bi bi-plus-circle"></i> Assign Workout
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assign Modal -->
            <div class="modal fade" id="assignModal{{ $index }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('planner.workouts.assign') }}" method="POST">
                            @csrf
                            <input type="hidden" name="day_of_week" value="{{ $index }}">
                            <div class="modal-header">
                                <h5 class="modal-title">Assign Workout to {{ $day }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label">Select Template</label>
                                <select class="form-select" name="template_id" required>
                                    <option value="">Choose a template...</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Assign</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5 class="card-title">Your Templates</h5>
            @if($templates->count() > 0)
                <div class="list-group">
                    @foreach($templates as $template)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $template->name }}</strong>
                                @if($template->day_of_week !== null)
                                    <span class="badge bg-primary ms-2">{{ $days[$template->day_of_week] }}</span>
                                @endif
                            </div>
                            <a href="{{ route('workout-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No templates yet. <a href="{{ route('workout-templates.create') }}">Create one</a> to get started!</p>
            @endif
        </div>
    </div>
</div>
@endsection

