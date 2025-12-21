@extends('layouts.app')

@section('title', 'Create Partner')

@section('content')
    <div class="mb-6">
        <a href="{{ route('partners.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M10.9235 12.667L6.75683 8.50033L10.9235 4.33366" stroke="" stroke-width="1.2"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Back to Partners
        </a>
    </div>
    <x-common.page-breadcrumb pageTitle="Create New Partner" />

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

    <x-common.component-card title="Partner Information" desc="Fill in the details to create a new partner">
        <form action="{{ route('partners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Left Column - Partner Details -->
                <div class="space-y-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Partner Details</h3>

                    <!-- Partner Name -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Partner Name <span class="text-red-500">*</span>
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

                    <!-- Slug -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            name="slug"
                            id="slug"
                            value="{{ old('slug') }}"
                            readonly
                            required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 cursor-not-allowed" />
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Auto-generated from partner name</p>
                    </div>

                    <!-- Domain -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Domain
                        </label>
                        <input type="text"
                            name="domain"
                            id="domain"
                            value="{{ old('domain') }}"
                            placeholder="partner.yourdomain.com"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('domain') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                        @error('domain')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Optional subdomain for this partner</p>
                    </div>

                    <!-- Active Checkbox -->
                    <div x-data="{ checkboxToggle: {{ old('is_active', true) ? 'true' : 'false' }} }">
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
                                    {{ old('is_active', true) ? 'checked' : '' }} />
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
                            Active
                        </label>
                    </div>
                </div>

                <!-- Right Column - Branding -->
                <div class="space-y-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Branding</h3>

                    <!-- Primary Color -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Primary Color <span class="text-red-500">*</span>
                        </label>
                        <input type="color"
                            name="primary_color"
                            id="primary_color"
                            value="{{ old('primary_color', '#ff6b35') }}"
                            required
                            class="h-12 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-700" />
                        @error('primary_color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secondary Color -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Secondary Color <span class="text-red-500">*</span>
                        </label>
                        <input type="color"
                            name="secondary_color"
                            id="secondary_color"
                            value="{{ old('secondary_color', '#4ecdc4') }}"
                            required
                            class="h-12 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-700" />
                        @error('secondary_color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Logo
                        </label>
                        <input type="file"
                            name="logo"
                            id="logo"
                            accept="image/*"
                            class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:text-white/90 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload partner logo (PNG, JPG, SVG)</p>
                    </div>

                    <!-- Font Family -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Font Family
                        </label>
                        <input type="text"
                            name="font_family"
                            id="font_family"
                            value="{{ old('font_family', 'Inter') }}"
                            placeholder="Inter, Poppins, Roboto"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('font_family') border-red-300 focus:border-red-300 focus:ring-red-500/10 dark:border-red-700 dark:focus:border-red-800 @enderror" />
                        @error('font_family')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                <a href="{{ route('partners.index') }}">
                    <x-ui.button variant="outline" size="md">
                        Cancel
                    </x-ui.button>
                </a>
                <x-ui.button variant="primary" size="md" type="submit">
                    Create Partner
                </x-ui.button>
            </div>
        </form>
    </x-common.component-card>

    @push('scripts')
        <script>
            // Auto-generate slug from name
            document.getElementById('name').addEventListener('input', function(e) {
                const slug = e.target.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
                document.getElementById('slug').value = slug;
            });
        </script>
    @endpush
@endsection
