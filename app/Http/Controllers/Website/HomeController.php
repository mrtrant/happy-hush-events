<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\Website\WebsiteService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Website Service
     */
    protected WebsiteService $websiteService;

    /**
     * Constructor
     */
    public function __construct(WebsiteService $websiteService)
    {
        $this->websiteService = $websiteService;
    }

    /**
     * Homepage
     */
    public function index(): View
    {
        return view(
            'website.home.index',
            $this->websiteService->homepage()
        );
    }
}
