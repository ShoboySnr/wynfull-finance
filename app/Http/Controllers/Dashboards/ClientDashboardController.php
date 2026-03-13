<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\ClientOnboarding;
use App\Services\Onboarding\ComputeAndStoreConfidenceService;
use App\Services\Onboarding\DebtJourneyService;
use App\Services\Onboarding\EmergencyReadinessService;
use App\Services\Onboarding\FinancialKnowledgeService;
use App\Services\Onboarding\InvestingHabitService;
use App\Services\Onboarding\PersonalFinanceConfidenceService;
use App\Services\Onboarding\WealthCardsService;
use App\Support\OnboardingGoals;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ClientDashboardController extends Controller
{
    public function index(Request $request,
                          ComputeAndStoreConfidenceService $storeConfidenceService,
                          DebtJourneyService $debtJourneyService,
                          EmergencyReadinessService $emergencyReadinessService,
                          FinancialKnowledgeService $financialKnowledgeService,
                          InvestingHabitService $investingHabitService,
                          PersonalFinanceConfidenceService $personalFinanceConfidenceService,
                          WealthCardsService $wealthCardsService
    )
    {
        $user = $request->user();

        $personalFinanceConfidence = $personalFinanceConfidenceService->forUser($user);
        $emergencyReadiness = $emergencyReadinessService->forUser($user);
        $investingHabit = $investingHabitService->forUser($user);

//        dd($picked);
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

        // Get all onboarding submissions for historical tracking
        $onboardings = ClientOnboarding::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'asc')
            ->get();

        // Prepare Budget Confidence chart data
        $budgetConfidenceChart = $this->prepareBudgetConfidenceChartData($onboardings);
        
        // Prepare Personal Finance Confidence chart data
        $personalFinanceConfidenceChart = $this->preparePersonalFinanceConfidenceChartData($onboardings);
        
        // Prepare Debt Knowledge Journey chart data
        $debtKnowledgeChart = $this->prepareDebtKnowledgeChartData($onboardings);
        
        // Prepare Investing Knowledge chart data
        $investingKnowledgeChart = $this->prepareInvestingKnowledgeChartData($onboardings);
        
        // Prepare Emergency Readiness chart data
        $emergencyReadinessChart = $this->prepareEmergencyReadinessChartData($onboardings);
        
        // Prepare Investing Habit chart data
        $investingHabitChart = $this->prepareInvestingHabitChartData($onboardings);

        return view('dashboards.client', [
            'user' => $user,
            'personalFinanceConfidence' => $personalFinanceConfidence,
            'emergencyReadiness' => $emergencyReadiness,
            'investingHabit' => $investingHabit,
            'confidence' => $result,
            'journey' => $journey,
            'financialKnowledge' => $financialKnowledge,
            'wealthCards' => $wealthCards,
            'activities' => $activities,
            'financialSituations' => $financialSituations,
            'investingStatus' => $investingStatus,
            'onboardings' => $onboardings,
            'budgetConfidenceChart' => $budgetConfidenceChart,
            'personalFinanceConfidenceChart' => $personalFinanceConfidenceChart,
            'debtKnowledgeChart' => $debtKnowledgeChart,
            'investingKnowledgeChart' => $investingKnowledgeChart,
            'emergencyReadinessChart' => $emergencyReadinessChart,
            'investingHabitChart' => $investingHabitChart,
        ]);
    }

    private function prepareBudgetConfidenceChartData($onboardings)
    {
        $chartData = [];

        // Add first submission as a separate bar if exists
        if ($onboardings->isNotEmpty()) {
            $firstOnboarding = $onboardings->first();
            $firstValue = $this->confidenceToPercentage($firstOnboarding->answers['primary_goal'] ?? '');
            
            $chartData[] = [
                'label' => 'First Submission',
                'value' => $firstValue,
                'backgroundColor' => 'rgba(245, 158, 11, 0.8)', // Gold/amber for first submission
                'borderColor' => 'rgba(245, 158, 11, 1)',
                'isFirst' => true,
                'date' => $firstOnboarding->completed_at->toDateTimeString(),
                'fullDate' => $firstOnboarding->completed_at->format('M d, Y'),
            ];
        }

        // Add all regular submissions (skip first since it's already added)
        foreach ($onboardings as $index => $onboarding) {
            if ($index === 0) continue; // Skip first submission
            
            $chartData[] = [
                'label' => $onboarding->completed_at->format('M d, Y'),
                'value' => $this->confidenceToPercentage($onboarding->answers['primary_goal'] ?? ''),
                'isFirst' => false,
                'date' => $onboarding->completed_at->toDateTimeString(),
            ];
        }

        return $chartData;
    }

    private function preparePersonalFinanceConfidenceChartData($onboardings)
    {
        $chartData = [];

        // Add first submission as a separate bar if exists
        if ($onboardings->isNotEmpty()) {
            $firstOnboarding = $onboardings->first();
            $firstValue = $this->confidenceToPercentage($firstOnboarding->answers['confidence_level'] ?? '');
            
            $chartData[] = [
                'label' => 'First Submission',
                'value' => $firstValue,
                'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                'borderColor' => 'rgba(245, 158, 11, 1)',
                'isFirst' => true,
                'date' => $firstOnboarding->completed_at->toDateTimeString(),
                'fullDate' => $firstOnboarding->completed_at->format('M d, Y'),
            ];
        }

        // Add all regular submissions (skip first since it's already added)
        foreach ($onboardings as $index => $onboarding) {
            if ($index === 0) continue; // Skip first submission
            
            $chartData[] = [
                'label' => $onboarding->completed_at->format('M d, Y'),
                'value' => $this->confidenceToPercentage($onboarding->answers['confidence_level'] ?? ''),
                'isFirst' => false,
                'date' => $onboarding->completed_at->toDateTimeString(),
            ];
        }

        return $chartData;
    }

    private function prepareDebtKnowledgeChartData($onboardings)
    {
        $chartData = [];

        // Add first submission as a separate bar if exists
        if ($onboardings->isNotEmpty()) {
            $firstOnboarding = $onboardings->first();
            $firstDebtLevel = $firstOnboarding->answers['debt_feeling'] ?? '';
            $firstLevelData = $this->debtLevelToData($firstDebtLevel);
            
            $chartData[] = [
                'label' => 'First Submission',
                'value' => $firstLevelData['percentage'],
                'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                'borderColor' => 'rgba(245, 158, 11, 1)',
                'isFirst' => true,
                'date' => $firstOnboarding->completed_at->toDateTimeString(),
                'fullDate' => $firstOnboarding->completed_at->format('M d, Y'),
            ];
        }

        // Add all regular submissions (skip first since it's already added)
        foreach ($onboardings as $index => $onboarding) {
            if ($index === 0) continue; // Skip first submission
            
            $debtLevel = $onboarding->answers['debt_feeling'] ?? '';
            $levelData = $this->debtLevelToData($debtLevel);
            
            $chartData[] = [
                'label' => $onboarding->completed_at->format('M d, Y'),
                'value' => $levelData['percentage'],
                'backgroundColor' => $levelData['backgroundColor'],
                'borderColor' => $levelData['borderColor'],
                'isFirst' => false,
                'date' => $onboarding->completed_at->toDateTimeString(),
            ];
        }

        return $chartData;
    }

    private function debtLevelToData($level)
    {
        return match($level) {
            'no-knowledge' => [
                'percentage' => 25,
                'backgroundColor' => 'rgba(239, 68, 68, 0.8)', // Red
                'borderColor' => 'rgba(239, 68, 68, 1)',
            ],
            'basics-stressful' => [
                'percentage' => 50,
                'backgroundColor' => 'rgba(251, 191, 36, 0.8)', // Yellow
                'borderColor' => 'rgba(251, 191, 36, 1)',
            ],
            'comfortable-applying' => [
                'percentage' => 75,
                'backgroundColor' => 'rgba(59, 130, 246, 0.8)', // Blue
                'borderColor' => 'rgba(59, 130, 246, 1)',
            ],
            'confident-teaching' => [
                'percentage' => 100,
                'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // Green
                'borderColor' => 'rgba(34, 197, 94, 1)',
            ],
            default => [
                'percentage' => 0,
                'backgroundColor' => 'rgba(156, 163, 175, 0.8)', // Gray
                'borderColor' => 'rgba(156, 163, 175, 1)',
            ]
        };
    }

    private function prepareInvestingKnowledgeChartData($onboardings)
    {
        $chartData = [];

        // Add first submission as a separate bar if exists
        if ($onboardings->isNotEmpty()) {
            $firstOnboarding = $onboardings->first();
            $firstInvestingLevel = $firstOnboarding->answers['investing_status'] ?? '';
            $firstLevelData = $this->investingLevelToData($firstInvestingLevel);
            
            $chartData[] = [
                'label' => 'First Submission',
                'value' => $firstLevelData['score'],
                'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                'borderColor' => 'rgba(245, 158, 11, 1)',
                'isFirst' => true,
                'date' => $firstOnboarding->completed_at->toDateTimeString(),
                'fullDate' => $firstOnboarding->completed_at->format('M d, Y'),
            ];
        }

        // Add all regular submissions (skip first since it's already added)
        foreach ($onboardings as $index => $onboarding) {
            if ($index === 0) continue; // Skip first submission
            
            $investingLevel = $onboarding->answers['investing_status'] ?? '';
            $levelData = $this->investingLevelToData($investingLevel);
            
            $chartData[] = [
                'label' => $onboarding->completed_at->format('M d, Y'),
                'value' => $levelData['score'],
                'backgroundColor' => $levelData['backgroundColor'],
                'borderColor' => $levelData['borderColor'],
                'isFirst' => false,
                'date' => $onboarding->completed_at->toDateTimeString(),
            ];
        }

        return $chartData;
    }

    private function investingLevelToData($level)
    {
        return match($level) {
            'not-familiar' => [
                'score' => 1,
                'backgroundColor' => 'rgba(239, 68, 68, 0.8)', // Red
                'borderColor' => 'rgba(239, 68, 68, 1)',
            ],
            'familiar-basics' => [
                'score' => 2,
                'backgroundColor' => 'rgba(251, 191, 36, 0.8)', // Yellow
                'borderColor' => 'rgba(251, 191, 36, 1)',
            ],
            'comfortable-applying' => [
                'score' => 4,
                'backgroundColor' => 'rgba(59, 130, 246, 0.8)', // Blue
                'borderColor' => 'rgba(59, 130, 246, 1)',
            ],
            'advanced-understanding' => [
                'score' => 5,
                'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // Green
                'borderColor' => 'rgba(34, 197, 94, 1)',
            ],
            default => [
                'score' => 0,
                'backgroundColor' => 'rgba(156, 163, 175, 0.8)', // Gray
                'borderColor' => 'rgba(156, 163, 175, 1)',
            ]
        };
    }

    private function prepareEmergencyReadinessChartData($onboardings)
    {
        $chartData = [];

        // Add first submission as a separate bar if exists
        if ($onboardings->isNotEmpty()) {
            $firstOnboarding = $onboardings->first();
            $firstReadinessLevel = $firstOnboarding->answers['savings_amount'] ?? '';
            $firstLevelData = $this->emergencyReadinessLevelToData($firstReadinessLevel);
            
            $chartData[] = [
                'label' => 'First Submission',
                'value' => $firstLevelData['score'],
                'backgroundColor' => 'rgba(245, 158, 11, 0.8)', // Gold/amber for first submission
                'borderColor' => 'rgba(245, 158, 11, 1)',
                'isFirst' => true,
                'date' => $firstOnboarding->completed_at->toDateTimeString(),
                'fullDate' => $firstOnboarding->completed_at->format('M d, Y'),
            ];
        }

        // Add all regular submissions (skip first since it's already added)
        foreach ($onboardings as $index => $onboarding) {
            if ($index === 0) continue; // Skip first submission
            
            $readinessLevel = $onboarding->answers['savings_amount'] ?? '';
            $levelData = $this->emergencyReadinessLevelToData($readinessLevel);
            
            $chartData[] = [
                'label' => $onboarding->completed_at->format('M d, Y'),
                'value' => $levelData['score'],
                'backgroundColor' => $levelData['backgroundColor'],
                'borderColor' => $levelData['borderColor'],
                'isFirst' => false,
                'date' => $onboarding->completed_at->toDateTimeString(),
            ];
        }

        return $chartData;
    }

    private function emergencyReadinessLevelToData($level)
    {
        return match($level) {
            'not-confident' => [
                'score' => 25,
                'backgroundColor' => 'rgba(239, 68, 68, 0.8)', // Red - Not Prepared
                'borderColor' => 'rgba(239, 68, 68, 1)',
            ],
            'somewhat-confident' => [
                'score' => 50,
                'backgroundColor' => 'rgba(251, 191, 36, 0.8)', // Orange/Yellow - Building Readiness
                'borderColor' => 'rgba(251, 191, 36, 1)',
            ],
            'confident' => [
                'score' => 75,
                'backgroundColor' => 'rgba(59, 130, 246, 0.8)', // Blue - Well Prepared
                'borderColor' => 'rgba(59, 130, 246, 1)',
            ],
            'very-confident' => [
                'score' => 100,
                'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // Green - Fully Prepared
                'borderColor' => 'rgba(34, 197, 94, 1)',
            ],
            default => [
                'score' => 25,
                'backgroundColor' => 'rgba(156, 163, 175, 0.8)', // Gray - Not Set
                'borderColor' => 'rgba(156, 163, 175, 1)',
            ]
        };
    }

    private function prepareInvestingHabitChartData($onboardings)
    {
        $chartData = [];

        // Add first submission as a separate bar if exists
        if ($onboardings->isNotEmpty()) {
            $firstOnboarding = $onboardings->first();
            $firstExperience = $firstOnboarding->answers['investing_experience'] ?? '';
            $firstLevelData = $this->investingExperienceToData($firstExperience);
            
            $chartData[] = [
                'label' => 'First Submission',
                'value' => $firstLevelData['percentage'],
                'backgroundColor' => 'rgba(245, 158, 11, 0.8)', // Gold/amber for first submission
                'borderColor' => 'rgba(245, 158, 11, 1)',
                'isFirst' => true,
                'date' => $firstOnboarding->completed_at->toDateTimeString(),
                'fullDate' => $firstOnboarding->completed_at->format('M d, Y'),
            ];
        }

        // Add all regular submissions (skip first since it's already added)
        foreach ($onboardings as $index => $onboarding) {
            if ($index === 0) continue; // Skip first submission
            
            $experience = $onboarding->answers['investing_experience'] ?? '';
            $levelData = $this->investingExperienceToData($experience);
            
            $chartData[] = [
                'label' => $onboarding->completed_at->format('M d, Y'),
                'value' => $levelData['percentage'],
                'backgroundColor' => $levelData['backgroundColor'],
                'borderColor' => $levelData['borderColor'],
                'isFirst' => false,
                'date' => $onboarding->completed_at->toDateTimeString(),
            ];
        }

        return $chartData;
    }

    private function investingExperienceToData($experience)
    {
        return match($experience) {
            'beginner' => [
                'percentage' => 33,
                'backgroundColor' => 'rgba(251, 191, 36, 0.8)', // Yellow/Orange - Building Foundation
                'borderColor' => 'rgba(251, 191, 36, 1)',
            ],
            'intermediate' => [
                'percentage' => 66,
                'backgroundColor' => 'rgba(59, 130, 246, 0.8)', // Blue - Growing Confidence
                'borderColor' => 'rgba(59, 130, 246, 1)',
            ],
            'advanced' => [
                'percentage' => 100,
                'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // Green - Experienced Investor
                'borderColor' => 'rgba(34, 197, 94, 1)',
            ],
            default => [
                'percentage' => 0,
                'backgroundColor' => 'rgba(156, 163, 175, 0.8)', // Gray - Not Set
                'borderColor' => 'rgba(156, 163, 175, 1)',
            ]
        };
    }

    private function confidenceToPercentage($level)
    {
        return match($level) {
            'not-confident' => 25,
            'somewhat-confident' => 50,
            'confident' => 75,
            'very-confident' => 100,
            default => 0
        };
    }
}
