<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\EventService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService
    ) {
    }

    public function index(): View
    {
        $clientId = (int) Auth::user()
            ->clientProfile
            ->id;

        $events = $this->eventService->getUpcomingEvents(
            $clientId
        );

        return view(
            'client.events.index',
            compact('events')
        );
    }

    public function show(int $event): View
    {
        $clientId = (int) Auth::user()
            ->clientProfile
            ->id;

        $event = $this->eventService->findEventForClient(
            $event,
            $clientId
        );

        return view(
            'client.events.show',
            compact('event')
        );
    }
}
