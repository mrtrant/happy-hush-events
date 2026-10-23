<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ClientProfile;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function view(
        User $user,
        Invoice $invoice
    ): bool {
        return ClientProfile::query()
            ->where('id', $invoice->client_id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
