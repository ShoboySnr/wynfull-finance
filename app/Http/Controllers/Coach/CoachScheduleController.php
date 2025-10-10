<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;

class CoachScheduleController extends Controller
{
    public function index()
    {
        return view('coach.schedule.index');
    }
}
