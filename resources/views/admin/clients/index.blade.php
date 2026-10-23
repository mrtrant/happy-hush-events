@extends('layouts.admin')

@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Review and manage registered clients.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.clients.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-6">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search" name="search"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           value="{{ $search ?? '' }}"
                           placeholder="Client number, name, email, phone...">
                </div>

                <!-- Client Type Filter -->
                <div class="md:col-span-4">
                    <label for="client_type" class="block text-sm font-medium text-gray-700 mb-1">Client Type</label>
                    <select id="client_type" name="client_type" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">All client types</option>
                        @foreach($clientTypes as $type)
                        <option value="{{ $type }}" @selected(($selectedClientType ?? '') === $type)>
                            {{ $type }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter & Reset Buttons -->
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-900 shadow-sm transition-colors text-center">
                        Filter
                    </button>
                    <a href="{{ route('admin.clients.index') }}" class="px-3 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 shadow-sm transition-colors text-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Registered</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse($clients as $client)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Client Name & Number -->
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">
                                {{ trim($client->first_name . ' ' . ($client->middle_name ?? '') . ' ' . $client->last_name) }}
                            </div>
                            <div class="text-xs font-mono text-gray-500">{{ $client->client_number }}</div>
                            @if($client->organization_name)
                            <div class="text-xs text-gray-400">{{ $client->organization_name }}</div>
                            @endif
                        </td>

                        <!-- Type -->
                        <td class="px-6 py-4">
                            {{ $client->client_type }}
                        </td>

                        <!-- Email -->
                        <td class="px-6 py-4">
                            {{ $client->email }}
                        </td>

                        <!-- Phone -->
                        <td class="px-6 py-4">
                            {{ $client->phone ?: '—' }}
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4">
                            @if($client->is_active)
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                            @else
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                            @endif
                        </td>

                        <!-- Registered Date -->
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($client->created_at)->format('d M Y') }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.clients.show', $client->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                    View
                                </a>
                                <a href="{{ route('admin.clients.edit', $client->id) }}" class="text-gray-600 hover:text-gray-900 font-medium text-xs">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No clients found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clients->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
