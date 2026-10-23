<?php

namespace App\Services\Admin;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceManagementService
{
    private const STATUSES = [
        'Draft',
        'Sent',
        'Partially Paid',
        'Paid',
        'Overdue',
        'Cancelled',
    ];

    /**
     * Create a new invoice.
     */
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'client_id' => $data['client_id'],
                'booking_id' => $data['booking_id'] ?? null,
                'quotation_id' => $data['quotation_id'] ?? null,
                'status' => $data['status'] ?? 'Draft',
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'balance_amount' => 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->recalculate($invoice);

            return $invoice->fresh();
        });
    }

    /**
     * Update invoice header information.
     */
    public function update(int $invoiceId, array $data): Invoice
    {
        return DB::transaction(function () use ($invoiceId, $data) {
            $invoice = $this->findInvoice($invoiceId);

            if ($invoice->status === 'Paid') {
                throw ValidationException::withMessages([
                    'invoice' => 'A paid invoice cannot be edited.',
                ]);
            }

            $invoice->update([
                'client_id' => $data['client_id'],
                'booking_id' => $data['booking_id'] ?? null,
                'quotation_id' => $data['quotation_id'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'] ?? null,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->recalculate($invoice);

            return $invoice->fresh();
        });
    }

    /**
     * Add an item to an invoice.
     */
    public function addItem(
        int $invoiceId,
        ?int $serviceId,
        string $description,
        int $quantity,
        float $unitPrice
    ): InvoiceItem {
        return DB::transaction(function () use (
            $invoiceId,
            $serviceId,
            $description,
            $quantity,
            $unitPrice
        ) {
            $invoice = $this->findInvoice($invoiceId);

            $this->ensureEditable($invoice);

            if ($serviceId !== null) {
                $service = Service::query()
                    ->whereKey($serviceId)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$service) {
                    throw ValidationException::withMessages([
                        'service_id' => 'The selected service is not available.',
                    ]);
                }
            }

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

            $item = InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_id' => $serviceId,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);

            $this->recalculate($invoice);

            return $item->fresh();
        });
    }

    /**
     * Update an existing invoice item.
     */
    public function updateItem(
        int $invoiceId,
        int $itemId,
        ?int $serviceId,
        string $description,
        int $quantity,
        float $unitPrice
    ): InvoiceItem {
        return DB::transaction(function () use (
            $invoiceId,
            $itemId,
            $serviceId,
            $description,
            $quantity,
            $unitPrice
        ) {
            $invoice = $this->findInvoice($invoiceId);

            $this->ensureEditable($invoice);

            $item = InvoiceItem::query()
                ->where('invoice_id', $invoice->id)
                ->whereKey($itemId)
                ->first();

            if (!$item) {
                throw ValidationException::withMessages([
                    'item' => 'Invoice item not found.',
                ]);
            }

            if ($serviceId !== null) {
                $service = Service::query()
                    ->whereKey($serviceId)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$service) {
                    throw ValidationException::withMessages([
                        'service_id' => 'The selected service is not available.',
                    ]);
                }
            }

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

            $item->update([
                'service_id' => $serviceId,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);

            $this->recalculate($invoice);

            return $item->fresh();
        });
    }

    /**
     * Remove an invoice item.
     */
    public function removeItem(int $invoiceId, int $itemId): void
    {
        DB::transaction(function () use ($invoiceId, $itemId) {
            $invoice = $this->findInvoice($invoiceId);

            $this->ensureEditable($invoice);

            $item = InvoiceItem::query()
                ->where('invoice_id', $invoice->id)
                ->whereKey($itemId)
                ->first();

            if (!$item) {
                throw ValidationException::withMessages([
                    'item' => 'Invoice item not found.',
                ]);
            }

            $item->delete();

            $this->recalculate($invoice);
        });
    }

    /**
     * Change invoice status.
     */
    public function changeStatus(int $invoiceId, string $status): Invoice
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Invalid invoice status.',
            ]);
        }

        return DB::transaction(function () use ($invoiceId, $status) {
            $invoice = $this->findInvoice($invoiceId);

            if ($status === 'Paid' && (float) $invoice->balance_amount > 0) {
                throw ValidationException::withMessages([
                    'status' => 'An invoice cannot be marked Paid while it has an outstanding balance.',
                ]);
            }

            if ($status === 'Partially Paid' && (float) $invoice->paid_amount <= 0) {
                throw ValidationException::withMessages([
                    'status' => 'A partially paid invoice must have a payment recorded.',
                ]);
            }

            $invoice->update([
                'status' => $status,
            ]);

            return $invoice->fresh();
        });
    }

    /**
     * Delete a draft invoice.
     */
    public function delete(int $invoiceId): void
    {
        DB::transaction(function () use ($invoiceId) {
            $invoice = $this->findInvoice($invoiceId);

            if ($invoice->status !== 'Draft') {
                throw ValidationException::withMessages([
                    'invoice' => 'Only draft invoices can be deleted.',
                ]);
            }

            InvoiceItem::query()
                ->where('invoice_id', $invoice->id)
                ->delete();

            $invoice->delete();
        });
    }

    /**
     * Recalculate invoice financial values.
     */
    public function recalculate(Invoice $invoice): void
    {
        $subtotal = (float) InvoiceItem::query()
            ->where('invoice_id', $invoice->id)
            ->sum('total_price');

        $discount = max(0, (float) $invoice->discount_amount);
        $tax = max(0, (float) $invoice->tax_amount);

        $total = max(0, $subtotal - $discount + $tax);

        $paid = max(0, (float) $invoice->paid_amount);

        $balance = max(0, $total - $paid);

        $invoice->update([
            'subtotal' => $subtotal,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'balance_amount' => $balance,
        ]);
    }

    /**
     * Return available invoice statuses.
     */
    public function statuses(): array
    {
        return self::STATUSES;
    }

    /**
     * Find invoice.
     */
    private function findInvoice(int $invoiceId): Invoice
    {
        $invoice = Invoice::query()->find($invoiceId);

        if (!$invoice) {
            throw ValidationException::withMessages([
                'invoice' => 'Invoice not found.',
            ]);
        }

        return $invoice;
    }

    /**
     * Ensure invoice can be modified.
     */
    private function ensureEditable(Invoice $invoice): void
    {
        if (in_array($invoice->status, ['Paid', 'Cancelled'], true)) {
            throw ValidationException::withMessages([
                'invoice' => 'This invoice cannot be modified in its current status.',
            ]);
        }
    }

    /**
     * Generate unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        do {
            $number = 'HH-I-' . now()->format('Ym') . '-' .
                strtoupper(\Illuminate\Support\Str::random(5));
        } while (
            Invoice::query()
                ->where('invoice_number', $number)
                ->exists()
        );

        return $number;
    }
}
