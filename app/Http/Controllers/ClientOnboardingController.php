<?php

namespace App\Http\Controllers;

use App\Models\ClientOnboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientOnboardingController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->onboarding_completed) {
            return response()->json(['ok' => true, 'already_completed' => true]);
        }

        $validated = $request->validate([
            'financial_situation' => ['required', 'array', 'min:1'],
            'financial_situation.*' => ['string', Rule::in([
                'struggling-debt', 'paycheck-to-paycheck', 'okay-not-saving',
                'saving-regularly', 'confident-focused', 'other'
            ])],
            'financial_situation_other' => ['nullable', 'string', 'max:500'],

            'primary_goal' => ['required', Rule::in([
                'not-confident','somewhat-confident','confident','very-confident'
            ])],
            'primary_goal_other' => ['nullable', 'string', 'max:500'],

            'confidence_level' => ['required', Rule::in([
                'not-confident','somewhat-confident','confident','very-confident'
            ])],

            'debt_feeling' => ['required', Rule::in([
                'overwhelmed','managing-stressful','comfortable','debt-free'
            ])],

            'savings_amount' => ['required', Rule::in([
                'zero','under-1k','1k-5k','5k-20k','20k-plus'
            ])],

            'investing_status' => ['required', Rule::in([
                'not-yet','just-starting','consistently'
            ])],

            'investing_experience' => ['required', Rule::in([
                'beginner','intermediate','advanced'
            ])],
        ]);

        $answers = [
            'financial_situation'      => $validated['financial_situation'],
            'financial_situation_other'=> $validated['financial_situation_other'] ?? null,
            'primary_goal'             => $validated['primary_goal'],
            'primary_goal_other'       => $validated['primary_goal_other'] ?? null,
            'confidence_level'         => $validated['confidence_level'],
            'debt_feeling'             => $validated['debt_feeling'],
            'savings_amount'           => $validated['savings_amount'],
            'investing_status'         => $validated['investing_status'],
            'investing_experience'     => $validated['investing_experience'],
        ];

        DB::transaction(function () use ($user, $answers) {
            ClientOnboarding::create([
                'user_id' => $user->id,
                'answers' => $answers,
                'completed_at' => now(),
            ]);

            $user->forceFill([
                'onboarding_completed' => true,
                'onboarding_completed_at' => now(),
            ])->save();

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties(['answers' => $answers])
                ->event('onboarding_completed')
                ->log('Client completed onboarding');
        });

        return response()->json(['ok' => true]);
    }
}
