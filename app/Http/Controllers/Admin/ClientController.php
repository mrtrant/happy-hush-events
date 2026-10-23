<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Services\Admin\ClientManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientManagementService $clientService
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $selectedClientType = $request->input('client_type');
        $status = $request->input('status');

        $clients = DB::table('client_profiles')
            ->join(
                'users',
                'users.id',
                '=',
                'client_profiles.user_id'
            )
            ->whereNull('client_profiles.deleted_at')
            ->select([
                'client_profiles.id',
                'client_profiles.client_number',
                'client_profiles.client_type',
                'client_profiles.organization_name',
                'client_profiles.preferred_contact_method',
                'client_profiles.created_at',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.is_active',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where(
                                'client_profiles.client_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.first_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.last_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'users.phone',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'client_profiles.organization_name',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->when(
                $selectedClientType,
                fn ($query) => $query->where(
                    'client_profiles.client_type',
                    $selectedClientType
                )
            )
            ->when(
                $status === 'active',
                fn ($query) => $query->where(
                    'users.is_active',
                    true
                )
            )
            ->when(
                $status === 'inactive',
                fn ($query) => $query->where(
                    'users.is_active',
                    false
                )
            )
            ->orderByDesc('client_profiles.created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'clientTypes' => $this->clientService->clientTypes(),
            'search' => $search,
            'selectedClientType' => $selectedClientType,
        ]);
    }

    public function show(int $id): View
    {
        $client = DB::table('client_profiles')
            ->join('users', 'users.id', '=', 'client_profiles.user_id')
            ->where('client_profiles.id', $id)
            ->whereNull('client_profiles.deleted_at')
            ->select([
                'client_profiles.*',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.is_active',
                'users.last_login_at',
            ])
            ->first();

        abort_if(!$client, 404);

        $bookings = DB::table('bookings')
            ->leftJoin('booking_events', 'bookings.id', '=', 'booking_events.booking_id')
            ->leftJoin('event_types', 'booking_events.event_type_id', '=', 'event_types.id')
            ->where('bookings.client_id', $id)
            ->select([
                'bookings.id',
                'bookings.booking_number',
                'bookings.status',
                'bookings.total_amount',
                'booking_events.event_name',
                'booking_events.event_date',
                'event_types.name AS event_type_name',
            ])
            ->orderByDesc('bookings.created_at')
            ->get();

        return view('admin.clients.show', [
            'client' => $client,
            'bookings' => $bookings,
        ]);
    }

    public function edit(int $id): View
    {
        $client = DB::table('client_profiles')
            ->join('users', 'users.id', '=', 'client_profiles.user_id')
            ->where('client_profiles.id', $id)
            ->whereNull('client_profiles.deleted_at')
            ->select([
                'client_profiles.*',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.is_active',
            ])
            ->first();

        abort_if(!$client, 404);

        return view('admin.clients.edit', [
            'client' => $client,
            'clientTypes' => $this->clientService->clientTypes(),
            'contactMethods' => $this->clientService->contactMethods(),
        ]);
    }

    public function update(UpdateClientRequest $request, int $id): RedirectResponse
    {
        $this->clientService->update($id, $request->validated());

        return redirect()
            ->route('admin.clients.show', $id)
            ->with('success', 'Client updated successfully.');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $this->clientService->toggleStatus($id);

        return back()->with('success', 'Client account status updated successfully.');
    }
}
