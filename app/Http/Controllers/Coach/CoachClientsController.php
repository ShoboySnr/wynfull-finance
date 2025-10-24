<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Clients\GoalScoringService;
use App\Services\Onboarding\ComputeAndStoreConfidenceService;
use Illuminate\Http\Request;

class CoachClientsController extends Controller
{
    public function __construct(
        private readonly GoalScoringService               $goals,
        private readonly ComputeAndStoreConfidenceService $confidence
    )
    {
    }

    public function index(Request $request)
    {
        $coach = $request->user();

        $q = $coach->clients()
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->withPivot(['id', 'assigned_by', 'assigned_at', 'status']);

        if ($s = trim((string)$request->query('search'))) {
            $q->where(fn($w) => $w->where('users.name', 'like', "%{$s}%")
                ->orWhere('users.email', 'like', "%{$s}%"));
        }

        $perPage = (int)$request->query('per_page', 20);
        $paginator = $q->orderByDesc('coach_client_assignments.assigned_at')->paginate($perPage);

        // Optionally allow turning off metrics for speed via ?metrics=0
        $includeMetrics = $request->boolean('metrics', true);

        $data = collect($paginator->items())->map(function (User $client) use ($includeMetrics) {
            $row = [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'assigned' => [
                    'id' => $client->pivot->id ?? null,
                    'status' => $client->pivot->status ?? null,
                    'assigned_by' => $client->pivot->assigned_by ?? null,
                    'assigned_at' => $client->pivot->assigned_at ?? null,
                ],
            ];

            if ($includeMetrics) {
                // Goal label + goal score
                $goal = $this->goals->forUser($client);

                // Confidence (don’t persist in index to keep it fast)
                $conf = $this->confidence->forUser($client, persist: false);

                $row['primary_goal_label'] = $goal['primary_goal_label'];
                $row['goal_score'] = $goal['goal_score'];
                $row['goal_metric_label'] = $goal['goal_metric_label'];
                $row['confidence_score'] = $conf['score'] ?? null;
                $row['confidence_band'] = $conf['band'] ?? null;
            }

            return $row;
        });

        $meta = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];

        activity()->useLog('clients')
            ->causedBy($coach)
            ->event('coach_clients_index_viewed')
            ->withProperties([
                'returned' => $data->count(),
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'include_metrics' => $includeMetrics,
                'ip' => $request->ip(),
            ])->log('Coach viewed clients list');

        return view('coach.clients.index', ['clients' => $data, 'meta' => $meta]);
    }
}
