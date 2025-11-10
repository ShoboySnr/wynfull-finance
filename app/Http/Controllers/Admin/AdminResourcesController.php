<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCollection;
use Illuminate\Http\Request;

class AdminResourcesController extends Controller
{
    public function index(Request $request)
    {
        $status = strtolower((string) $request->query('status', ''));

        $collections = ResourceCollection::with('modules', 'approver')
            ->when($status === 'approved', fn ($q) => $q->approved())
            ->when($status === 'pending',  fn ($q) => $q->pending())
            ->when($status === 'rejected', fn ($q) => $q->rejected())
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.resources.index', ['collections' => $collections]);
    }
}
