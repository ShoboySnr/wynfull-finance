<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Services\Onboarding\ComputeAndStoreConfidenceService;
use App\Services\Onboarding\DebtJourneyService;
use App\Services\Onboarding\FinancialKnowledgeService;
use App\Services\Onboarding\WealthCardsService;
use App\Support\OnboardingGoals;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ClientDashboardController extends Controller
{
    public function index(Request $request,
                          ComputeAndStoreConfidenceService $storeConfidenceService,
                          DebtJourneyService $debtJourneyService,
                          FinancialKnowledgeService $financialKnowledgeService,
                          WealthCardsService $wealthCardsService
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

        $monthlyExpenses = (float) ($user->monthly_expenses ?? 1000);

        $wealthCards = $wealthCardsService->emergencyFundCard($user, $monthlyExpenses);

        activity()
            ->useLog('clients')
            ->performedOn($user)
            ->causedBy($user)
            ->event('clients.card.emergency_fund.view')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'progress' => $card['progress_pct'] ?? null,
                'multiplier' => $card['multiplier'] ?? null,
            ])->log('Viewed Emergency Fund card');

        $financialSituations = OnboardingGoals::financialSituationsForUser($user->id);

        $activities = Activity::query()
            ->where(function ($q) use ($user) {
                $q->where(fn($q) => $q->where('causer_type', get_class($user))->where('causer_id', $user->id))
                    ->orWhere(fn($q) => $q->where('subject_type', get_class($user))->where('subject_id', $user->id));
            })
            ->latest('created_at')
            ->limit(3)
            ->get(['id','description','event','created_at','log_name','properties']);

        $investingStatus = OnboardingGoals::investingStatusForUser($user->id);

        return view('dashboards.client', [
            'user' => $user,
            'pickedGoals' => $picked,
            'confidence' => $result,
            'journey' => $journey,
            'financialKnowledge' => $financialKnowledge,
            'wealthCards' => $wealthCards,
            'activities' => $activities,
            'financialSituations' => $financialSituations,
            'investingStatus' => $investingStatus
        ]);
    }
}
