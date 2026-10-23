@extends('layouts.client')

@section('title', 'Create Booking')

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="mb-4">
        <a href="{{ route('client.bookings.index') }}" class="text-decoration-none fw-semibold text-muted">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to My Bookings
        </a>
        <h2 class="fw-bold text-dark mt-2 mb-1">
            Create New Booking
        </h2>
        <p class="text-muted mb-0">
            Fill out the details below to schedule your event and select the services you need.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('client.bookings.store') }}">
        @csrf

        @include('client.bookings.partials.form', [
            'booking' => null,
            'event' => null,
        ])

        <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
            <a href="{{ route('client.bookings.index') }}" class="btn btn-outline-secondary px-4 py-2">
                Cancel
            </a>
            <button type="submit" class="btn btn-brand px-4 py-2 fw-semibold">
                <i class="fa-solid fa-check me-2"></i> Submit Booking
            </button>
        </div>

    </form>

</div>

@endsection
