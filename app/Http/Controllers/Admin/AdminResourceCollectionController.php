<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BroadcastCollectionPublished;
use App\Models\ResourceCollection;
use App\Services\Notifications\NotifyUsersOfCollection;
use Illuminate\Http\Request;

class AdminResourceCollectionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'icon_class'  => ['nullable','string','max:100'],
            'title'       => ['required','string','max:200'],
            'description' => ['nullable','string'],
        ]);


        $collection = ResourceCollection::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'visibility'  => 'global',
            'icon_class' => $data['icon_class'],
            'coach_id' => $request->user()->id,
            'created_by' => $request->user()->id,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'status' => 'approved',
        ]);

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($collection)
            ->event('collection created by admin')
            ->withProperties(['coach_id' => $collection->coach_id])
            ->log('Admin created resource collection');

        BroadcastCollectionPublished::dispatch($collection->id);

        return redirect()->route('admin.resources.collection.modules', $collection->id)->with('success', 'Resource collection created successfully');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'icon_class'  => ['nullable','string','max:100'],
            'title'       => ['required','string','max:200'],
            'description' => ['nullable','string'],
        ]);

        $collection = ResourceCollection::findOrFail($id);
        $collection->update($data);

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($collection)
            ->event('collection_updated_admin')
            ->log('Admin updated resource collection');

        return redirect()->back()->with('success', 'Resource collection updated successfully');
    }



}
