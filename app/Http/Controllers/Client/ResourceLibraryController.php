<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CoachClientAssignment;
use App\Models\ResourceCollection;
use App\Models\ResourceModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResourceLibraryController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->user();

//        dd($client);
        abort_unless($client?->hasRole('client'), 403);

        // Get active coach assignments for this client (supports multiple)
        $coachIds = CoachClientAssignment::query()
            ->where('client_id', $client->id)
            ->when($request->query('status', 'active'), fn($q, $status) => $q->where('status', $status)   // default: active
            )
            ->pluck('coach_id')
            ->unique()
            ->values();


        $collections = ResourceCollection::query()
            ->withCount('modules')
            ->with([
                'modules' => function ($m) use ($client) {
                    $m->select('id', 'resource_collection_id', 'title', 'type', 'file_name', 'file_path', 'video_link', 'created_by')
                        ->latest('id')
                        ->with([
                            'completions' => fn($c) => $c->where('users.id', $client->id)
                                ->whereNotNull('resource_module_users.completed_at')
                        ]);
                },
            ])
            ->whereNotNull('approved_at')
            ->where(function ($q) use ($coachIds, $client) {
                $q->where('visibility', 'global')
                    ->orWhere(function ($w) use ($coachIds) {
                        $w->where('visibility', 'coach_only')
                            ->whereIn('coach_id', $coachIds);
                    })
                    ->orWhere(function ($w) use ($client) {
                        $w->where('visibility', 'assigned_only')
                            ->whereHas('modules.directAssignees', fn($d) => $d->where('user_id', $client->id));
                    });
            })
            // Put admin/global on top, then newest approvals
            ->orderByRaw("CASE WHEN visibility = 'global' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('approved_at')
            ->paginate(10)
            ->withQueryString();


        $toolsAndTemplates = ResourceModule::query()
            ->with([
                'collection:id,title,coach_id,visibility,approved_at',
                'collection.coach:id,name',
                'creator:id,name',
                'creator.profile:id,user_id,first_name,last_name,avatar_path',
            ])
            // Parent collection must be approved and pass visibility gate
            ->whereHas('collection', function ($c) use ($coachIds) {
                $c->whereNotNull('approved_at')
                    ->where(function ($v) use ($coachIds) {
                        $v->where('visibility', 'global')
                            ->orWhere(function ($w) use ($coachIds) {
                                $w->where('visibility', 'coach_only')
                                    ->whereIn('coach_id', $coachIds);
                            })
                            ->orWhere(function ($w) {
                                $w->where('visibility', 'assigned_only');
                            });
                    });
            })
            // For assigned_only: ensure module is assigned to this client
            ->where(function ($q) use ($client) {
                $q->whereHas('collection', fn($c) => $c->where('visibility', '!=', 'assigned_only'))
                    ->orWhereHas('directAssignees', fn($d) => $d->where('user_id', $client->id));
            })
            ->select([
                'id',
                'resource_collection_id',
                'title',
                'description',
                'type',
                'file_name',
                'file_path',
                'video_link',
                'created_by',
            ])
            ->addSelect([
                'collection_visibility' => ResourceCollection::query()
                    ->select('visibility')
                    ->whereColumn('resource_collections.id', 'resource_modules.resource_collection_id')
                    ->whereNull('resource_collections.deleted_at')
                    ->limit(1),
                'collection_approved_at' => ResourceCollection::query()
                    ->select('approved_at')
                    ->whereColumn('resource_collections.id', 'resource_modules.resource_collection_id')
                    ->whereNull('resource_collections.deleted_at')
                    ->limit(1),
            ])
            ->withCompletionFor($client->id)

            // Admin/global first, then by collection approval recency, then newest modules
            ->orderByRaw("CASE WHEN collection_visibility = 'global' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('collection_approved_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('client.resource-library.index', ['collections' => $collections, 'toolsAndTemplates' => $toolsAndTemplates]);
    }

    public function learn(ResourceCollection $resourceCollection, ResourceModule $resourceModule)
    {
        // If this is an assessment module, redirect to the assessment taking page
        if ($resourceModule->type === 'assessment') {
            return redirect()->route('client.assessments.show', [$resourceCollection->id, $resourceModule->id]);
        }

        $collection = $resourceCollection->load('modules');
        $modules = $collection->modules->values(); // reindex 0..n-1

        // Find current index
        $currentIndex = $modules->search(fn ($m) => $m->id === $resourceModule->id);

        $prevModule = null;
        $nextModule = null;

        if ($currentIndex !== false) {
            if ($currentIndex > 0) {
                $prevModule = $modules[$currentIndex - 1];
            }
            if ($currentIndex < $modules->count() - 1) {
                $nextModule = $modules[$currentIndex + 1];
            }
        }

        return view('client.learning.index', [
            'collection'  => $collection,
            'module'      => $resourceModule,
            'prevModule'  => $prevModule,
            'nextModule'  => $nextModule,
        ]);
    }
}
