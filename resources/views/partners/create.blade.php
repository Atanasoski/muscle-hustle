@extends('layouts.app')

@section('title', 'Create Partner')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('partners.index') }}">
            <x-bladewind::button color="gray" class="mb-4">
                ← Back to Partners
            </x-bladewind::button>
        </a>
    </div>

    <x-bladewind::card>
        <h2 class="text-2xl font-bold mb-6">Create New Partner</h2>
        
        @if ($errors->any())
            <x-bladewind::alert type="error" shade="dark" class="mb-6">
                <div class="font-semibold mb-2">There were some errors with your submission:</div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-bladewind::alert>
        @endif
        
        <form action="{{ route('partners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column - Partner Details -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Partner Details</h3>
                    
                    <x-bladewind::input 
                        label="Partner Name *"
                        name="name"
                        id="name"
                        required="true"
                        value="{{ old('name') }}"
                        error_message="{{ $errors->first('name') }}"
                    />

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Slug *</label>
                        <input type="text" 
                               name="slug" 
                               id="slug"
                               value="{{ old('slug') }}"
                               readonly
                               required
                               class="w-full rounded border border-gray-300 dark:border-gray-600 p-2 dark:bg-gray-700 dark:text-white bg-gray-100 dark:bg-gray-800 cursor-not-allowed">
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Auto-generated from partner name</p>
                    </div>

                    <x-bladewind::input 
                        label="Domain"
                        name="domain"
                        value="{{ old('domain') }}"
                        placeholder="partner.yourdomain.com"
                        error_message="{{ $errors->first('domain') }}"
                    />
                    <p class="text-sm text-gray-500 -mt-2 mb-4">Optional subdomain for this partner</p>

                    <x-bladewind::checkbox 
                        label="Active"
                        name="is_active"
                        value="1"
                        checked="true"
                    />
                </div>

                <!-- Right Column - Branding -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Branding</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Primary Color *</label>
                        <input type="color" 
                               name="primary_color" 
                               value="{{ old('primary_color', '#ff6b35') }}"
                               class="w-full h-12 rounded border cursor-pointer"
                               required>
                        @error('primary_color')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Secondary Color *</label>
                        <input type="color" 
                               name="secondary_color" 
                               value="{{ old('secondary_color', '#4ecdc4') }}"
                               class="w-full h-12 rounded border cursor-pointer"
                               required>
                        @error('secondary_color')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Logo</label>
                        <input type="file" 
                               name="logo" 
                               id="logo"
                               accept="image/*"
                               class="w-full rounded border border-gray-300 dark:border-gray-600 p-2 dark:bg-gray-700 dark:text-white">
                        @error('logo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Upload partner logo (PNG, JPG, SVG)</p>
                    </div>

                    <x-bladewind::input 
                        label="Font Family"
                        name="font_family"
                        value="{{ old('font_family', 'Inter') }}"
                        placeholder="Inter, Poppins, Roboto"
                        error_message="{{ $errors->first('font_family') }}"
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('partners.index') }}">
                    <x-bladewind::button color="gray">
                        Cancel
                    </x-bladewind::button>
                </a>
                <x-bladewind::button can_submit="true">
                    Create Partner
                </x-bladewind::button>
            </div>
        </form>
    </x-bladewind::card>
</div>

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
