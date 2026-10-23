<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\Website\WebsiteService;
use Illuminate\Contracts\View\View;

class ServicesController extends Controller
{
    protected WebsiteService $websiteService;

    public function __construct(WebsiteService $websiteService)
    {
        $this->websiteService = $websiteService;
    }

    public function index(): View
    {
        return view(
            'pages.services',
            [
                'services' => $this->websiteService->services(),
            ]
        );
    }
}
