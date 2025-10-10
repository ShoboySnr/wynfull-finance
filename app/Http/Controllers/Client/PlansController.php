<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class PlansController extends Controller
{
    public function index()
    {
        return view('client.plans.index');
    }
}
