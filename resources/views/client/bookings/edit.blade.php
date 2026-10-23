@extends('layouts.client')

@section('title', 'Edit Booking')

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                Edit Booking
            </h2>
            <p class="text-muted mb-0">
                Booking Reference: <span class="fw-semibold text-dark">{{ $booking->booking_number }}</span>
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('client.bookings.show', $booking) }}" class="btn btn-outline-secondary px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Booking
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('client.bookings.update', $booking) }}" id="bookingForm">
        @csrf
        @method('PUT')

        @include('client.bookings.partials.form', [
            'booking' => $booking,
            'event' => $event,
        ])

        <!-- Updated Estimated Total Card -->
        <div class="card border-0 shadow-sm mt-4 rounded-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">
                            Updated Estimated Total
                        </h5>
                        <small class="text-muted">
                            Final pricing is subject to final review and confirmation.
                        </small>
                    </div>
                    <h3 id="bookingTotal" class="fw-bold mb-0" style="color: var(--brand-orange);">
                        KES {{ number_format($booking->total_amount, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
            <a href="{{ route('client.bookings.show', $booking) }}" class="btn btn-outline-secondary px-4 py-2">
                Cancel
            </a>
            <button type="submit" class="btn btn-brand px-4 py-2 fw-semibold">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
            </button>
        </div>

    </form>

</div>

@endsection
