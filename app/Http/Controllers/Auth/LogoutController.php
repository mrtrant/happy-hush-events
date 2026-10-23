<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    /**
     * Authentication Service.
     */
    protected AuthService $authService;

    /**
     * Constructor.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Logout user.
     */
    public function __invoke(): RedirectResponse
    {
        $user = auth()->user();

        if ($user) {

            LoginHistory::where('user_id', $user->id)
                ->whereNull('logout_time')
                ->latest('login_time')
                ->first()?->update([
                    'logout_time' => now(),
                ]);
        }

        $this->authService->logout();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
