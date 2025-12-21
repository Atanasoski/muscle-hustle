@extends('layouts.app')

@section('title', 'Partners')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Partners</h2>
    </div>
    <div>
        <a href="{{ route('partners.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-600 dark:bg-blue-500 dark:hover:bg-blue-600">
            CREATE PARTNER
        </a>
    </div>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[1102px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Name
                        </p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Slug
                        </p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Domain
                        </p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Branding
                        </p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Status
                        </p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Users
                        </p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            Actions
                        </p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($partners as $partner)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-5 py-4 sm:px-6">
                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                {{ $partner->name }}
                            </p>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <code class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $partner->slug }}</code>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            @if($partner->domain)
                                <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500">
                                    {{ $partner->domain }}
                                </span>
                            @else
                                <span class="text-gray-400 text-theme-sm dark:text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            @if($partner->identity)
                                <div class="flex gap-2 items-center">
                                    <div class="w-6 h-6 rounded" style="background-color: {{ $partner->identity->primary_color }};"></div>
                                    <div class="w-6 h-6 rounded" style="background-color: {{ $partner->identity->secondary_color }};"></div>
                                </div>
                            @else
                                <span class="text-gray-400 text-theme-sm dark:text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            @if($partner->is_active)
                                <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500">
                                    Active
                                </span>
                            @else
                                <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <span class="text-theme-xs inline-block rounded-full px-2 py-0.5 font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500">
                                {{ $partner->users->count() }}
                            </span>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('partners.show', $partner) }}" class="text-theme-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    VIEW
                                </a>
                                <span class="text-gray-300 dark:text-gray-700">|</span>
                                <a href="{{ route('partners.edit', $partner) }}" class="text-theme-xs font-medium text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300">
                                    EDIT
                                </a>
                                <span class="text-gray-300 dark:text-gray-700">|</span>
                                <form action="{{ route('partners.destroy', $partner) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this partner?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-theme-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        DELETE
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center sm:px-6">
                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                No partners found. <a href="{{ route('partners.create') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 underline">Create your first partner</a>.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
