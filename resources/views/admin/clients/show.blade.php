@extends('layouts.admin')

@section('title', 'Client Details')
@section('page-title', trim($client->first_name . ' ' . ($client->middle_name ?? '') . ' ' . $client->last_name))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Notifications -->
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm flex items-center justify-between">
        <span>{{ session('success') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 font-mono">{{ $client->client_number }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.clients.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                Back
            </a>
            <a href="{{ route('admin.clients.edit', $client->id) }}"
               class="inline-flex items-center px-4 py-2 border border-blue-600 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors">
                Edit Client
            </a>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column (8 Spans) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Client Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-800">Client Information</h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Client Number</span>
                            <div class="font-semibold text-gray-900 font-mono">{{ $client->client_number }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Client Type</span>
                            <div class="font-semibold text-gray-900">{{ $client->client_type }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Email</span>
                            <div class="font-semibold text-gray-900">{{ $client->email }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Phone</span>
                            <div class="font-semibold text-gray-900">{{ $client->phone ?: '—' }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Organization</span>
                            <div class="font-semibold text-gray-900">{{ $client->organization_name ?: '—' }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">KRA PIN</span>
                            <div class="font-semibold text-gray-900">{{ $client->kra_pin ?: '—' }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Website</span>
                            @if($client->website)
                                <a href="{{ $client->website }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">
                                    {{ $client->website }}
                                </a>
                            @else
                                <div class="text-gray-900">—</div>
                            @endif
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Preferred Contact</span>
                            <div class="font-semibold text-gray-900">{{ $client->preferred_contact_method ?: '—' }}</div>
                        </div>

                        @if ($client->notes)
                        <div class="md:col-span-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Notes</span>
                            <div class="border rounded-lg p-3 bg-gray-50 text-gray-700">
                                {!! nl2br(e($client->notes)) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Booking History Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-800">Booking History</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-3">Booking</th>
                                <th class="px-4 py-3">Event</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-blue-600 hover:underline font-mono">
                                        {{ $booking->booking_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $booking->event_name ?: '—' }}</div>
                                    @if($booking->event_type_name)
                                    <div class="text-xs text-gray-500">{{ $booking->event_type_name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $booking->event_date ? \Carbon\Carbon::parse($booking->event_date)->format('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    KES {{ number_format((float) $booking->total_amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    No bookings found for this client.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Column (4 Spans) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Account Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">Account</h2>

                <div>
                    <div class="text-xs text-gray-500 mb-1">Status</div>
                    @if($client->is_active)
                        <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                    @endif
                </div>

                <div>
                    <div class="text-xs text-gray-500 mb-1">Last Login</div>
                    <div class="text-sm text-gray-800">
                        {{ $client->last_login_at ? \Carbon\Carbon::parse($client->last_login_at)->format('d M Y H:i') : 'Never' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-500 mb-1">Registered</div>
                    <div class="text-sm text-gray-800">
                        {{ \Carbon\Carbon::parse($client->created_at)->format('d M Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Account Status Control Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">Account Status</h2>
                <p class="text-xs text-gray-500">
                    Deactivating the account prevents the client from using the account while retaining their historical records.
                </p>

                <form method="POST" action="{{ route('admin.clients.toggle-status', $client->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="w-full px-4 py-2 border text-sm font-medium rounded-lg shadow-sm transition-colors text-center {{ $client->is_active ? 'border-red-300 text-red-700 bg-red-50 hover:bg-red-100' : 'border-green-300 text-green-700 bg-green-50 hover:bg-green-100' }}">
                        {{ $client->is_active ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
