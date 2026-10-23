<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $client = $this->clientProfile();

        $invoices = Invoice::query()
            ->with([
                'booking',
                'quotation',
            ])
            ->where(
                'client_id',
                $client->id
            )
            ->latest('invoice_date')
            ->paginate(10);

        return view(
            'client.invoices.index',
            compact('invoices')
        );
    }

    public function show(
        Invoice $invoice
    ): View {
        Gate::authorize(
            'view',
            $invoice
        );

        $invoice->load([
            'booking',
            'quotation',
            'items.service',
            'payments.paymentMethod',
        ]);

        return view(
            'client.invoices.show',
            compact('invoice')
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
