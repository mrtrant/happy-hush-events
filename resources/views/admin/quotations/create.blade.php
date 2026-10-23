@extends('layouts.admin')

@section('title', 'New Quotation')
@section('page-title', 'New Quotation')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Create a quotation for a client.</p>
        </div>
        <a href="{{ route('admin.quotations.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
            Cancel
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

    <form method="POST" action="{{ route('admin.quotations.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column (8 Spans) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Quotation Details Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-base font-semibold text-gray-800">Quotation Details</h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
                                <select id="client_id" name="client_id" required
                                        class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select client</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                        {{ $client->first_name }} {{ $client->last_name }} — {{ $client->client_number }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label>
                                <input type="date" id="valid_until" name="valid_until"
                                       value="{{ old('valid_until') }}"
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="booking_id" class="block text-sm font-medium text-gray-700 mb-1">Booking ID</label>
                                <input type="number" id="booking_id" name="booking_id" placeholder="Optional"
                                       value="{{ old('booking_id') }}"
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-400 mt-1">Leave blank if this quotation is not linked to a booking.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Settings Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-base font-semibold text-gray-800">Financial Settings</h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="discount_amount" class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                                <input type="number" step="0.01" min="0" id="discount_amount" name="discount_amount"
                                       value="{{ old('discount_amount', 0) }}"
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="tax_amount" class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                                <input type="number" step="0.01" min="0" id="tax_amount" name="tax_amount"
                                       value="{{ old('tax_amount', 0) }}"
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea id="notes" name="notes" rows="5"
                                          class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column (4 Spans) -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6 space-y-4">
                    <button type="submit"
                            class="w-full py-2.5 bg-gray-900 text-white font-medium text-sm rounded-lg hover:bg-gray-800 shadow-sm transition-colors text-center">
                        Create Quotation
                    </button>
                    <a href="{{ route('admin.quotations.index') }}"
                       class="block w-full py-2.5 bg-white border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 shadow-sm transition-colors text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
