@extends('layouts.admin')

@section('title', 'Event Details')
@section('page-title', $event->event_name)

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Notifications -->
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm shadow-sm flex items-center justify-between">
        <span>{{ session('success') }}</span>
        <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">&times;</button>
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

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 font-mono">Booking #{{ $event->booking_number }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
                Back
            </a>
            <a href="{{ route('admin.events.edit', $event->id) }}"
               class="inline-flex items-center px-4 py-2 border border-blue-600 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors">
                Edit Event
            </a>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column (8 Spans) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Event Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-800">Event Information</h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Event Name</span>
                            <div class="font-semibold text-gray-900">{{ $event->event_name }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Event Type</span>
                            <div class="font-semibold text-gray-900">{{ $event->event_type_name ?? '—' }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Event Date</span>
                            <div class="font-semibold text-gray-900">
                                {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : '—' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Time</span>
                            <div class="font-semibold text-gray-900">
                                @if ($event->start_time || $event->end_time)
                                    {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '—' }}
                                    -
                                    {{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '—' }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Venue</span>
                            <div class="font-semibold text-gray-900">{{ $event->venue_name ?? '—' }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Package</span>
                            <div class="font-semibold text-gray-900">{{ $event->package_name ?? '—' }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Guest Count</span>
                            <div class="font-semibold text-gray-900">{{ number_format($event->guest_count ?? 0) }}</div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Event UUID</span>
                            <div class="text-xs font-mono text-gray-500 break-all">{{ $event->uuid }}</div>
                        </div>

                        @if ($event->special_requirements)
                        <div class="md:col-span-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Special Requirements</span>
                            <div class="border rounded-lg p-3 bg-gray-50 text-gray-700">
                                {!! nl2br(e($event->special_requirements)) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Event Services Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">Event Services</h2>
                        <p class="text-xs text-gray-500">Services assigned to this event</p>
                    </div>
                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $eventServices->count() }}
                    </span>
                </div>

                <div class="p-6 space-y-6">
                    @if ($eventServices->isEmpty())
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500 text-center">
                            No services have been assigned to this event yet.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Service</th>
                                        <th class="px-4 py-3">Source</th>
                                        <th class="px-4 py-3 text-center">Qty</th>
                                        <th class="px-4 py-3 text-right">Unit Price</th>
                                        <th class="px-4 py-3 text-right">Total</th>
                                        <th class="px-4 py-3">Notes</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                                    @foreach ($eventServices as $eventService)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $eventService->service->name ?? 'Unknown Service' }}</div>
                                            @if ($eventService->service)
                                                <div class="text-xs font-mono text-gray-500">{{ $eventService->service->service_code }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($eventService->source === 'Package')
                                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-md bg-indigo-100 text-indigo-800">Package</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-md bg-gray-100 text-gray-800">Custom</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">{{ $eventService->quantity }}</td>
                                        <td class="px-4 py-3 text-right">KES {{ number_format((float) $eventService->unit_price, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900">KES {{ number_format((float) $eventService->total_price, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $eventService->notes ?: '—' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button"
                                                        onclick="document.getElementById('editServiceRow{{ $eventService->id }}').classList.toggle('hidden')"
                                                        class="text-blue-600 hover:text-blue-900 font-medium text-xs">
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('admin.events.services.destroy', [$event->id, $eventService->id]) }}" onsubmit="return confirm('Remove this service from the event?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Inline Edit Row Toggle -->
                                    <tr id="editServiceRow{{ $eventService->id }}" class="hidden bg-gray-50">
                                        <td colspan="7" class="px-4 py-4">
                                            <form method="POST" action="{{ route('admin.events.services.update', [$event->id, $eventService->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                                    <div class="md:col-span-3">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                                                        <input type="number" name="quantity" min="1" value="{{ $eventService->quantity }}" required class="w-full text-xs rounded-lg border-gray-300 border px-2 py-1.5">
                                                    </div>
                                                    <div class="md:col-span-3">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price (KES)</label>
                                                        <input type="number" name="unit_price" min="0" step="0.01" value="{{ $eventService->unit_price }}" required class="w-full text-xs rounded-lg border-gray-300 border px-2 py-1.5">
                                                    </div>
                                                    <div class="md:col-span-4">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                                                        <input type="text" name="notes" value="{{ $eventService->notes }}" class="w-full text-xs rounded-lg border-gray-300 border px-2 py-1.5">
                                                    </div>
                                                    <div class="md:col-span-2 flex gap-2">
                                                        <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700">Save</button>
                                                        <button type="button" onclick="document.getElementById('editServiceRow{{ $eventService->id }}').classList.add('hidden')" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg text-xs font-medium">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 border-t border-gray-200 text-sm">
                                    <tr>
                                        <th colspan="4" class="px-4 py-3 text-right font-medium text-gray-700">Services Total</th>
                                        <th colspan="3" class="px-4 py-3 text-left font-bold text-gray-900">
                                            KES {{ number_format($eventServices->sum('total_price'), 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                    <!-- Add Service Sub-Form -->
                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Add Service</h3>
                        <form method="POST" action="{{ route('admin.events.services.store', $event->id) }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Service <span class="text-red-500">*</span></label>
                                    <select name="service_id" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select service</option>
                                        @foreach ($availableServices as $service)
                                            <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Source <span class="text-red-500">*</span></label>
                                    <select name="source" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="Custom" @selected(old('source', 'Custom') === 'Custom')>Custom</option>
                                        <option value="Package" @selected(old('source') === 'Package')>Package</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                    <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required
                                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price (Opt.)</label>
                                    <input type="number" name="unit_price" min="0" step="0.01" value="{{ old('unit_price') }}" placeholder="Default"
                                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="md:col-span-2">
                                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                                        Add Service
                                    </button>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Optional notes for this service assignment..."
                                       class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (4 Spans - Quick Actions / Status change) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">Event Status</h2>
                <form method="POST" action="{{ route('admin.events.status.update', $event->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <select name="status" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected($event->status === $status)>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-900 shadow-sm transition-colors">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
