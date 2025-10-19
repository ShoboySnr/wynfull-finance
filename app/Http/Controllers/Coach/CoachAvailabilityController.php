<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coach\UpdateAvailabilityRequest;
use App\Services\CoachAvailability\AvailabilitySettingsService;

class CoachAvailabilityController extends Controller
{
    public function __construct(
        private readonly AvailabilitySettingsService $service
    ) {
    }

    public function update(UpdateAvailabilityRequest $request)
    {
        $this->service->upsertFor($request->user(), $request->validated());

        return redirect()
            ->back()
            ->with('status', 'Availability updated');
    }
}
