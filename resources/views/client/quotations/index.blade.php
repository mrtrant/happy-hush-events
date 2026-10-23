@extends('layouts.client')

@section('title', 'My Quotations')

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-signature me-2" style="color: var(--brand-orange);"></i> My Quotations
            </h2>
            <p class="text-muted mb-0">
                Review and approve event estimates prepared specifically for your events.
            </p>
        </div>
    </div>

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

    <!-- Quotations Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                Issued Quotations
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3 px-4">Quotation No.</th>
                            <th class="py-3">Booking Ref</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Valid Until</th>
                            <th class="py-3 text-end">Total Amount</th>
                            <th class="py-3 text-end px-4" width="130">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($quotations as $quotation)
                        @php
                            $statusClass = match ($quotation->status) {
                                'Draft' => 'bg-secondary',
                                'Sent' => 'bg-primary',
                                'Viewed' => 'bg-info text-dark',
                                'Approved' => 'bg-success',
                                'Rejected' => 'bg-danger',
                                'Expired' => 'bg-dark',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td class="px-4 fw-semibold text-dark">
                                {{ $quotation->quotation_number }}
                            </td>
                            <td class="text-muted fw-semibold">
                                {{ $quotation->booking?->booking_number ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="badge {{ $statusClass }} px-3 py-2 fw-semibold">
                                    {{ ucfirst($quotation->status) }}
                                </span>
                            </td>
                            <td class="text-muted">
                                @if($quotation->valid_until)
                                    {{ $quotation->valid_until->format('d M Y') }}
                                @else
                                    <span class="text-muted fs-7">Not specified</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">
                                KES {{ number_format((float) $quotation->total_amount, 2) }}
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('client.quotations.show', $quotation) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="my-3">
                                    <i class="fa-solid fa-file-circle-xmark fs-1 text-secondary opacity-50 mb-2"></i>
                                    <h5 class="fw-bold text-dark mt-2">No quotations found</h5>
                                    <p class="mb-0">Quotations prepared for your event bookings will appear here.</p>
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
    @if($quotations->hasPages())
        <div class="mt-4">
            {{ $quotations->links() }}
        </div>
    @endif

</div>

@endsection
