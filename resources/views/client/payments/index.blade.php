@extends('layouts.client')

@section('title', 'Payment History')

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-receipt me-2" style="color: var(--brand-orange);"></i> Payment History
            </h2>
            <p class="text-muted mb-0">
                View all payment transactions and official receipts recorded against your invoices.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Payment Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                Recorded Payments
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3 px-4">Receipt No.</th>
                            <th class="py-3">Invoice Ref</th>
                            <th class="py-3">Method</th>
                            <th class="py-3">Transaction Reference</th>
                            <th class="py-3">Payment Date</th>
                            <th class="py-3 text-end">Amount</th>
                            <th class="py-3 text-end px-4" width="130">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="px-4 fw-semibold text-dark">
                                {{ $payment->receipt_number }}
                            </td>
                            <td>
                                @if($payment->invoice)
                                    <a href="{{ route('client.invoices.show', $payment->invoice) }}" class="text-decoration-none fw-semibold" style="color: var(--brand-orange);">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fa-solid fa-wallet me-1 text-warning"></i> {{ $payment->paymentMethod?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="font-monospace text-dark">
                                {{ $payment->transaction_reference ?: '—' }}
                            </td>
                            <td class="text-muted">
                                {{ $payment->payment_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="text-end fw-bold text-success">
                                KES {{ number_format((float) $payment->amount, 2) }}
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('client.payments.show', $payment) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-receipt me-1"></i> Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="my-3">
                                    <i class="fa-solid fa-receipt fs-1 text-secondary opacity-50 mb-2"></i>
                                    <h5 class="fw-bold text-dark mt-2">No payments found</h5>
                                    <p class="mb-0">Your payment history will appear here once payments are recorded.</p>
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
    @if($payments->hasPages())
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    @endif

</div>

@endsection
