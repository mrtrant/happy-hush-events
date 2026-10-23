@extends('layouts.client')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')

<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-file-invoice me-2" style="color: var(--brand-orange);"></i> Invoice {{ $invoice->invoice_number }}
            </h2>
            <p class="text-muted mb-0">
                Here is the detailed breakdown of your charges, items, and payments.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('client.invoices.index') }}" class="btn btn-outline-secondary px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Invoices
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Invoice Summary & Items (Left Column) -->
        <div class="col-lg-8">
            <!-- Metadata Card -->
            <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">Invoice Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6 col-md-6">
                            <span class="text-muted small d-block fw-semibold">Invoice Number</span>
                            <strong class="text-dark">{{ $invoice->invoice_number }}</strong>
                        </div>
                        <div class="col-6 col-md-6">
                            <span class="text-muted small d-block fw-semibold">Status</span>
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
                            <span class="badge {{ $statusClass }} px-3 py-1 fw-semibold mt-1">
                                {{ $invoice->status }}
                            </span>
                        </div>
                        <div class="col-6 col-md-6">
                            <span class="text-muted small d-block fw-semibold">Invoice Date</span>
                            <span class="text-dark">{{ $invoice->invoice_date?->format('d M Y') }}</span>
                        </div>
                        <div class="col-6 col-md-6">
                            <span class="text-muted small d-block fw-semibold">Due Date</span>
                            <span class="text-dark">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</span>
                        </div>
                        @if($invoice->booking)
                            <div class="col-12 pt-2">
                                <span class="text-muted small d-block fw-semibold">Associated Booking</span>
                                <a href="{{ route('client.bookings.show', $invoice->booking) }}" class="fw-semibold text-decoration-none" style="color: var(--brand-orange);">
                                    <i class="fa-solid fa-link me-1"></i> {{ $invoice->booking->booking_number }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items Section (Responsive: Desktop Table / Mobile Stacked Cards) -->
            <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">Invoice Items</h5>
                </div>

                <!-- Desktop View Table -->
                <div class="card-body p-0 d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fs-7">
                                <tr>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 text-center">Qty</th>
                                    <th class="py-3 text-end">Unit Price</th>
                                    <th class="py-3 text-end px-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->items as $item)
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-semibold text-dark">
                                                {{ $item->description }}
                                            </div>
                                            @if($item->service)
                                                <small class="text-muted">
                                                    Service: {{ $item->service->name }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">KES {{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="text-end px-4 fw-semibold">KES {{ number_format((float) $item->total_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            No invoice items found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile View Cards -->
                <div class="card-body p-3 d-md-none d-flex flex-column gap-3">
                    @forelse($invoice->items as $item)
                        <div class="p-3 bg-light rounded-3 border-0">
                            <div class="fw-bold text-dark mb-1">{{ $item->description }}</div>
                            @if($item->service)
                                <div class="text-muted small mb-2">Service: {{ $item->service->name }}</div>
                            @endif
                            <div class="d-flex justify-content-between text-muted small border-top pt-2 mt-2">
                                <span>Qty: {{ $item->quantity }} × KES {{ number_format((float) $item->unit_price, 2) }}</span>
                                <strong class="text-dark">KES {{ number_format((float) $item->total_price, 2) }}</strong>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted">
                            No invoice items found.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Notes Section -->
            @if($invoice->notes)
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark">Notes & Instructions</h5>
                    </div>
                    <div class="card-body p-4 text-muted">
                        {!! nl2br(e($invoice->notes)) !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Payment Summary, Action & History (Right Column) -->
        <div class="col-lg-4">
            <!-- Make a Payment / Status Section -->
            @if($invoice->balance_amount > 0)
                <div class="card border-0 shadow-sm p-4 bg-light mb-4 rounded-3 border-start border-4 border-warning">
                    <h5 class="fw-bold mb-2">Make a Payment</h5>
                    <p class="text-muted small mb-3">Clear your balance securely using M-Pesa or Card.</p>
                    <form action="{{ route('client.payments.store', $invoice) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select shadow-none" required>
                                <option value="M-Pesa">M-Pesa (STK Push)</option>
                                <option value="Card">Credit / Debit Card</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Amount to Pay (KES)</label>
                            <input type="number" name="amount" class="form-control shadow-none" value="{{ $invoice->balance_amount }}" max="{{ $invoice->balance_amount }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone Number (For M-Pesa)</label>
                            <input type="text" name="phone_number" class="form-control shadow-none" placeholder="07XXXXXXXX" value="{{ auth()->user()->phone ?? '' }}">
                        </div>

                        <button type="submit" class="btn btn-brand text-white w-100 py-2 fw-semibold shadow-sm">
                            Proceed to Pay KES {{ number_format($invoice->balance_amount, 2) }}
                        </button>
                    </form>
                </div>
            @else
                <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                    <div>
                        <strong>All caught up!</strong><br>
                        <span class="small">This invoice has been fully paid. Thank you!</span>
                    </div>
                </div>
            @endif

            <!-- Payment Totals Card -->
            <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">Payment Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <strong class="text-dark">KES {{ number_format((float) $invoice->subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Discount</span>
                        <strong class="text-dark">KES {{ number_format((float) $invoice->discount_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tax</span>
                        <strong class="text-dark">KES {{ number_format((float) $invoice->tax_amount, 2) }}</strong>
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold text-dark">Total Amount</span>
                        <strong class="text-dark">KES {{ number_format((float) $invoice->total_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Amount Paid</span>
                        <strong class="text-success">KES {{ number_format((float) $invoice->paid_amount, 2) }}</strong>
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold text-dark">Balance Due</span>
                        <strong class="{{ (float) $invoice->balance_amount > 0 ? 'text-danger' : 'text-success' }} fs-5">
                            KES {{ number_format((float) $invoice->balance_amount, 2) }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Payment History Card -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">Payment History</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($invoice->payments as $payment)
                        <div class="p-3 border-bottom last-border-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-success">
                                    KES {{ number_format((float) $payment->amount, 2) }}
                                </strong>
                                <small class="text-muted">
                                    {{ $payment->payment_date?->format('d M Y') }}
                                </small>
                            </div>
                            <div class="small text-dark fw-semibold">
                                <i class="fa-solid fa-wallet me-1 text-warning"></i> {{ $payment->paymentMethod?->name ?? 'Payment' }}
                            </div>
                            @if($payment->transaction_reference)
                                <div class="small text-muted mt-1">
                                    Ref: <span class="font-monospace text-dark">{{ $payment->transaction_reference }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fa-solid fa-receipt fs-3 text-secondary opacity-50 mb-2"></i>
                            <p class="mb-0">No payments recorded yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
