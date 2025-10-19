<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profiles) {}

    public function show(Request $request)
    {
        $profile = $this->profiles->getFor($request->user());
        $data = (new ProfileResource($profile))->toArray($request);
        return view('coach.profile.index', ['profile' => $data ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $profile = $this->profiles->update($request->user(), $request->validated());
        return redirect()->back();
    }


    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $profile = $this->profiles->updateAvatar(
            $request->user(),
            $request->validated('avatar')
        );

        return redirect()->back();
    }
}
