@extends('layouts.app')

@section('title', 'Create Workout Split')

@section('content')
<div class="space-y-6">
    <x-common.page-breadcrumb
        pageTitle="Create Workout Split"
        :items="[['label' => 'Workout Splits', 'url' => route('workout-splits.index')]]"
    />

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Add a new split day configuration
            </p>
        </div>
    </div>

    <form action="{{ route('workout-splits.store') }}" method="POST">
        @csrf

        @php
            $workoutSplit = new \App\Models\WorkoutSplit();
        @endphp

        @include('workout-splits._form')
    </form>
</div>
@endsection
