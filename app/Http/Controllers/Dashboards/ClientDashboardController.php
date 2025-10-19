<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Services\Onboarding\ComputeAndStoreConfidenceService;
use App\Support\OnboardingGoals;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index(Request $request, ComputeAndStoreConfidenceService $svc)
    {
        $user = $request->user();

        $label = OnboardingGoals::primaryGoalLabelForUser($request->user()->id);
        $picked = $label ? [$label] : [];

        $result = $svc->forUser($request->user(), persist: true);

        return view('dashboards.client', [
            'user' => $user,
            'pickedGoals' => $picked,
            'confidence' => $result
        ]);
    }
}
