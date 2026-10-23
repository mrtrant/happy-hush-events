@extends('layouts.admin')

@section('title', 'Create Invoice')
@section('page-title', 'Create Invoice')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Create a new customer invoice.</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
            &larr; Back to Invoices
        </a>
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

    <form method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-6">
        @csrf

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
                                    <option value="">Select client</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
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
                                    <option value="{{ $booking->id }}" @selected(old('booking_id') == $booking->id)>
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
                                       value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                <input type="date" name="due_date"
                                       value="{{ old('due_date') }}"
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Adjustments Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-800">Financial Adjustments</h2>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Discount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                                <div class="relative flex rounded-lg shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium">KES</span>
                                    <input type="number" name="discount_amount" min="0" step="0.01"
                                           value="{{ old('discount_amount', 0) }}"
                                           class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Tax -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                                <div class="relative flex rounded-lg shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium">KES</span>
                                    <input type="number" name="tax_amount" min="0" step="0.01"
                                           value="{{ old('tax_amount', 0) }}"
                                           class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea name="notes" rows="5" placeholder="Optional invoice notes..."
                                          class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-base font-semibold text-gray-800">Invoice Status</h2>
                    </div>
                    <div class="p-6">
                        <div class="p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                            New invoices are created as <strong class="font-semibold">Draft</strong>. Add invoice items after creation.
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-3">
                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm transition-colors text-center">
                        Create Invoice
                    </button>
                    <a href="{{ route('admin.invoices.index') }}" class="w-full block py-2.5 px-4 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium text-sm rounded-lg shadow-sm transition-colors text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
