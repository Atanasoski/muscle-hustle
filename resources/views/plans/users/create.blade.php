@extends('layouts.app')

@section('title', 'Create Plan for ' . $user->name)

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Create Plan for ' . $user->name" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $user->name, 'url' => route('users.show', $user)]]" />

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

    <x-common.component-card title="Plan Information" :desc="'Create a new workout plan for ' . $user->name">
        @include('plans._form', [
            'action' => route('plans.store', $user),
            'method' => 'POST',
            'context' => 'user',
            'user' => $user
        ])
    </x-common.component-card>
@endsection
