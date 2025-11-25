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

        $coachId = (int) $user->id;

        // Coaching sessions + admin meetings for this week
        $weeklySessions      = $this->service->weeklySessions($coachId);
        $weeklyAdminMeetings = $this->service->weeklyAdminMeetings($coachId);

        // Count now includes both sessions + admin meetings
        $weeklySessionsCount   = $this->service->weeklySessionsCount($coachId);
        $weeklyScheduleEntries = $this->service->formatSessionsForDashboard(
            $weeklySessions,
            null,
            $weeklyAdminMeetings
        );

        // Recent activities by this coach
        $recentActivities = $this->service->recentActivitiesForCoach($coachId, 3);

        return view('dashboards.coach', [
            'user'                  => $user,
            'specialtiesCsv'        => $specialtiesCsv,
            'activeClientsCount'    => $this->service->activeClientsCount($coachId),
            'weeklySessionsCount'   => $weeklySessionsCount,
            'weeklyScheduleEntries' => $weeklyScheduleEntries,
            'recentActivities'      => $recentActivities,
        ]);
    }

}
