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

        $resourceModules = ResourceModule::query()
            ->with([
                'collection:id,title,coach_id',
                'creator:id,name',
                'creator.profile:id,user_id,first_name,last_name,avatar_path',
            ])
            ->whereIn('created_by', $coachIds)
            ->select([
                'id',
                'resource_collection_id',
                'title',
                'type',
                'file_name',
                'video_link',
                'approved_at',
                'created_by',
            ])
            ->addSelect([
                'completed_at' => DB::table('resource_module_users')
                    ->select('completed_at')
                    ->whereColumn('resource_module_users.resource_module_id', 'resource_modules.id')
                    ->where('resource_module_users.user_id', $client->id)
                    ->limit(1),
            ])
            ->orderByDesc('approved_at')
            ->paginate(12)
            ->withQueryString();

        return view('client.resource-library.index', ['collections' => $collections]);
    }
}
