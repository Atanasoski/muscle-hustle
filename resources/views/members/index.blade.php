@extends('layouts.app')

@section('title', 'Members Management')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center">
                @if($partner->identity && $partner->identity->logo_url)
                    <img src="{{ $partner->identity->logo_url }}" alt="{{ $partner->name }}" class="w-12 h-12 rounded mr-3 object-cover">
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Members Management
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage your gym members and invitations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Invite New Member Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Invite New Member
        </h3>

        <form method="POST" action="{{ route('members.invite') }}" class="flex gap-3">
            @csrf
            <div class="flex-1">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="member@example.com" 
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    value="{{ old('email') }}"
                    required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button 
                type="submit" 
                class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Send Invitation
            </button>
        </form>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            An email invitation will be sent to the member with a signup link. Invitations expire in 7 days.
        </p>
    </div>

    <!-- Tabs -->
    <div class="mb-6" x-data="{ activeTab: 'members' }">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex gap-4">
                <button 
                    @click="activeTab = 'members'"
                    :class="activeTab === 'members' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Members ({{ $members->total() }})
                </button>
                <button 
                    @click="activeTab = 'pending'"
                    :class="activeTab === 'pending' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Pending Invitations ({{ $pendingInvitations->count() }})
                </button>
                <button 
                    @click="activeTab = 'expired'"
                    :class="activeTab === 'expired' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Expired ({{ $expiredInvitations->count() }})
                </button>
            </nav>
        </div>

        <!-- Members Tab -->
        <div x-show="activeTab === 'members'" class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                @if($members->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Login</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($members as $member)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($member->profile_photo)
                                                    <img src="{{ $member->profile_photo }}" alt="{{ $member->name }}" class="w-10 h-10 rounded-full mr-3 object-cover">
                                                @else
                                                    <div class="w-10 h-10 rounded-full mr-3 bg-blue-500 flex items-center justify-center text-white font-bold">
                                                        {{ substr($member->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $member->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $member->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $member->last_login_at ? $member->last_login_at->diffForHumans() : 'Never' }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $members->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No members yet. Start by inviting members!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Invitations Tab -->
        <div x-show="activeTab === 'pending'" class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                @if($pendingInvitations->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pendingInvitations as $invitation)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold mr-3">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $invitation->email }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Invited by {{ $invitation->inviter->name }} • {{ $invitation->created_at->diffForHumans() }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Expires {{ $invitation->expires_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <!-- Resend -->
                                        <form method="POST" action="{{ route('members.resend', $invitation) }}" class="inline">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                                Resend
                                            </button>
                                        </form>
                                        <!-- Cancel -->
                                        <form method="POST" action="{{ route('members.cancel', $invitation) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
                                                onclick="return confirm('Are you sure you want to cancel this invitation?')">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No pending invitations</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Expired Invitations Tab -->
        <div x-show="activeTab === 'expired'" class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                @if($expiredInvitations->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($expiredInvitations as $invitation)
                            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center text-white font-bold mr-3">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $invitation->email }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Invited by {{ $invitation->inviter->name }} • Expired {{ $invitation->expires_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <form method="POST" action="{{ route('members.resend', $invitation) }}" class="inline">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                                                Resend New Invitation
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No expired invitations in the last 30 days</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

