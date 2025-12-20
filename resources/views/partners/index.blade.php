@extends('layouts.app')

@section('title', 'Partners')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Partners</h1>
        <a href="{{ route('partners.create') }}">
            <x-bladewind::button>
                Create Partner
            </x-bladewind::button>
        </a>
    </div>

    @if($partners->count() > 0)
        <x-bladewind::card>
            <x-bladewind::table striped="true" divider="thin">
                <x-slot name="header">
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Domain</th>
                    <th>Branding</th>
                    <th>Status</th>
                    <th>Users</th>
                    <th>Actions</th>
                </x-slot>
                @foreach($partners as $partner)
                    <tr>
                        <td><strong>{{ $partner->name }}</strong></td>
                        <td><code class="text-sm">{{ $partner->slug }}</code></td>
                        <td>
                            @if($partner->domain)
                                <x-bladewind::tag color="blue">{{ $partner->domain }}</x-bladewind::tag>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td>
                            @if($partner->identity)
                                <div class="flex gap-2 items-center">
                                    <div style="width: 24px; height: 24px; border-radius: 4px; background-color: {{ $partner->identity->primary_color }};"></div>
                                    <div style="width: 24px; height: 24px; border-radius: 4px; background-color: {{ $partner->identity->secondary_color }};"></div>
                                </div>
                            @else
                                <span class="text-gray-400">No identity</span>
                            @endif
                        </td>
                        <td>
                            @if($partner->is_active)
                                <x-bladewind::tag color="green">Active</x-bladewind::tag>
                            @else
                                <x-bladewind::tag color="gray">Inactive</x-bladewind::tag>
                            @endif
                        </td>
                        <td>
                            <x-bladewind::tag color="blue">{{ $partner->users->count() }}</x-bladewind::tag>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('partners.show', $partner) }}" class="inline-block">
                                    <x-bladewind::button size="tiny">
                                        View
                                    </x-bladewind::button>
                                </a>
                                <a href="{{ route('partners.edit', $partner) }}" class="inline-block">
                                    <x-bladewind::button size="tiny" color="orange">
                                        Edit
                                    </x-bladewind::button>
                                </a>
                                <form action="{{ route('partners.destroy', $partner) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this partner?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-bladewind::button size="tiny" color="red" can_submit="true">
                                        Delete
                                    </x-bladewind::button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-bladewind::table>
        </x-bladewind::card>
    @else
        <x-bladewind::alert type="info">
            No partners found. <a href="{{ route('partners.create') }}" class="underline">Create your first partner</a>.
        </x-bladewind::alert>
    @endif
</div>
@endsection
