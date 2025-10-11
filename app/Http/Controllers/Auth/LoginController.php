<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email:rfc,dns'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many attempts. Try again in {$seconds} seconds.",
            ]);
        }

        // Fetch user first to check activation early (don’t leak which field failed)
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Auth::validate($credentials)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        if (! $user->is_active) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => __('Your account is pending activation by an administrator.'),
            ]);
        }

        // Passed checks: clear throttle, log them in
        RateLimiter::clear($key);
        Auth::login($user, (bool)($credentials['remember'] ?? false));

        // Spatie activity log
        activity()
            ->useLog('auth')
            ->performedOn($user)
            ->causedBy($user)
            ->event('login')
            ->withProperties([
                'ip'         => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ])
            ->log('User logged in');

        return redirect()->intended($this->redirectPathFor($user));
    }

    public function destroy(Request $request)
    {
        if ($user = $request->user()) {
            activity()
                ->useLog('auth')
                ->performedOn($user)
                ->causedBy($user)
                ->event('logout')
                ->withProperties([
                    'ip'         => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ])
                ->log('User logged out');
        }

        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectPathFor($user): string
    {

        if ($user->hasRole('coach')) {
            return route('coach.dashboard');
        }

        if ($user->hasRole('client')) {
            return route('dashboard.client');
        }

        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }

        return route('home');
    }

    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }
}
