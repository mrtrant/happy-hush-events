<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Models\Booking;
use App\Models\BookingEvent;
use App\Models\Invoice;
use App\Models\Quotation;

class DashboardService
{
    public function dashboard(string|int $clientId): array
    {
        return [

            'bookingCount' => Booking::where(
                'client_id',
                $clientId
            )->count(),

            'eventCount' => BookingEvent::query()
                ->join(
                    'bookings',
                    'booking_events.booking_id',
                    '=',
                    'bookings.id'
                )
                ->where(
                    'bookings.client_id',
                    $clientId
                )
                ->count(),

            'quotationCount' => Quotation::where(
                'client_id',
                $clientId
            )->count(),

            'outstandingBalance' => (float) Invoice::where(
                'client_id',
                $clientId
            )->sum(
                'balance_amount'
            ),

            'recentBookings' => Booking::where(
                'client_id',
                $clientId
            )
                ->latest()
                ->take(5)
                ->get(),

        ];
    }
}
