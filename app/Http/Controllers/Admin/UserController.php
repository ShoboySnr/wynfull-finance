<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\UserAdminService;
use App\Services\Admin\ViewUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{
    public function __construct(
        private readonly UserAdminService $service,
        private readonly ViewUserService $viewService
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

//        dd($coachAssignments);
        return view('admin.users.show', [
            'user'    => $user,
            'coaches' => $coaches,
            'activities' => $activities,
            'lastActiveAt'  => $context['lastActiveAt'],
            'totalSessions' => $context['totalSessions'],
            'assignedCoachIds' => $context['assignedCoachIds'],
            'profile' => $user->profile,
            'coachAssignments' => $coachAssignments
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

