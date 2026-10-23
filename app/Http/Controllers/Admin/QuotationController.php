<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\QuotationManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class QuotationController extends Controller
{
    public function __construct(
        private readonly QuotationManagementService $quotationService
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');

        $query = DB::table('quotations')
            ->join(
                'client_profiles',
                'client_profiles.id',
                '=',
                'quotations.client_id'
            )
            ->join(
                'users',
                'users.id',
                '=',
                'client_profiles.user_id'
            )
            ->leftJoin(
                'bookings',
                'bookings.id',
                '=',
                'quotations.booking_id'
            )
            ->whereNull('client_profiles.deleted_at')
            ->whereNull('users.deleted_at')
            ->select([
                'quotations.*',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'client_profiles.client_number',
                'bookings.booking_number',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $like = '%' . $search . '%';

                $q->where('quotations.quotation_number', 'like', $like)
                    ->orWhere('client_profiles.client_number', 'like', $like)
                    ->orWhere('users.first_name', 'like', $like)
                    ->orWhere('users.last_name', 'like', $like)
                    ->orWhere('users.email', 'like', $like)
                    ->orWhere('bookings.booking_number', 'like', $like);
            });
        }

        if (
            $status !== null &&
            in_array($status, $this->quotationService->statuses(), true)
        ) {
            $query->where('quotations.status', $status);
        }

        $quotations = $query
            ->orderByDesc('quotations.created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.quotations.index', [
            'quotations' => $quotations,
            'statuses' => $this->quotationService->statuses(),
            'search' => $search,
            'selectedStatus' => $status,
        ]);
    }

    public function create(): View
    {
        $clients = DB::table('client_profiles')
            ->join(
                'users',
                'users.id',
                '=',
                'client_profiles.user_id'
            )
            ->whereNull('client_profiles.deleted_at')
            ->whereNull('users.deleted_at')
            ->where('users.is_active', 1)
            ->select([
                'client_profiles.id',
                'client_profiles.client_number',
                'client_profiles.client_type',
                'client_profiles.organization_name',
                'users.first_name',
                'users.last_name',
                'users.email',
            ])
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();

        $services = DB::table('services')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->select([
                'id',
                'service_code',
                'name',
                'base_price',
                'pricing_type',
            ])
            ->orderBy('name')
            ->get();

        return view('admin.quotations.create', [
            'clients' => $clients,
            'services' => $services,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:client_profiles,id'],
            'booking_id' => ['nullable', 'integer'],
            'valid_until' => ['nullable', 'date'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $quotationId = $this->quotationService->create($validated);

            return redirect()
                ->route('admin.quotations.show', $quotationId)
                ->with('success', 'Quotation created successfully.');
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }
    }

    public function show(int $quotation): View
    {
        $quotationData = DB::table('quotations')
            ->join(
                'client_profiles',
                'client_profiles.id',
                '=',
                'quotations.client_id'
            )
            ->join(
                'users',
                'users.id',
                '=',
                'client_profiles.user_id'
            )
            ->leftJoin(
                'bookings',
                'bookings.id',
                '=',
                'quotations.booking_id'
            )
            ->where('quotations.id', $quotation)
            ->select([
                'quotations.*',
                'client_profiles.client_number',
                'client_profiles.client_type',
                'client_profiles.organization_name',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'bookings.booking_number',
            ])
            ->first();

        abort_if(!$quotationData, 404);

        $items = DB::table('quotation_items')
            ->leftJoin(
                'services',
                'services.id',
                '=',
                'quotation_items.service_id'
            )
            ->where('quotation_items.quotation_id', $quotation)
            ->select([
                'quotation_items.*',
                'services.service_code',
                'services.name as service_name',
            ])
            ->orderBy('quotation_items.id')
            ->get();

        $services = DB::table('services')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->select([
                'id',
                'service_code',
                'name',
                'base_price',
                'pricing_type',
            ])
            ->orderBy('name')
            ->get();

        return view('admin.quotations.show', [
            'quotation' => $quotationData,
            'items' => $items,
            'services' => $services,
            'statuses' => $this->quotationService->statuses(),
        ]);
    }

    public function edit(int $quotation): View
    {
        $quotationData = $this->quotationService->getQuotation($quotation);

        if (!in_array($quotationData->status, ['Draft', 'Rejected'], true)) {
            abort(403, 'Only draft or rejected quotations can be edited.');
        }

        $clients = DB::table('client_profiles')
            ->join(
                'users',
                'users.id',
                '=',
                'client_profiles.user_id'
            )
            ->whereNull('client_profiles.deleted_at')
            ->whereNull('users.deleted_at')
            ->select([
                'client_profiles.id',
                'client_profiles.client_number',
                'client_profiles.client_type',
                'client_profiles.organization_name',
                'users.first_name',
                'users.last_name',
                'users.email',
            ])
            ->orderBy('users.first_name')
            ->get();

        $bookings = DB::table('bookings')
            ->where('client_id', $quotationData->client_id)
            ->whereNull('deleted_at')
            ->select([
                'id',
                'booking_number',
                'status',
            ])
            ->orderByDesc('id')
            ->get();

        return view('admin.quotations.edit', [
            'quotation' => $quotationData,
            'clients' => $clients,
            'bookings' => $bookings,
        ]);
    }

    public function update(
        Request $request,
        int $quotation
    ): RedirectResponse {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:client_profiles,id'],
            'booking_id' => ['nullable', 'integer'],
            'valid_until' => ['nullable', 'date'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->quotationService->update($quotation, $validated);

            return redirect()
                ->route('admin.quotations.show', $quotation)
                ->with('success', 'Quotation updated successfully.');
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }
    }

    public function addItem(
        Request $request,
        int $quotation
    ): RedirectResponse {
        $validated = $request->validate([
            'service_id' => ['nullable', 'integer'],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $this->quotationService->addItem(
                $quotation,
                $validated['service_id'] ?? null,
                $validated['description'],
                $validated['quantity'],
                (float) $validated['unit_price']
            );

            return back()->with('success', 'Quotation item added.');
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }
    }

    public function updateItem(
        Request $request,
        int $quotation,
        int $item
    ): RedirectResponse {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $this->quotationService->updateItem(
                $quotation,
                $item,
                $validated['description'],
                $validated['quantity'],
                (float) $validated['unit_price']
            );

            return back()->with('success', 'Quotation item updated.');
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }
    }

    public function removeItem(
        int $quotation,
        int $item
    ): RedirectResponse {
        try {
            $this->quotationService->removeItem($quotation, $item);

            return back()->with('success', 'Quotation item removed.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }
    }

    public function changeStatus(
        Request $request,
        int $quotation
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => ['required', 'string'],
        ]);

        try {
            $this->quotationService->changeStatus(
                $quotation,
                $validated['status']
            );

            return back()->with('success', 'Quotation status updated.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }
    }

    public function destroy(int $quotation): RedirectResponse
    {
        try {
            $this->quotationService->delete($quotation);

            return redirect()
                ->route('admin.quotations.index')
                ->with('success', 'Quotation deleted successfully.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }
    }
}
