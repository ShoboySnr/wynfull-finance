<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoachDashboardController extends Controller
{
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

        return view('dashboards.coach', [
            'user' => $user,
            'specialtiesCsv' => $specialtiesCsv,
        ]);
    }
}
