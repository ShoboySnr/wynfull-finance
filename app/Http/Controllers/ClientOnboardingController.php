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

        // Allow multiple submissions for tracking progress over time
        // if ($user->onboarding_completed) {
        //     return response()->json(['ok' => true, 'already_completed' => true]);
        // }

        $validated = $request->validate([
            'financial_situation' => ['required', 'array', 'min:1'],
            'financial_situation.*' => ['string', Rule::in([
                'debt-management', 'cash-flow', 'savings-habits',
                'investing-basics', 'wealth-building', 'financial-education', 'other'
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
                'no-knowledge','basics-stressful','comfortable-applying','confident-teaching'
            ])],

            'savings_amount' => ['required', Rule::in([
                'not-confident','somewhat-confident','confident','very-confident'
            ])],

            'investing_status' => ['required', Rule::in([
                'not-familiar','familiar-basics','comfortable-applying','advanced-understanding'
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
