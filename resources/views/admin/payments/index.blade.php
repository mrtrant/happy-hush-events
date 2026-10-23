@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Recorded payments and M-Pesa references</p>
        </div>

        <a href="{{ route('admin.payments.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 shadow-sm transition-colors">
            Record Payment
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm flex items-center justify-between">
        <span>{{ session('success') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif

    <!-- Search Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.payments.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-10">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="search" id="search" name="search" value="{{ $search }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Search receipt, M-Pesa reference, invoice or client">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-gray-900 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-800 shadow-sm transition-colors text-center">
                        Search
                    </button>
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
                        <th class="px-6 py-3">Receipt</th>
                        <th class="px-6 py-3">Invoice</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Method</th>
                        <th class="px-6 py-3">Reference</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Amount (KES)</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono font-medium text-gray-900">{{ $payment->receipt_number }}</td>
                        <td class="px-6 py-4 font-mono text-gray-600">{{ $payment->invoice_number }}</td>
                        <td class="px-6 py-4">{{ $payment->first_name }} {{ $payment->last_name }}</td>
                        <td class="px-6 py-4">{{ $payment->payment_method }}</td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $payment->transaction_reference ?: '—' }}</td>
                        <td class="px-6 py-4 text-xs text-gray-500">{{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.payments.show', $payment->id) }}"
                               class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                Receipt
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 text-sm">
                            No payments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-white">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
