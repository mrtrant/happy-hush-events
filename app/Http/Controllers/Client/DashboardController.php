<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\DashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    public function index(): View
    {
        $clientId = Auth::id();

        return view(
            'client.dashboard',
            $this->dashboardService->dashboard($clientId)
        );
    }
}
