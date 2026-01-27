@extends('layouts.app')

@section('title', 'Create Workout Template for ' . $plan->name)

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Create Workout Template'" :items="[['label' => 'Users', 'url' => route('users.index')], ['label' => $plan->user->name, 'url' => route('users.show', $plan->user)], ['label' => $plan->name, 'url' => route('plans.show', $plan)]]" />

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
        <form action="{{ route('workouts.store', $plan) }}" method="POST">
            @csrf
            <input type="hidden" name="plan_id" value="{{ $plan->id }}">

            <div class="space-y-5">
                <!-- Workout Name -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Workout Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('name') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Description
                    </label>
                    <textarea name="description"
                        id="description"
                        rows="4"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('description') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Day of Week -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Day of Week
                    </label>
                    <select name="day_of_week"
                        id="day_of_week"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('day_of_week') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror">
                        <option value="">Select day (optional)</option>
                        <option value="0" {{ old('day_of_week') == '0' ? 'selected' : '' }}>Monday</option>
                        <option value="1" {{ old('day_of_week') == '1' ? 'selected' : '' }}>Tuesday</option>
                        <option value="2" {{ old('day_of_week') == '2' ? 'selected' : '' }}>Wednesday</option>
                        <option value="3" {{ old('day_of_week') == '3' ? 'selected' : '' }}>Thursday</option>
                        <option value="4" {{ old('day_of_week') == '4' ? 'selected' : '' }}>Friday</option>
                        <option value="5" {{ old('day_of_week') == '5' ? 'selected' : '' }}>Saturday</option>
                        <option value="6" {{ old('day_of_week') == '6' ? 'selected' : '' }}>Sunday</option>
                    </select>
                    @error('day_of_week')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <a href="{{ route('plans.show', $plan) }}">
                    <x-ui.button variant="outline" size="md">
                        Cancel
                    </x-ui.button>
                </a>
                <x-ui.button variant="primary" size="md" type="submit">
                    Create Workout Template
                </x-ui.button>
            </div>
        </form>
    </x-common.component-card>
@endsection
