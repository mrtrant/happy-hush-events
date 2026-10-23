@extends('layouts.admin')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Record a received payment against an invoice.</p>
        </div>
        <a href="{{ route('admin.payments.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
            Back to Payments
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm shadow-sm">
        <div class="font-semibold mb-1">Please correct the following:</div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <form method="POST" action="{{ route('admin.payments.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Invoice Select -->
                <div>
                    <label for="invoice_id" class="block text-sm font-medium text-gray-700 mb-1">Invoice <span class="text-red-500">*</span></label>
                    <select name="invoice_id" id="invoice_id" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select an invoice</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" @selected(old('invoice_id', $selectedInvoiceId) == $invoice->id)>
                                {{ $invoice->invoice_number }} — Balance: KES {{ number_format((float) $invoice->total_amount - (float) $invoice->paid_amount, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @if($invoices->isEmpty())
                        <p class="text-xs text-red-600 mt-1">No eligible invoices with outstanding balances were found.</p>
                    @endif
                </div>

                <!-- Payment Method Select -->
                <div>
                    <label for="payment_method_id" class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method_id" id="payment_method_id" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a payment method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>
                                {{ $method->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (KES) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" min="0.01" step="0.01" required
                           value="{{ old('amount') }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Payment Date -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" id="payment_date" required
                           max="{{ now()->toDateString() }}"
                           value="{{ old('payment_date', now()->toDateString()) }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Transaction Reference -->
                <div class="md:col-span-2">
                    <label for="transaction_reference" class="block text-sm font-medium text-gray-700 mb-1">Transaction Reference</label>
                    <input type="text" name="transaction_reference" id="transaction_reference" maxlength="100"
                           value="{{ old('transaction_reference') }}"
                           placeholder="Enter the M-Pesa transaction code, if applicable"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Enter the exact transaction code from the payment confirmation.</p>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="3" maxlength="2000"
                              class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.payments.index') }}"
                   class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 shadow-sm transition-colors">
                    Cancel
                </a>
                <button type="submit" @disabled($invoices->isEmpty())
                        class="px-5 py-2.5 bg-gray-900 text-white font-medium text-sm rounded-lg hover:bg-gray-800 shadow-sm disabled:opacity-50 transition-colors">
                    Save Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
