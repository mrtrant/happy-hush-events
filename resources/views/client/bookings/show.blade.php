@extends('layouts.client')

@section('title', 'Booking ' . $booking->booking_number)

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-invoice me-2" style="color: var(--brand-orange);"></i> {{ $booking->booking_number }}
            </h2>
            <p class="text-muted mb-0">
                Booking created on {{ optional($booking->created_at)->format('d M Y, h:i A') }}
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            @if(in_array($booking->status, ['Draft', 'Pending Review']))
                <a href="{{ route('client.bookings.edit', $booking) }}" class="btn btn-outline-warning text-dark fw-semibold px-3">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Booking
                </a>
            @endif
            <a href="{{ route('client.bookings.index') }}" class="btn btn-outline-secondary px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Bookings
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Booking Status Card --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                <div class="col-md-6">
                    <span class="text-muted small text-uppercase tracking-wider d-block fw-semibold">Booking Status</span>
                    @php
                        $statusClass = match ($booking->status) {
                            'Draft' => 'bg-secondary',
                            'Pending Review' => 'bg-warning text-dark',
                            'Approved' => 'bg-success',
                            'Rejected' => 'bg-danger',
                            'Completed' => 'bg-primary',
                            'Cancelled' => 'bg-dark',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} mt-2 px-3 py-2 fs-6 fw-semibold">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small text-uppercase tracking-wider d-block fw-semibold">Total Amount</span>
                    <h4 class="fw-bold text-dark mt-1 mb-0">
                        KES {{ number_format($booking->total_amount, 2) }}
                    </h4>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small text-uppercase tracking-wider d-block fw-semibold">Outstanding Balance</span>
                    <h4 class="fw-bold text-danger mt-1 mb-0">
                        KES {{ number_format($booking->balance_amount, 2) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Events Loop --}}
    @foreach($booking->events as $event)
        <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-champagne-glasses me-2" style="color: var(--brand-orange);"></i> {{ $event->event_name }}
                    </h5>
                    <small class="text-muted">Event Item #{{ $loop->iteration }}</small>
                </div>
                <span class="badge bg-primary px-3 py-2 fw-semibold">
                    {{ ucfirst($event->status) }}
                </span>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <span class="text-muted small d-block fw-semibold">Event Type</span>
                        <strong class="text-dark">{{ $event->eventType?->name ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block fw-semibold">Date</span>
                        <strong class="text-dark">{{ optional($event->event_date)->format('d M Y') }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block fw-semibold">Expected Guests</span>
                        <strong class="text-dark">{{ number_format($event->guest_count) }} Guests</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block fw-semibold">Venue</span>
                        <strong class="text-dark">{{ $event->venue?->name ?? 'Not specified' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block fw-semibold">Start Time</span>
                        <strong class="text-dark">{{ $event->start_time?->format('h:i A') ?? 'Not specified' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block fw-semibold">End Time</span>
                        <strong class="text-dark">{{ $event->end_time?->format('h:i A') ?? 'Not specified' }}</strong>
                    </div>
                </div>

                {{-- Package Section --}}
                @if($event->package)
                    <hr class="my-4 text-muted opacity-25">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-box-open me-2 text-warning"></i> Selected Package
                    </h6>
                    <div class="border rounded-3 p-3 bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark">{{ $event->package->name }}</span>
                            @if($event->package->description)
                                <p class="text-muted small mb-0 mt-1">{{ $event->package->description }}</p>
                            @endif
                        </div>
                        <span class="fw-bold text-dark">
                            KES {{ number_format($event->package->package_price, 2) }}
                        </span>
                    </div>
                @endif

                {{-- Additional Services Section --}}
                @if($event->services->isNotEmpty())
                    <hr class="my-4 text-muted opacity-25">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-list-check me-2 text-warning"></i> Additional Services
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fs-7">
                                <tr>
                                    <th class="py-2 px-3">Service</th>
                                    <th class="py-2 text-center">Quantity</th>
                                    <th class="py-2 text-end">Unit Price</th>
                                    <th class="py-2 text-end px-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($event->services as $bookingService)
                                <tr>
                                    <td class="px-3 fw-semibold text-dark">
                                        {{ $bookingService->service?->name ?? 'Service' }}
                                    </td>
                                    <td class="text-center">{{ $bookingService->quantity }}</td>
                                    <td class="text-end">KES {{ number_format($bookingService->unit_price, 2) }}</td>
                                    <td class="text-end px-3 fw-semibold">KES {{ number_format($bookingService->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Special Requirements --}}
                @if($event->special_requirements)
                    <hr class="my-4 text-muted opacity-25">
                    <h6 class="fw-bold text-dark mb-2">Special Requirements</h6>
                    <p class="text-muted mb-0 bg-light p-3 rounded-3">{{ $event->special_requirements }}</p>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Financial Summary & Notes Grid --}}
    <div class="row g-4">
        <div class="col-lg-7">
            @if($booking->client_notes)
                <div class="card border-0 shadow-sm h-100 rounded-3">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark">Your Client Notes</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-0">{{ $booking->client_notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">Financial Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Amount</span>
                        <strong class="text-dark">KES {{ number_format($booking->total_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Discount Applied</span>
                        <span class="text-dark">KES {{ number_format($booking->discount_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Amount Paid</span>
                        <span class="text-success fw-semibold">KES {{ number_format($booking->paid_amount, 2) }}</span>
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="d-flex justify-content-between">
                        <strong class="text-dark">Balance Due</strong>
                        <strong class="text-danger fs-5">KES {{ number_format($booking->balance_amount, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
