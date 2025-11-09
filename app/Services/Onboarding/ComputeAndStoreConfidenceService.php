<?php

namespace App\Services\Onboarding;

use App\Models\ClientOnboarding;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ComputeAndStoreConfidenceService
{
    public function __construct(private readonly ConfidenceScoringService $scorer) {}

    /**
     * Compute score from latest onboarding and (optionally) persist a snapshot.
     * Returns ['score'=>int,'band'=>string,'raw_total'=>int,'breakdown'=>array]
     */
    public function forUser(User $user, bool $persist = true): ?array
    {
        $co = ClientOnboarding::where('user_id', $user->id)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) return null;

        $result = $this->scorer->computeFromAnswers($co->answers);

//        dd($result);
        if ($persist) {
            DB::table('client_confidence_scores')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'score'       => $result['score'],
                    'band'        => $result['band'],
                    'breakdown'   => json_encode($result['breakdown']),
                    'computed_at' => now(),
                    'updated_at'  => now(),
                    'created_at'  => now(),
                ]
            );
        }

        // Activity log (view/compute event)
        activity()
            ->useLog('clients')
            ->performedOn($user)
            ->causedBy($user)
            ->event('clients.confidence_score.computed')
            ->withProperties([
                'score'   => $result['score'],
                'band'    => $result['band'],
                'raw'     => $result['raw_total'],
            ])->log('Computed confidence score from latest onboarding');

        return $result;
    }
}
