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
        abort_unless($client?->hasRole('client'), 403);

        // Get active coach assignments for this client (supports multiple)
        $coachIds = CoachClientAssignment::query()
            ->where('client_id', $client->id)
            ->when($request->query('status', 'active'), fn ($q, $status) =>
            $q->where('status', $status)   // default: active
            )
            ->pluck('coach_id')
            ->unique()
            ->values();


        $collections = ResourceCollection::query()
            ->withCount('modules')
            ->with(['modules' => function ($m) {
                $m->select('id','resource_collection_id','title','type','file_name','file_path','video_link','created_by')
                    ->latest('id');
            }])
            ->whereNotNull('approved_at')
            ->where(function ($q) use ($coachIds, $client) {
                $q->where('visibility', 'global')
                    ->orWhere(function ($w) use ($coachIds) {
                        $w->where('visibility', 'coach_only')
                            ->whereIn('coach_id', $coachIds);
                    })
                    ->orWhere(function ($w) use ($client) {
                        $w->where('visibility', 'assigned_only')
                            ->whereHas('modules.directAssignees', fn ($d) => $d->where('user_id', $client->id));
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
                $q->whereHas('collection', fn ($c) => $c->where('visibility', '!=', 'assigned_only'))
                    ->orWhereHas('directAssignees', fn ($d) => $d->where('user_id', $client->id));
            })

            // === SELECT BASE COLUMNS FIRST ===
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

            // === THEN ADD SUBSELECT ALIASES (DON'T CALL select() AGAIN) ===
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
}
