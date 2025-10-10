<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class CoachingController extends Controller
{
    public function index()
    {
        return view('client.coaching.index');
    }
}
