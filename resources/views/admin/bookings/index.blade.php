@extends('layouts.admin')

@section('title', 'Bookings')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Bookings
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Review and manage customer event bookings.
        </p>
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

    <div class="rounded-xl bg-white p-4 shadow-sm">

        <form
            method="GET"
            action="{{ route('admin.bookings.index') }}"
            class="grid grid-cols-1 gap-3 md:grid-cols-3"
        >

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Booking number, client, email..."
                class="rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
            >

            <select
                name="status"
                class="rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500"
            >
                <option value="">
                    All statuses
                </option>

                @foreach($statuses as $bookingStatus)
                    <option
                        value="{{ $bookingStatus }}"
                        @selected($status === $bookingStatus)
                    >
                        {{ $bookingStatus }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">

                <button
                    type="submit"
                    class="flex-1 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white"
                >
                    Filter
                </button>

                <a
                    href="{{ route('admin.bookings.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700"
                >
                    Reset
                </a>

            </div>

        </form>

    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Booking
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Client
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                            Total
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                            Balance
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($bookings as $booking)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">

                                <div class="font-semibold text-gray-900">
                                    {{ $booking->booking_number }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $booking->created_at }}
                                </div>

                            </td>

                            <td class="px-6 py-4">

                                <div class="font-medium text-gray-900">
                                    {{ $booking->first_name }}
                                    @if($booking->middle_name)
                                        {{ $booking->middle_name }}
                                    @endif
                                    {{ $booking->last_name }}
                                </div>

                                <div class="mt-1 text-sm text-gray-500">
                                    {{ $booking->email }}
                                </div>

                                <div class="mt-1 text-xs text-gray-400">
                                    {{ $booking->client_number }}
                                </div>

                            </td>

                            <td class="px-6 py-4">

                                @php
                                    $statusClasses = match ($booking->status) {
                                        'Approved' => 'bg-green-100 text-green-700',
                                        'Pending Review' => 'bg-yellow-100 text-yellow-700',
                                        'Completed' => 'bg-blue-100 text-blue-700',
                                        'Rejected', 'Cancelled' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                    {{ $booking->status }}
                                </span>

                            </td>

                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                KES {{ number_format((float) $booking->total_amount, 2) }}
                            </td>

                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                KES {{ number_format((float) $booking->balance_amount, 2) }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="flex justify-end gap-2">

                                    <a
                                        href="{{ route('admin.bookings.show', $booking->id) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700"
                                    >
                                        View
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="px-6 py-12 text-center text-sm text-gray-500"
                            >
                                No bookings found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="border-t border-gray-200 px-6 py-4">
            {{ $bookings->links() }}
        </div>

    </div>

</div>
@endsection
