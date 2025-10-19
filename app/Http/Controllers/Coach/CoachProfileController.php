<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class CoachProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profiles) {}
    public function index(Request $request)
    {
        $profile = $this->profiles->getFor($request->user());
        $data = (new ProfileResource($profile))->toArray($request);
        $settings = $request->user()->settings;
        return view('coach.profile.index', ['profile' => $data, 'settings' => $settings]);
    }
}
