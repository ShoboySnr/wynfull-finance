<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ResourceModule;
use App\Services\ModuleCompletionService;
use Illuminate\Http\Request;

class ModuleCompletionController extends Controller
{
    public function __construct(private readonly ModuleCompletionService $service) {}

    public function store(Request $request, ResourceModule $resourceModule)
    {
        abort_unless($request->user()?->hasRole('client'), 403);

        $this->service->complete($request->user(), $resourceModule);

        return back()->with('success', 'Module marked as completed.');
    }

    public function destroy(Request $request, ResourceModule $resourceModule)
    {
        abort_unless($request->user()?->hasRole('client'), 403);

        $this->service->undo($request->user(), $resourceModule);

        return back()->with('success', 'Module completion undone.');
    }
}
