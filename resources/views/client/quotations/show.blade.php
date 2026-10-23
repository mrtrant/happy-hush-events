@extends('layouts.client')

@section('title', 'Quotation ' . $quotation->quotation_number)

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-signature me-2" style="color: var(--brand-orange);"></i> Quotation {{ $quotation->quotation_number }}
            </h2>
            <p class="text-muted mb-0">
                @if($quotation->booking)
                    Booking Reference: <strong class="text-dark">{{ $quotation->booking->booking_number }}</strong>
                @endif
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('client.quotations.index') }}" class="btn btn-outline-secondary px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Quotations
            </a>
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

    <!-- Metadata Banner Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-md-4">
                    <span class="text-muted small d-block fw-semibold text-uppercase tracking-wider">Quotation Status</span>
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
                    <span class="badge {{ $statusClass }} px-3 py-2 fw-semibold fs-6 mt-1">
                        {{ ucfirst($quotation->status) }}
                    </span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block fw-semibold text-uppercase tracking-wider">Valid Until</span>
                    <strong class="text-dark fs-6 d-block mt-1">
                        @if($quotation->valid_until)
                            <i class="fa-solid fa-calendar-day me-1 text-warning"></i> {{ $quotation->valid_until->format('d M Y') }}
                        @else
                            Not specified
                        @endif
                    </strong>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block fw-semibold text-uppercase tracking-wider">Booking Number</span>
                    <strong class="text-dark fs-6 d-block mt-1">
                        <i class="fa-solid fa-bookmark me-1 text-warning"></i> {{ $quotation->booking?->booking_number ?? 'N/A' }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold text-dark mb-0">Quotation Line Items</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 text-center">Quantity</th>
                            <th class="py-3 text-end">Unit Price</th>
                            <th class="py-3 text-end px-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($quotation->items as $item)
                        <tr>
                            <td class="px-4">
                                <strong class="text-dark d-block">{{ $item->description }}</strong>
                                @if($item->service)
                                    <small class="text-muted">Code: {{ $item->service->service_code }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">KES {{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="text-end px-4 fw-semibold text-dark">KES {{ number_format((float) $item->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No quotation items available.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Financial Summary & Decision Grid -->
    <div class="row g-4">
        <!-- Notes & Action Card (Left Column) -->
        <div class="col-lg-7">
            @if($quotation->notes)
                <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">Notes & Terms</h5>
                    </div>
                    <div class="card-body p-4 text-muted">
                        {!! nl2br(e($quotation->notes)) !!}
                    </div>
                </div>
            @endif

            <!-- Decision Box -->
            @if(in_array($quotation->status, ['Sent', 'Viewed']))
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden border-start border-warning border-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="fa-solid fa-gavel me-2" style="color: var(--brand-orange);"></i> Quotation Decision
                        </h5>
                        <p class="text-muted small mb-3">
                            Please review the pricing and item breakdown above. You can accept or decline this estimation below.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('client.quotations.approve', $quotation) }}">
                                @csrf
                                <button type="submit" class="btn btn-success fw-semibold px-4" onclick="return confirm('Are you sure you want to approve this quotation?')">
                                    <i class="fa-solid fa-check me-1"></i> Approve Quotation
                                </button>
                            </form>

                            <form method="POST" action="{{ route('client.quotations.reject', $quotation) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger fw-semibold px-4" onclick="return confirm('Are you sure you want to reject this quotation?')">
                                    <i class="fa-solid fa-xmark me-1"></i> Reject Quotation
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif($quotation->status === 'Approved')
                <div class="alert alert-success shadow-sm border-0 p-4 rounded-3 d-flex align-items-center">
                    <i class="fa-solid fa-circle-check fs-2 me-3"></i>
                    <div>
                        <strong class="d-block fs-6">Quotation Approved</strong>
                        <span>Thank you! This quotation has been accepted and processed for your booking.</span>
                    </div>
                </div>
            @elseif($quotation->status === 'Rejected')
                <div class="alert alert-danger shadow-sm border-0 p-4 rounded-3 d-flex align-items-center">
                    <i class="fa-solid fa-circle-xmark fs-2 me-3"></i>
                    <div>
                        <strong class="d-block fs-6">Quotation Declined</strong>
                        <span>This quotation was rejected. Please contact our support if you'd like a revised quote.</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Cost Summary Card (Right Column) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-0">Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <strong class="text-dark">KES {{ number_format((float) $quotation->subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Discount</span>
                        <strong class="text-dark">KES {{ number_format((float) $quotation->discount_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tax</span>
                        <strong class="text-dark">KES {{ number_format((float) $quotation->tax_amount, 2) }}</strong>
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold text-dark fs-5">Total Amount</span>
                        <strong class="fs-5" style="color: var(--brand-orange);">
                            KES {{ number_format((float) $quotation->total_amount, 2) }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
