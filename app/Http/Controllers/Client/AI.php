<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class AI extends Controller
{
    public function index()
    {
        return view('client.ai.index');
    }
}
