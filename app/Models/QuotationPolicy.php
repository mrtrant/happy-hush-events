<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ClientProfile;
use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function view(
        User $user,
        Quotation $quotation
    ): bool {
        return ClientProfile::query()
            ->where('id', $quotation->client_id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function approve(
        User $user,
        Quotation $quotation
    ): bool {
        return ClientProfile::query()
            ->where('id', $quotation->client_id)
            ->where('user_id', $user->id)
            ->exists()
            && $quotation->status === 'Sent';
    }

    public function reject(
        User $user,
        Quotation $quotation
    ): bool {
        return ClientProfile::query()
            ->where('id', $quotation->client_id)
            ->where('user_id', $user->id)
            ->exists()
            && $quotation->status === 'Sent';
    }
}
