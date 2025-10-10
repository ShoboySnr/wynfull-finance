<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class MessagingController extends Controller
{
    public function index()
    {
        return view('client.messaging.index');
    }
}
