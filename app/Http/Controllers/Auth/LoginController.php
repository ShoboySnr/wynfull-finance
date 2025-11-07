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
        $base = $request->validate([
            'email'    => ['required', 'email:rfc'],
            'password' => ['required', 'string'],
        ]);

        $request->validate([
            'remember' => ['sometimes', 'boolean'],
        ]);

        $remember = filter_var($request->input('remember', false), FILTER_VALIDATE_BOOLEAN);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many attempts. Try again in {$seconds} seconds.",
            ]);
        }

        $user = User::where('email', $base['email'])->first();

        if (! $user || ! Auth::validate($base)) {
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

        RateLimiter::clear($key);

        Auth::login($user, $remember);

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
