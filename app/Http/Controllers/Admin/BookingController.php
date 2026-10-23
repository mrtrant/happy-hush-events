<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\BookingManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingManagementService $bookingService
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->input('search')
        );

        $status = $request->input('status');

        $statuses = [
            'Draft',
            'Pending Review',
            'Approved',
            'Rejected',
            'Completed',
            'Cancelled',
        ];

        $bookings = DB::table('bookings')
            ->join(
                'client_profiles',
                'client_profiles.id',
                '=',
                'bookings.client_id'
            )
            ->join(
                'users',
                'users.id',
                '=',
                'client_profiles.user_id'
            )
            ->whereNull('bookings.deleted_at')
            ->select([
                'bookings.id',
                'bookings.booking_number',
                'bookings.status',
                'bookings.quotation_required',
                'bookings.total_amount',
                'bookings.paid_amount',
                'bookings.balance_amount',
                'bookings.created_at',

                'client_profiles.client_number',
                'client_profiles.client_type',

                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.phone',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(function ($query) use (
                        $search
                    ): void {
                        $query
                            ->where(
                                'bookings.booking_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'client_profiles.client_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.first_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.last_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.email',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $status,
                fn ($query) => $query->where(
                    'bookings.status',
                    $status
                )
            )
            ->orderByDesc('bookings.created_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.bookings.index',
            compact(
                'bookings',
                'statuses',
                'search',
                'status'
            )
        );
    }

    public function show(int $booking): View
    {
        $bookingRecord = DB::table('bookings')
            ->join(
                'client_profiles',
                'client_profiles.id',
                '=',
                'bookings.client_id'
            )
            ->join(
                'users',
                'users.id',
                '=',
                'client_profiles.user_id'
            )
            ->where('bookings.id', $booking)
            ->whereNull('bookings.deleted_at')
            ->select([
                'bookings.*',

                'client_profiles.client_number',
                'client_profiles.client_type',
                'client_profiles.organization_name',
                'client_profiles.preferred_contact_method',

                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.phone',
            ])
            ->first();

        abort_unless($bookingRecord, 404);

        $events = DB::table('booking_events')
            ->leftJoin(
                'event_types',
                'event_types.id',
                '=',
                'booking_events.event_type_id'
            )
            ->leftJoin(
                'venues',
                'venues.id',
                '=',
                'booking_events.venue_id'
            )
            ->leftJoin(
                'packages',
                'packages.id',
                '=',
                'booking_events.package_id'
            )
            ->where(
                'booking_events.booking_id',
                $booking
            )
            ->select([
                'booking_events.*',
                'event_types.name as event_type_name',
                'venues.name as venue_name',
                'packages.name as package_name',
            ])
            ->orderBy('booking_events.event_date')
            ->orderBy('booking_events.start_time')
            ->get();

        return view(
            'admin.bookings.show',
            compact(
                'bookingRecord',
                'events'
            )
        );
    }

    public function changeStatus(
        Request $request,
        int $booking
    ): RedirectResponse {
        $request->validate([
            'status' => [
                'required',
                'string',
                'in:Draft,Pending Review,Approved,Rejected,Completed,Cancelled',
            ],
        ]);

        try {
            $this->bookingService->changeStatus(
                $booking,
                $request->string('status')->toString()
            );

            return back()->with(
                'success',
                'Booking status updated successfully.'
            );
        } catch (RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }
    }

    public function updateNotes(
        Request $request,
        int $booking
    ): RedirectResponse {
        $validated = $request->validate([
            'internal_notes' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            $this->bookingService->updateInternalNotes(
                $booking,
                $validated['internal_notes'] ?? null
            );

            return back()->with(
                'success',
                'Internal notes updated successfully.'
            );
        } catch (RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }
    }

    public function destroy(int $booking): RedirectResponse
    {
        try {
            $this->bookingService->delete($booking);

            return redirect()
                ->route('admin.bookings.index')
                ->with(
                    'success',
                    'Booking removed successfully.'
                );
        } catch (RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }
    }
}
