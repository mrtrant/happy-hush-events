<?php

namespace App\Services\Admin;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentManagementService
{
    public const INVOICE_STATUSES = [
        'Draft',
        'Sent',
        'Partially Paid',
        'Paid',
        'Overdue',
        'Cancelled',
    ];

    public function create(
        int $invoiceId,
        int $paymentMethodId,
        float $amount,
        string $paymentDate,
        ?string $transactionReference = null,
        ?string $notes = null
    ): Payment {
        return DB::transaction(function () use (
            $invoiceId,
            $paymentMethodId,
            $amount,
            $paymentDate,
            $transactionReference,
            $notes
        ) {
            $invoice = Invoice::query()
                ->lockForUpdate()
                ->find($invoiceId);

            if (!$invoice) {
                throw ValidationException::withMessages([
                    'invoice_id' => 'The selected invoice does not exist.',
                ]);
            }

            if ($invoice->status === 'Cancelled') {
                throw ValidationException::withMessages([
                    'invoice_id' => 'Payments cannot be recorded against a cancelled invoice.',
                ]);
            }

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount must be greater than zero.',
                ]);
            }

            $currentPaid = (float) Payment::query()
                ->where('invoice_id', $invoice->id)
                ->sum('amount');

            $balance = round(
                (float) $invoice->total_amount - $currentPaid,
                2
            );

            if ($balance <= 0) {
                throw ValidationException::withMessages([
                    'invoice_id' => 'This invoice has already been fully paid.',
                ]);
            }

            if ($amount > $balance) {
                throw ValidationException::withMessages([
                    'amount' => sprintf(
                        'Payment cannot exceed the outstanding balance of KES %s.',
                        number_format($balance, 2)
                    ),
                ]);
            }

            $payment = Payment::query()->create([
                'uuid' => (string) Str::uuid(),
                'receipt_number' => $this->generateReceiptNumber(),
                'invoice_id' => $invoice->id,
                'payment_method_id' => $paymentMethodId,
                'amount' => $amount,
                'transaction_reference' => $transactionReference,
                'payment_date' => $paymentDate,
                'notes' => $notes,
                'received_by' => auth()->id(),
            ]);

            $this->recalculateInvoice($invoice->fresh());

            return $payment->fresh();
        });
    }

    public function delete(int $paymentId): void
    {
        DB::transaction(function () use ($paymentId) {
            $payment = Payment::query()
                ->lockForUpdate()
                ->find($paymentId);

            if (!$payment) {
                throw ValidationException::withMessages([
                    'payment' => 'The payment does not exist.',
                ]);
            }

            $invoiceId = $payment->invoice_id;

            $payment->delete();

            $invoice = Invoice::query()
                ->lockForUpdate()
                ->find($invoiceId);

            if ($invoice) {
                $this->recalculateInvoice($invoice);
            }
        });
    }

    public function recalculateInvoice(Invoice $invoice): void
    {
        $paidAmount = (float) Payment::query()
            ->where('invoice_id', $invoice->id)
            ->sum('amount');

        $totalAmount = (float) $invoice->total_amount;

        $paidAmount = round($paidAmount, 2);

        $balanceAmount = round(
            max($totalAmount - $paidAmount, 0),
            2
        );

        if ($paidAmount <= 0) {
            $status = $invoice->status;

            if (in_array(
                $status,
                ['Partially Paid', 'Paid'],
                true
            )) {
                $status = 'Sent';
            }
        } elseif ($balanceAmount <= 0) {
            $paidAmount = $totalAmount;
            $status = 'Paid';
        } else {
            $status = 'Partially Paid';
        }

        $invoice->update([
            'paid_amount' => $paidAmount,
            'balance_amount' => $balanceAmount,
            'status' => $status,
        ]);
    }

    protected function generateReceiptNumber(): string
    {
        do {
            $number = 'HH-R-' . now()->format('Ym') . '-' .
                strtoupper(Str::random(5));
        } while (
            Payment::query()
                ->where('receipt_number', $number)
                ->exists()
        );

        return $number;
    }
}
