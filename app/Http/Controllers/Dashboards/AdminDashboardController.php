<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $authenticatedAdmin = auth()->user();

        if (!$authenticatedAdmin || !$authenticatedAdmin->hasRole('admin')) {
            abort(403, 'Access denied');
        }

        // Cache totals for 5 minutes
        $totals = Cache::remember('admin_dashboard_totals', now()->addMinutes(5), function () {
            return [
                'coaches' => User::role('coach')->count(),
                'clients' => User::role('client')->count(),
                'admins'  => User::role('admin')->count(),
            ];
        });

        // Optional filters
        $logNameFilter  = $request->query('log_name');   // e.g., "security", "auth", "request"
        $eventFilter    = $request->query('event');      // e.g., "login,2fa_enabled"
        $dateFromFilter = $request->query('date_from');  // YYYY-MM-DD
        $dateToFilter   = $request->query('date_to');    // YYYY-MM-DD

        $activitiesQuery = Activity::query()
            ->whereCauserId($authenticatedAdmin->id)
            ->latest();

        if (!empty($logNameFilter)) {
            $activitiesQuery->where('log_name', $logNameFilter);
        }

        if (!empty($eventFilter)) {
            $eventList = collect(explode(',', $eventFilter))
                ->map(fn ($v) => trim($v))
                ->filter()
                ->values()
                ->all();

            if (!empty($eventList)) {
                $activitiesQuery->whereIn('event', $eventList);
            }
        }

        if (!empty($dateFromFilter)) {
            $activitiesQuery->whereDate('created_at', '>=', $dateFromFilter);
        }

        if (!empty($dateToFilter)) {
            $activitiesQuery->whereDate('created_at', '<=', $dateToFilter);
        }

        // Fetch only the latest 6 activities (no pagination)
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

        // Audit the dashboard view
        activity()->useLog('admin')
            ->causedBy($authenticatedAdmin)
            ->event('admin_dashboard_viewed')
            ->withProperties([
                'ip'      => $request->ip(),
                'filters' => [
                    'log_name'  => $logNameFilter,
                    'event'     => $eventFilter,
                    'date_from' => $dateFromFilter,
                    'date_to'   => $dateToFilter,
                ],
            ])->log('Admin viewed API dashboard');

//        dd($totals);
        return view('dashboards.admin', [
            'user'       => $authenticatedAdmin,
            'totals'     => $totals,
            'activities' => $activities,
            'meta'       => [
                'count' => $activities->count(),
                'limit' => 6,
            ],
        ]);
    }

    public function listUsers()
    {

    }
}
