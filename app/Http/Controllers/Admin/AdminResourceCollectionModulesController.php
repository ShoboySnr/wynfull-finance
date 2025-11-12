<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCollection;
use App\Models\ResourceModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminResourceCollectionModulesController extends Controller
{
    public function index(ResourceCollection $resourceCollection)
    {
        return view('admin.resources.modules.index', [
            'collection' => $resourceCollection
        ]);
    }

    public function store(Request $request, ResourceCollection $resourceCollection)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:file,template,word,pdf,excel,video'],
            'file' => ['nullable', 'file', 'max:51200'], // 50MB
            'video_link' => ['nullable', 'url'],
        ]);

        $filePath = $fileName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store("resources/collections/{$resourceCollection->id}", 'public');
            $fileName = $file->getClientOriginalName();
        }

        $module = ResourceModule::create([
            'resource_collection_id' => $resourceCollection->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'file_path' => $filePath,
            'file_name' => $fileName,
            'video_link' => $data['video_link'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($module)
            ->event('module_created_admin')
            ->withProperties([
                'collection_id' => $resourceCollection->id,
                'authored_by' => 'admin',
            ])
            ->log('Admin created module');

        return redirect()->back()->with('success', 'Admin created module successfully');
    }

    public function update(Request $request, ResourceCollection $resourceCollection, ResourceModule $resourceModule)
    {
        abort_unless($resourceModule->resource_collection_id === $resourceCollection->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:file,template,word,pdf,excel,video'],
            'file' => ['nullable', 'file', 'max:51200'],
            'video_link' => ['nullable', 'url'],
        ]);

        if ($request->hasFile('file')) {
            if ($resourceModule->file_path) {
                Storage::disk('public')->delete($resourceModule->file_path);
            }
            $newPath = $request->file('file')->store("resources/collections/{$resourceCollection->id}", 'public');
            $data['file_path'] = $newPath;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        unset($data['approved_at'], $data['approved_by'], $data['rejection_reason'], $data['status']);
        $resourceModule->update($data);

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($resourceModule)
            ->event('module updated admin')
            ->log('Admin updated module');

        return redirect()->back()->with('success', 'Admin updated module successfully');
    }

    public function destroy(Request $request, ResourceCollection $resourceCollection, ResourceModule $resourceModule)
    {
        abort_unless($resourceModule->resource_collection_id == $resourceCollection->id, 404);

        if ($resourceModule->file_path) {
            Storage::disk('public')->delete($resourceModule->file_path);
        }

        $resourceModule->delete();

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($resourceModule)
            ->event('module deleted by admin')
            ->log('Admin deleted module');

        return redirect()->back()->with('success', 'Module deleted successfully');
    }
}
