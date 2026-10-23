<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Admin\PaymentManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentManagementService $paymentService
    ) {
    }

    public function index(Request $request): View
    {
        $query = Payment::query()
            ->with([
                'invoice.client.user',
                'paymentMethod',
                'receiver',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where(
                    'receipt_number',
                    'like',
                    "%{$search}%"
                )->orWhere(
                    'transaction_reference',
                    'like',
                    "%{$search}%"
                )->orWhereHas(
                    'invoice',
                    function ($invoiceQuery) use ($search) {
                        $invoiceQuery->where(
                            'invoice_number',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
            });
        }

        $payments = $query
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact(
            'payments'
        ));
    }

    public function create(): View
    {
        $invoices = Invoice::query()
            ->with('client.user')
            ->whereNotIn('status', ['Cancelled'])
            ->where('balance_amount', '>', 0)
            ->orderByDesc('invoice_date')
            ->get();

        $paymentMethods = PaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.payments.create',
            compact(
                'invoices',
                'paymentMethods'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_id' => [
                'required',
                'integer',
                'exists:invoices,id',
            ],
            'payment_method_id' => [
                'required',
                'integer',
                'exists:payment_methods,id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'transaction_reference' => [
                'nullable',
                'string',
                'max:100',
            ],
            'payment_date' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $payment = $this->paymentService->create(
            invoiceId: (int) $validated['invoice_id'],
            paymentMethodId: (int) $validated['payment_method_id'],
            amount: (float) $validated['amount'],
            paymentDate: $validated['payment_date'],
            transactionReference:
                $validated['transaction_reference'] ?? null,
            notes: $validated['notes'] ?? null,
        );

        return redirect()
            ->route(
                'admin.payments.show',
                $payment->id
            )
            ->with(
                'success',
                'Payment recorded successfully.'
            );
    }

    public function show(int $payment): View
    {
        $payment = Payment::query()
            ->with([
                'invoice.client.user',
                'invoice.items.service',
                'paymentMethod',
                'receiver',
            ])
            ->findOrFail($payment);

        return view(
            'admin.payments.show',
            compact('payment')
        );
    }

    public function destroy(int $payment): RedirectResponse
    {
        $this->paymentService->delete($payment);

        return redirect()
            ->route('admin.payments.index')
            ->with(
                'success',
                'Payment deleted and invoice balance recalculated.'
            );
    }
}
