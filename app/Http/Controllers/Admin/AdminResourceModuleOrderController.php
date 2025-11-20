<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCollection;
use App\Models\ResourceModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminResourceModuleOrderController extends Controller
{
    public function reorder(Request $request, ResourceCollection $resourceCollection)
    {
        $user = $request->user();

        // Only the owner can reorder
        abort_unless(
            $user->hasRole('admin') && $resourceCollection->coach_id === $user->id,
            403
        );

        $data = $request->validate([
            'ordered_ids'   => ['required', 'array', 'min:1'],
            'ordered_ids.*' => [
                'integer',
                Rule::exists('resource_modules', 'id')
                    ->where('resource_collection_id', $resourceCollection->id),
            ],
        ]);

        DB::transaction(function () use ($data, $resourceCollection) {
            foreach ($data['ordered_ids'] as $index => $moduleId) {
                ResourceModule::where('id', $moduleId)
                    ->where('resource_collection_id', $resourceCollection->id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        activity()->useLog('content')
            ->causedBy(auth()->user())
            ->performedOn($resourceCollection)
            ->event('modules_reordered')
            ->withProperties(['module_ids' => $data['ordered_ids']])
            ->log('Coach reordered modules in collection');

        return response()->json([
            'ok'      => true,
            'message' => 'Module order updated.',
        ]);
    }
}
