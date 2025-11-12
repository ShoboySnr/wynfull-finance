<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function index()
    {

    }

    public function create()
    {
        // If user is already authenticated, redirect to their dashboard
        if (Auth::check()) {
            return redirect($this->redirectPathFor(Auth::user()));
        }

        return view('auth.forgot-password');
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
        $validated = $request->validate([
            'email' => ['required', 'email:rfc'],
        ]);

        $status = Password::sendResetLink(['email' => $validated['email']]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->with('status', __(Lang::get('passwords.sent')));

    }
}
