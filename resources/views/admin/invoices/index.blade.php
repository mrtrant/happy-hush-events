@extends('layouts.admin')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Manage customer invoices and outstanding balances.</p>
        </div>

        <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
            Create Invoice
        </a>
    </div>

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

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.invoices.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           value="{{ request('search') }}"
                           placeholder="Invoice number, client, email...">
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">All statuses</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ $status }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-900 shadow-sm transition-colors text-center">
                        Filter
                    </button>
                    <a href="{{ route('admin.invoices.index') }}" class="px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 shadow-sm text-center transition-colors">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($invoices->count())
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Invoice</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Invoice Date</th>
                        <th class="px-6 py-3">Due Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Paid</th>
                        <th class="px-6 py-3 text-right">Balance</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Invoice Number -->
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="font-semibold text-blue-600 hover:text-blue-900">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>

                        <!-- Client -->
                        <td class="px-6 py-4">
                            @if($invoice->organization_name)
                                <div class="font-semibold text-gray-900">{{ $invoice->organization_name }}</div>
                            @else
                                <div class="font-semibold text-gray-900">{{ $invoice->first_name }} {{ $invoice->last_name }}</div>
                            @endif
                            <div class="text-xs text-gray-500">{{ $invoice->client_number }}</div>
                        </td>

                        <!-- Invoice Date -->
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}
                        </td>

                        <!-- Due Date -->
                        <td class="px-6 py-4">
                            @if($invoice->due_date)
                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4">
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
                        </td>

                        <!-- Total -->
                        <td class="px-6 py-4 text-right font-medium text-gray-900">
                            KES {{ number_format((float) $invoice->total_amount, 2) }}
                        </td>

                        <!-- Paid -->
                        <td class="px-6 py-4 text-right font-medium text-gray-600">
                            KES {{ number_format((float) $invoice->paid_amount, 2) }}
                        </td>

                        <!-- Balance -->
                        <td class="px-6 py-4 text-right">
                            <span class="{{ (float) $invoice->balance_amount > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-medium' }}">
                                KES {{ number_format((float) $invoice->balance_amount, 2) }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-2 justify-end">
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                    View
                                </a>

                                @if(!in_array($invoice->status, ['Paid', 'Cancelled']))
                                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="text-gray-600 hover:text-gray-900 font-medium text-xs">
                                    Edit
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $invoices->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="text-center py-12 px-6">
            <h3 class="text-base font-semibold text-gray-800 mb-1">No invoices found</h3>
            <p class="text-sm text-gray-500 mb-4">There are no invoices matching the current filters.</p>
            <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                Create Invoice
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
