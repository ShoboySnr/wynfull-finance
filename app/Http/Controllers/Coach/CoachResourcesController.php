<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;

class CoachResourcesController extends Controller
{
    public function index()
    {
        return view('coach.resources.index');
    }
}
