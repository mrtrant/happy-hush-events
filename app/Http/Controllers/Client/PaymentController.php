<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::where('client_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('client.payments.index', compact('payments'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:M-Pesa,Card',
            'amount'         => 'required|numeric|min:1|max:' . $invoice->balance_amount,
            'phone_number'   => 'nullable|required_if:payment_method,M-Pesa|string|max:15',
        ]);

        // Generate a unique transaction reference code
        $transactionRef = 'TXN-' . strtoupper(Str::random(8));

        if ($validated['payment_method'] === 'M-Pesa') {
            // TODO: Integrate M-Pesa STK Push API trigger here using $validated['phone_number'] and $validated['amount']
            // For now, we record it as pending or simulate success for testing
        }

        // Record the payment in database
        $payment = Payment::create([
            'uuid'           => (string) Str::uuid(),
            'client_id'      => auth()->id(),
            'invoice_id'     => $invoice->id,
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_code' => $transactionRef,
            'status'         => 'Pending', // Will update to 'Completed' once gateway callback confirms
        ]);

        return redirect()->route('client.invoices.index')
            ->with('success', 'Payment request initiated successfully. Reference: ' . $transactionRef);
    }
}
