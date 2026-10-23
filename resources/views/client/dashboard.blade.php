@extends('layouts.client')

@section('title', 'Client Dashboard')

@section('content')

<div class="container-fluid py-2">
    <!-- Welcome Header & Mobile View Integration -->
    <div class="container py-4 px-0 mb-4">
        <div class="mb-4">
            <h2 class="fw-bold fs-3">
                Welcome back, <span class="text-danger">{{ Auth::user()->first_name ?? 'Client' }}</span>! 👋
            </h2>
            <p class="text-muted small">Here is an overview of your events, bookings, and active quotations with Happy Hush Events.</p>

            <a href="{{ route('client.bookings.create') }}" class="btn btn-brand text-white w-100 py-2 mt-2 shadow-sm">
                + Book New Event
            </a>
        </div>
    </div>

    <!-- Statistics Grid (4 columns on desktop, responsive stack) -->
    <div class="row g-4 mb-5">
        <!-- Total Bookings -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('client.bookings.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning transition-hover p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-bold">Total Bookings</span>
                            <h2 class="fw-bold mb-0 display-6 text-dark mt-2">{{ $bookingCount ?? 0 }}</h2>
                        </div>
                        <i class="bi bi-calendar-check fs-1 text-warning"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Scheduled Events -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('client.events.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary transition-hover p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-bold">Scheduled Events</span>
                            <h2 class="fw-bold mb-0 display-6 text-dark mt-2">{{ $eventCount ?? 0 }}</h2>
                        </div>
                        <i class="bi bi-glass-cheers fs-1 text-primary"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Quotations -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('client.quotations.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-info transition-hover p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-bold">Quotations</span>
                            <h2 class="fw-bold mb-0 display-6 text-dark mt-2">{{ $quotationCount ?? 0 }}</h2>
                        </div>
                        <div class="fs-1 text-info opacity-75">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Outstanding Balance -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('client.invoices.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger transition-hover p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small fw-bold">Outstanding Balance</span>
                            <h3 class="fw-bold text-danger mt-2 mb-0">KES {{ number_format($outstandingBalance ?? 0, 2) }}</h3>
                        </div>
                        <div class="fs-1 text-danger opacity-75">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Bookings Table Section -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-0">
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-clock-rotate-left me-2" style="color: var(--brand-orange);"></i> Recent Bookings
            </h5>
            <a href="{{ route('client.bookings.index') }}" class="text-decoration-none fw-semibold small" style="color: var(--brand-orange);">
                View All <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7">
                    <tr>
                        <th class="py-3 px-4">Booking No.</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Total Amount</th>
                        <th class="py-3">Created Date</th>
                        <th class="py-3 text-end px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings ?? [] as $booking)
                        <tr>
                            <td class="px-4 fw-semibold text-dark">
                                {{ $booking->booking_number }}
                            </td>
                            <td>
                                @php
                                    $bookingStatusClass = match ($booking->status) {
                                        'Confirmed' => 'bg-success',
                                        'Pending' => 'bg-warning text-dark',
                                        'Cancelled' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $bookingStatusClass }} px-3 py-2 fw-semibold">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="fw-semibold">
                                KES {{ number_format($booking->total_amount, 2) }}
                            </td>
                            <td class="text-muted">
                                {{ $booking->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('client.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-eye me-1"></i> Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="my-3">
                                    <i class="fa-solid fa-folder-open fs-1 text-secondary opacity-50 mb-2"></i>
                                    <p class="mb-1">You have not made any bookings yet.</p>
                                    <a href="{{ route('client.bookings.create') }}" class="btn btn-sm btn-brand mt-2">Create Your First Booking</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
