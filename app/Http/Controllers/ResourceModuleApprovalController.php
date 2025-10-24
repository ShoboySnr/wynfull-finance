<?php

namespace App\Http\Controllers;

use App\Models\ResourceModule;
use Illuminate\Http\Request;

class ResourceModuleApprovalController extends Controller
{
    public function approve(Request $request, ResourceModule $resourceModule)
    {
        $resourceModule->update([
            'status' => ResourceModule::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        activity()->useLog('content')->causedBy($request->user())->performedOn($resourceModule)
            ->event('module_approved')->log('Module approved');

        return back()->with('success', 'Module approved.');
    }

    public function reject(Request $request, ResourceModule $resourceModule)
    {
        $data = $request->validate(['rejection_reason' => ['required','string','max:2000']]);

        $resourceModule->update([
            'status' => ResourceModule::STATUS_REJECTED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        activity()->useLog('content')->causedBy($request->user())->performedOn($resourceModule)
            ->event('module_rejected')->withProperties(['reason'=>$data['rejection_reason']])->log('Module rejected');

        return back()->with('success', 'Module rejected.');
    }
}
