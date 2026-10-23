@extends('layouts.admin')

@section('title', 'Booking Details')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <a
                href="{{ route('admin.bookings.index') }}"
                class="text-sm font-medium text-gray-500"
            >
                ← Back to Bookings
            </a>

            <h1 class="mt-2 text-2xl font-bold text-gray-900">
                {{ $bookingRecord->booking_number }}
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Booking created {{ $bookingRecord->created_at }}
            </p>

        </div>

        <form
            method="POST"
            action="{{ route('admin.bookings.change-status', $bookingRecord->id) }}"
            class="flex gap-2"
        >
            @csrf
            @method('PATCH')

            <select
                name="status"
                class="rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
            >
                @foreach([
                    'Draft',
                    'Pending Review',
                    'Approved',
                    'Rejected',
                    'Completed',
                    'Cancelled'
                ] as $bookingStatus)

                    <option
                        value="{{ $bookingStatus }}"
                        @selected($bookingRecord->status === $bookingStatus)
                    >
                        {{ $bookingStatus }}
                    </option>

                @endforeach
            </select>

            <button
                type="submit"
                class="rounded-lg bg-yellow-500 px-4 py-2 text-sm font-semibold text-gray-900"
            >
                Update Status
            </button>

        </form>

    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="rounded-xl bg-white p-6 shadow-sm lg:col-span-2">

            <h2 class="text-lg font-semibold text-gray-900">
                Client
            </h2>

            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">

                <div>
                    <div class="text-xs font-semibold uppercase text-gray-500">
                        Name
                    </div>

                    <div class="mt-1 text-sm font-medium text-gray-900">
                        {{ $bookingRecord->first_name }}

                        @if($bookingRecord->middle_name)
                            {{ $bookingRecord->middle_name }}
                        @endif

                        {{ $bookingRecord->last_name }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase text-gray-500">
                        Client Number
                    </div>

                    <div class="mt-1 text-sm text-gray-900">
                        {{ $bookingRecord->client_number }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase text-gray-500">
                        Email
                    </div>

                    <div class="mt-1 text-sm text-gray-900">
                        {{ $bookingRecord->email }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase text-gray-500">
                        Phone
                    </div>

                    <div class="mt-1 text-sm text-gray-900">
                        {{ $bookingRecord->phone ?: '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase text-gray-500">
                        Client Type
                    </div>

                    <div class="mt-1 text-sm text-gray-900">
                        {{ $bookingRecord->client_type }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase text-gray-500">
                        Organization
                    </div>

                    <div class="mt-1 text-sm text-gray-900">
                        {{ $bookingRecord->organization_name ?: '—' }}
                    </div>
                </div>

            </div>

        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Financial Summary
            </h2>

            <dl class="mt-5 space-y-4">

                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">
                        Total
                    </dt>

                    <dd class="text-sm font-semibold text-gray-900">
                        KES {{ number_format((float) $bookingRecord->total_amount, 2) }}
                    </dd>
                </div>

                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">
                        Discount
                    </dt>

                    <dd class="text-sm text-gray-900">
                        KES {{ number_format((float) $bookingRecord->discount_amount, 2) }}
                    </dd>
                </div>

                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">
                        Paid
                    </dt>

                    <dd class="text-sm font-semibold text-green-700">
                        KES {{ number_format((float) $bookingRecord->paid_amount, 2) }}
                    </dd>
                </div>

                <div class="border-t border-gray-200 pt-4 flex justify-between">
                    <dt class="text-sm font-semibold text-gray-700">
                        Balance
                    </dt>

                    <dd class="text-sm font-bold text-red-700">
                        KES {{ number_format((float) $bookingRecord->balance_amount, 2) }}
                    </dd>
                </div>

            </dl>

        </div>

    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Events
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Events included in this booking.
                </p>
            </div>

        </div>

        <div class="mt-5 overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Event
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Type
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Date
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Venue
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Guests
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Status
                        </th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($events as $event)

                        <tr>

                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $event->event_name }}
                                </div>

                                @if($event->package_name)
                                    <div class="mt-1 text-xs text-gray-500">
                                        Package: {{ $event->package_name }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $event->event_type_name }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $event->event_date }}

                                @if($event->start_time)
                                    <div class="text-xs text-gray-500">
                                        {{ $event->start_time }}

                                        @if($event->end_time)
                                            - {{ $event->end_time }}
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $event->venue_name ?: 'Not specified' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ number_format((int) $event->guest_count) }}
                            </td>

                            <td class="px-4 py-4">

                                @php
                                    $eventStatusClasses = match ($event->status) {
                                        'Confirmed' => 'bg-green-100 text-green-700',
                                        'In Progress' => 'bg-blue-100 text-blue-700',
                                        'Completed' => 'bg-gray-200 text-gray-700',
                                        'Cancelled' => 'bg-red-100 text-red-700',
                                        default => 'bg-yellow-100 text-yellow-700',
                                    };
                                @endphp

                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $eventStatusClasses }}">
                                    {{ $event->status }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="px-4 py-10 text-center text-sm text-gray-500"
                            >
                                No events are attached to this booking.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Client Notes
            </h2>

            <div class="mt-4 whitespace-pre-line text-sm text-gray-700">
                {{ $bookingRecord->client_notes ?: 'No client notes.' }}
            </div>

        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Internal Notes
            </h2>

            <form
                method="POST"
                action="{{ route('admin.bookings.update-notes', $bookingRecord->id) }}"
                class="mt-4"
            >
                @csrf
                @method('PATCH')

                <textarea
                    name="internal_notes"
                    rows="5"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                    placeholder="Internal notes visible only to administration..."
                >{{ old('internal_notes', $bookingRecord->internal_notes) }}</textarea>

                <button
                    type="submit"
                    class="mt-3 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white"
                >
                    Save Internal Notes
                </button>

            </form>

        </div>

    </div>

</div>
@endsection
