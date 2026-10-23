@extends('layouts.admin')

@section('title', 'Edit Invoice')
@section('page-title', 'Edit Invoice')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-gray-800">{{ $invoice->invoice_number }}</p>
            <p class="text-sm text-gray-500">Update invoice details and financial adjustments.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.invoices.show', $invoice) }}"
               class="inline-flex items-center px-4 py-2 border border-blue-600 rounded-lg text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 shadow-sm transition-colors">
                View Invoice
            </a>
            <a href="{{ route('admin.invoices.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                Back
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm shadow-sm space-y-1">
        <div class="font-semibold">Please correct the following:</div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.invoices.update', $invoice) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Main Column -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Invoice Information Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-800">Invoice Information</h2>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Client -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Client <span class="text-red-500">*</span>
                                </label>
                                <select name="client_id" required
                                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id', $invoice->client_id) == $client->id)>
                                        {{ $client->client_number }} —
                                        @if($client->organization_name)
                                            {{ $client->organization_name }}
                                        @else
                                            {{ $client->first_name }} {{ $client->last_name }}
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Booking -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Booking</label>
                                <select name="booking_id"
                                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">No booking</option>
                                    @foreach($bookings as $booking)
                                    <option value="{{ $booking->id }}" @selected(old('booking_id', $invoice->booking_id) == $booking->id)>
                                        {{ $booking->booking_number }} — {{ $booking->status }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Invoice Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Invoice Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="invoice_date" required
                                       value="{{ old('invoice_date', optional($invoice->invoice_date)->format('Y-m-d')) }}"
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                <input type="date" name="due_date"
                                       value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}"
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>

                            <!-- Discount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                                <div class="relative flex rounded-lg shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium">KES</span>
                                    <input type="number" name="discount_amount" min="0" step="0.01"
                                           value="{{ old('discount_amount', $invoice->discount_amount) }}"
                                           class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Tax -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                                <div class="relative flex rounded-lg shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium">KES</span>
                                    <input type="number" name="tax_amount" min="0" step="0.01"
                                           value="{{ old('tax_amount', $invoice->tax_amount) }}"
                                           class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea name="notes" rows="5"
                                          class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('notes', $invoice->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Current Totals Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-800">Current Totals</h2>
                    </div>

                    <div class="p-6 space-y-3 text-sm text-gray-700">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-900">KES {{ number_format((float) $invoice->subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Discount</span>
                            <span class="font-semibold text-gray-900">KES {{ number_format((float) $invoice->discount_amount, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Tax</span>
                            <span class="font-semibold text-gray-900">KES {{ number_format((float) $invoice->tax_amount, 2) }}</span>
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-base">
                                <span class="font-medium text-gray-900">Total</span>
                                <span class="font-bold text-gray-900">KES {{ number_format((float) $invoice->total_amount, 2) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Paid</span>
                            <span class="font-semibold text-green-600">KES {{ number_format((float) $invoice->paid_amount, 2) }}</span>
                        </div>

                        <div class="flex justify-between border-t border-gray-200 pt-3">
                            <span class="font-medium text-gray-900">Balance</span>
                            <span class="font-bold text-red-600">KES {{ number_format((float) $invoice->balance_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm transition-colors text-center">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
