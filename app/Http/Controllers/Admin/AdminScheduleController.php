<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminScheduleController extends Controller
{
    public function index()
    {
        return view('admin.schedule.index');
    }
}
