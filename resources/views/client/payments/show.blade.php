@extends('layouts.client')

@section('title', 'Payment Receipt ' . $payment->receipt_number)

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-invoice-dollar me-2" style="color: var(--brand-orange);"></i> Payment Receipt
            </h2>
            <p class="text-muted mb-0">
                Official Receipt Number: <strong class="text-dark">{{ $payment->receipt_number }}</strong>
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('client.payments.index') }}" class="btn btn-outline-secondary px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Payments
            </a>
            <button type="button" onclick="window.print()" class="btn btn-brand px-3 fw-semibold">
                <i class="fa-solid fa-print me-1"></i> Print Receipt
            </button>
        </div>
    </div>

    <!-- Printable Receipt Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden" id="printableReceipt">
        <div class="card-body p-4 p-lg-5">

            <!-- Branding & Metadata Header -->
            <div class="row align-items-center mb-4 pb-4 border-bottom">
                <div class="col-md-6">
                    <h3 class="fw-bold mb-1" style="color: var(--brand-orange);">
                        Happy Hush Events Kenya
                    </h3>
                    <p class="text-muted mb-0">Official Payment Receipt</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="text-muted small fw-semibold text-uppercase">Receipt Reference</div>
                    <div class="fw-bold text-dark fs-5">{{ $payment->receipt_number }}</div>

                    <div class="text-muted small fw-semibold text-uppercase mt-2">Payment Date</div>
                    <div class="fw-semibold text-dark">{{ $payment->payment_date?->format('d M Y') ?? '—' }}</div>
                </div>
            </div>

            <!-- Detailed Grid -->
            <div class="row g-4 my-2">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <h6 class="text-uppercase small fw-bold text-muted mb-3 border-bottom pb-2">
                            Payment Details
                        </h6>
                        <dl class="row mb-0 g-2">
                            <dt class="col-sm-5 text-muted fw-semibold">Amount Paid</dt>
                            <dd class="col-sm-7 text-success fw-bold">KES {{ number_format((float) $payment->amount, 2) }}</dd>

                            <dt class="col-sm-5 text-muted fw-semibold">Payment Method</dt>
                            <dd class="col-sm-7 text-dark fw-semibold">{{ $payment->paymentMethod?->name ?? '—' }}</dd>

                            <dt class="col-sm-5 text-muted fw-semibold">Transaction Ref</dt>
                            <dd class="col-sm-7 text-dark font-monospace">{{ $payment->transaction_reference ?: '—' }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <h6 class="text-uppercase small fw-bold text-muted mb-3 border-bottom pb-2">
                            Associated Invoice
                        </h6>
                        <dl class="row mb-0 g-2">
                            <dt class="col-sm-5 text-muted fw-semibold">Invoice Ref</dt>
                            <dd class="col-sm-7 text-dark fw-bold">{{ $payment->invoice?->invoice_number ?? '—' }}</dd>

                            <dt class="col-sm-5 text-muted fw-semibold">Invoice Date</dt>
                            <dd class="col-sm-7 text-dark">{{ $payment->invoice?->invoice_date?->format('d M Y') ?? '—' }}</dd>

                            <dt class="col-sm-5 text-muted fw-semibold">Invoice Total</dt>
                            <dd class="col-sm-7 text-dark fw-semibold">KES {{ number_format((float) ($payment->invoice?->total_amount ?? 0), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Box -->
            <div class="my-4 pt-3 border-top">
                <h6 class="text-uppercase small fw-bold text-muted mb-3">
                    Balance Breakdown
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white">
                            <small class="text-muted d-block fw-semibold text-uppercase">Amount Received</small>
                            <span class="fs-4 fw-bold text-success">
                                KES {{ number_format((float) $payment->amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white">
                            <small class="text-muted d-block fw-semibold text-uppercase">Remaining Invoice Balance</small>
                            <span class="fs-4 fw-bold {{ ($payment->invoice?->balance_amount ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                KES {{ number_format((float) ($payment->invoice?->balance_amount ?? 0), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($payment->notes)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-uppercase small fw-bold text-muted mb-2">Notes</h6>
                    <p class="mb-0 text-muted bg-light p-3 rounded-3">{{ $payment->notes }}</p>
                </div>
            @endif

        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printableReceipt, #printableReceipt * {
        visibility: visible;
    }
    #printableReceipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none !important;
        box-shadow: none !important;
    }
    .btn, nav, footer, .sidebar {
        display: none !important;
    }
}
</style>
@endpush
