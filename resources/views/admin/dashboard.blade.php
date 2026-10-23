@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- Clients --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Clients</p>
                    <p class="text-3xl font-bold mt-2">
                        {{ number_format($statistics['clients']) }}
                    </p>
                </div>

                <div class="text-2xl">
                    👥
                </div>
            </div>
        </div>

        {{-- Bookings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Bookings</p>
                    <p class="text-3xl font-bold mt-2">
                        {{ number_format($statistics['bookings']) }}
                    </p>
                </div>

                <div class="text-2xl">
                    📋
                </div>
            </div>

            <div class="mt-3 text-sm">
                <span class="text-yellow-600">
                    {{ number_format($statistics['pending_bookings']) }}
                </span>
                pending review
            </div>
        </div>

        {{-- Events --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Upcoming Events</p>
                    <p class="text-3xl font-bold mt-2">
                        {{ number_format($statistics['upcoming_events']) }}
                    </p>
                </div>

                <div class="text-2xl">
                    📅
                </div>
            </div>
        </div>

        {{-- Staff --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Staff</p>
                    <p class="text-3xl font-bold mt-2">
                        {{ number_format($statistics['staff']) }}
                    </p>
                </div>

                <div class="text-2xl">
                    👤
                </div>
            </div>
        </div>

    </div>


    {{-- Financial Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-500">
                Payments Received
            </p>

            <p class="text-2xl font-bold mt-2">
                KES {{ number_format($statistics['payments_received'], 2) }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-500">
                Outstanding Invoices
            </p>

            <p class="text-2xl font-bold mt-2">
                KES {{ number_format($statistics['outstanding_invoices'], 2) }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-500">
                Active Services
            </p>

            <p class="text-2xl font-bold mt-2">
                {{ number_format($statistics['services']) }}
            </p>
        </div>

    </div>


    {{-- Main Dashboard Tables --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">

        {{-- Recent Bookings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="font-semibold">
                    Recent Bookings
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">
                                Booking
                            </th>

                            <th class="text-left px-6 py-3 font-medium text-gray-500">
                                Client
                            </th>

                            <th class="text-left px-6 py-3 font-medium text-gray-500">
                                Status
                            </th>

                            <th class="text-right px-6 py-3 font-medium text-gray-500">
                                Amount
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                    @forelse($recentBookings as $booking)

                        <tr>

                            <td class="px-6 py-4">
                                <div class="font-medium">
                                    {{ $booking->booking_number }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $booking->created_at }}
                                </div>
                            </td>

                            <td class="px-6 py-4">

                                <div>
                                    {{ $booking->first_name }}
                                    {{ $booking->last_name }}
                                </div>

                                @if($booking->organization_name)
                                    <div class="text-xs text-gray-500">
                                        {{ $booking->organization_name }}
                                    </div>
                                @endif

                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                    @switch($booking->status)
                                        @case('Approved')
                                            bg-green-100 text-green-700
                                            @break

                                        @case('Pending Review')
                                            bg-yellow-100 text-yellow-700
                                            @break

                                        @case('Rejected')
                                            bg-red-100 text-red-700
                                            @break

                                        @case('Completed')
                                            bg-blue-100 text-blue-700
                                            @break

                                        @default
                                            bg-gray-100 text-gray-700
                                    @endswitch
                                ">
                                    {{ $booking->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                KES {{ number_format($booking->total_amount, 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4"
                                class="px-6 py-8 text-center text-gray-500">
                                No bookings found.
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Upcoming Events --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="font-semibold">
                    Upcoming Events
                </h2>
            </div>

            <div class="divide-y divide-gray-100">

                @forelse($upcomingEvents as $event)

                    <div class="px-6 py-4">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <div class="font-medium">
                                    {{ $event->event_name }}
                                </div>

                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $event->event_type_name ?? 'Event' }}
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}

                                    @if($event->start_time)
                                        · {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                    @endif
                                </div>

                                @if($event->venue_name)
                                    <div class="text-xs text-gray-500">
                                        {{ $event->venue_name }}
                                    </div>
                                @endif

                            </div>

                            <div class="text-right">

                                <div class="text-xs text-gray-500">
                                    Guests
                                </div>

                                <div class="font-semibold">
                                    {{ number_format($event->guest_count) }}
                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="px-6 py-8 text-center text-gray-500">
                        No upcoming events.
                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- Recent Payments --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6">

        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="font-semibold">
                Recent Payments
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="text-left px-6 py-3 font-medium text-gray-500">
                            Receipt
                        </th>

                        <th class="text-left px-6 py-3 font-medium text-gray-500">
                            Invoice
                        </th>

                        <th class="text-left px-6 py-3 font-medium text-gray-500">
                            Method
                        </th>

                        <th class="text-left px-6 py-3 font-medium text-gray-500">
                            Date
                        </th>

                        <th class="text-left px-6 py-3 font-medium text-gray-500">
                            Reference
                        </th>

                        <th class="text-right px-6 py-3 font-medium text-gray-500">
                            Amount
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100">

                @forelse($recentPayments as $payment)

                    <tr>

                        <td class="px-6 py-4 font-medium">
                            {{ $payment->receipt_number }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $payment->invoice_number }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $payment->payment_method_name ?? '—' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $payment->transaction_reference ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-right font-medium">
                            KES {{ number_format($payment->amount, 2) }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6"
                            class="px-6 py-8 text-center text-gray-500">
                            No payments found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection
