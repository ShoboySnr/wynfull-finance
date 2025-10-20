<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private readonly ProfileService $profiles) {}
    public function index(Request $request)
    {
        $user = $request->user();
        $profile = $this->profiles->getFor($user);
        return view('client.account.index', ['user' => $user, 'profile' => $profile]);
    }
}
