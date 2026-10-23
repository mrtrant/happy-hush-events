<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Models\BookingEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventService
{
    public function getUpcomingEvents(
        int $clientId,
        int $perPage = 10
    ): LengthAwarePaginator {
        return BookingEvent::query()
            ->with([
                'booking',
                'eventType',
                'venue',
                'package',
            ])
            ->whereHas(
                'booking',
                function ($query) use ($clientId): void {
                    $query->where('client_id', $clientId);
                }
            )
            ->whereDate(
                'event_date',
                '>=',
                now()->toDateString()
            )
            ->where(
                'status',
                '!=',
                'Cancelled'
            )
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    public function findEventForClient(
        int $eventId,
        int $clientId
    ): BookingEvent {
        return BookingEvent::query()
            ->with([
                'booking',
                'eventType',
                'venue',
                'package',
                'services.service',
                'timeline' => function ($query): void {
                    $query
                        ->orderBy('completed')
                        ->orderBy('created_at');
                },
            ])
            ->whereKey($eventId)
            ->whereHas(
                'booking',
                function ($query) use ($clientId): void {
                    $query->where('client_id', $clientId);
                }
            )
            ->firstOrFail();
    }
}
