<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\UserAdminService;
use App\Services\Admin\ViewUserService;
use Illuminate\Http\Request;
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
        [$user, $context] = $this->viewService->getUserWithContext($user->id);

        $coaches = $this->viewService->listAssignableCoaches($user);

        $activitiesQuery = Activity::query()
            ->whereCauserId($user->id)
            ->latest();

        $activities = $activitiesQuery
            ->select(['event', 'description', 'created_at'])
            ->limit(6)
            ->get()
            ->map(function (Activity $activity) {
                return [
                    'event'       => $activity->event,
                    'description' => $activity->description,
                    'time_ago'    => optional($activity->created_at)->diffForHumans(),
                ];
            });

        activity()->useLog('admin')
            ->causedBy($request->user())
            ->event('admin view user details')
            ->withProperties([
                'ip'      => $request->ip()
            ])->log('Admin viewed User profile for ' . $user->name);

        return view('admin.users.show', [
            'user'    => $user,
            'coaches' => $coaches,
            'activities' => $activities,
            'lastActiveAt'  => $context['lastActiveAt'],
            'totalSessions' => $context['totalSessions'],
            'assignedCoachIds' => $context['assignedCoachIds'],
        ]);
    }
}
