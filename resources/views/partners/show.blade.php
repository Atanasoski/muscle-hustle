@extends('layouts.app')

@section('title', $partner->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('partners.index') }}">
            <x-bladewind::button color="gray">
                ← Back to Partners
            </x-bladewind::button>
        </a>
        <div class="flex gap-3">
            <a href="{{ route('partners.edit', $partner) }}">
                <x-bladewind::button color="orange">
                    Edit Partner
                </x-bladewind::button>
            </a>
            <form action="{{ route('partners.destroy', $partner) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this partner?');">
                @csrf
                @method('DELETE')
                <x-bladewind::button color="red" can_submit="true">
                    Delete
                </x-bladewind::button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Partner Details -->
        <x-bladewind::card>
            <h3 class="text-xl font-bold mb-4">Partner Details</h3>
            <div class="space-y-3">
                <div>
                    <span class="font-semibold">Name:</span>
                    <span class="ml-2">{{ $partner->name }}</span>
                </div>
                <div>
                    <span class="font-semibold">Slug:</span>
                    <code class="ml-2 text-sm bg-gray-100 px-2 py-1 rounded">{{ $partner->slug }}</code>
                </div>
                <div>
                    <span class="font-semibold">Domain:</span>
                    @if($partner->domain)
                        <x-bladewind::tag color="blue">{{ $partner->domain }}</x-bladewind::tag>
                    @else
                        <span class="ml-2 text-gray-400">Not set</span>
                    @endif
                </div>
                <div>
                    <span class="font-semibold">Status:</span>
                    @if($partner->is_active)
                        <x-bladewind::tag color="green">Active</x-bladewind::tag>
                    @else
                        <x-bladewind::tag color="gray">Inactive</x-bladewind::tag>
                    @endif
                </div>
                <div>
                    <span class="font-semibold">Total Users:</span>
                    <x-bladewind::tag color="blue">{{ $partner->users->count() }}</x-bladewind::tag>
                </div>
                <div>
                    <span class="font-semibold">Created:</span>
                    <span class="ml-2">{{ $partner->created_at->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="font-semibold">Updated:</span>
                    <span class="ml-2">{{ $partner->updated_at->format('M d, Y') }}</span>
                </div>
            </div>
        </x-bladewind::card>

        <!-- Branding -->
        <x-bladewind::card>
            <h3 class="text-xl font-bold mb-4">Branding</h3>
            @if($partner->identity)
                <div class="space-y-4">
                    <div>
                        <span class="font-semibold block mb-2">Primary Color:</span>
                        <div class="flex items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background-color: {{ $partner->identity->primary_color }}; border: 2px solid #e5e7eb;"></div>
                            <code class="text-sm">{{ $partner->identity->primary_color }}</code>
                        </div>
                    </div>
                    <div>
                        <span class="font-semibold block mb-2">Secondary Color:</span>
                        <div class="flex items-center gap-3">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background-color: {{ $partner->identity->secondary_color }}; border: 2px solid #e5e7eb;"></div>
                            <code class="text-sm">{{ $partner->identity->secondary_color }}</code>
                        </div>
                    </div>
                    <div>
                        <span class="font-semibold block mb-2">Logo:</span>
                        @if($partner->identity->logo)
                            <code class="text-sm">{{ $partner->identity->logo }}</code>
                        @else
                            <span class="text-gray-400">Not set</span>
                        @endif
                    </div>
                    <div>
                        <span class="font-semibold block mb-2">Font Family:</span>
                        @if($partner->identity->font_family)
                            <span style="font-family: {{ $partner->identity->font_family }};">{{ $partner->identity->font_family }}</span>
                        @else
                            <span class="text-gray-400">Default</span>
                        @endif
                    </div>

                    <!-- Preview -->
                    <div class="mt-6">
                        <span class="font-semibold block mb-2">Preview:</span>
                        <div class="p-6 rounded-lg" style="background: linear-gradient(135deg, {{ $partner->identity->primary_color }} 0%, {{ $partner->identity->secondary_color }} 100%);">
                            <p class="text-white font-bold text-xl" style="font-family: {{ $partner->identity->font_family ?? 'inherit' }};">
                                {{ $partner->name }}
                            </p>
                            <p class="text-white text-sm opacity-90">Branded Gradient</p>
                        </div>
                    </div>
                </div>
            @else
                <x-bladewind::alert type="warning">
                    No branding identity configured.
                </x-bladewind::alert>
            @endif
        </x-bladewind::card>
    </div>

    <!-- Users List -->
    @if($partner->users->count() > 0)
        <x-bladewind::card class="mt-6">
            <h3 class="text-xl font-bold mb-4">Users ({{ $partner->users->count() }})</h3>
            <x-bladewind::table striped="true">
                <x-slot name="header">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined</th>
                </x-slot>
                @foreach($partner->users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </x-bladewind::table>
        </x-bladewind::card>
    @endif
</div>
@endsection
