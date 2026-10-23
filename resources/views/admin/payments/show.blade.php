@extends('layouts.admin')

@section('title', 'Payment Receipt')
@section('page-title', $payment->receipt_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Navigation & Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 font-mono">Payment receipt details</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                Print Receipt
            </button>
            <a href="{{ route('admin.payments.index') }}"
               class="inline-flex items-center px-4 py-2 border border-blue-600 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors">
                All Payments
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm flex items-center justify-between">
        <span>{{ session('success'] }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif

    <!-- Receipt Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 space-y-6">
        <!-- Top Invoice Meta -->
        <div class="flex flex-col sm:flex-row justify-between border-b border-gray-200 pb-6 gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Happy Hush Events Kenya</h3>
                <p class="text-sm text-gray-500">Official Payment Receipt</p>
            </div>
            <div class="sm:text-right space-y-1 text-sm">
                <div><span class="font-semibold text-gray-700">Receipt:</span> <span class="font-mono text-gray-900">{{ $payment->receipt_number }}</span></div>
                <div><span class="font-semibold text-gray-700">Date:</span> <span class="text-gray-900">{{ $payment->payment_date->format('d M Y') }}</span></div>
            </div>
        </div>

        <!-- Sender / Invoice Metadata Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Received From</h4>
                @if($payment->invoice?->client?->user)
                    <div class="font-medium text-gray-900">
                        {{ $payment->invoice->client->user->first_name }}
                        {{ $payment->invoice->client->user->last_name }}
                    </div>
                    <div class="text-gray-600">{{ $payment->invoice->client->user->email }}</div>
                @else
                    <div class="text-gray-500 italic">Client information unavailable</div>
                @endif
            </div>

            <div class="space-y-1">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Invoice & Payment Info</h4>
                <div><span class="font-medium text-gray-700">Invoice:</span> <span class="font-mono text-gray-900">{{ $payment->invoice?->invoice_number ?? 'Invoice unavailable' }}</span></div>
                <div><span class="font-medium text-gray-700">Method:</span> <span class="text-gray-900">{{ $payment->paymentMethod?->name ?? 'Not specified' }}</span></div>
                <div><span class="font-medium text-gray-700">Reference:</span> <span class="font-mono text-gray-900">{{ $payment->transaction_reference ?: '—' }}</span></div>
            </div>
        </div>

        <!-- Financial Table -->
        <div class="overflow-x-auto pt-2">
            <table class="w-full text-left border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3 border-b border-gray-200">Description</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    <tr>
                        <td class="px-4 py-3">Payment received against invoice</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900">{{ number_format((float) $payment->amount, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-semibold text-gray-900 text-sm">
                        <td class="px-4 py-3 border-t border-gray-200">Amount Received</td>
                        <td class="px-4 py-3 border-t border-gray-200 text-right">KES {{ number_format((float) $payment->amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes Section -->
        @if($payment->notes)
        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
            <h5 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Notes</h5>
            <p>{{ $payment->notes }}</p>
        </div>
        @endif

        <!-- Footer Meta -->
        <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-2">
            <div>
                Recorded by: {{ $payment->receiver?->first_name ?? 'System user' }} {{ $payment->receiver?->last_name ?? '' }}
            </div>
            <div class="italic text-gray-400">
                Thank you for your payment.
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .max-w-4xl, .max-w-4xl * {
        visibility: visible;
    }
    .max-w-4xl {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
    }
    button, a {
        display: none !important;
    }
}
</style>
@endsection
