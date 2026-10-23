@extends('layouts.admin')

@section('title', 'Events')
@section('page-title', 'Events')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Manage scheduled and confirmed Happy Hush events.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.events.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           value="{{ $search ?? '' }}"
                           placeholder="Event, booking number, client...">
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">All statuses</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(($selectedStatus ?? '') === $status)>
                            {{ $status }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-900 shadow-sm transition-colors text-center">
                        Filter
                    </button>
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
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Venue</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Guests</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse($events as $event)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Event Name & Booking -->
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $event->event_name }}</div>
                            <div class="text-xs font-mono text-gray-500">{{ $event->booking_number }}</div>
                        </td>

                        <!-- Client -->
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $event->client_name ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $event->client_number }}</div>
                        </td>

                        <!-- Type -->
                        <td class="px-6 py-4">
                            {{ $event->event_type_name }}
                        </td>

                        <!-- Venue -->
                        <td class="px-6 py-4">
                            {{ $event->venue_name ?? 'Not assigned' }}
                        </td>

                        <!-- Date & Time -->
                        <td class="px-6 py-4">
                            <div>{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</div>
                            @if($event->start_time)
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                @if($event->end_time)
                                    - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                @endif
                            </div>
                            @endif
                        </td>

                        <!-- Guests -->
                        <td class="px-6 py-4">
                            {{ number_format($event->guest_count ?? 0) }}
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = match($event->status) {
                                    'Scheduled' => 'bg-gray-100 text-gray-800',
                                    'Confirmed' => 'bg-blue-100 text-blue-800',
                                    'In Progress' => 'bg-yellow-100 text-yellow-800',
                                    'Completed' => 'bg-green-100 text-green-800',
                                    'Cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                {{ $event->status }}
                            </span>
                        </td>

                        <!-- Action -->
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.events.show', $event->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            No events found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
