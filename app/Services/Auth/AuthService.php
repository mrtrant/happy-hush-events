<?php

namespace App\Services\Auth;

use App\Models\LoginHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Maximum failed attempts.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lock duration.
     */
    private const LOCK_MINUTES = 15;

    /**
     * Authenticate a user.
     */
    public function login(array $credentials, string $ip, string $userAgent): User
    {
        $user = User::with('roles')
            ->where('email', $credentials['email'])
            ->first();

        if (!$user) {
            throw new \Exception('Invalid email or password.');
        }

        if (!$user->is_active) {
            throw new \Exception('This account has been disabled.');
        }

        if (
            $user->account_locked_until &&
            $user->account_locked_until->isFuture()
        ) {
            throw new \Exception(
                'Account locked until ' .
                $user->account_locked_until->format('d M Y H:i')
            );
        }

        if (!Hash::check($credentials['password'], $user->password)) {

            $this->failedLogin($user, $ip, $userAgent);

            throw new \Exception('Invalid email or password.');
        }

        DB::transaction(function () use ($user, $ip, $userAgent) {

            $user->update([

                'failed_login_attempts' => 0,

                'account_locked_until' => null,

                'last_login_at' => now(),

            ]);

            LoginHistory::create([

                'user_id' => $user->id,

                'login_time' => now(),

                'ip_address' => $ip,

                'user_agent' => $userAgent,

                'login_status' => 'Success',

            ]);

        });

        Auth::login($user, request()->boolean('remember'));

        return $user;
    }

    /**
     * Handle failed login.
     */
    private function failedLogin(
        User $user,
        string $ip,
        string $userAgent
    ): void {

        $attempts = $user->failed_login_attempts + 1;

        $lockedUntil = null;

        if ($attempts >= self::MAX_ATTEMPTS) {

            $lockedUntil = Carbon::now()
                ->addMinutes(self::LOCK_MINUTES);

            $attempts = 0;
        }

        $user->update([

            'failed_login_attempts' => $attempts,

            'account_locked_until' => $lockedUntil,

        ]);

        LoginHistory::create([

            'user_id' => $user->id,

            'login_time' => now(),

            'ip_address' => $ip,

            'user_agent' => $userAgent,

            'login_status' => 'Failed',

        ]);
    }

    /**
     * Logout.
     */
    public function logout(): void
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();
    }

    /**
     * Determine redirect destination.
     */
    public function redirectTo(User $user): string
    {
        if ($user->hasRole('Admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('Staff')) {
            return route('staff.dashboard');
        }

        return route('client.dashboard');
    }
}
