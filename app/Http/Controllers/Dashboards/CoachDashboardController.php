<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\CoachDashboardService;
use Illuminate\Http\Request;

class CoachDashboardController extends Controller
{
    public function __construct(private readonly CoachDashboardService $service)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user()->loadMissing('coachProfile');

        // Pull specialties from coach profile or fallback to a column on users table
        $specialties = data_get($user, 'coachProfile.specialties', $user->specialties ?? []);

        // Normalize to array then CSV
        if (is_string($specialties)) {
            $specialties = array_filter(array_map('trim', explode(',', $specialties)));
        } elseif (!is_array($specialties)) {
            $specialties = [];
        }
        $specialtiesCsv = implode(', ', $specialties);

        // Dashboard stats & data
        $coachId = (int) $user->id;
        $activeClientsCount    = $this->service->activeClientsCount($coachId);
        $weeklySessions        = $this->service->weeklySessions($coachId);
        $weeklySessionsCount   = $this->service->weeklySessionsCount($coachId);
        $weeklyScheduleEntries = $this->service->formatSessionsForDashboard($weeklySessions);

        // Recent activities by this coach
        $recentActivities = $this->service->recentActivitiesForCoach($coachId, 3);

//        dd($recentActivities);
        return view('dashboards.coach', [
            'user'                  => $user,
            'specialtiesCsv'        => $specialtiesCsv,
            'activeClientsCount'    => $activeClientsCount,
            'weeklySessionsCount'   => $weeklySessionsCount,
            'weeklyScheduleEntries' => $weeklyScheduleEntries,
            'recentActivities'      => $recentActivities,      // collection from activity_log
        ]);
    }
}
