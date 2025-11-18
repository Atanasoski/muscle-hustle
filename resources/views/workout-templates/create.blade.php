@extends('layouts.app')

@section('title', 'Create Workout Template')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4"><i class="bi bi-plus-circle"></i> Create Workout Template</h1>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('workout-templates.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="day_of_week" class="form-label">Assign to Day (optional)</label>
                            <select class="form-select @error('day_of_week') is-invalid @enderror" 
                                    id="day_of_week" name="day_of_week">
                                <option value="">Not assigned</option>
                                <option value="0" {{ old('day_of_week') == '0' ? 'selected' : '' }}>Monday</option>
                                <option value="1" {{ old('day_of_week') == '1' ? 'selected' : '' }}>Tuesday</option>
                                <option value="2" {{ old('day_of_week') == '2' ? 'selected' : '' }}>Wednesday</option>
                                <option value="3" {{ old('day_of_week') == '3' ? 'selected' : '' }}>Thursday</option>
                                <option value="4" {{ old('day_of_week') == '4' ? 'selected' : '' }}>Friday</option>
                                <option value="5" {{ old('day_of_week') == '5' ? 'selected' : '' }}>Saturday</option>
                                <option value="6" {{ old('day_of_week') == '6' ? 'selected' : '' }}>Sunday</option>
                            </select>
                            @error('day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('workout-templates.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

