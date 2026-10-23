<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Throwable;

class RegisterController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService,
        protected AuthService $authService
    ) {
    }

    /**
     * Show the registration page.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new client.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        try {

            $user = $this->registrationService
                ->register($request->validated());

            return redirect()->to(
                $this->authService->redirectTo($user)
            );

        } catch (Throwable $exception) {

            return back()
                ->withInput()
                ->withErrors([
                    'registration' => $exception->getMessage(),
                ]);
        }
    }
}
