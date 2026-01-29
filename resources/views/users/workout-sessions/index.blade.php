@extends('layouts.app')

@section('title', 'Workout History - ' . $user->name)

@section('content')
<div class="space-y-6">
    <x-common.page-breadcrumb
        :pageTitle="'Workout History'"
        :items="[
            ['label' => 'Users', 'url' => route('users.index')],
            ['label' => $user->name, 'url' => route('users.show', $user)],
        ]"
    />

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900/30">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">Workout Sessions</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">All sessions for {{ $user->name }}</div>
            </div>
        </div>

        @if($workoutSessions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Workout</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($workoutSessions as $session)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $session->performed_at?->format('M d, Y') ?? '—' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $session->performed_at?->format('g:i A') ?? '' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $session->workoutTemplate?->name ?? 'Custom Workout' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($session->completed_at)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                            In Progress
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('users.workout-sessions.show', [$user, $session]) }}" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $workoutSessions->links() }}
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-200 p-10 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                No workout sessions yet
            </div>
        @endif
    </div>
</div>
@endsection

