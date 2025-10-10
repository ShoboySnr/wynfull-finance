<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Access denied');
        }

        return view('dashboards.admin', ['user' => $user]);
    }

    public function listUsers()
    {

    }
}
