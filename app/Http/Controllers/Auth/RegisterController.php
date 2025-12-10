<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterCoachRequest;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(private readonly RegistrationService $service)
    {
    }

    public function registerClient(RegisterClientRequest $request): RedirectResponse
    {
        $user = $this->service->registerClient($request->validated());

        // Log the user in
        Auth::login($user);

        // Redirect to client dashboard
        return redirect()->route('dashboard.client')->with('success', 'Welcome to Wynfull Finance! Your account has been created successfully.');
    }

    public function registerCoach(RegisterCoachRequest $request): RedirectResponse
    {
        $user = $this->service->registerCoach($request->validated());

        // Log the user in
        Auth::login($user);

        // Redirect to coach dashboard
        return redirect()->route('coach.dashboard')->with('success', 'Welcome to Wynfull Finance! Your coach account has been created successfully.');
    }
}
