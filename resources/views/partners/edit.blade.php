@extends('layouts.app')

@section('title', 'Edit Partner')

@section('content')
<div class="mb-6">
    <a href="{{ route('partners.index') }}" class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        BACK TO PARTNERS
    </a>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <!-- Card Header -->
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Edit Partner: {{ $partner->name }}</h3>
    </div>

    <!-- Card Body -->
    <div class="p-6">
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-error-500/20 bg-error-50 p-4 dark:bg-error-500/15">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 shrink-0" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18.3333C14.6024 18.3333 18.3333 14.6024 18.3333 10C18.3333 5.39763 14.6024 1.66667 10 1.66667C5.39763 1.66667 1.66667 5.39763 1.66667 10C1.66667 14.6024 5.39763 18.3333 10 18.3333ZM10 6.66667C10.4602 6.66667 10.8333 7.03976 10.8333 7.5V10.8333C10.8333 11.2936 10.4602 11.6667 10 11.6667C9.53976 11.6667 9.16667 11.2936 9.16667 10.8333V7.5C9.16667 7.03976 9.53976 6.66667 10 6.66667ZM10 14.1667C10.4602 14.1667 10.8333 13.7936 10.8333 13.3333C10.8333 12.8731 10.4602 12.5 10 12.5C9.53976 12.5 9.16667 12.8731 9.16667 13.3333C9.16667 13.7936 9.53976 14.1667 10 14.1667Z" fill="currentColor"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-medium text-error-600 dark:text-error-500 mb-2">There were some errors with your submission:</p>
                        <ul class="list-disc list-inside space-y-1 text-sm text-error-600 dark:text-error-500">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('partners.update', $partner) }}" method="POST" enctype="multipart/form-data" id="partner-form">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column - Partner Details -->
                <div class="space-y-5">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-5">Partner Details</h4>
                    
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Partner Name <span class="text-error-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $partner->name) }}"
                            required
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('name') border-error-500 @enderror" />
                        @error('name')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Slug <span class="text-error-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="slug" 
                            name="slug" 
                            value="{{ old('slug', $partner->slug) }}"
                            readonly
                            required
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" />
                        @error('slug')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Cannot be changed after creation</p>
                    </div>

                    <div>
                        <label for="domain" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Domain
                        </label>
                        <input 
                            type="text" 
                            id="domain" 
                            name="domain" 
                            value="{{ old('domain', $partner->domain) }}"
                            placeholder="partner.yourdomain.com"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('domain') border-error-500 @enderror" />
                        @error('domain')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Optional subdomain</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Active
                        </label>
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                value="1"
                                {{ old('is_active', $partner->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800" />
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-400">Active</label>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Branding -->
                <div class="space-y-5">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-5">Branding</h4>
                    
                    @php
                        // Helper function to convert RGB to hex (for backward compatibility with existing data)
                        function rgbToHex($rgb) {
                            if (!$rgb) return '#ff6b35';
                            // Check if already hex format
                            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $rgb)) {
                                return $rgb;
                            }
                            // Convert RGB format to hex
                            $parts = explode(',', $rgb);
                            if (count($parts) !== 3) return '#ff6b35';
                            $r = (int)trim($parts[0]);
                            $g = (int)trim($parts[1]);
                            $b = (int)trim($parts[2]);
                            return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . 
                                   str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . 
                                   str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
                        }
                        $primaryHex = old('primary_color', $partner->identity ? rgbToHex($partner->identity->primary_color) : '#ff6b35');
                        $secondaryHex = old('secondary_color', $partner->identity ? rgbToHex($partner->identity->secondary_color) : '#4ecdc4');
                    @endphp
                    <div>
                        <label for="primary_color" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Primary Color <span class="text-error-500">*</span>
                        </label>
                        <input 
                            type="color" 
                            id="primary_color" 
                            name="primary_color" 
                            value="{{ $primaryHex }}"
                            class="w-full h-12 rounded-lg border border-gray-300 cursor-pointer dark:border-gray-700 @error('primary_color') border-error-500 @enderror"
                            required>
                        @error('primary_color')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="secondary_color" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Secondary Color <span class="text-error-500">*</span>
                        </label>
                        <input 
                            type="color" 
                            id="secondary_color" 
                            name="secondary_color" 
                            value="{{ $secondaryHex }}"
                            class="w-full h-12 rounded-lg border border-gray-300 cursor-pointer dark:border-gray-700 @error('secondary_color') border-error-500 @enderror"
                            required>
                        @error('secondary_color')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="logo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Logo
                        </label>
                        @if($partner->identity?->logo)
                            <div class="mb-2 flex items-center gap-3">
                                <img src="{{ asset($partner->identity->logo) }}" alt="Current logo" class="h-12 w-12 object-contain border border-gray-200 rounded-lg dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Current logo</span>
                            </div>
                        @endif
                        <input 
                            type="file" 
                            id="logo" 
                            name="logo" 
                            accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                            class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('logo') border-error-500 @enderror" />
                        @error('logo')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Upload new logo to replace (PNG, JPG, SVG)</p>
                    </div>

                    <div>
                        <label for="font_family" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Font Family
                        </label>
                        <input 
                            type="text" 
                            id="font_family" 
                            name="font_family" 
                            value="{{ old('font_family', $partner->identity->font_family ?? 'Inter') }}"
                            placeholder="Inter, Poppins, Roboto"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('font_family') border-error-500 @enderror" />
                        @error('font_family')
                            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('partners.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    CANCEL
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-600 dark:bg-blue-500 dark:hover:bg-blue-600">
                    UPDATE PARTNER
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
