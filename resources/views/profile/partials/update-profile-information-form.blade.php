<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method('patch')

    <!-- Profile Photo -->
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Profile Photo</label>
        <div class="flex items-center gap-4">
            @if($user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo" 
                     class="h-20 w-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
            @else
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-500 text-2xl font-bold text-white">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif
            <div class="flex-1">
                <input 
                    type="file" 
                    id="profile_photo" 
                    name="profile_photo" 
                    accept="image/*"
                    class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('profile_photo') border-error-500 @enderror" />
                @error('profile_photo')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">JPG, PNG, or GIF (Max 2MB)</p>
                
                @if($user->profile_photo)
                    <button 
                        type="button" 
                        onclick="if(confirm('Remove your profile photo?')) { document.getElementById('removePhotoForm').submit(); }"
                        class="mt-2 inline-flex items-center text-sm text-error-600 hover:text-error-700 dark:text-error-500 dark:hover:text-error-400">
                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Remove Photo
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Name -->
    <div>
        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Name <span class="text-error-500">*</span>
        </label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            value="{{ old('name', $user->name) }}" 
            required 
            autofocus
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('name') border-error-500 @enderror" />
        @error('name')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Email <span class="text-error-500">*</span>
        </label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email', $user->email) }}" 
            required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('email') border-error-500 @enderror" />
        @error('email')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-3 rounded-lg border border-warning-500/20 bg-warning-50 p-3 dark:bg-warning-500/15">
                <p class="mb-2 text-sm text-warning-700 dark:text-warning-400">Your email address is unverified.</p>
                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center text-sm font-medium text-warning-700 hover:text-warning-800 dark:text-warning-400 dark:hover:text-warning-300">
                        Click here to re-send the verification email.
                    </button>
                </form>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm text-success-600 dark:text-success-500">
                        A new verification link has been sent to your email address.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <!-- Submit Button -->
    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-600 dark:bg-blue-500 dark:hover:bg-blue-600">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Save Changes
        </button>
    </div>
</form>

<!-- Separate form for removing photo -->
@if($user->profile_photo)
    <form id="removePhotoForm" action="{{ route('profile.photo.delete') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endif
