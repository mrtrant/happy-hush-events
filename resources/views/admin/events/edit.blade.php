@extends('layouts.admin')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Update event details and specifications.</p>
        </div>
        <a href="{{ route('admin.events.show', $event->id) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-colors">
            Cancel
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm shadow-sm">
        <div class="font-semibold mb-1">Please fix the following errors:</div>
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
        <form method="POST" action="{{ route('admin.events.update', $event->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Event Name -->
                <div>
                    <label for="event_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Event Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="event_name" name="event_name" maxlength="150" required
                           value="{{ old('event_name', $event->event_name) }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <!-- Event Type -->
                <div>
                    <label for="event_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Event Type <span class="text-red-500">*</span>
                    </label>
                    <select id="event_type_id" name="event_type_id" required
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @foreach($eventTypes as $eventType)
                        <option value="{{ $eventType->id }}"
                            @selected((int) old('event_type_id', $event->event_type_id) === (int) $eventType->id)>
                            {{ $eventType->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Venue -->
                <div>
                    <label for="venue_id" class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                    <select id="venue_id" name="venue_id"
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">No venue assigned</option>
                        @foreach($venues as $venue)
                        <option value="{{ $venue->id }}"
                            @selected((int) old('venue_id', $event->venue_id) === (int) $venue->id)>
                            {{ $venue->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Package -->
                <div>
                    <label for="package_id" class="block text-sm font-medium text-gray-700 mb-1">Package</label>
                    <select id="package_id" name="package_id"
                            class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">No package</option>
                        @foreach($packages as $package)
                        <option value="{{ $package->id }}"
                            @selected((int) old('package_id', $event->package_id) === (int) $package->id)>
                            {{ $package->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Event Date -->
                <div>
                    <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Event Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="event_date" name="event_date" required
                           value="{{ old('event_date', $event->event_date) }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <!-- Start Time -->
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="time" id="start_time" name="start_time"
                           value="{{ old('start_time', $event->start_time) }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <!-- End Time -->
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="time" id="end_time" name="end_time"
                           value="{{ old('end_time', $event->end_time) }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <!-- Guest Count -->
                <div>
                    <label for="guest_count" class="block text-sm font-medium text-gray-700 mb-1">
                        Guest Count <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="guest_count" name="guest_count" min="0" required
                           value="{{ old('guest_count', $event->guest_count ?? 0) }}"
                           class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <!-- Special Requirements -->
                <div class="md:col-span-2">
                    <label for="special_requirements" class="block text-sm font-medium text-gray-700 mb-1">
                        Special Requirements
                    </label>
                    <textarea id="special_requirements" name="special_requirements" rows="6"
                              placeholder="Notes on setup, audio requirements, catering specs, etc..."
                              class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('special_requirements', $event->special_requirements) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.events.show', $event->id) }}"
                   class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium text-sm rounded-lg hover:bg-gray-50 shadow-sm transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
