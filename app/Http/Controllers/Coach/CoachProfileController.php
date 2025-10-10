<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;

class CoachProfileController extends Controller
{
    public function index()
    {
        return view('coach.profile.index');
    }
}
