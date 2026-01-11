<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            
            $dashboardData = [
                'personalFinanceConfidence' => $personalFinanceConfidence,
                'emergencyReadiness' => $emergencyReadiness,
                'investingHabit' => $investingHabit,
                'confidence' => $confidence,
                'journey' => $journey,
                'financialKnowledge' => $financialKnowledge,
                'wealthCards' => $wealthCards,
                'financialSituations' => $financialSituations,
                'investingStatus' => $investingStatus
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
}

