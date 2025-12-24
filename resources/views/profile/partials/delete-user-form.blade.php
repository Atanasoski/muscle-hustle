<div>
    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Before deleting your account, please download any data or information that you wish to retain.
    </p>

    <button 
        type="button" 
        @click="window.dispatchEvent(new CustomEvent('open-delete-modal'))"
        class="inline-flex items-center justify-center rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-error-600 dark:bg-error-500 dark:hover:bg-error-600">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        Delete Account
    </button>
</div>
