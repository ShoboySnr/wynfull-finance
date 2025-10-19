<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Support\OnboardingGoals;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $label = OnboardingGoals::primaryGoalLabelForUser($request->user()->id);
        $picked = $label ? [$label] : [];

        return view('dashboards.client', [
            'user' => $user,
            'pickedGoals' => $picked
        ]);
    }
}
