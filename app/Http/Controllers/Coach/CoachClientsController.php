<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;

class CoachClientsController extends Controller
{
    public function index()
    {
        return view('coach.clients.index');
    }
}
