@extends('layouts.client')

@section('title', 'My Bookings')

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                My Bookings
            </h2>
            <p class="text-muted mb-0">
                Manage all your event bookings and check real-time progress.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('client.bookings.create') }}" class="btn btn-brand px-4 py-2 fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> New Booking
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- DESKTOP VIEW: Standard Table (Hidden on Mobile) -->
    <div class="d-none d-md-block card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7">
                    <tr>
                        <th class="py-3 px-3">BOOKING NO.</th>
                        <th class="py-3 px-3">EVENT NAME</th>
                        <th class="py-3 px-3">DATE</th>
                        <th class="py-3 px-3">STATUS</th>
                        <th class="py-3 px-3">TOTAL AMOUNT</th>
                        <th class="py-3 px-3 text-end" width="180">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $event = $booking->events->first();
                            $bookingStatusClass = match ($booking->status) {
                                'Confirmed' => 'bg-success',
                                'Pending' => 'bg-warning text-dark',
                                'Cancelled' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td class="px-3 fw-bold text-dark">#{{ $booking->booking_number }}</td>
                            <td class="px-3">{{ $event?->event_name ?: '—' }}</td>
                            <td class="px-3 text-muted">{{ optional($event?->event_date)->format('d M Y') ?: '—' }}</td>
                            <td class="px-3">
                                <span class="badge {{ $bookingStatusClass }} px-3 py-2 fw-semibold">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="px-3 fw-semibold">
                                KES {{ number_format($booking->total_amount, 2) }}
                            </td>
                            <td class="px-3 text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('client.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('client.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning text-dark">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x display-5 d-block mb-2 text-secondary opacity-50"></i>
                                <h5 class="fw-bold text-dark mt-2">No bookings found</h5>
                                <p class="mb-2">Start planning your memorable events with Happy Hush today!</p>
                                <a href="{{ route('client.bookings.create') }}" class="btn btn-sm btn-brand text-white mt-2">Create First Booking</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBILE VIEW: Stacked Cards (Visible only on Mobile) -->
    <div class="d-md-none d-flex flex-column gap-3">
        @forelse($bookings as $booking)
            @php
                $event = $booking->events->first();
                $bookingStatusClass = match ($booking->status) {
                    'Confirmed' => 'bg-success',
                    'Pending' => 'bg-warning text-dark',
                    'Cancelled' => 'bg-danger',
                    default => 'bg-secondary',
                };
            @endphp
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-light text-dark fw-bold">#{{ $booking->booking_number }}</span>
                    <span class="badge {{ $bookingStatusClass }}">{{ ucfirst($booking->status) }}</span>
                </div>
                <h6 class="fw-bold mb-1 text-dark">{{ $event?->event_name ?: 'Unnamed Event' }}</h6>
                <p class="text-muted small mb-1"><i class="bi bi-calendar3 me-1"></i> {{ optional($event?->event_date)->format('d M Y') ?: '—' }}</p>
                <p class="fw-semibold text-dark small mb-3">Total: KES {{ number_format($booking->total_amount, 2) }}</p>

                <div class="d-flex gap-2">
                    <a href="{{ route('client.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">
                        <i class="fa-solid fa-eye me-1"></i> View
                    </a>
                    <a href="{{ route('client.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning text-dark flex-grow-1">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded shadow-sm px-3">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3 opacity-50"></i>
                <h6 class="fw-bold text-dark">No bookings found</h6>
                <p class="text-muted small mb-3">Start planning your memorable event with us today.</p>
                <a href="{{ route('client.bookings.create') }}" class="btn btn-brand text-white w-100 py-2">
                    + Create First Booking
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($bookings, 'links'))
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    @endif

</div>

@endsection
