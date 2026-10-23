<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ClientProfile;
use App\Models\Invoice;
use App\Models\Service;
use App\Services\Admin\InvoiceManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceManagementService $invoiceService
    ) {
    }

    public function index(Request $request): View
    {
        $query = DB::table('invoices')
            ->join('client_profiles', 'client_profiles.id', '=', 'invoices.client_id')
            ->join('users', 'users.id', '=', 'client_profiles.user_id')
            ->select([
                'invoices.*',
                'client_profiles.client_number',
                'client_profiles.organization_name',
                'users.first_name',
                'users.last_name',
                'users.email',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('invoices.invoice_number', 'like', "%{$search}%")
                    ->orWhere('client_profiles.client_number', 'like', "%{$search}%")
                    ->orWhere('client_profiles.organization_name', 'like', "%{$search}%")
                    ->orWhere('users.first_name', 'like', "%{$search}%")
                    ->orWhere('users.last_name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('invoices.status', $request->string('status')->toString());
        }

        $invoices = $query
            ->orderByDesc('invoices.created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.invoices.index', [
            'invoices' => $invoices,
            'statuses' => $this->invoiceService->statuses(),
        ]);
    }

    public function create(): View
    {
        $clients = ClientProfile::query()
            ->join('users', 'users.id', '=', 'client_profiles.user_id')
            ->whereNull('client_profiles.deleted_at')
            ->whereNull('users.deleted_at')
            ->select([
                'client_profiles.id',
                'client_profiles.client_number',
                'client_profiles.organization_name',
                'users.first_name',
                'users.last_name',
                'users.email',
            ])
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();

        $bookings = Booking::query()
            ->orderByDesc('created_at')
            ->get(['id', 'booking_number', 'client_id', 'status', 'total_amount']);

        $services = Service::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'service_code', 'name', 'base_price']);

        return view('admin.invoices.create', compact('clients', 'bookings', 'services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:client_profiles,id'],
            'booking_id' => ['nullable', 'integer', 'exists:bookings,id'],
            'quotation_id' => ['nullable', 'integer', 'exists:quotations,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice = $this->invoiceService->create($validated);

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice): View
    {
        // Updated with 'client.user' as requested
        $invoice->load([
            'items.service',
            'client.user',
            'booking',
            'quotation',
            'creator',
        ]);

        $services = Service::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'service_code', 'name', 'base_price']);

        return view('admin.invoices.show', [
            'invoice' => $invoice,
            'services' => $services,
            'statuses' => $this->invoiceService->statuses(),
        ]);
    }

    public function edit(Invoice $invoice): View
    {
        $invoice->load('items');

        $clients = ClientProfile::query()
            ->join('users', 'users.id', '=', 'client_profiles.user_id')
            ->whereNull('client_profiles.deleted_at')
            ->whereNull('users.deleted_at')
            ->select([
                'client_profiles.id',
                'client_profiles.client_number',
                'client_profiles.organization_name',
                'users.first_name',
                'users.last_name',
                'users.email',
            ])
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();

        $bookings = Booking::query()
            ->orderByDesc('created_at')
            ->get(['id', 'booking_number', 'client_id', 'status', 'total_amount']);

        return view('admin.invoices.edit', [
            'invoice' => $invoice,
            'clients' => $clients,
            'bookings' => $bookings,
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:client_profiles,id'],
            'booking_id' => ['nullable', 'integer', 'exists:bookings,id'],
            'quotation_id' => ['nullable', 'integer', 'exists:quotations,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->invoiceService->update($invoice->id, $validated);

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function addItem(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->invoiceService->addItem(
            $invoice->id,
            $validated['service_id'] ?? null,
            $validated['description'],
            $validated['quantity'],
            $validated['unit_price']
        );

        return back()->with('success', 'Invoice item added successfully.');
    }

    public function updateItem(Request $request, Invoice $invoice, int $item): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->invoiceService->updateItem(
            $invoice->id,
            $item,
            $validated['service_id'] ?? null,
            $validated['description'],
            $validated['quantity'],
            $validated['unit_price']
        );

        return back()->with('success', 'Invoice item updated successfully.');
    }

    public function removeItem(Invoice $invoice, int $item): RedirectResponse
    {
        $this->invoiceService->removeItem($invoice->id, $item);

        return back()->with('success', 'Invoice item removed successfully.');
    }

    public function changeStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $this->invoiceService->changeStatus($invoice->id, $validated['status']);

        return back()->with('success', 'Invoice status updated successfully.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->delete($invoice->id);

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
