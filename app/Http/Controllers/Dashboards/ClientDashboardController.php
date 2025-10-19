<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Services\Onboarding\ComputeAndStoreConfidenceService;
use App\Services\Onboarding\DebtJourneyService;
use App\Services\Onboarding\FinancialKnowledgeService;
use App\Support\OnboardingGoals;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index(Request $request,
                          ComputeAndStoreConfidenceService $storeConfidenceService,
                          DebtJourneyService $debtJourneyService,
                          FinancialKnowledgeService $financialKnowledgeService,
    )
    {
        $user = $request->user();

        $label = OnboardingGoals::primaryGoalLabelForUser($request->user()->id);
        $picked = $label ? [$label] : [];

        $result = $storeConfidenceService->forUser($request->user(), persist: true);
        $journey = $debtJourneyService->forUser($user);

        activity()
            ->useLog('clients')
            ->performedOn($user)
            ->causedBy($user)
            ->event('clients.debt_journey.view')
            ->withProperties([
                'ip'         => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'status'     => $journey['status'] ?? null,
                'score'      => $journey['score']  ?? null,
            ])->log('Viewed debt journey');

        $financialKnowledge = $financialKnowledgeService->forUser($user);

        return view('dashboards.client', [
            'user' => $user,
            'pickedGoals' => $picked,
            'confidence' => $result,
            'journey' => $journey,
            'financialKnowledge' => $financialKnowledge,
        ]);
    }
}
