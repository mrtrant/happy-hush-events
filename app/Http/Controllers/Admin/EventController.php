<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingEventService;
use App\Models\Service;
use App\Services\Admin\EventManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        private readonly EventManagementService $eventManagementService
    ) {
    }

    /**
     * Display all events.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $selectedStatus = $request->input('status');

        $query = DB::table('booking_events')
            ->join(
                'bookings',
                'booking_events.booking_id',
                '=',
                'bookings.id'
            )
            ->join(
                'client_profiles',
                'bookings.client_id',
                '=',
                'client_profiles.id'
            )
            ->join(
                'users',
                'client_profiles.user_id',
                '=',
                'users.id'
            )
            ->join(
                'event_types',
                'booking_events.event_type_id',
                '=',
                'event_types.id'
            )
            ->leftJoin(
                'venues',
                'booking_events.venue_id',
                '=',
                'venues.id'
            )
            ->leftJoin(
                'packages',
                'booking_events.package_id',
                '=',
                'packages.id'
            );

        if (!empty($search)) {
            $searchTerm = trim($search);

            $query->where(function ($q) use ($searchTerm) {
                $q->where(
                    'booking_events.event_name',
                    'like',
                    "%{$searchTerm}%"
                )
                    ->orWhere(
                        'bookings.booking_number',
                        'like',
                        "%{$searchTerm}%"
                    )
                    ->orWhere(
                        'client_profiles.client_number',
                        'like',
                        "%{$searchTerm}%"
                    )
                    ->orWhere(
                        'users.first_name',
                        'like',
                        "%{$searchTerm}%"
                    )
                    ->orWhere(
                        'users.last_name',
                        'like',
                        "%{$searchTerm}%"
                    )
                    ->orWhere(
                        'users.email',
                        'like',
                        "%{$searchTerm}%"
                    );
            });
        }

        if (!empty($selectedStatus)) {
            $query->where(
                'booking_events.status',
                $selectedStatus
            );
        }

        $events = $query
            ->select([
                'booking_events.id',
                'booking_events.uuid',
                'booking_events.booking_id',
                'booking_events.event_name',
                'booking_events.event_date',
                'booking_events.start_time',
                'booking_events.end_time',
                'booking_events.guest_count',
                'booking_events.status',
                'bookings.booking_number',
                'client_profiles.client_number',
                DB::raw(
                    "CONCAT(users.first_name, ' ', users.last_name) AS client_name"
                ),
                'users.email AS client_email',
                'event_types.name AS event_type_name',
                'venues.name AS venue_name',
                'packages.name AS package_name',
            ])
            ->orderByDesc('booking_events.event_date')
            ->orderByDesc('booking_events.id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', [
            'events' => $events,
            'statuses' => $this->eventManagementService->statuses(),
            'search' => $search,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    /**
     * Display a single event.
     */
    public function show(int $event): View
    {
        $eventRecord = DB::table('booking_events')
            ->join(
                'bookings',
                'booking_events.booking_id',
                '=',
                'bookings.id'
            )
            ->join(
                'client_profiles',
                'bookings.client_id',
                '=',
                'client_profiles.id'
            )
            ->join(
                'users',
                'client_profiles.user_id',
                '=',
                'users.id'
            )
            ->join(
                'event_types',
                'booking_events.event_type_id',
                '=',
                'event_types.id'
            )
            ->leftJoin(
                'venues',
                'booking_events.venue_id',
                '=',
                'venues.id'
            )
            ->leftJoin(
                'packages',
                'booking_events.package_id',
                '=',
                'packages.id'
            )
            ->where('booking_events.id', $event)
            ->select([
                'booking_events.*',

                'bookings.booking_number',
                'bookings.status AS booking_status',
                'bookings.total_amount',
                'bookings.paid_amount',
                'bookings.balance_amount',

                'client_profiles.id AS client_id',
                'client_profiles.client_number',

                'users.first_name AS client_first_name',
                'users.last_name AS client_last_name',
                'users.email AS client_email',
                'users.phone AS client_phone',

                'event_types.name AS event_type_name',
                'venues.name AS venue_name',
                'packages.name AS package_name',
            ])
            ->first();

        abort_if(!$eventRecord, 404);

        $eventServices = BookingEventService::query()
            ->with('service')
            ->where('booking_event_id', $event)
            ->orderBy('id')
            ->get();

        $availableServices = Service::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('admin.events.show', [
            'event' => $eventRecord,
            'statuses' => $this->eventManagementService->statuses(),
            'eventServices' => $eventServices,
            'availableServices' => $availableServices,
        ]);
    }

    /**
     * Show event edit page.
     */
    public function edit(int $event): View
    {
        $eventRecord = DB::table('booking_events')
            ->where('booking_events.id', $event)
            ->first();

        abort_if(!$eventRecord, 404);

        $eventTypes = DB::table('event_types')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $venues = DB::table('venues')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $packages = DB::table('packages')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('admin.events.edit', [
            'event' => $eventRecord,
            'eventTypes' => $eventTypes,
            'venues' => $venues,
            'packages' => $packages,
        ]);
    }

    /**
     * Update event.
     */
    public function update(
        Request $request,
        int $event
    ): RedirectResponse {
        $validated = $request->validate([
            'event_type_id' => [
                'required',
                'integer',
                'exists:event_types,id',
            ],
            'venue_id' => [
                'nullable',
                'integer',
                'exists:venues,id',
            ],
            'package_id' => [
                'nullable',
                'integer',
                'exists:packages,id',
            ],
            'event_name' => [
                'required',
                'string',
                'max:150',
            ],
            'event_date' => [
                'required',
                'date',
            ],
            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],
            'end_time' => [
                'nullable',
                'date_format:H:i',
            ],
            'guest_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'special_requirements' => [
                'nullable',
                'string',
            ],
        ]);

        $this->eventManagementService->update(
            $event,
            $validated
        );

        return redirect()
            ->route('admin.events.show', $event)
            ->with(
                'success',
                'Event updated successfully.'
            );
    }

    /**
     * Change event status.
     */
    public function changeStatus(
        Request $request,
        int $event
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:' . implode(
                    ',',
                    $this->eventManagementService->statuses()
                ),
            ],
        ]);

        $this->eventManagementService->changeStatus(
            $event,
            $validated['status']
        );

        return back()->with(
            'success',
            'Event status updated successfully.'
        );
    }

    /**
     * Add a service to an event.
     */
    public function addService(
        Request $request,
        int $event
    ): RedirectResponse {
        $validated = $request->validate([
            'service_id' => [
                'required',
                'integer',
                'exists:services,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'unit_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'source' => [
                'required',
                'in:Package,Custom',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $this->eventManagementService->addService(
            $event,
            (int) $validated['service_id'],
            (int) $validated['quantity'],
            isset($validated['unit_price'])
                ? (float) $validated['unit_price']
                : null,
            $validated['source'],
            $validated['notes'] ?? null
        );

        return back()->with(
            'success',
            'Service added to the event successfully.'
        );
    }

    /**
     * Update an event service.
     */
    public function updateService(
        Request $request,
        int $event,
        int $eventService
    ): RedirectResponse {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $this->eventManagementService->updateService(
            $event,
            $eventService,
            (int) $validated['quantity'],
            (float) $validated['unit_price'],
            $validated['notes'] ?? null
        );

        return back()->with(
            'success',
            'Event service updated successfully.'
        );
    }

    /**
     * Remove an event service.
     */
    public function removeService(
        int $event,
        int $eventService
    ): RedirectResponse {
        $this->eventManagementService->removeService(
            $event,
            $eventService
        );

        return back()->with(
            'success',
            'Service removed from the event successfully.'
        );
    }
}
