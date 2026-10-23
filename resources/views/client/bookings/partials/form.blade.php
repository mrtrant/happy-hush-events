@csrf

{{-- Event Details Card --}}
<div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="fa-solid fa-calendar-days me-2" style="color: var(--brand-orange);"></i> Event Details
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Event Name *</label>
                <input type="text" name="event_name" class="form-control" value="{{ old('event_name', $event?->event_name) }}" maxlength="150" required placeholder="e.g. Sarah & Michael Wedding">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Expected Guests *</label>
                <input type="number" name="guest_count" class="form-control" min="1" value="{{ old('guest_count', $event?->guest_count ?? 1) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Event Type *</label>
                <select name="event_type_id" class="form-select" required>
                    <option value="">Select Event Type</option>
                    @foreach($eventTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('event_type_id', $event?->event_type_id) == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Venue</label>
                <select name="venue_id" class="form-select">
                    <option value="">Select Venue (Optional)</option>
                    @foreach($venues as $venue)
                        <option value="{{ $venue->id }}" @selected(old('venue_id', $event?->venue_id) == $venue->id)>
                            {{ $venue->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Package Selection</label>
                <select name="package_id" class="form-select" id="package_id">
                    <option value="">No Package — Select Individual Services Only</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" @selected(old('package_id', $event?->package_id) == $package->id)>
                            {{ $package->name }} — KES {{ number_format($package->package_price, 2) }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted mt-1 d-block">Selecting a package automatically includes its pre-configured services.</small>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Event Date *</label>
                <input type="date" name="event_date" class="form-control" min="{{ now()->format('Y-m-d') }}" value="{{ old('event_date', $event?->event_date?->format('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Start Time</label>
                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $event?->start_time?->format('H:i')) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">End Time</label>
                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $event?->end_time?->format('H:i')) }}">
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Special Requirements</label>
                <textarea name="special_requirements" rows="3" class="form-control" maxlength="5000" placeholder="Any special requests or details about your setup...">{{ old('special_requirements', $event?->special_requirements) }}</textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Client Notes</label>
                <textarea name="client_notes" rows="3" class="form-control" maxlength="5000" placeholder="General booking notes for Happy Hush team...">{{ old('client_notes', $booking?->client_notes) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Additional Services Card --}}
<div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="mb-1 fw-bold text-dark">
            <i class="fa-solid fa-puzzle-piece me-2" style="color: var(--brand-orange);"></i> Additional Services
        </h5>
        <small class="text-muted">Select additional individual services to customize your event.</small>
    </div>
    <div class="card-body p-4">
        @php
            $oldServices = old('services', []);
        @endphp

        <div id="service-list">
            @if(count($oldServices) > 0)
                @foreach($oldServices as $index => $selectedService)
                    <div class="row service-row g-3 mb-3 align-items-center bg-light p-3 rounded-3 position-relative">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Service</label>
                            <select name="services[{{ $index }}][service_id]" class="form-select">
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" @selected(($selectedService['service_id'] ?? null) == $service->id)>
                                        {{ $service->name }} — KES {{ number_format($service->base_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Quantity</label>
                            <input type="number" name="services[{{ $index }}][quantity]" class="form-control" min="1" value="{{ $selectedService['quantity'] ?? 1 }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Notes</label>
                            <input type="text" name="services[{{ $index }}][notes]" class="form-control" placeholder="Optional notes" value="{{ $selectedService['notes'] ?? '' }}">
                        </div>
                        <div class="col-md-1 text-end pt-4">
                            <button type="button" class="btn btn-outline-danger remove-service w-100 mt-1">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="row service-row g-3 mb-3 align-items-center bg-light p-3 rounded-3 position-relative">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Service</label>
                        <select name="services[0][service_id]" class="form-select">
                            <option value="">Select Service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">
                                    {{ $service->name }} — KES {{ number_format($service->base_price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Quantity</label>
                        <input type="number" name="services[0][quantity]" class="form-control" min="1" value="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Notes</label>
                        <input type="text" name="services[0][notes]" class="form-control" placeholder="Optional notes">
                    </div>
                    <div class="col-md-1 text-end pt-4">
                        <button type="button" class="btn btn-outline-danger remove-service w-100 mt-1">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <button type="button" class="btn btn-outline-brand mt-2 fw-semibold px-3" id="add-service">
            <i class="fa-solid fa-plus me-1"></i> Add Another Service
        </button>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const serviceList = document.getElementById('service-list');
                const addButton = document.getElementById('add-service');
                let serviceIndex = serviceList.querySelectorAll('.service-row').length;

                addButton.addEventListener('click', function () {
                    const firstRow = serviceList.querySelector('.service-row');
                    if (!firstRow) return;

                    const row = firstRow.cloneNode(true);

                    row.querySelectorAll('select, input').forEach(function (field) {
                        field.value = '';
                        const name = field.getAttribute('name');
                        if (!name) return;

                        field.setAttribute(
                            'name',
                            name.replace(/\[\d+\]/, '[' + serviceIndex + ']')
                        );
                    });

                    const quantity = row.querySelector('input[type="number"]');
                    if (quantity) {
                        quantity.value = 1;
                    }

                    serviceList.appendChild(row);
                    serviceIndex++;
                });

                serviceList.addEventListener('click', function (event) {
                    const removeBtn = event.target.closest('.remove-service');
                    if (!removeBtn) return;

                    const rows = serviceList.querySelectorAll('.service-row');
                    if (rows.length <= 1) {
                        return; // Keep at least one row
                    }

                    removeBtn.closest('.service-row').remove();
                });
            });
        </script>
    @endpush
@endonce
