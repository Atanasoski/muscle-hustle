@extends('layouts.app')

@section('title', 'Create Workout Template for ' . $plan->name)

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Create Workout Template'" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $user->name, 'url' => route('users.show', $user)], ['label' => $plan->name, 'url' => route('plans.show', $plan)]]" />

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

    <x-common.component-card title="Workout Template Information" :desc="'Create a new workout template for ' . $plan->name">
        @include('workout-templates._form', [
            'plan' => $plan,
            'action' => route('workouts.store', $plan),
            'method' => 'POST',
            'context' => 'user',
            'dayOfWeekOptions' => $dayOfWeekOptions,
            'dayOfWeekValue' => $dayOfWeekValue,
        ])
    </x-common.component-card>
@endsection
