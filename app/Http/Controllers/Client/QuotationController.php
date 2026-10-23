<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class QuotationController extends Controller
{
    public function index(): View
    {
        $client = $this->clientProfile();

        $quotations = Quotation::query()
            ->with([
                'booking',
            ])
            ->where('client_id', $client->id)
            ->latest()
            ->paginate(10);

        return view(
            'client.quotations.index',
            compact('quotations')
        );
    }

    public function show(
        Quotation $quotation
    ): View {
        Gate::authorize(
            'view',
            $quotation
        );

        $quotation->load([
            'booking',
            'items.service',
            'invoice',
        ]);

        return view(
            'client.quotations.show',
            compact('quotation')
        );
    }

    public function approve(
        Quotation $quotation
    ): RedirectResponse {
        Gate::authorize(
            'approve',
            $quotation
        );

        $quotation->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route(
                'client.quotations.show',
                $quotation
            )
            ->with(
                'success',
                'Quotation approved successfully.'
            );
    }

    public function reject(
        Quotation $quotation
    ): RedirectResponse {
        Gate::authorize(
            'reject',
            $quotation
        );

        $quotation->update([
            'status' => 'Rejected',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()
            ->route(
                'client.quotations.show',
                $quotation
            )
            ->with(
                'success',
                'Quotation rejected.'
            );
    }

    private function clientProfile(): ClientProfile
    {
        return ClientProfile::query()
            ->where(
                'user_id',
                Auth::id()
            )
            ->firstOrFail();
    }
}
