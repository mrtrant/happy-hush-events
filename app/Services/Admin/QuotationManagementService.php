<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QuotationManagementService
{
    private const STATUSES = [
        'Draft',
        'Sent',
        'Viewed',
        'Approved',
        'Rejected',
        'Expired',
    ];

    public function statuses(): array
    {
        return self::STATUSES;
    }

    public function create(array $data): int
    {
        $client = DB::table('client_profiles')
            ->where('id', $data['client_id'])
            ->whereNull('deleted_at')
            ->first();

        if (!$client) {
            throw ValidationException::withMessages([
                'client_id' => 'The selected client could not be found.',
            ]);
        }

        if (!empty($data['booking_id'])) {
            $booking = DB::table('bookings')
                ->where('id', $data['booking_id'])
                ->where('client_id', $data['client_id'])
                ->whereNull('deleted_at')
                ->first();

            if (!$booking) {
                throw ValidationException::withMessages([
                    'booking_id' => 'The selected booking does not belong to this client.',
                ]);
            }
        }

        return DB::transaction(function () use ($data): int {
            $quotationId = DB::table('quotations')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'quotation_number' => $this->generateQuotationNumber(),
                'client_id' => $data['client_id'],
                'booking_id' => $data['booking_id'] ?? null,
                'status' => 'Draft',
                'valid_until' => $data['valid_until'] ?? null,
                'subtotal' => 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total_amount' => 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->recalculate($quotationId);

            return $quotationId;
        });
    }

    public function update(int $quotationId, array $data): void
    {
        $quotation = $this->getQuotation($quotationId);

        if (!in_array($quotation->status, ['Draft', 'Rejected'], true)) {
            throw ValidationException::withMessages([
                'quotation' => 'Only draft or rejected quotations can be edited.',
            ]);
        }

        if (!empty($data['booking_id'])) {
            $booking = DB::table('bookings')
                ->where('id', $data['booking_id'])
                ->where('client_id', $data['client_id'])
                ->whereNull('deleted_at')
                ->first();

            if (!$booking) {
                throw ValidationException::withMessages([
                    'booking_id' => 'The selected booking does not belong to this client.',
                ]);
            }
        }

        DB::transaction(function () use ($quotationId, $data): void {
            DB::table('quotations')
                ->where('id', $quotationId)
                ->update([
                    'client_id' => $data['client_id'],
                    'booking_id' => $data['booking_id'] ?? null,
                    'valid_until' => $data['valid_until'] ?? null,
                    'discount_amount' => $data['discount_amount'] ?? 0,
                    'tax_amount' => $data['tax_amount'] ?? 0,
                    'notes' => $data['notes'] ?? null,
                    'updated_at' => now(),
                ]);

            $this->recalculate($quotationId);
        });
    }

    public function addItem(
        int $quotationId,
        ?int $serviceId,
        string $description,
        int $quantity,
        float $unitPrice
    ): void {
        $quotation = $this->getQuotation($quotationId);

        $this->ensureEditable($quotation);

        if ($serviceId !== null) {
            $service = DB::table('services')
                ->where('id', $serviceId)
                ->whereNull('deleted_at')
                ->where('is_active', 1)
                ->first();

            if (!$service) {
                throw ValidationException::withMessages([
                    'service_id' => 'The selected service could not be found.',
                ]);
            }
        }

        DB::transaction(function () use (
            $quotationId,
            $serviceId,
            $description,
            $quantity,
            $unitPrice
        ): void {
            DB::table('quotation_items')->insert([
                'quotation_id' => $quotationId,
                'service_id' => $serviceId,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);

            $this->recalculate($quotationId);
        });
    }

    public function updateItem(
        int $quotationId,
        int $itemId,
        string $description,
        int $quantity,
        float $unitPrice
    ): void {
        $quotation = $this->getQuotation($quotationId);

        $this->ensureEditable($quotation);

        $item = DB::table('quotation_items')
            ->where('id', $itemId)
            ->where('quotation_id', $quotationId)
            ->first();

        if (!$item) {
            throw ValidationException::withMessages([
                'item' => 'Quotation item could not be found.',
            ]);
        }

        DB::transaction(function () use (
            $quotationId,
            $itemId,
            $description,
            $quantity,
            $unitPrice
        ): void {
            DB::table('quotation_items')
                ->where('id', $itemId)
                ->where('quotation_id', $quotationId)
                ->update([
                    'description' => $description,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                ]);

            $this->recalculate($quotationId);
        });
    }

    public function removeItem(int $quotationId, int $itemId): void
    {
        $quotation = $this->getQuotation($quotationId);

        $this->ensureEditable($quotation);

        DB::transaction(function () use ($quotationId, $itemId): void {
            DB::table('quotation_items')
                ->where('id', $itemId)
                ->where('quotation_id', $quotationId)
                ->delete();

            $this->recalculate($quotationId);
        });
    }

    public function changeStatus(int $quotationId, string $status): void
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Invalid quotation status.',
            ]);
        }

        $quotation = $this->getQuotation($quotationId);

        $update = [
            'status' => $status,
            'updated_at' => now(),
        ];

        if ($status === 'Approved') {
            $update['approved_by'] = auth()->id();
            $update['approved_at'] = now();
        }

        DB::table('quotations')
            ->where('id', $quotation->id)
            ->update($update);
    }

    public function delete(int $quotationId): void
    {
        $quotation = $this->getQuotation($quotationId);

        if ($quotation->status !== 'Draft') {
            throw ValidationException::withMessages([
                'quotation' => 'Only draft quotations can be deleted.',
            ]);
        }

        DB::transaction(function () use ($quotationId): void {
            DB::table('quotation_items')
                ->where('quotation_id', $quotationId)
                ->delete();

            DB::table('quotations')
                ->where('id', $quotationId)
                ->delete();
        });
    }

    public function recalculate(int $quotationId): void
    {
        $quotation = $this->getQuotation($quotationId);

        $subtotal = (float) DB::table('quotation_items')
            ->where('quotation_id', $quotationId)
            ->sum('total_price');

        $discount = min(
            max((float) $quotation->discount_amount, 0),
            $subtotal
        );

        $tax = max((float) $quotation->tax_amount, 0);

        $total = max(
            $subtotal - $discount + $tax,
            0
        );

        DB::table('quotations')
            ->where('id', $quotationId)
            ->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'updated_at' => now(),
            ]);
    }

    public function getQuotation(int $quotationId): object
    {
        $quotation = DB::table('quotations')
            ->where('id', $quotationId)
            ->first();

        if (!$quotation) {
            abort(404);
        }

        return $quotation;
    }

    private function ensureEditable(object $quotation): void
    {
        if (!in_array($quotation->status, ['Draft', 'Rejected'], true)) {
            throw ValidationException::withMessages([
                'quotation' => 'Only draft or rejected quotations can be modified.',
            ]);
        }
    }

    private function generateQuotationNumber(): string
    {
        do {
            $number = 'HH-Q-' . now()->format('Ym') . '-' .
                strtoupper(Str::random(5));
        } while (
            DB::table('quotations')
                ->where('quotation_number', $number)
                ->exists()
        );

        return $number;
    }
}
