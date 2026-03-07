@extends('layouts.app')

@section('title', 'Edit Workout Split')

@section('content')
<div class="space-y-6">
    <x-common.page-breadcrumb
        pageTitle="Edit Workout Split"
        :items="[['label' => 'Workout Splits', 'url' => route('workout-splits.index')]]"
    />

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Edit split day configuration
            </p>
        </div>
    </div>

    <form action="{{ route('workout-splits.update', $workoutSplit) }}" method="POST">
        @csrf
        @method('PUT')

        @include('workout-splits._form')
    </form>
</div>
@endsection
