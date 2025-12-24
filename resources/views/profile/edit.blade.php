@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
    <div class="mb-6">
        <h3 class="text-2xl font-semibold text-gray-800 dark:text-white/90 mb-2">Profile Settings</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">Manage your account information and security</p>
    </div>

    <!-- Success Messages -->
    @if (session('status') === 'profile-updated')
        <div class="mb-6 rounded-lg border border-success-500/20 bg-success-50 p-4 dark:bg-success-500/15">
            <div class="flex items-center gap-3">
                <svg class="shrink-0 text-success-600 dark:text-success-500" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18.3333C14.6024 18.3333 18.3333 14.6024 18.3333 10C18.3333 5.39763 14.6024 1.66667 10 1.66667C5.39763 1.66667 1.66667 5.39763 1.66667 10C1.66667 14.6024 5.39763 18.3333 10 18.3333ZM13.0893 8.08926C13.414 7.76462 13.414 7.23772 13.0893 6.91308C12.7647 6.58844 12.2378 6.58844 11.9131 6.91308L8.74998 10.0763L8.08689 9.41318C7.76225 9.08854 7.23535 9.08854 6.91071 9.41318C6.58607 9.73782 6.58607 10.2647 6.91071 10.5894L8.16188 11.8405C8.48652 12.1652 9.01342 12.1652 9.33806 11.8405L13.0893 8.08926Z" fill="currentColor"/>
                </svg>
                <p class="font-medium text-success-600 dark:text-success-500">Profile updated successfully!</p>
            </div>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="mb-6 rounded-lg border border-success-500/20 bg-success-50 p-4 dark:bg-success-500/15">
            <div class="flex items-center gap-3">
                <svg class="shrink-0 text-success-600 dark:text-success-500" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18.3333C14.6024 18.3333 18.3333 14.6024 18.3333 10C18.3333 5.39763 14.6024 1.66667 10 1.66667C5.39763 1.66667 1.66667 5.39763 1.66667 10C1.66667 14.6024 5.39763 18.3333 10 18.3333ZM13.0893 8.08926C13.414 7.76462 13.414 7.23772 13.0893 6.91308C12.7647 6.58844 12.2378 6.58844 11.9131 6.91308L8.74998 10.0763L8.08689 9.41318C7.76225 9.08854 7.23535 9.08854 6.91071 9.41318C6.58607 9.73782 6.58607 10.2647 6.91071 10.5894L8.16188 11.8405C8.48652 12.1652 9.01342 12.1652 9.33806 11.8405L13.0893 8.08926Z" fill="currentColor"/>
                </svg>
                <p class="font-medium text-success-600 dark:text-success-500">Password updated successfully!</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Update Profile Information -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Profile Information</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update your account's profile information and email address.</p>
            </div>
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Update Password</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ensure your account is using a long, random password to stay secure.</p>
            </div>
            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection
