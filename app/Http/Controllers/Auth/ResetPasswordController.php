<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPasswordController extends Controller
{
    public function index()
    {

    }

    public function create(Request $request, string $token)
    {
        // If user is already authenticated, redirect to their dashboard
        if (Auth::check()) {
            return redirect($this->redirectPathFor(Auth::user()));
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
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

    public function store(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => bcrypt($password),
                    'password_changed_at' => now(),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
