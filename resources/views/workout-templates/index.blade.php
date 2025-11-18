@extends('layouts.app')

@section('title', 'Workout Templates')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="bi bi-journal-text"></i> Workout Templates</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('workout-templates.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Template
            </a>
        </div>
    </div>

    @if($templates->count() > 0)
        <div class="row">
            @foreach($templates as $template)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $template->name }}</h5>
                            <p class="card-text text-muted">{{ $template->description ?: 'No description' }}</p>
                            @if($template->day_of_week !== null)
                                @php
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                @endphp
                                <span class="badge bg-primary">{{ $days[$template->day_of_week] }}</span>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('workout-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('workout-templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
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
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No templates yet. Create your first workout template!
        </div>
    @endif
</div>
@endsection

