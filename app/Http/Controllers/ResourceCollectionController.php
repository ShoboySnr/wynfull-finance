<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceCollectionRequest;
use App\Models\ResourceCollection;
use Illuminate\Http\Request;

class ResourceCollectionController extends Controller
{
    public function index()
    {

    }

    public function store(StoreResourceCollectionRequest $request)
    {
        $collection = ResourceCollection::create([
            'coach_id'   => $request->user()->id,
            'icon_class' => $request->icon_class,
            'title'      => $request->title,
            'description'=> $request->description,
        ]);

        activity()->useLog('content')->causedBy($request->user())->performedOn($collection)
            ->event('collection_created')->withProperties(['title'=>$collection->title])->log('Resource collection created');

        return back()->with('success', 'Collection created.');
    }

    public function update(StoreResourceCollectionRequest $request, ResourceCollection $resourceCollection)
    {
        abort_unless($resourceCollection->coach_id === $request->user()->id, 403);

        $resourceCollection->update($request->validated());

        activity()->useLog('content')->causedBy($request->user())->performedOn($resourceCollection)
            ->event('collection_updated')->log('Resource collection updated');

        return back()->with('success', 'Collection updated.');
    }

    public function edit(Request $request, ResourceCollection $resourceCollection)
    {
        $resourceCollection->load('modules');
        return view('coach.resources.collections.shows', compact('resourceCollection'));
    }
}
