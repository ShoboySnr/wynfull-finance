<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResourceCollectionApprovalController extends Controller
{
    public function approve(Request $request, ResourceCollection $resourceCollection)
    {
        abort_unless($request->user()?->hasRole('admin'), 403);

        DB::transaction(function () use ($request, $resourceCollection) {
            $resourceCollection->forceFill([
                'approved_by'       => $request->user()->id,
                'approved_at'       => now(),
                'rejection_reason'  => null,
            ])->save();

            activity('admin')
                ->causedBy($request->user())
                ->performedOn($resourceCollection)
                ->withProperties([
                    'collection_id' => $resourceCollection->id,
                    'action'        => 'approved',
                ])
                ->log('resource_collection_approved');
        });

        //  $collection->coach->notify(new CollectionApprovedNotification($collection));

        return back()->with('success', 'Resource collection approved.');
    }

    public function reject(Request $request, ResourceCollection $resourceCollection)
    {
        abort_unless($request->user()?->hasRole('admin'), 403);


        $data = $request->validate([
            'rejection_reason' => ['required','string','min:5','max:1000'],
        ]);

        DB::transaction(function () use ($request, $resourceCollection, $data) {
            $resourceCollection->forceFill([
                'rejection_reason'  => $data['rejection_reason'],
                'approved_at'       => null,
                'approved_by'       => null,
            ])->save();

            activity('admin')
                ->causedBy($request->user())
                ->performedOn($resourceCollection)
                ->withProperties([
                    'collection_id' => $resourceCollection->id,
                    'action'        => 'rejected',
                    'reason'        => $data['rejection_reason'],
                ])
                ->log('resource_collection_rejected');
        });

        //  $collection->coach->notify(new CollectionRejectedNotification($collection, $data['reason']));

        return back()->with('success', 'Resource collection rejected.');
    }
}
