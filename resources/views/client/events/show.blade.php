@extends('layouts.client')

@section('title', $event->event_name)

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-champagne-glasses me-2" style="color: var(--brand-orange);"></i> {{ $event->event_name }}
            </h2>
            <p class="text-muted mb-0">
                @if($event->booking)
                    Associated Booking Reference: <span class="fw-semibold text-dark">{{ $event->booking->booking_number }}</span>
                @endif
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('client.events.index') }}" class="btn btn-outline-secondary px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Events
            </a>
        </div>
    </div>

    <!-- Event Information Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                Event Information
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <dl class="row mb-0 g-3">
                        <dt class="col-sm-5 text-muted fw-semibold">Event Name</dt>
                        <dd class="col-sm-7 text-dark fw-bold">{{ $event->event_name }}</dd>

                        <dt class="col-sm-5 text-muted fw-semibold">Event Type</dt>
                        <dd class="col-sm-7 text-dark">{{ $event->eventType?->name ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted fw-semibold">Date</dt>
                        <dd class="col-sm-7 text-dark">{{ $event->event_date?->format('d M Y') }}</dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <dl class="row mb-0 g-3">
                        <dt class="col-sm-5 text-muted fw-semibold">Start Time</dt>
                        <dd class="col-sm-7 text-dark">{{ $event->start_time?->format('h:i A') ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted fw-semibold">End Time</dt>
                        <dd class="col-sm-7 text-dark">{{ $event->end_time?->format('h:i A') ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted fw-semibold">Guests</dt>
                        <dd class="col-sm-7 text-dark">{{ number_format((int) $event->guest_count) }}</dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <dl class="row mb-0 g-3">
                        <dt class="col-sm-5 text-muted fw-semibold">Venue</dt>
                        <dd class="col-sm-7 text-dark">{{ $event->venue?->name ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted fw-semibold">Package</dt>
                        <dd class="col-sm-7 text-dark">{{ $event->package?->name ?? 'Custom Services' }}</dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <dl class="row mb-0 g-3">
                        <dt class="col-sm-5 text-muted fw-semibold">Status</dt>
                        <dd class="col-sm-7">
                            @php
                                $statusClass = match ($event->status) {
                                    'Confirmed' => 'bg-success',
                                    'In Progress' => 'bg-warning text-dark',
                                    'Completed' => 'bg-secondary',
                                    'Cancelled' => 'bg-danger',
                                    default => 'bg-primary',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} px-3 py-2 fw-semibold">
                                {{ ucfirst($event->status) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            @if($event->special_requirements)
                <hr class="my-4 text-muted opacity-25">
                <h6 class="text-uppercase small fw-bold text-muted mb-2">
                    Special Requirements
                </h6>
                <p class="mb-0 bg-light p-3 rounded-3 text-muted">
                    {{ $event->special_requirements }}
                </p>
            @endif
        </div>
    </div>

    <!-- Event Services Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                Event Services
            </h5>
        </div>
        <div class="card-body p-0">
            @if($event->services->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7">
                            <tr>
                                <th class="py-3 px-4">Service</th>
                                <th class="py-3 text-center">Quantity</th>
                                <th class="py-3 text-end">Unit Price</th>
                                <th class="py-3 text-end px-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($event->services as $eventService)
                            <tr>
                                <td class="px-4 fw-semibold text-dark">
                                    {{ $eventService->service?->name ?? 'Service' }}
                                </td>
                                <td class="text-center">{{ $eventService->quantity }}</td>
                                <td class="text-end">KES {{ number_format((float) $eventService->unit_price, 2) }}</td>
                                <td class="text-end px-4 fw-semibold">KES {{ number_format((float) $eventService->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    <p class="mb-0">No individual services have been assigned to this event.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Event Timeline Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                Event Timeline & Progress
            </h5>
        </div>
        <div class="card-body p-4">
            @forelse($event->timeline as $timeline)
                <div class="d-flex gap-3 mb-4 pb-3 border-bottom last-border-0">
                    <div class="pt-1">
                        @if($timeline->completed)
                            <span class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                <i class="fa-solid fa-check small"></i>
                            </span>
                        @else
                            <span class="badge bg-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                <i class="fa-solid fa-clock small"></i>
                            </span>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-dark mb-0">
                                {{ $timeline->title }}
                            </h6>
                            @if($timeline->completed)
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </div>
                        @if($timeline->description)
                            <p class="text-muted small mb-1">
                                {{ $timeline->description }}
                            </p>
                        @endif
                        @if($timeline->completed_at)
                            <small class="text-muted d-block">
                                Completed on {{ $timeline->completed_at->format('d M Y, h:i A') }}
                            </small>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <h6 class="fw-bold text-dark">No timeline updates yet.</h6>
                    <p class="mb-0">Event progress milestones and updates will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
