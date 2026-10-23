@extends('layouts.client')

@section('title', 'Upcoming Events')

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-champagne-glasses me-2" style="color: var(--brand-orange);"></i> Upcoming Events
            </h2>
            <p class="text-muted mb-0">
                View your scheduled events, dates, timings, and status with Happy Hush.
            </p>
        </div>
    </div>

    <!-- Events Card Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                My Scheduled Events
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3 px-4">Event Name</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Venue</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Time</th>
                            <th class="py-3">Guests</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end px-4" width="130">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td class="px-4">
                                <div class="fw-semibold text-dark">
                                    {{ $event->event_name }}
                                </div>
                                @if($event->booking)
                                    <small class="text-muted">
                                        Ref: {{ $event->booking->booking_number }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $event->eventType?->name ?? '—' }}</td>
                            <td>{{ $event->venue?->name ?? '—' }}</td>
                            <td class="text-muted">{{ $event->event_date?->format('d M Y') }}</td>
                            <td>
                                @if($event->start_time)
                                    {{ $event->start_time->format('h:i A') }}
                                    @if($event->end_time)
                                        - {{ $event->end_time->format('h:i A') }}
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ number_format((int) $event->guest_count) }}</td>
                            <td>
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
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('client.events.show', $event) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="my-3">
                                    <i class="fa-solid fa-calendar-xmark fs-1 text-secondary opacity-50 mb-2"></i>
                                    <h5 class="fw-bold text-dark mt-2">No upcoming events found</h5>
                                    <p class="mb-0">Your scheduled events will appear here once booked.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
        <div class="mt-4">
            {{ $events->links() }}
        </div>
    @endif

</div>

@endsection
