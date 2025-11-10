<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceModule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResourceModuleAssignmentController extends Controller
{
    public function assign(Request $request, ResourceModule $module)
    {
        $data = $request->validate([
            'user_id' => ['required', 'array', 'min:1'],
            'user_id.*' => ['integer', Rule::exists('users', 'id')],
        ]);

        $now = now();
        $adminId = $request->user()->id;

        $syncData = [];
        foreach ($data['user_id'] as $uid) {
            $syncData[$uid] = ['assigned_by' => $adminId, 'assigned_at' => $now];
        }

        $module->directAssignees()->syncWithoutDetaching($syncData);

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($module)
            ->event('module assigned by admin')
            ->withProperties(['user_ids' => $data['user_id']])
            ->log('Admin assigned module to clients');

        return redirect()->back()->with('success', 'Module assigned successfully');
    }

    public function unassign(Request $request, ResourceModule $module)
    {
        $data = $request->validate([
            'user_id' => ['required','array','min:1'],
            'user_id.*' => ['integer', Rule::exists('users','id')],
        ]);

        $module->directAssignees()->detach($data['user_id']);

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($module)
            ->event('module_unassigned_admin')
            ->withProperties(['user_ids' => $data['user_id']])
            ->log('Admin unassigned module from clients');

        return redirect()->back()->with('success', 'Module unassigned successfully');
    }
}
