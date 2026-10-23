@extends('layouts.admin')

@section('title', 'Quotations')
@section('page-title', 'Quotations')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Create and manage customer quotations.</p>
        </div>

        <a href="{{ route('admin.quotations.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 shadow-sm transition-colors">
            New Quotation
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm flex items-center justify-between">
        <span>{{ session('success') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.quotations.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-6">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search" name="search"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           value="{{ $search ?? '' }}"
                           placeholder="Quotation number, client, email, booking...">
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">All statuses</option>
                        @foreach($statuses as $itemStatus)
                        <option value="{{ $itemStatus }}" @selected(($selectedStatus ?? '') === $itemStatus)>
                            {{ $itemStatus }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter & Reset Buttons -->
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-900 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-800 shadow-sm transition-colors text-center">
                        Filter
                    </button>
                    <a href="{{ route('admin.quotations.index') }}" class="px-3 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 shadow-sm transition-colors text-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Quotation</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Booking</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Valid Until</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse($quotations as $quotation)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Quotation Number & Date -->
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 font-mono">
                                {{ $quotation->quotation_number }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($quotation->created_at)->format('d M Y') }}
                            </div>
                        </td>

                        <!-- Client -->
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">
                                {{ trim($quotation->first_name . ' ' . ($quotation->middle_name ?? '') . ' ' . $quotation->last_name) }}
                            </div>
                            <div class="text-xs font-mono text-gray-500">
                                {{ $quotation->client_number }}
                            </div>
                        </td>

                        <!-- Booking -->
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">
                            {{ $quotation->booking_number ?: '—' }}
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border">
                                {{ $quotation->status }}
                            </span>
                        </td>

                        <!-- Valid Until -->
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d M Y') : '—' }}
                        </td>

                        <!-- Total -->
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                            KES {{ number_format((float) $quotation->total_amount, 2) }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No quotations found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($quotations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $quotations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
