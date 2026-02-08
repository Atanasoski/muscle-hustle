@props([
    'plan' => null,
    'action' => '',
    'method' => 'POST',
    'context' => 'library', // 'library' or 'user'
    'user' => null,
    'cancelUrl' => null,
])

<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-5">
        <!-- Plan Name -->
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                {{ $context === 'library' ? 'Program' : 'Plan' }} Name <span class="text-red-500">*</span>
            </label>
            <input type="text"
                name="name"
                id="name"
                value="{{ old('name', $plan?->name) }}"
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
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('description') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror">{{ old('description', $plan?->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        @if($context === 'user')
            <!-- User context: Show type selector with conditional duration -->
            <div x-data="{ planType: '{{ old('type', $plan?->type?->value ?? 'custom') }}' }">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Plan Type <span class="text-red-500">*</span>
                </label>
                <select name="type"
                    id="type"
                    x-model="planType"
                    required
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('type') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror">
                    <option value="custom">Custom Plan</option>
                    <option value="program">Program</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <!-- Duration shown only for programs -->
                <div x-show="planType === 'program'" x-transition class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Duration (weeks)
                    </label>
                    <input type="number"
                        name="duration_weeks"
                        id="duration_weeks"
                        value="{{ old('duration_weeks', $plan?->duration_weeks) }}"
                        min="1"
                        max="52"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('duration_weeks') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                    @error('duration_weeks')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Number of weeks for this program (1-52)</p>
                </div>
            </div>

            <!-- Active Checkbox (only for user plans) -->
            <div x-data="{ checkboxToggle: {{ old('is_active', $plan?->is_active ?? false) ? 'true' : 'false' }} }">
                <input type="hidden" name="is_active" value="0" />
                <label for="is_active"
                    class="flex cursor-pointer items-center text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                    <div class="relative">
                        <input type="checkbox"
                            id="is_active"
                            name="is_active"
                            value="1"
                            class="sr-only"
                            @change="checkboxToggle = !checkboxToggle"
                            {{ old('is_active', $plan?->is_active ?? false) ? 'checked' : '' }} />
                        <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' :
                            'bg-transparent border-gray-300 dark:border-gray-700'"
                            class="hover:border-brand-500 dark:hover:border-brand-500 mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                            <span :class="checkboxToggle ? '' : 'opacity-0'">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white"
                                        stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    Set as Active Plan
                </label>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Only one plan can be active at a time. Activating this plan will deactivate all other plans for this user.</p>
            </div>
        @else
            <!-- Library context: Always type=program, always show duration -->
            <input type="hidden" name="type" value="program">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Duration (weeks)
                </label>
                <input type="number"
                    name="duration_weeks"
                    id="duration_weeks"
                    value="{{ old('duration_weeks', $plan?->duration_weeks) }}"
                    min="1"
                    max="52"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('duration_weeks') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                @error('duration_weeks')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Recommended duration for this program (1-52 weeks)</p>
            </div>
        @endif
    </div>

    <!-- Form Actions -->
    <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
        <a href="{{ $context === 'library' ? ($cancelUrl ?? route('partner.programs.index')) : route('plans.index', $user) }}">
            <x-ui.button variant="outline" size="md">
                Cancel
            </x-ui.button>
        </a>
        <x-ui.button variant="primary" size="md" type="submit">
            {{ $plan ? 'Update' : 'Create' }} {{ $context === 'library' ? 'Program' : 'Plan' }}
        </x-ui.button>
    </div>
</form>
