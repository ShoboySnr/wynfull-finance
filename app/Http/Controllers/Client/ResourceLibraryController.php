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

//        if ($coachIds->isEmpty()) {
//            $collections = ResourceCollection::query()->whereRaw('1=0')->paginate(10);
//            $toolsAndTemplates = [];
//            return view('client.resource-library.index', compact('collections', 'toolsAndTemplates'));
//        }

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
            ->orderByDesc('approved_at')
            ->paginate(10)
            ->withQueryString();

//        $toolsAndTemplates = ResourceModule::query()
//            ->with([
//                'collection:id,title,coach_id',
//                'creator:id,name',
//                'creator.profile:id,user_id,first_name,last_name,avatar_path',
//            ])
//            ->accessibleViaCoaches($coachIds)
//            ->orWhereHas('directAssignees', fn ($sq) => $sq->where('user_id', $client->id))
//            ->withCompletionFor($client->id)
//            ->select(['id','resource_collection_id','title', 'description', 'type','file_name', 'file_path', 'video_link','created_by'])
//            ->orderByDesc('id')
//            ->paginate(12)
//            ->withQueryString();

        $toolsAndTemplates = ResourceModule::query()
            ->with([
                'collection:id,title,coach_id,visibility,approved_at',
                'collection.coach:id,name',
                'creator:id,name',
                'creator.profile:id,user_id,first_name,last_name,avatar_path',
            ])
            // Collection must be approved AND pass one of the visibility gates
            ->whereHas('collection', function ($c) use ($coachIds, $client) {
                $c->whereNotNull('approved_at')
                    ->where(function ($v) use ($coachIds, $client) {
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
            // For assigned_only, ensure the module is assigned to this client
            ->where(function ($q) use ($client) {
                $q->whereHas('collection', fn ($c) => $c->where('visibility', '!=', 'assigned_only'))
                    ->orWhereHas('directAssignees', fn ($d) => $d->where('user_id', $client->id));
            })
            ->withCompletionFor($client->id)
            ->select(['id','resource_collection_id','title','description','type','file_name','file_path','video_link','created_by'])
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('client.resource-library.index', ['collections' => $collections, 'toolsAndTemplates' => $toolsAndTemplates]);
    }
}
