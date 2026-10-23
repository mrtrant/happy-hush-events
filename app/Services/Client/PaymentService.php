<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentService
{
    public function getPaymentsForClient(
        int $clientId,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Payment::query()
            ->with([
                'invoice',
                'paymentMethod',
            ])
            ->whereHas(
                'invoice',
                function ($query) use ($clientId): void {
                    $query->where(
                        'client_id',
                        $clientId
                    );
                }
            )
            ->latest('payment_date')
            ->latest('id')
            ->paginate($perPage);
    }

    public function findPaymentForClient(
        int $paymentId,
        int $clientId
    ): Payment {
        return Payment::query()
            ->with([
                'invoice.booking',
                'invoice.quotation',
                'paymentMethod',
                'receivedBy',
            ])
            ->whereKey($paymentId)
            ->whereHas(
                'invoice',
                function ($query) use ($clientId): void {
                    $query->where(
                        'client_id',
                        $clientId
                    );
                }
            )
            ->firstOrFail();
    }
}
