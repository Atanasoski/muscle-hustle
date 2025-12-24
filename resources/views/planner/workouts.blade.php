@extends('layouts.app')

@section('title', 'Weekly Workout Planner')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-6 fw-bold">
                <i class="bi bi-calendar-week-fill text-info"></i> Weekly Workout Planner
            </h1>
            <p class="text-muted">Plan your training week - assign workouts to specific days</p>
        </div>
    </div>

    <div class="row g-4">
        @foreach($days as $index => $day)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100 border-0 {{ $weeklyPlan[$index] ? 'border-start border-5 border-info' : '' }}">
                    <div class="card-header {{ $weeklyPlan[$index] ? 'bg-info' : 'bg-secondary' }} bg-opacity-10 border-0 py-3">
                        <h5 class="mb-0 fw-bold">{{ $day }}</h5>
                    </div>
                    <div class="card-body p-4">
                        @if($weeklyPlan[$index])
                            <h6 class="card-title fw-bold mb-2">{{ $weeklyPlan[$index]->name }}</h6>
                            <p class="card-text text-muted mb-3">{{ $weeklyPlan[$index]->description ?: 'No description provided' }}</p>
                            
                            <div class="d-flex gap-2">
                                <x-button variant="edit" size="sm" href="{{ route('workout-templates.edit', $weeklyPlan[$index]) }}" class="flex-fill">
                                    Edit
                                </x-button>
                                <form action="{{ route('planner.workouts.unassign') }}" method="POST" class="flex-fill" onsubmit="return confirm('Remove this workout from {{ $day }}?')">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{ $weeklyPlan[$index]->id }}">
                                    <x-button variant="cancel" size="sm" type="submit" class="btn-outline-secondary w-100" icon="bi-x-circle">
                                        Remove
                                    </x-button>
                                </form>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-3">
                                    <i class="bi bi-dash-circle"></i> No workout assigned
                                </p>
                                <x-button variant="info" data-bs-toggle="modal" data-bs-target="#assignModal{{ $index }}">
                                    Assign Workout
                                </x-button>
                            </div>
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
                                <x-button variant="cancel" data-bs-dismiss="modal">Cancel</x-button>
                                <x-button variant="save" type="submit">Assign</x-button>
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
                                    <span class="badge bg-primary text-white ms-2">{{ $days[$template->day_of_week] }}</span>
                                @endif
                            </div>
                            <x-button variant="edit" size="sm" href="{{ route('workout-templates.edit', $template) }}" class="btn-outline-primary">
                                <span class="visually-hidden">Edit</span>
                            </x-button>
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

