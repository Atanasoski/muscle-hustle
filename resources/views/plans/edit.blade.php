@extends('layouts.app')

@section('title', 'Edit Program - ' . $plan->name)

@section('content')
    <x-common.page-breadcrumb 
        :pageTitle="'Edit Program'" 
        :items="[
            ['label' => 'Programs', 'url' => route('partner.programs.index')],
            ['label' => $plan->name, 'url' => route('partner.programs.show', $plan)]
        ]" 
    />

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

    <x-common.component-card title="Program Information" desc="Update program details">
        @include('plans._form', [
            'plan' => $plan,
            'action' => route('partner.programs.update', $plan),
            'method' => 'PUT',
            'context' => 'library'
        ])
    </x-common.component-card>
@endsection
