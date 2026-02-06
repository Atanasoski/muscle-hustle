@extends('layouts.app')

@section('title', 'Edit Workout Template - ' . $workoutTemplate->name)

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Edit Workout Template'" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $user->name, 'url' => route('users.show', $user)], ['label' => $workoutTemplate->plan->name, 'url' => route('plans.show', $workoutTemplate->plan)], ['label' => $workoutTemplate->name, 'url' => route('workouts.show', $workoutTemplate)]]" />

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
            <div class="mb-2 text-sm font-semibold text-red-800 dark:text-red-400">
                There were some errors with your submission:
            </div>
            <ul class="list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-common.component-card title="Edit Workout Template Information" :desc="'Update workout template details'">
        @include('workout-templates._form', [
            'plan' => $workoutTemplate->plan,
            'workoutTemplate' => $workoutTemplate,
            'action' => route('workouts.update', $workoutTemplate),
            'method' => 'PUT',
            'context' => 'user',
            'dayOfWeekOptions' => $dayOfWeekOptions,
            'dayOfWeekValue' => $dayOfWeekValue,
        ])
    </x-common.component-card>
@endsection
