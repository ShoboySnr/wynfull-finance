<?php

namespace App\Services\Onboarding;

use Illuminate\Support\Arr;

class ConfidenceScoringService
{
    /**
     * Compute confidence score (0–100) + band + per-dimension breakdown
     * from the onboarding $answers array.
     */
//    public function computeFromAnswers(array $answers): array
//    {
//        // 1) Weight maps (tweak as needed)
//        $financialMap = [
//            'struggling-debt'     => -30,
//            'paycheck-to-paycheck'=> -20,
//            'okay-not-saving'     => -10,
//            'saving-regularly'    => +10,
//            'confident-focused'   => +20,
//            'other'               => 0,
//        ];
//
//        $confidenceMap = [
//            'not-confident'       => -20,
//            'somewhat-confident'  =>  -5,
//            'confident'           => +10,
//            'very-confident'      => +20,
//        ];
//
//        $debtMap = [
//            'overwhelmed'         => -20,
//            'managing-stressful'  =>  -5,
//            'comfortable'         => +10,
//            'debt-free'           => +15,
//        ];
//
//        $savingsMap = [
//            'zero'                =>  -20,
//            'under-1k'            =>  -10,
//            '1k-5k'               =>   +5,
//            '5k-20k'              =>   +10,
//            '20k-plus'            =>   +20,
//        ];
//
//        $investingStatusMap = [
//            'not-yet'             =>  -10,
//            'just-starting'       =>   +5,
//            'consistently'        =>   +15,
//        ];
//
//        $investingExpMap = [
//            'beginner'            =>   0,
//            'intermediate'        =>  +5,
//            'advanced'            => +10,
//        ];
//
//        // 2) Extract values
//        $financialSituations = (array) Arr::get($answers, 'financial_situation', []);
//        $confidenceLevel     = Arr::get($answers, 'confidence_level');
//        $debtFeeling         = Arr::get($answers, 'debt_feeling');
//        $savingsAmount       = Arr::get($answers, 'savings_amount');
//        $investingStatus     = Arr::get($answers, 'investing_status');
//        $investingExperience = Arr::get($answers, 'investing_experience');
//
//        // 3) Financial situation can be multi-select — score the “worst” (min weight)
//        $financialScores = [];
//        foreach ($financialSituations as $opt) {
//            $financialScores[] = $financialMap[$opt] ?? 0;
//        }
//        $financialScore = count($financialScores)
//            ? min($financialScores) // worst-case
//            : 0;
//
//        // 4) Lookups with default 0
//        $confidenceScore  = $confidenceMap[$confidenceLevel]     ?? 0;
//        $debtScore        = $debtMap[$debtFeeling]               ?? 0;
//        $savingsScore     = $savingsMap[$savingsAmount]          ?? 0;
//        $investingScore   = $investingStatusMap[$investingStatus]?? 0;
//        $investingExp     = $investingExpMap[$investingExperience] ?? 0;
//
//        // 5) Raw total and normalization to 0–100
//        // Min possible ≈ -100, max ≈ +100 with the maps above.
//        $raw = $financialScore + $confidenceScore + $debtScore + $savingsScore + $investingScore + $investingExp;
//
//        $normalized = (int) round(
//            $this->normalize($raw, min: -100, max: 100, toMin: 0, toMax: 100)
//        );
//
//        // 6) Band
//        $band = match (true) {
//            $normalized < 40   => 'Low',
//            $normalized < 70   => 'Medium',
//            default            => 'High',
//        };
//
//        return [
//            'score'     => $normalized,
//            'band'      => $band,
//            'raw_total' => $raw,
//            'breakdown' => [
//                'financial_situation' => $financialScore,
//                'confidence_level'    => $confidenceScore,
//                'debt_feeling'        => $debtScore,
//                'savings_amount'      => $savingsScore,
//                'investing_status'    => $investingScore,
//                'investing_experience'=> $investingExp,
//            ],
//        ];
//    }

    /**
     * @param array|string|null $answers  Latest onboarding answers (array preferred; supports JSON string fallback)
     * @return array{
     *   score:int,
     *   band:string,
     *   raw_total:int,
     *   breakdown:array<string,mixed>
     * }
     */
    public function computeFromAnswers(array|string|null $answers): array
    {
        if (is_string($answers)) {
            $decoded = json_decode($answers, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $answers = $decoded;
            } else {
                $answers = [];
            }
        }

        // Use primary_goal (Step 2: Budget understanding confidence)
        $key = strtolower(trim((string) Arr::get($answers, 'primary_goal', '')));

        // Canonical mapping (max = 100)
        $map = [
            'not-confident'        => 25,
            'somewhat-confident'   => 50,
            'confident'            => 75,
            'very-confident'       => 100,
        ];

        $score = $map[$key] ?? 25; // default to lowest if missing/invalid

        // Optional band labels (tweak names to taste)
        $band = match (true) {
            $score <= 25 => 'Low',
            $score <= 50 => 'Mid',
            $score <= 75 => 'High',
            default      => 'Low',
        };

        return [
            'score'     => $score,
            'band'      => $band,
            'raw_total' => $score,
            'breakdown' => [
                'primary_goal' => $key !== '' ? $key : 'not-provided',
                'mapped_score' => $score,
            ],
        ];
    }
    private function normalize(float $value, float $min, float $max, float $toMin, float $toMax): float
    {
        if ($max === $min) return $toMin;
        $clamped = max($min, min($value, $max));
        $ratio = ($clamped - $min) / ($max - $min);
        return $toMin + $ratio * ($toMax - $toMin);
    }
}
