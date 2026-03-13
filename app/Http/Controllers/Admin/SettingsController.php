<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $nextOnboardingDate = Setting::get('next_onboarding_date');
        
        return view('admin.settings.index', [
            'nextOnboardingDate' => $nextOnboardingDate,
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'next_onboarding_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Setting::set(
            'next_onboarding_date',
            $request->next_onboarding_date,
            $request->user()->id
        );

        activity()
            ->causedBy($request->user())
            ->withProperties([
                'next_onboarding_date' => $request->next_onboarding_date,
                'admin_name' => $request->user()->name,
            ])
            ->log('admin_updated_global_onboarding_schedule');

        $message = $request->next_onboarding_date
            ? 'Onboarding scheduled for all clients on ' . \Carbon\Carbon::parse($request->next_onboarding_date)->format('M d, Y \a\t g:i A')
            : 'Onboarding schedule cleared for all clients';

        return back()->with('success', $message);
    }
}
