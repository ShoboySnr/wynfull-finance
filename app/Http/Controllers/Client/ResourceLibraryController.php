<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class ResourceLibraryController extends Controller
{
    public function index()
    {
        return view('client.resource-library.index');
    }
}
