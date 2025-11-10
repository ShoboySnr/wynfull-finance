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

        if ($coachIds->isEmpty()) {
            $collections = ResourceCollection::query()->whereRaw('1=0')->paginate(10);
            return view('client.resource-library.index', compact('collections'));
        }

        $collections = ResourceCollection::query()
            ->with('modules')
            ->whereIn('coach_id', $coachIds)
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at')
            ->paginate(10)
            ->withQueryString();

        $toolsAndTemplates = ResourceModule::query()
            ->with([
                'collection:id,title,coach_id',
                'creator:id,name',
                'creator.profile:id,user_id,first_name,last_name,avatar_path',
            ])
            ->accessibleViaCoaches($coachIds)
            ->orWhereHas('directAssignees', fn ($sq) => $sq->where('user_id', $client->id))
            ->withCompletionFor($client->id)
            ->select(['id','resource_collection_id','title', 'description', 'type','file_name', 'file_path', 'video_link','created_by'])
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

//        dd($toolsAndTemplates);

        return view('client.resource-library.index', ['collections' => $collections, 'toolsAndTemplates' => $toolsAndTemplates]);
    }
}
