@extends('layouts.app')

@section('title', 'Workout Templates')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold">
                <i class="bi bi-journal-text text-danger"></i> Workout Templates
            </h1>
            <p class="text-muted">Create and manage your workout routines</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('workout-templates.create') }}" class="btn btn-danger btn-lg shadow">
                <i class="bi bi-plus-circle me-2"></i> New Template
            </a>
        </div>
    </div>

    @if($templates->count() > 0)
        <div class="row g-4">
            @foreach($templates as $template)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header {{ $template->day_of_week !== null ? 'bg-danger' : 'bg-secondary' }} bg-opacity-10 border-0 py-3">
                            @if($template->day_of_week !== null)
                                @php
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                    $dayIcons = ['📅', '📅', '📅', '📅', '📅', '📅', '📅'];
                                @endphp
                                <span class="badge bg-danger text-white">
                                    {{ $dayIcons[$template->day_of_week] }} {{ $days[$template->day_of_week] }}
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-dash-circle"></i> Not Scheduled
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-2">{{ $template->name }}</h5>
                            <p class="card-text text-muted mb-3">
                                {{ $template->description ?: 'No description provided' }}
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-list-check"></i> {{ $template->workoutTemplateExercises->count() }} exercises
                                </span>
                                @if($template->workoutTemplateExercises->sum('target_sets') > 0)
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-layers"></i> {{ $template->workoutTemplateExercises->sum('target_sets') }} sets
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 p-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('workout-templates.edit', $template) }}" class="btn btn-outline-danger flex-fill">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('workout-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Delete this template?')" class="flex-fill">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted opacity-50 mb-4"></i>
                        <h4 class="mb-3">No Templates Yet</h4>
                        <p class="text-muted mb-4">Create your first workout template to get started!</p>
                        <a href="{{ route('workout-templates.create') }}" class="btn btn-danger btn-lg">
                            <i class="bi bi-plus-circle me-2"></i> Create Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
