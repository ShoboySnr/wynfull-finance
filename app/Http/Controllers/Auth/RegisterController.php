<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterCoachRequest;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(private readonly RegistrationService $service) {}

    public function registerClient(RegisterClientRequest $request): RedirectResponse
    {
        $user = $this->service->registerClient($request->validated());

        Auth::login($user);
        return redirect()->route('dashboard.client');
    }

    public function registerCoach(RegisterCoachRequest $request): RedirectResponse
    {
        $user = $this->service->registerCoach($request->validated());

        Auth::login($user);
        return redirect()->route('dashboard.coach');
    }
}
