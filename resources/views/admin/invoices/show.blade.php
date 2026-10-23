@extends('layouts.admin')

@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', $invoice->invoice_number)

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm">
        {{ session('success') }}
    </div>
    @endif

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

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Created {{ $invoice->created_at->format('d M Y H:i') }}</p>
        </div>

        <div class="flex items-center gap-2">
            @if(!in_array($invoice->status, ['Paid', 'Cancelled']))
            <a href="{{ route('admin.invoices.edit', $invoice) }}"
               class="inline-flex items-center px-4 py-2 border border-blue-600 rounded-lg text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 shadow-sm transition-colors">
                Edit Invoice
            </a>
            @endif

            <a href="{{ route('admin.invoices.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                Back to Invoices
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Details & Items -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Invoice Details Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800">Invoice Details</h2>

                    @php
                        $statusClasses = match($invoice->status) {
                            'Draft' => 'bg-gray-100 text-gray-800',
                            'Sent' => 'bg-blue-100 text-blue-800',
                            'Partially Paid' => 'bg-yellow-100 text-yellow-800',
                            'Paid' => 'bg-green-100 text-green-800',
                            'Overdue' => 'bg-red-100 text-red-800',
                            'Cancelled' => 'bg-gray-800 text-white',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                        {{ $invoice->status }}
                    </span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bill To -->
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Bill To</h3>
                            @if($invoice->client)
                                @if($invoice->client->organization_name)
                                    <div class="font-semibold text-gray-900">{{ $invoice->client->organization_name }}</div>
                                @endif
                                @if($invoice->client->user)
                                    <div class="text-gray-700 text-sm">
                                        {{ $invoice->client->user->first_name }} {{ $invoice->client->user->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $invoice->client->user->email }}</div>
                                @endif
                                <div class="text-xs text-gray-400 mt-1 font-mono">{{ $invoice->client->client_number }}</div>
                            @else
                                <span class="text-sm text-gray-400 italic">Client unavailable</span>
                            @endif
                        </div>

                        <!-- Invoice Information -->
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Information</h3>
                            <div class="space-y-1.5 text-sm text-gray-700">
                                <div><span class="font-medium text-gray-900">Invoice Date:</span> {{ optional($invoice->invoice_date)->format('d M Y') }}</div>
                                <div>
                                    <span class="font-medium text-gray-900">Due Date:</span>
                                    @if($invoice->due_date)
                                        {{ optional($invoice->due_date)->format('d M Y') }}
                                    @else
                                        <span class="text-gray-400">Not specified</span>
                                    @endif
                                </div>
                                @if($invoice->booking)
                                <div><span class="font-medium text-gray-900">Booking:</span> <a href="#" class="text-blue-600 hover:underline">{{ $invoice->booking->booking_number }}</a></div>
                                @endif
                                @if($invoice->quotation)
                                <div><span class="font-medium text-gray-900">Quotation:</span> <span class="text-gray-600">{{ $invoice->quotation->quotation_number }}</span></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-800">Invoice Items</h2>
                </div>

                <div class="overflow-x-auto">
                    @if($invoice->items->count())
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3 text-center">Qty</th>
                                <th class="px-6 py-3 text-right">Unit Price</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @foreach($invoice->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $item->description }}</div>
                                    @if($item->service)
                                        <div class="text-xs font-mono text-gray-500">{{ $item->service->service_code }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right">KES {{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">KES {{ number_format((float) $item->total_price, 2) }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if(!in_array($invoice->status, ['Paid', 'Cancelled']))
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <!-- Inline toggle for editing item via Alpine.js or simple details toggle -->
                                        <button type="button" onclick="document.getElementById('edit-item-row-{{ $item->id }}').classList.toggle('hidden')" class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('admin.invoices.items.destroy', [$invoice->id, $item->id]) }}" onsubmit="return confirm('Remove this invoice item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>

                            <!-- Hidden Edit Item Row -->
                            @if(!in_array($invoice->status, ['Paid', 'Cancelled']))
                            <tr id="edit-item-row-{{ $item->id }}" class="hidden bg-gray-50">
                                <td colspan="5" class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.invoices.items.update', [$invoice->id, $item->id]) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                                            <input type="text" name="description" value="{{ $item->description }}" required class="w-full text-xs rounded-lg border-gray-300 border px-2 py-1.5">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                                            <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" required class="w-full text-xs rounded-lg border-gray-300 border px-2 py-1.5">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price (KES)</label>
                                            <input type="number" name="unit_price" min="0" step="0.01" value="{{ $item->unit_price }}" required class="w-full text-xs rounded-lg border-gray-300 border px-2 py-1.5">
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700">Save</button>
                                            <button type="button" onclick="document.getElementById('edit-item-row-{{ $item->id }}').classList.add('hidden')" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-300">Cancel</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-12 px-6 text-gray-500 text-sm">
                        No invoice items. Add services or custom items below.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Add Item Form -->
            @if(!in_array($invoice->status, ['Paid', 'Cancelled']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-800">Add Invoice Item</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.invoices.items.store', $invoice) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service (Optional)</label>
                                <select name="service_id" id="service_id" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">Custom item</option>
                                    @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-name="{{ $service->name }}" data-price="{{ $service->base_price }}">
                                        {{ $service->service_code }} — {{ $service->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                <input type="text" name="description" id="item_description" maxlength="255" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" min="1" value="1" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price (KES) <span class="text-red-500">*</span></label>
                                <input type="number" name="unit_price" id="item_unit_price" min="0" step="0.01" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm transition-colors">
                                    Add Item
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Summary, Status & Danger Zone -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Financial Summary Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-800">Financial Summary</h2>
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

            <!-- Change Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-800">Change Status</h2>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('admin.invoices.status', $invoice) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <select name="status" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected($invoice->status === $status)>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>

                        <button type="submit" class="w-full py-2 px-4 bg-gray-800 hover:bg-gray-900 text-white font-medium text-sm rounded-lg shadow-sm transition-colors text-center">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Notes Card -->
            @if($invoice->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-800">Notes</h2>
                </div>
                <div class="p-6 text-sm text-gray-600 space-y-2">
                    {!! nl2br(e($invoice->notes)) !!}
                </div>
            </div>
            @endif

            <!-- Danger Zone -->
            @if($invoice->status === 'Draft')
            <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-red-100 bg-red-50 text-red-700">
                    <h2 class="text-base font-semibold">Danger Zone</h2>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-xs text-gray-500">Draft invoices can be permanently deleted. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" onsubmit="return confirm('Permanently delete this draft invoice?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-medium text-sm rounded-lg shadow-sm transition-colors text-center">
                            Delete Draft Invoice
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const description = document.getElementById('item_description');
    const unitPrice = document.getElementById('item_unit_price');

    if (!serviceSelect || !description || !unitPrice) {
        return;
    }

    serviceSelect.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];

        if (!this.value) {
            description.value = '';
            unitPrice.value = '';
            return;
        }

        description.value = option.dataset.name || '';
        unitPrice.value = option.dataset.price || '';
    });
});
</script>
@endpush
