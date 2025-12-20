@extends('layouts.app')

@section('title', $partner->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section with Partner Branding -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('partners.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div class="h-6 w-px bg-gray-300 dark:bg-gray-700"></div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Partner Details</h1>
            </div>

            <!-- Partner Hero Card -->
            <x-bladewind::card class="overflow-hidden">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <!-- Logo/Avatar -->
                    <div class="flex-shrink-0">
                        @if($partner->identity?->logo)
                            <div class="w-24 h-24 rounded-2xl border-4 border-white dark:border-gray-800 shadow-lg overflow-hidden bg-white dark:bg-gray-800 p-2">
                                <img src="{{ asset($partner->identity->logo) }}" alt="{{ $partner->name }}" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-orange-400 via-orange-500 to-teal-400 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                                {{ strtoupper(substr($partner->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Partner Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $partner->name }}</h2>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <code class="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-lg text-gray-700 dark:text-gray-300">{{ $partner->slug }}</code>
                                    @if($partner->domain)
                                        <x-bladewind::tag color="blue" size="small">{{ $partner->domain }}</x-bladewind::tag>
                                    @endif
                                    @if($partner->is_active)
                                        <x-bladewind::tag color="green" size="small">
                                            <i class="bi bi-check-circle mr-1"></i>Active
                                        </x-bladewind::tag>
                                    @else
                                        <x-bladewind::tag color="gray" size="small">
                                            <i class="bi bi-pause-circle mr-1"></i>Inactive
                                        </x-bladewind::tag>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <a href="{{ route('partners.edit', $partner) }}">
                                    <x-bladewind::button color="orange" size="small">
                                        <i class="bi bi-pencil mr-1"></i>Edit
                                    </x-bladewind::button>
                                </a>
                                <form action="{{ route('partners.destroy', $partner) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this partner?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-bladewind::button color="red" size="small" can_submit="true">
                                        <i class="bi bi-trash mr-1"></i>Delete
                                    </x-bladewind::button>
                                </form>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-calendar3"></i>
                                <span>Created {{ $partner->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="bi bi-clock-history"></i>
                                <span>Updated {{ $partner->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-bladewind::card>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-bladewind::card class="border-l-4 border-l-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $partner->users->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="bi bi-people text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </x-bladewind::card>

            <x-bladewind::card class="border-l-4 border-l-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Status</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            @if($partner->is_active)
                                <span class="text-green-600 dark:text-green-400">Active</span>
                            @else
                                <span class="text-gray-500">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i class="bi bi-{{ $partner->is_active ? 'check' : 'pause' }}-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </x-bladewind::card>

            <x-bladewind::card class="border-l-4 border-l-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Branding</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $partner->identity ? 'Configured' : 'Not Set' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <i class="bi bi-{{ $partner->identity ? 'palette-fill' : 'palette' }} text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </x-bladewind::card>

            <x-bladewind::card class="border-l-4 border-l-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Member Since</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $partner->created_at->format('M Y') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <i class="bi bi-calendar-check text-orange-600 dark:text-orange-400 text-xl"></i>
                    </div>
                </div>
            </x-bladewind::card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Branding Section -->
            <x-bladewind::card class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <i class="bi bi-palette text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Brand Identity</h3>
                </div>

                @if($partner->identity)
                    <div class="space-y-6">
                        <!-- Color Palette -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Color Palette</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background-color: {{ $partner->identity->primary_color }}; border: 3px solid #f3f4f6; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"></div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Primary</p>
                                            <code class="text-xs font-mono text-gray-700 dark:text-gray-300">{{ $partner->identity->primary_color }}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background-color: {{ $partner->identity->secondary_color }}; border: 3px solid #f3f4f6; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"></div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Secondary</p>
                                            <code class="text-xs font-mono text-gray-700 dark:text-gray-300">{{ $partner->identity->secondary_color }}</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Logo</h4>
                            @if($partner->identity->logo)
                                <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset($partner->identity->logo) }}" alt="{{ $partner->name }} Logo" class="w-20 h-20 object-contain rounded-lg border-2 border-gray-200 dark:border-gray-700 p-2 bg-white dark:bg-gray-900 shadow-sm">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">File Path</p>
                                        <code class="text-xs text-gray-600 dark:text-gray-400 block mb-3 break-all">{{ $partner->identity->logo }}</code>
                                        <a href="{{ asset($partner->identity->logo) }}" target="_blank" class="inline-flex items-center gap-1 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                            View full size
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="p-8 text-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                    <i class="bi bi-image text-3xl text-gray-400 dark:text-gray-600 mb-2 block"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No logo uploaded</p>
                                </div>
                            @endif
                        </div>

                        <!-- Font Family -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Typography</h4>
                            @if($partner->identity->font_family)
                                <div class="p-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Font Family</p>
                                    <p style="font-family: {{ $partner->identity->font_family }}; font-size: 24px;" class="text-gray-900 dark:text-white font-semibold">
                                        {{ $partner->identity->font_family }}
                                    </p>
                                    <p style="font-family: {{ $partner->identity->font_family }}; font-size: 16px;" class="text-gray-700 dark:text-gray-300 mt-2">
                                        The quick brown fox jumps over the lazy dog
                                    </p>
                                </div>
                            @else
                                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Using default system font</p>
                                </div>
                            @endif
                        </div>

                        <!-- Brand Preview -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Brand Preview</h4>
                            <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background: linear-gradient(135deg, {{ $partner->identity->primary_color }} 0%, {{ $partner->identity->secondary_color }} 100%);">
                                <div class="p-8 text-white">
                                    @if($partner->identity->logo)
                                        <div class="mb-4">
                                            <img src="{{ asset($partner->identity->logo) }}" alt="{{ $partner->name }}" class="h-12 object-contain filter brightness-0 invert">
                                        </div>
                                    @endif
                                    <h2 class="text-3xl font-bold mb-2" style="font-family: {{ $partner->identity->font_family ?? 'inherit' }};">
                                        {{ $partner->name }}
                                    </h2>
                                    <p class="text-white/90 text-sm">Branded Experience Preview</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-palette text-2xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Branding Configured</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">Set up your partner's visual identity to create a branded experience.</p>
                        <a href="{{ route('partners.edit', $partner) }}">
                            <x-bladewind::button color="orange">
                                <i class="bi bi-plus-circle mr-2"></i>Configure Branding
                            </x-bladewind::button>
                        </a>
                    </div>
                @endif
            </x-bladewind::card>

            <!-- Quick Info Sidebar -->
            <div class="space-y-6">
                <x-bladewind::card>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <i class="bi bi-info-circle text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Quick Info</h3>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Slug</p>
                            <code class="text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-700 dark:text-gray-300">{{ $partner->slug }}</code>
                        </div>
                        @if($partner->domain)
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Domain</p>
                                <x-bladewind::tag color="blue">{{ $partner->domain }}</x-bladewind::tag>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Created</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $partner->created_at->format('F d, Y') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $partner->created_at->diffForHumans() }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Last Updated</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $partner->updated_at->format('F d, Y') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $partner->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </x-bladewind::card>
            </div>
        </div>

        <!-- Users Section -->
        <x-bladewind::card>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="bi bi-people text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Team Members</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $partner->users->count() }} {{ Str::plural('user', $partner->users->count()) }} assigned</p>
                    </div>
                </div>
                <x-bladewind::tag color="blue" size="large">{{ $partner->users->count() }}</x-bladewind::tag>
            </div>

            @if($partner->users->count() > 0)
                <div class="overflow-x-auto">
                    <x-bladewind::table striped="true" divider="thin" hover_effect="true">
                        <x-slot name="header">
                            <th>Member</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </x-slot>
                        @foreach($partner->users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if($user->profile_photo)
                                            <img src="{{ asset($user->profile_photo) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold ring-2 ring-gray-200 dark:ring-gray-700">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $user->email }}</p>
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <x-bladewind::tag color="green" size="small">
                                            <i class="bi bi-check-circle mr-1"></i>Verified
                                        </x-bladewind::tag>
                                    @else
                                        <x-bladewind::tag color="gray" size="small">
                                            <i class="bi bi-clock mr-1"></i>Unverified
                                        </x-bladewind::tag>
                                    @endif
                                </td>
                                <td>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </x-bladewind::table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-person-plus text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Team Members Yet</h4>
                    <p class="text-gray-600 dark:text-gray-400">Users assigned to this partner will appear here.</p>
                </div>
            @endif
        </x-bladewind::card>
    </div>
</div>
@endsection
