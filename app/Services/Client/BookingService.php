<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Models\Booking;
use App\Models\BookingEvent;
use App\Models\BookingEventService;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BookingService
{
    /**
     * Create a new client booking.
     */
    public function create(array $data): Booking
    {
        return DB::transaction(function () use ($data): Booking {
            $user = auth()->user();

            if ($user === null) {
                throw new RuntimeException(
                    'An authenticated user is required to create a booking.'
                );
            }

            $client = $user->clientProfile;

            if ($client === null) {
                throw new RuntimeException(
                    'The authenticated user does not have a client profile.'
                );
            }

            $package = null;

            if (!empty($data['package_id'])) {
                $package = Package::query()
                    ->where('id', $data['package_id'])
                    ->where('is_active', true)
                    ->first();

                if ($package === null) {
                    throw new RuntimeException(
                        'The selected package is no longer available.'
                    );
                }
            }

            $booking = Booking::query()->create([
                'uuid' => (string) Str::uuid(),
                'booking_number' => $this->generateBookingNumber(),
                'client_id' => $client->id,
                'status' => 'Pending Review',
                'quotation_required' => true,
                'total_amount' => 0,
                'discount_amount' => 0,
                'paid_amount' => 0,
                'balance_amount' => 0,
                'client_notes' => $data['client_notes'] ?? null,
                'created_by' => $user->id,
            ]);

            $event = BookingEvent::query()->create([
                'uuid' => (string) Str::uuid(),
                'booking_id' => $booking->id,
                'event_type_id' => $data['event_type_id'],
                'venue_id' => $data['venue_id'] ?? null,
                'package_id' => $data['package_id'] ?? null,
                'event_name' => $data['event_name'],
                'event_date' => $data['event_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'guest_count' => $data['guest_count'] ?? 0,
                'status' => 'Scheduled',
                'special_requirements' =>
                    $data['special_requirements'] ?? null,
            ]);

            $total = 0;

            if ($package !== null) {
                $packageTotal = $this->addPackageServices(
                    $event,
                    $package
                );

                $total += $packageTotal;
            }

            if (!empty($data['services'])) {
                $serviceTotal = $this->addServices(
                    $event,
                    $data['services']
                );

                $total += $serviceTotal;
            }

            $booking->update([
                'total_amount' => $total,
                'balance_amount' => $total,
            ]);

            return $booking->fresh([
                'events.eventType',
                'events.venue',
                'events.package',
                'events.services.service',
            ]);
        });
    }

    /**
     * Update an existing booking.
     */
    public function update(
        Booking $booking,
        array $data
    ): Booking {
        return DB::transaction(function () use (
            $booking,
            $data
        ): Booking {
            $this->ensureOwnership($booking);

            if (
                !in_array(
                    $booking->status,
                    ['Draft', 'Pending Review'],
                    true
                )
            ) {
                throw new RuntimeException(
                    'This booking can no longer be edited.'
                );
            }

            $event = $booking->events()->first();

            if ($event === null) {
                throw new RuntimeException(
                    'The booking does not contain an event.'
                );
            }

            $package = null;

            if (!empty($data['package_id'])) {
                $package = Package::query()
                    ->where('id', $data['package_id'])
                    ->where('is_active', true)
                    ->first();

                if ($package === null) {
                    throw new RuntimeException(
                        'The selected package is no longer available.'
                    );
                }
            }

            $booking->update([
                'client_notes' => $data['client_notes'] ?? null,
            ]);

            $event->update([
                'event_type_id' => $data['event_type_id'],
                'venue_id' => $data['venue_id'] ?? null,
                'package_id' => $data['package_id'] ?? null,
                'event_name' => $data['event_name'],
                'event_date' => $data['event_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'guest_count' => $data['guest_count'] ?? 0,
                'special_requirements' =>
                    $data['special_requirements'] ?? null,
            ]);

            $event->services()->delete();

            $total = 0;

            if ($package !== null) {
                $total += $this->addPackageServices(
                    $event,
                    $package
                );
            }

            if (!empty($data['services'])) {
                $total += $this->addServices(
                    $event,
                    $data['services']
                );
            }

            $paidAmount = (float) $booking->paid_amount;

            $booking->update([
                'total_amount' => $total,
                'balance_amount' => max(
                    0,
                    $total - $paidAmount
                ),
            ]);

            return $booking->fresh([
                'events.eventType',
                'events.venue',
                'events.package',
                'events.services.service',
            ]);
        });
    }

    /**
     * Delete a client booking.
     */
    public function delete(Booking $booking): void
    {
        DB::transaction(function () use ($booking): void {
            $this->ensureOwnership($booking);

            if (
                !in_array(
                    $booking->status,
                    ['Draft', 'Pending Review'],
                    true
                )
            ) {
                throw new RuntimeException(
                    'This booking can no longer be deleted.'
                );
            }

            $booking->events()->each(
                function (BookingEvent $event): void {
                    $event->services()->delete();
                    $event->delete();
                }
            );

            $booking->delete();
        });
    }

    /**
     * Add services belonging to a package.
     */
    private function addPackageServices(
        BookingEvent $event,
        Package $package
    ): float {
        $total = 0;

        $packageServices = $package->services()
            ->where('services.is_active', true)
            ->get();

        foreach ($packageServices as $service) {
            $quantity = max(
                1,
                (int) (
                    $package->pivot->quantity
                    ?? 1
                )
            );

            $unitPrice = $service->pivot->custom_price !== null
                ? (float) $service->pivot->custom_price
                : (float) $service->base_price;

            $lineTotal = $unitPrice * $quantity;

            BookingEventService::query()->create([
                'booking_event_id' => $event->id,
                'service_id' => $service->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $lineTotal,
                'notes' => 'Included in package: ' . $package->name,
            ]);

            $total += $lineTotal;
        }

        if ($total === 0.0) {
            $total = (float) $package->package_price;
        }

        return $total;
    }

    /**
     * Add individually selected services.
     */
    private function addServices(
        BookingEvent $event,
        array $services
    ): float {
        $total = 0;

        $serviceIds = collect($services)
            ->pluck('service_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($serviceIds->isEmpty()) {
            return 0;
        }

        $availableServices = Service::query()
            ->whereIn('id', $serviceIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        foreach ($services as $serviceData) {
            $serviceId = (int) (
                $serviceData['service_id'] ?? 0
            );

            if (!$availableServices->has($serviceId)) {
                continue;
            }

            $service = $availableServices->get($serviceId);

            $quantity = max(
                1,
                (int) (
                    $serviceData['quantity'] ?? 1
                )
            );

            $unitPrice = (float) $service->base_price;

            $lineTotal = $unitPrice * $quantity;

            BookingEventService::query()->create([
                'booking_event_id' => $event->id,
                'service_id' => $service->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $lineTotal,
                'notes' => $serviceData['notes'] ?? null,
            ]);

            $total += $lineTotal;
        }

        return $total;
    }

    /**
     * Generate a unique booking number.
     */
    private function generateBookingNumber(): string
    {
        do {
            $number = 'HH-' . now()->format('Ym') . '-'
                . strtoupper(Str::random(6));
        } while (
            Booking::query()
                ->where('booking_number', $number)
                ->exists()
        );

        return $number;
    }

    /**
     * Ensure the booking belongs to the authenticated client.
     */
    private function ensureOwnership(Booking $booking): void
    {
        $user = auth()->user();

        if ($user === null) {
            throw new RuntimeException(
                'Authentication is required.'
            );
        }

        $ownsBooking = $booking->client()
            ->where('user_id', $user->id)
            ->exists();

        if (!$ownsBooking) {
            abort(403);
        }
    }
}
