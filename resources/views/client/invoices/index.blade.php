@extends('layouts.client')

@section('title', 'My Invoices')

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-invoice-dollar me-2" style="color: var(--brand-orange);"></i> My Invoices
            </h2>
            <p class="text-muted mb-0">
                View your issued invoices, track payment balances, and review billing history.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Invoices Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                Invoice Records
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="py-3 px-4">Invoice No.</th>
                            <th class="py-3">Invoice Date</th>
                            <th class="py-3">Due Date</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Paid</th>
                            <th class="py-3">Balance</th>
                            <th class="py-3 text-end px-4" width="130">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="px-4 fw-semibold text-dark">
                                    {{ $invoice->invoice_number }}
                                </td>
                                <td class="text-muted">
                                    {{ $invoice->invoice_date?->format('d M Y') }}
                                </td>
                                <td class="text-muted">
                                    {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($invoice->status) {
                                            'Paid' => 'bg-success',
                                            'Partially Paid' => 'bg-warning text-dark',
                                            'Overdue' => 'bg-danger',
                                            'Cancelled' => 'bg-secondary',
                                            'Sent' => 'bg-primary',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-3 py-2 fw-semibold">
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                                <td class="fw-semibold">
                                    KES {{ number_format((float) $invoice->total_amount, 2) }}
                                </td>
                                <td class="text-success fw-semibold">
                                    KES {{ number_format((float) $invoice->paid_amount, 2) }}
                                </td>
                                <td class="fw-semibold">
                                    @if((float) $invoice->balance_amount > 0)
                                        <span class="text-danger">
                                            KES {{ number_format((float) $invoice->balance_amount, 2) }}
                                        </span>
                                    @else
                                        <span class="text-success">
                                            KES 0.00
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <a href="{{ route('client.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="my-3">
                                        <i class="fa-solid fa-file-circle-xmark fs-1 text-secondary opacity-50 mb-2"></i>
                                        <h5 class="fw-bold text-dark mt-2">No invoices found</h5>
                                        <p class="mb-0">Your invoices will appear here once they are generated and issued.</p>
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
    @if($invoices->hasPages())
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    @endif

</div>

@endsection
