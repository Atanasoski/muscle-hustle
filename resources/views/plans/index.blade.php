@extends('layouts.app')

@section('title', 'Plans - ' . $user->name)

@section('content')
<div class="space-y-6">
    <x-common.page-breadcrumb
        pageTitle="Plans"
        :items="[
            ['label' => 'Users', 'url' => route('users.index')],
            ['label' => $user->name, 'url' => route('users.show', $user)],
        ]"
    />

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-xl font-semibold text-gray-900 dark:text-white">Plans for {{ $user->name }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Manage and create plans for this user.</div>
        </div>
        <a href="{{ route('plans.create', $user) }}">
            <x-ui.button variant="primary">
                Create Plan
            </x-ui.button>
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
        @if($plans->count() > 0)
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($plans as $plan)
                    <div class="rounded-xl border border-gray-200 p-4 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900/40 {{ $plan->is_active ? 'bg-green-50/40 dark:bg-green-900/10 border-green-200 dark:border-green-900/40' : '' }}">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $plan->is_active ? 'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h2m-2 0V8m0 4v4m0-4H1m2 0h2m14-4h-2m2 0v4m0-4V8m0 4h2m-2 0h-2M6 12h12M6 12V8m0 4v4m12-4V8m0 4v4M6 12H4m14 0h2M6 16H4m14 0h2"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $plan->name }}
                                        </div>
                                        @if($plan->description)
                                            <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $plan->description }}</div>
                                        @else
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Plan</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    @if($plan->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 dark:bg-red-900/30 dark:text-red-300">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $plan->workout_templates_count ?? 0 }} templates
                                </div>
                                <a href="{{ route('plans.show', $plan) }}" class="flex items-center gap-1 text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">
                                    Manage
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $plans->links() }}
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-200 p-10 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                No plans yet. Create the first plan for this user.
            </div>
        @endif
    </div>
</div>
@endsection

