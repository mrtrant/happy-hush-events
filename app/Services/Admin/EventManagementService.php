<?php

namespace App\Services\Admin;

use App\Models\BookingEvent;
use App\Models\BookingEventService;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventManagementService
{
    /**
     * Allowed event statuses.
     */
    private const STATUSES = [
        'Scheduled',
        'Confirmed',
        'In Progress',
        'Completed',
        'Cancelled',
    ];

    /**
     * Update event details.
     */
    public function update(int $eventId, array $data): BookingEvent
    {
        return DB::transaction(function () use ($eventId, $data) {
            $event = BookingEvent::query()->find($eventId);

            if (!$event) {
                throw ValidationException::withMessages([
                    'event' => 'The selected event does not exist.',
                ]);
            }

            $event->update([
                'event_type_id' => $data['event_type_id'],
                'venue_id' => $data['venue_id'] ?? null,
                'package_id' => $data['package_id'] ?? null,
                'event_name' => $data['event_name'],
                'event_date' => $data['event_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'guest_count' => $data['guest_count'] ?? 0,
                'special_requirements' => $data['special_requirements'] ?? null,
                'updated_at' => now(),
            ]);

            return $event->fresh();
        });
    }

    /**
     * Change event status.
     */
    public function changeStatus(int $eventId, string $status): BookingEvent
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'The selected event status is invalid.',
            ]);
        }

        return DB::transaction(function () use ($eventId, $status) {
            $event = BookingEvent::query()->find($eventId);

            if (!$event) {
                throw ValidationException::withMessages([
                    'event' => 'The selected event does not exist.',
                ]);
            }

            $event->update([
                'status' => $status,
                'updated_at' => now(),
            ]);

            return $event->fresh();
        });
    }

    /**
     * Return available event statuses.
     */
    public function statuses(): array
    {
        return self::STATUSES;
    }

    /**
     * Add a service to an event.
     */
    public function addService(
        int $eventId,
        int $serviceId,
        int $quantity = 1,
        ?float $unitPrice = null,
        ?string $source = 'Custom',
        ?string $notes = null
    ): BookingEventService {
        return DB::transaction(function () use (
            $eventId,
            $serviceId,
            $quantity,
            $unitPrice,
            $source,
            $notes
        ) {
            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity must be at least 1.',
                ]);
            }

            if (!in_array($source, ['Package', 'Custom'], true)) {
                throw ValidationException::withMessages([
                    'source' => 'The selected service source is invalid.',
                ]);
            }

            $event = BookingEvent::query()->find($eventId);

            if (!$event) {
                throw ValidationException::withMessages([
                    'event' => 'The selected event does not exist.',
                ]);
            }

            $service = Service::query()
                ->whereKey($serviceId)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->first();

            if (!$service) {
                throw ValidationException::withMessages([
                    'service_id' => 'The selected service is not available.',
                ]);
            }

            if ($unitPrice === null) {
                $unitPrice = (float) $service->base_price;
            }

            if ($unitPrice < 0) {
                throw ValidationException::withMessages([
                    'unit_price' => 'Unit price cannot be negative.',
                ]);
            }

            $totalPrice = round($quantity * $unitPrice, 2);

            return BookingEventService::query()->create([
                'booking_event_id' => $eventId,
                'service_id' => $serviceId,
                'source' => $source,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'notes' => $notes,
            ])->load('service');
        });
    }

    /**
     * Update a service assigned to an event.
     *
     * The event ID is deliberately included so that an administrator
     * cannot modify a service belonging to another event by guessing
     * its ID.
     */
    public function updateService(
        int $eventId,
        int $eventServiceId,
        int $quantity,
        float $unitPrice,
        ?string $notes = null
    ): BookingEventService {
        return DB::transaction(function () use (
            $eventId,
            $eventServiceId,
            $quantity,
            $unitPrice,
            $notes
        ) {
            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity must be at least 1.',
                ]);
            }

            if ($unitPrice < 0) {
                throw ValidationException::withMessages([
                    'unit_price' => 'Unit price cannot be negative.',
                ]);
            }

            $event = BookingEvent::query()->find($eventId);

            if (!$event) {
                throw ValidationException::withMessages([
                    'event' => 'The selected event does not exist.',
                ]);
            }

            $eventService = BookingEventService::query()
                ->where('booking_event_id', $eventId)
                ->whereKey($eventServiceId)
                ->first();

            if (!$eventService) {
                throw ValidationException::withMessages([
                    'service' => 'The selected event service does not exist.',
                ]);
            }

            $totalPrice = round($quantity * $unitPrice, 2);

            $eventService->update([
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'notes' => $notes,
            ]);

            return $eventService->fresh(['service']);
        });
    }

    /**
     * Remove a service from an event.
     */
    public function removeService(
        int $eventId,
        int $eventServiceId
    ): void {
        DB::transaction(function () use ($eventId, $eventServiceId) {
            $event = BookingEvent::query()->find($eventId);

            if (!$event) {
                throw ValidationException::withMessages([
                    'event' => 'The selected event does not exist.',
                ]);
            }

            $eventService = BookingEventService::query()
                ->where('booking_event_id', $eventId)
                ->whereKey($eventServiceId)
                ->first();

            if (!$eventService) {
                throw ValidationException::withMessages([
                    'service' => 'The selected event service does not exist.',
                ]);
            }

            $eventService->delete();
        });
    }
}
