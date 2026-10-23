<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the administration dashboard.
     */
    public function index(): View
    {
        $statistics = [
            'clients' => DB::table('client_profiles')
                ->whereNull('deleted_at')
                ->count(),

            'bookings' => DB::table('bookings')
                ->whereNull('deleted_at')
                ->count(),

            'pending_bookings' => DB::table('bookings')
                ->whereNull('deleted_at')
                ->where('status', 'Pending Review')
                ->count(),

            'approved_bookings' => DB::table('bookings')
                ->whereNull('deleted_at')
                ->where('status', 'Approved')
                ->count(),

            'events' => DB::table('booking_events')
                ->count(),

            'upcoming_events' => DB::table('booking_events')
                ->whereDate('event_date', '>=', today())
                ->whereNotIn('status', ['Cancelled', 'Completed'])
                ->count(),

            'quotations' => DB::table('quotations')
                ->count(),

            'pending_quotations' => DB::table('quotations')
                ->whereIn('status', ['Draft', 'Sent', 'Viewed'])
                ->count(),

            'invoices' => DB::table('invoices')
                ->count(),

            'outstanding_invoices' => DB::table('invoices')
                ->whereIn('status', ['Sent', 'Partially Paid', 'Overdue'])
                ->sum('balance_amount'),

            'payments_received' => DB::table('payments')
                ->sum('amount'),

            'services' => DB::table('services')
                ->whereNull('deleted_at')
                ->where('is_active', 1)
                ->count(),

            'staff' => DB::table('staff_profiles')
                ->count(),
        ];

        $recentBookings = DB::table('bookings')
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
            ->select([
                'bookings.id',
                'bookings.booking_number',
                'bookings.status',
                'bookings.total_amount',
                'bookings.created_at',
                'users.first_name',
                'users.last_name',
                'client_profiles.organization_name',
            ])
            ->whereNull('bookings.deleted_at')
            ->orderByDesc('bookings.created_at')
            ->limit(8)
            ->get();

        $upcomingEvents = DB::table('booking_events')
            ->join(
                'bookings',
                'bookings.id',
                '=',
                'booking_events.booking_id'
            )
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
            ->select([
                'booking_events.id',
                'booking_events.event_name',
                'booking_events.event_date',
                'booking_events.start_time',
                'booking_events.end_time',
                'booking_events.guest_count',
                'booking_events.status',
                'bookings.booking_number',
                'event_types.name as event_type_name',
                'venues.name as venue_name',
            ])
            ->whereDate('booking_events.event_date', '>=', today())
            ->whereNotIn('booking_events.status', ['Cancelled', 'Completed'])
            ->orderBy('booking_events.event_date')
            ->orderBy('booking_events.start_time')
            ->limit(8)
            ->get();

        $recentPayments = DB::table('payments')
            ->join(
                'invoices',
                'invoices.id',
                '=',
                'payments.invoice_id'
            )
            ->leftJoin(
                'payment_methods',
                'payment_methods.id',
                '=',
                'payments.payment_method_id'
            )
            ->select([
                'payments.id',
                'payments.receipt_number',
                'payments.amount',
                'payments.payment_date',
                'payments.transaction_reference',
                'invoices.invoice_number',
                'payment_methods.name as payment_method_name',
            ])
            ->orderByDesc('payments.payment_date')
            ->orderByDesc('payments.id')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'statistics' => $statistics,
            'recentBookings' => $recentBookings,
            'upcomingEvents' => $upcomingEvents,
            'recentPayments' => $recentPayments,
        ]);
    }
}
