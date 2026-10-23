<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingManagementService
{
    private const STATUSES = [
        'Draft',
        'Pending Review',
        'Approved',
        'Rejected',
        'Completed',
        'Cancelled',
    ];

    public function changeStatus(
        int $bookingId,
        string $status
    ): void {
        if (!in_array($status, self::STATUSES, true)) {
            throw new RuntimeException(
                'Invalid booking status.'
            );
        }

        DB::transaction(function () use (
            $bookingId,
            $status
        ): void {
            $booking = DB::table('bookings')
                ->where('id', $bookingId)
                ->whereNull('deleted_at')
                ->first();

            if (!$booking) {
                throw new RuntimeException(
                    'Booking not found.'
                );
            }

            $update = [
                'status' => $status,
                'updated_at' => now(),
            ];

            if ($status === 'Approved') {
                $update['approved_by'] = auth()->id();
                $update['approved_at'] = now();
            }

            DB::table('bookings')
                ->where('id', $bookingId)
                ->update($update);
        });
    }

    public function updateInternalNotes(
        int $bookingId,
        ?string $notes
    ): void {
        $updated = DB::table('bookings')
            ->where('id', $bookingId)
            ->whereNull('deleted_at')
            ->update([
                'internal_notes' => $notes,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            throw new RuntimeException(
                'Booking not found.'
            );
        }
    }

    public function delete(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId): void {
            $booking = DB::table('bookings')
                ->where('id', $bookingId)
                ->whereNull('deleted_at')
                ->first();

            if (!$booking) {
                throw new RuntimeException(
                    'Booking not found.'
                );
            }

            /*
             * Preserve the booking and its financial history.
             * Only soft-delete the booking itself.
             */
            DB::table('bookings')
                ->where('id', $bookingId)
                ->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);
        });
    }
}
