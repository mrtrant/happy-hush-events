<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class LoginController extends Controller
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
     * Display login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Authenticate user.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {

            $user = $this->authService->login(

                $request->validated(),

                $request->ip(),

                $request->userAgent()

            );

            return redirect()->to(
                $this->authService->redirectTo($user)
            );

        } catch (Throwable $exception) {

            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => $exception->getMessage(),
                ]);
        }
    }
}
