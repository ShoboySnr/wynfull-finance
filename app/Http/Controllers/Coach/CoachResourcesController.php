<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResourceRequest;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoachResourcesController extends Controller
{
    /**
     * Display a listing of the coach's resources.
     */
    public function index(Request $request)
    {
        $query = Resource::where('coach_id', Auth::id());

        if ($request->has('filter') && $request->filter !== 'all') {
            $query->where('type', $request->filter);
        }

        $resources = $query->latest()->paginate(10);

        return view('coach.resources.index', compact('resources'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResourceRequest $request)
    {
        $data = $request->validated();
        $data['coach_id'] = Auth::id();
        $data['status'] = 'pending'; // Resources require admin approval

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('resources', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        Resource::create($data);

        return redirect()->route('coach.resources')
            ->with('success', 'Resource uploaded successfully! It will be available to clients after admin approval.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(StoreResourceRequest $request, Resource $resource)
    {
        // Authorization check: ensure the coach owns this resource
        if ($resource->coach_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        // Handle new file upload
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            // Store the new file
            $path = $request->file('file')->store('resources', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
            $data['video_link'] = null; // Clear video link if a file is uploaded
        } elseif ($data['type'] === 'video') {
            // If type is video, clear file path and name
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $data['file_path'] = null;
            $data['file_name'] = null;
        }


        // When updating, the resource goes back to pending for re-approval
        $data['status'] = 'pending';

        $resource->update($data);

        return redirect()->route('coach.resources')
            ->with('success', 'Resource updated successfully. It is pending re-approval from an admin.');
    }
}

