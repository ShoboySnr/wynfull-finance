<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceModuleRequest;
use App\Http\Requests\UpdateResourceModuleRequest;
use App\Models\ResourceCollection;
use App\Models\ResourceModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceModuleController extends Controller
{
    public function index()
    {

    }

    public function store(StoreResourceModuleRequest $request, ResourceCollection $resourceCollection)
    {
        abort_unless($resourceCollection->coach_id === $request->user()->id, 403);

        $data = $request->validated();
        $filePath = $fileName = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("resources/collections/{$resourceCollection->id}", 'public');
            $fileName = $request->file('file')->getClientOriginalName();
        }

        $module = ResourceModule::create([
            'resource_collection_id' => $resourceCollection->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'],
            'file_path'   => $filePath,
            'file_name'   => $fileName,
            'video_link'  => $data['video_link'] ?? null,
            'status'      => $data['status'] ?? ResourceModule::STATUS_DRAFT,
            'created_by'  => $request->user()->id,
        ]);

        activity()->useLog('content')->causedBy($request->user())->performedOn($module)
            ->event('module_created')->withProperties(['type'=>$module->type,'status'=>$module->status])->log('Resource module created');

        return redirect()->route('coach.resources.collection.edit', $resourceCollection)->with('success', 'Module created.');
    }

    public function update(UpdateResourceModuleRequest $request, ResourceCollection $resourceCollection, ResourceModule $resourceModule)
    {
        abort_unless($resourceCollection->coach_id === $request->user()->id, 403);

        $data = $request->validated();

        if ($request->hasFile('file')) {
            // optional: delete old
            if ($resourceModule->file_path) {
                Storage::disk('public')->delete($resourceModule->file_path);
            }
            $newPath = $request->file('file')->store("resources/collections/{$resourceCollection->id}", 'public');
            $data['file_path'] = $newPath;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        unset($data['approved_by'], $data['approved_at'], $data['rejection_reason']);

        $resourceModule->update($data);

        activity()->useLog('content')->causedBy($request->user())->performedOn($resourceModule)
            ->event('module_updated')->log('Resource module updated');

        return back()->with('success', 'Module updated.');
    }


    public function destroy(Request $request, ResourceCollection $resourceCollection, ResourceModule $resourceModule)
    {
        abort_unless($resourceCollection->coach_id === $request->user()->id, 403);

        if ($resourceModule->file_path) {
            Storage::disk('public')->delete($resourceModule->file_path);
        }

        $resourceModule->delete();

        activity()->useLog('content')->causedBy($request->user())->performedOn($resourceModule)
            ->event('module_deleted')->log('Resource module deleted');

        return back()->with('success', 'Module deleted.');
    }
}
