<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientOnboarding;
use App\Models\User;
use App\Notifications\NewUserPendingActivation;
use App\Notifications\YourAccountActivatedNotification;
use App\Services\Admin\UserAdminService;
use App\Services\Admin\ViewUserService;
use App\Services\Onboarding\ComputeAndStoreConfidenceService;
use App\Services\Onboarding\DebtJourneyService;
use App\Services\Onboarding\EmergencyReadinessService;
use App\Services\Onboarding\FinancialKnowledgeService;
use App\Services\Onboarding\InvestingHabitService;
use App\Services\Onboarding\PersonalFinanceConfidenceService;
use App\Services\Onboarding\WealthCardsService;
use App\Support\OnboardingGoals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{
    public function __construct(
        private readonly UserAdminService $service,
        private readonly ViewUserService $viewService,
        private readonly ComputeAndStoreConfidenceService $storeConfidenceService,
        private readonly DebtJourneyService $debtJourneyService,
        private readonly EmergencyReadinessService $emergencyReadinessService,
        private readonly FinancialKnowledgeService $financialKnowledgeService,
        private readonly InvestingHabitService $investingHabitService,
        private readonly PersonalFinanceConfidenceService $personalFinanceConfidenceService,
        private readonly WealthCardsService $wealthCardsService
    )
    {
    }

    public function index(Request $request)
    {
        $stats = $this->service->getStats();

        $filters = [
            'q'        => trim((string) $request->query('q', '')),
            'role'     => trim((string) $request->query('role', '')),
            'status'   => trim((string) $request->query('status', '')),
            'per_page' => (int) $request->query('per_page', 15),
            'sort'     => trim((string) $request->query('sort', '')),
            'dir'      => trim((string) $request->query('dir', '')),
        ];

        $users = $this->service->listUsers($filters);

        return view('admin.users.index', [
            'totalUsers'    => $stats['totalUsers'],
            'activeClients' => $stats['activeClients'],
            'totalCoaches'  => $stats['totalCoaches'],
            'users'         => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:client,coach,admin',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput([
                'form_type' => 'add_user'
            ]);
        }

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->boolean('is_active', false),
                'activated_at' => $request->boolean('is_active') ? now() : null,
                'activated_by_id' => $request->boolean('is_active') ? $request->user()->id : null,
                'email_verified_at' => now(), // Auto-verify admin-created users
            ]);

            // Assign role
            $user->assignRole($request->role);

            // Create profile based on role
            if ($request->role === 'client') {
                $user->clientProfile()->create([]);
            } elseif ($request->role === 'coach') {
                $user->coachProfile()->create([]);
            }

            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy($request->user())
                ->withProperties([
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_role' => $request->role,
                    'is_active' => $user->is_active,
                ])
                ->log('user_created');

            return $user;
        });

        // Send appropriate notification based on activation status
        if ($user->is_active) {
            // User is activated - send activation email with password reset link
            $token = Password::createToken($user);
            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);

            $user->notify(new YourAccountActivatedNotification($resetUrl));

            activity()->causedBy($request->user())
                ->performedOn($user)
                ->withProperties(['notification' => 'YourAccountActivatedNotification'])
                ->log('notification_dispatched');

            $message = 'User created and activated successfully! Activation email sent.';
        } else {
            // User is pending activation - send pending activation email
            $user->notify(new NewUserPendingActivation($user));

            activity()->causedBy($request->user())
                ->performedOn($user)
                ->withProperties(['notification' => 'NewUserPendingActivation'])
                ->log('notification_dispatched');

            $message = 'User created successfully! Pending activation email sent.';
        }

        return redirect()->route('admin.users')
            ->with('success', $message);
    }

    public function show(User $user, Request $request)
    {
        $user = $user->load('profile');
        [$user, $context] = $this->viewService->getUserWithContext($user->id);

        $coaches = $this->viewService->listAssignableCoaches($user);

        $coachAssignments = $user->coachAssignmentsAsClient()
            ->with([
                'coach:id,name,email,is_active',              // coach basic
                'coach.profile:id,user_id,avatar_path,first_name,last_name,professional_title',
                'assignedBy:id,name,email',         // who assigned
                'assignedBy.profile:id,user_id,avatar_path,first_name,last_name,professional_title',
            ])
            ->latest('assigned_at')
            ->get();

        $activities = Activity::query()
            ->whereCauserId($user->id)
            ->latest()
            ->select(['event','description','created_at'])
            ->limit(6)
            ->get()
            ->map(fn(Activity $a) => [
                'event'       => $a->event,
                'description' => $a->description,
                'time_ago'    => optional($a->created_at)->diffForHumans(),
            ]);

        activity()->useLog('admin')
            ->causedBy($request->user())
            ->event('admin view user details')
            ->withProperties([
                'ip'      => $request->ip()
            ])->log('Admin viewed User profile for ' . $user->name);

        // Fetch client dashboard data if user is a client
        $dashboardData = null;
        if ($user->hasRole('client')) {
            $personalFinanceConfidence = $this->personalFinanceConfidenceService->forUser($user);
            $emergencyReadiness = $this->emergencyReadinessService->forUser($user);
            $investingHabit = $this->investingHabitService->forUser($user);
            $confidence = $this->storeConfidenceService->forUser($user, persist: false);
            $journey = $this->debtJourneyService->forUser($user);
            $financialKnowledge = $this->financialKnowledgeService->forUser($user);
            
            $monthlyExpenses = (float) ($user->monthly_expenses ?? 1000);
            $wealthCards = $this->wealthCardsService->emergencyFundCard($user, $monthlyExpenses);
            
            $financialSituations = OnboardingGoals::financialSituationsForUser($user->id);
            $investingStatus = OnboardingGoals::investingStatusForUser($user->id);
            
            // Get all onboarding submissions for historical tracking
            $onboardings = ClientOnboarding::where('user_id', $user->id)
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
            
            $dashboardData = [
                'personalFinanceConfidence' => $personalFinanceConfidence,
                'emergencyReadiness' => $emergencyReadiness,
                'investingHabit' => $investingHabit,
                'confidence' => $confidence,
                'journey' => $journey,
                'financialKnowledge' => $financialKnowledge,
                'wealthCards' => $wealthCards,
                'financialSituations' => $financialSituations,
                'investingStatus' => $investingStatus,
                'onboardings' => $onboardings,
                'budgetConfidenceChart' => $budgetConfidenceChart,
                'personalFinanceConfidenceChart' => $personalFinanceConfidenceChart,
                'debtKnowledgeChart' => $debtKnowledgeChart,
                'investingKnowledgeChart' => $investingKnowledgeChart,
                'emergencyReadinessChart' => $emergencyReadinessChart,
                'investingHabitChart' => $investingHabitChart,
            ];
        }

        return view('admin.users.show', [
            'user'    => $user,
            'coaches' => $coaches,
            'activities' => $activities,
            'lastActiveAt'  => $context['lastActiveAt'],
            'totalSessions' => $context['totalSessions'],
            'assignedCoachIds' => $context['assignedCoachIds'],
            'profile' => $user->profile,
            'coachAssignments' => $coachAssignments,
            'dashboardData' => $dashboardData
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        // Only admins can delete users
        abort_unless($request->user()?->hasRole('admin'), 403);

        // Prevent admin from deleting themselves
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting other admins (optional safety measure)
        if ($user->hasRole('admin')) {
            return back()->with('error', 'Admin accounts cannot be deleted for security reasons.');
        }

        DB::transaction(function () use ($request, $user) {
            // Log the deletion activity before deleting
            activity()
                ->performedOn($user)
                ->causedBy($request->user())
                ->withProperties([
                    'deleted_user_id' => $user->id,
                    'deleted_user_name' => $user->name,
                    'deleted_user_email' => $user->email,
                    'deleted_user_roles' => $user->getRoleNames()->toArray()
                ])
                ->log('user_deleted');

            // Delete related data
            $this->deleteUserRelatedData($user);

            // Delete the user
            $user->delete();
        });

        return redirect()->route('admin.users')
            ->with('success', "User '{$user->name}' has been permanently deleted.");
    }

    /**
     * Delete user-related data to maintain referential integrity
     */
    private function deleteUserRelatedData(User $user): void
    {
        // Delete user sessions
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Delete coach-client assignments where user is involved
//        DB::table('coach_client_assignments')
//            ->where('coach_id', $user->id)
//            ->orWhere('client_id', $user->id)
//            ->delete();
//
//        // Delete messages sent by this user
//        DB::table('messages')->where('sender_id', $user->id)->delete();

        DB::table('coach_client_assignments')
            ->where('coach_id', $user->id)
            ->orWhere('client_id', $user->id)
            ->delete();

        // Delete messages sent by this user
        DB::table('messages')->where('sender_id', $user->id)->delete();

        // Delete module completions
//        DB::table('module_completions')->where('user_id', $user->id)->delete();

        // Delete user profile (if exists)
        if ($user->coachProfile) {
            $user->coachProfile->delete();
        }

        if ($user->clientProfile) {
            $user->clientProfile->delete();
        }

        // Delete password reset tokens
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Note: Activity logs are kept for audit trail purposes
        // They will show as "deleted user" but maintain the log integrity
    }

    public function updatePassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput([
                'form_type' => 'change_password',
                'user_id' => $user->id
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully for ' . $user->name);
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
                'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                'borderColor' => 'rgba(239, 68, 68, 1)',
            ],
            'basics-stressful' => [
                'percentage' => 50,
                'backgroundColor' => 'rgba(251, 191, 36, 0.8)',
                'borderColor' => 'rgba(251, 191, 36, 1)',
            ],
            'comfortable-applying' => [
                'percentage' => 75,
                'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                'borderColor' => 'rgba(59, 130, 246, 1)',
            ],
            'confident-teaching' => [
                'percentage' => 100,
                'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                'borderColor' => 'rgba(34, 197, 94, 1)',
            ],
            default => [
                'percentage' => 0,
                'backgroundColor' => 'rgba(156, 163, 175, 0.8)',
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
                'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                'borderColor' => 'rgba(239, 68, 68, 1)',
            ],
            'familiar-basics' => [
                'score' => 2,
                'backgroundColor' => 'rgba(251, 191, 36, 0.8)',
                'borderColor' => 'rgba(251, 191, 36, 1)',
            ],
            'comfortable-applying' => [
                'score' => 4,
                'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                'borderColor' => 'rgba(59, 130, 246, 1)',
            ],
            'advanced-understanding' => [
                'score' => 5,
                'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                'borderColor' => 'rgba(34, 197, 94, 1)',
            ],
            default => [
                'score' => 0,
                'backgroundColor' => 'rgba(156, 163, 175, 0.8)',
                'borderColor' => 'rgba(156, 163, 175, 1)',
            ],
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
                'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                'borderColor' => 'rgba(239, 68, 68, 1)',
            ],
            'somewhat-confident' => [
                'score' => 50,
                'backgroundColor' => 'rgba(251, 191, 36, 0.8)',
                'borderColor' => 'rgba(251, 191, 36, 1)',
            ],
            'confident' => [
                'score' => 75,
                'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                'borderColor' => 'rgba(59, 130, 246, 1)',
            ],
            'very-confident' => [
                'score' => 100,
                'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                'borderColor' => 'rgba(34, 197, 94, 1)',
            ],
            default => [
                'score' => 25,
                'backgroundColor' => 'rgba(156, 163, 175, 0.8)',
                'borderColor' => 'rgba(156, 163, 175, 1)',
            ],
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
}

