<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function __construct(private readonly ProfileService $profiles) {}
    public function index(Request $request)
    {
        $user = $request->user();
        $profile = $this->profiles->getFor($user);
        return view('client.account.index', ['user' => $user, 'profile' => $profile]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // 1) Validate the current password
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        // 2) Validate the new password
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // (Optional) Prevent reusing the same password
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'New password must be different from your current password.']);
        }

        // 3) Update the password + timestamp in one go
        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ])->save();

         activity()->causedBy($user)->performedOn($user)->withProperties([])->log('password_changed');

        return back()->with('success', 'Password changed successfully!');
    }
}
