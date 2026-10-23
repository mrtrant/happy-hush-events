@extends('layouts.admin')

@section('title', 'Quotation')
@section('page-title', $quotation->quotation_number)

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Actions & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Quotation details and items.</p>
        </div>

        <div class="flex items-center gap-2">
            @if(in_array($quotation->status, ['Draft', 'Rejected'], true))
                <a href="{{ route('admin.quotations.edit', $quotation->id) }}"
                   class="inline-flex items-center px-4 py-2 border border-blue-600 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors">
                    Edit
                </a>
            @endif

            <a href="{{ route('admin.quotations.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                Back
            </a>
        </div>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">&times;</button>
        </div>
    @endif

    <!-- Validation Errors -->
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

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column (8 Spans) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Client Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-base font-semibold text-gray-800">Client</h3>
                </div>

                <div class="p-6 text-sm space-y-1">
                    <div class="font-semibold text-gray-900">
                        {{ trim($quotation->first_name . ' ' . ($quotation->middle_name ?? '') . ' ' . $quotation->last_name) }}
                    </div>

                    <div class="text-xs font-mono text-gray-500">
                        {{ $quotation->client_number }}
                    </div>

                    <div class="text-gray-700 pt-1">
                        {{ $quotation->email }}
                    </div>

                    @if($quotation->phone)
                        <div class="text-gray-700">
                            {{ $quotation->phone }}
                        </div>
                    @endif

                    @if($quotation->organization_name)
                        <div class="text-gray-500 pt-1">
                            {{ $quotation->organization_name }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quotation Items Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-base font-semibold text-gray-800">Quotation Items</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3 text-center">Qty</th>
                                <th class="px-6 py-3 text-right">Unit Price</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @forelse($items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            {{ $item->description }}
                                        </div>

                                        @if($item->service_name)
                                            <div class="text-xs text-gray-500 font-mono mt-0.5">
                                                {{ $item->service_code }} — {{ $item->service_name }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        KES {{ number_format((float) $item->unit_price, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                        KES {{ number_format((float) $item->total_price, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        @if(in_array($quotation->status, ['Draft', 'Rejected'], true))
                                            <form method="POST" action="{{ route('admin.quotations.items.destroy', [$quotation->id, $item->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 font-medium text-xs"
                                                        onclick="return confirm('Remove this item?')">
                                                    Remove
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                        No quotation items have been added.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Item Form Card -->
            @if(in_array($quotation->status, ['Draft', 'Rejected'], true))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-base font-semibold text-gray-800">Add Item</h3>
                    </div>

                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.quotations.items.store', $quotation->id) }}">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                                    <select name="service_id" id="service_id"
                                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Custom item</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}"
                                                    data-price="{{ $service->base_price }}"
                                                    data-name="{{ $service->name }}">
                                                {{ $service->service_code }} — {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                    <input type="text" name="description" id="description" required
                                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                    <input type="number" name="quantity" min="1" value="1" required
                                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-1">Unit Price <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" required
                                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="md:col-span-2 flex justify-end pt-2">
                                    <button type="submit"
                                            class="px-5 py-2.5 bg-gray-900 text-white font-medium text-sm rounded-lg hover:bg-gray-800 shadow-sm transition-colors">
                                        Add Item
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Notes Card -->
            @if($quotation->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-base font-semibold text-gray-800">Notes</h3>
                    </div>
                    <div class="p-6 text-sm text-gray-700">
                        {!! nl2br(e($quotation->notes)) !!}
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Column (4 Spans) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Quotation Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3">Quotation Summary</h3>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium text-gray-900">KES {{ number_format((float) $quotation->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600">
                        <span>Discount</span>
                        <span class="font-medium text-gray-900">KES {{ number_format((float) $quotation->discount_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 pb-2 border-b border-gray-100">
                        <span>Tax</span>
                        <span class="font-medium text-gray-900">KES {{ number_format((float) $quotation->tax_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-base font-semibold text-gray-900 pt-1">
                        <span>Total</span>
                        <span>KES {{ number_format((float) $quotation->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Update Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-800">Status</h3>

                <div>
                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-900 text-white">
                        {{ $quotation->status }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.quotations.change-status', $quotation->id) }}" class="space-y-3 pt-2 border-t border-gray-100">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Change Status</label>
                        <select name="status"
                                class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected($quotation->status === $status)>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full py-2 bg-gray-900 text-white font-medium text-sm rounded-lg hover:bg-gray-800 shadow-sm transition-colors text-center">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Delete Draft Card -->
            @if($quotation->status === 'Draft')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <form method="POST" action="{{ route('admin.quotations.destroy', $quotation->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full py-2 border border-red-300 text-red-700 bg-red-50 font-medium text-sm rounded-lg hover:bg-red-100 shadow-sm transition-colors text-center"
                                onclick="return confirm('Delete this draft quotation?')">
                            Delete Quotation
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>

</div>

<!-- Service Auto-Fill Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const service = document.getElementById('service_id');
    const description = document.getElementById('description');
    const price = document.getElementById('unit_price');

    if (!service) {
        return;
    }

    service.addEventListener('change', function () {
        const option = service.options[service.selectedIndex];

        if (!option.value) {
            description.value = '';
            price.value = '';
            return;
        }

        description.value = option.dataset.name || '';
        price.value = option.dataset.price || '';
    });
});
</script>
@endsection
