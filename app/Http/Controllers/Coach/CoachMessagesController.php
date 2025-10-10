<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;

class CoachMessagesController extends Controller
{
    public function index()
    {
        return view('coach.messages.index');
    }
}
