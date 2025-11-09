<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Clients\GoalScoringService;
use App\Services\Dashboard\CoachDashboardService;
use App\Services\Onboarding\ComputeAndStoreConfidenceService;
use App\Services\Onboarding\DebtJourneyService;
use App\Services\Onboarding\FinancialKnowledgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CoachClientsController extends Controller
{
    public function __construct(
        private readonly GoalScoringService               $goals,
        private readonly ComputeAndStoreConfidenceService $confidence,
        private readonly CoachDashboardService $dashboardService,
        private readonly ComputeAndStoreConfidenceService $computeAndStoreConfidenceService,
        private readonly DebtJourneyService $debtJourneyService,
        private readonly FinancialKnowledgeService $financialKnowledgeService
    )
    {
    }

    public function index(Request $request)
    {
        $coach = $request->user();

        $q = $coach->clients()
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->withPivot(['id', 'assigned_by', 'assigned_at', 'status'])
            ->with(['profile:id,user_id,avatar_path'])
            ->addSelect([
                'last_activity_at' => DB::table('activity_log')
                    ->select('created_at')
                    ->whereColumn('activity_log.causer_id', 'users.id')
                    ->orderByDesc('activity_log.created_at')
                    ->limit(1),
            ]);

        if ($s = trim((string)$request->query('search'))) {
            $q->where(fn($w) => $w->where('users.name', 'like', "%{$s}%")
                ->orWhere('users.email', 'like', "%{$s}%"));
        }

        $perPage = (int)$request->query('per_page', 20);
        $paginator = $q->orderByDesc('coach_client_assignments.assigned_at')->paginate($perPage);

        // Optionally allow turning off metrics for speed via ?metrics=0
        $includeMetrics = $request->boolean('metrics', true);

        $data = collect($paginator->items())->map(function (User $client) use ($includeMetrics) {
            $avatarPath = optional($client->profile)->avatar_path;
            $lastAt = $client->last_activity_at
                ? Carbon::parse($client->last_activity_at)
                : null;

            $row = [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'avatar_path'=> $avatarPath,
                'last_activity_at'  => $lastAt?->toIso8601String(),   // e.g. "2025-10-24T10:32:11+01:00"
                'last_activity_ago' => $lastAt?->diffForHumans(),
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
                $row['plan_name'] = 'Beginner';
            }

            return $row;
        });

        $meta = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];

        activity()
            ->useLog('clients')
            ->performedOn($coach)
            ->causedBy($coach)
            ->event('coach clients index page viewed')
            ->withProperties([
                'returned'        => $data->count(),
                'page'            => $paginator->currentPage(),
                'per_page'        => $paginator->perPage(),
                'include_metrics' => $includeMetrics,
                'ip'              => $request->ip(),
                'user_agent'      => substr((string) $request->userAgent(), 0, 255),
                'client_ids'      => $data->pluck('id')->all(),
            ])
            ->log('Coach viewed clients list');

        return view('coach.clients.index', ['clients' => $data, 'meta' => $meta]);
    }

    public function show(User $user)
    {
        $recentActivities = $this->dashboardService->recentActivitiesForCoach($user->id, 3);
        $confidence = $this->computeAndStoreConfidenceService->forUser($user, persist: true);
        $journey = $this->debtJourneyService->forUser($user);
        $financialKnowledge = $this->financialKnowledgeService->forUser($user);
        return view('coach.clients.profile.show', [
            'client' => $user,
            'profile' => $user->profile,
            'recentActivities' => $recentActivities,
            'confidence' => $confidence,
            'journey' => $journey,
            'financialKnowledge' => $financialKnowledge
        ]);
    }
}
