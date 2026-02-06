<form method="post" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    @method('put')

    <!-- Current Password -->
    <div>
        <label for="update_password_current_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Current Password <span class="text-error-500">*</span>
        </label>
        <input 
            type="password" 
            id="update_password_current_password" 
            name="current_password" 
            autocomplete="current-password"
            required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('current_password', 'updatePassword') border-error-500 @enderror" />
        @error('current_password', 'updatePassword')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- New Password -->
    <div>
        <label for="update_password_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            New Password <span class="text-error-500">*</span>
        </label>
        <input 
            type="password" 
            id="update_password_password" 
            name="password" 
            autocomplete="new-password"
            required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('password', 'updatePassword') border-error-500 @enderror" />
        @error('password', 'updatePassword')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div>
        <label for="update_password_password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Confirm Password <span class="text-error-500">*</span>
        </label>
        <input 
            type="password" 
            id="update_password_password_confirmation" 
            name="password_confirmation" 
            autocomplete="new-password"
            required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('password_confirmation', 'updatePassword') border-error-500 @enderror" />
        @error('password_confirmation', 'updatePassword')
            <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Update Password
        </button>
    </div>
</form>
