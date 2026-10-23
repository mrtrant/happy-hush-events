<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreBookingRequest;
use App\Http\Requests\Client\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\EventType;
use App\Models\Package;
use App\Models\Service;
use App\Models\Venue;
use App\Services\Client\BookingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {
    }

    public function index(): View
    {
        $bookings = Booking::query()
            ->with([
                'events.eventType',
                'events.venue',
            ])
            ->whereHas(
                'client',
                fn ($query) => $query->where(
                    'user_id',
                    auth()->id()
                )
            )
            ->latest()
            ->paginate(10);

        return view(
            'client.bookings.index',
            compact('bookings')
        );
    }

    public function create(): View
    {
        return view(
            'client.bookings.create',
            [
                'eventTypes' => EventType::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),

                'venues' => Venue::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),

                'packages' => Package::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),

                'services' => Service::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),
            ]
        );
    }

    public function store(
        StoreBookingRequest $request
    ): RedirectResponse {
        $booking = $this->bookingService->create(
            $request->validated()
        );

        return redirect()
            ->route(
                'client.bookings.show',
                $booking
            )
            ->with(
                'success',
                'Booking submitted successfully.'
            );
    }

    public function show(
        Booking $booking
    ): View {
        $this->authorizeBooking($booking);

        $booking->load([
            'events.eventType',
            'events.venue',
            'events.package',
            'events.services.service',
            'events.staff.staff',
            'events.inventory.inventoryItem',
            'events.timeline',
            'quotations',
            'invoices',
        ]);

        return view(
            'client.bookings.show',
            compact('booking')
        );
    }

    public function edit(
        Booking $booking
    ): View {
        $this->authorizeBooking($booking);

        abort_unless(
            in_array(
                $booking->status,
                ['Draft', 'Pending Review'],
                true
            ),
            403
        );

        $booking->load('events.services.service');

        return view(
            'client.bookings.edit',
            [
                'booking' => $booking,
                'event' => $booking->events->first(),

                'eventTypes' => EventType::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),

                'venues' => Venue::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),

                'packages' => Package::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),

                'services' => Service::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(),
            ]
        );
    }

    public function update(
        UpdateBookingRequest $request,
        Booking $booking
    ): RedirectResponse {
        $this->authorizeBooking($booking);

        $this->bookingService->update(
            $booking,
            $request->validated()
        );

        return redirect()
            ->route(
                'client.bookings.show',
                $booking
            )
            ->with(
                'success',
                'Booking updated successfully.'
            );
    }

    public function destroy(
        Booking $booking
    ): RedirectResponse {
        $this->authorizeBooking($booking);

        $this->bookingService->delete($booking);

        return redirect()
            ->route('client.bookings.index')
            ->with(
                'success',
                'Booking deleted successfully.'
            );
    }

    private function authorizeBooking(
        Booking $booking
    ): void {
        abort_unless(
            $booking->client()
                ->where('user_id', auth()->id())
                ->exists(),
            403
        );
    }
}
