<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CoachClientAssignment;
use App\Models\ResourceCollection;
use Illuminate\Http\Request;

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
            ->whereNotNull('approved_at')         // approved only
            ->orderByDesc('approved_at')
            ->paginate(10)
            ->withQueryString();

        return view('client.resource-library.index', ['collections' => $collections]);
    }
}
