<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\UserAdminService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserAdminService $service)
    {
    }

    public function index(Request $request)
    {
        $stats = $this->service->getStats();

        $users = $this->service->listUsers([
            'q'        => $request->string('q')->toString(),
            'role'     => $request->string('role')->toString(),     // 'admin'|'coach'|'client'
            'status'   => $request->string('status')->toString(),   // 'active'|'inactive'
            'per_page' => $request->integer('per_page') ?: 15,
            'sort'     => $request->string('sort')->toString(),     // e.g. 'created_at'|'name'|'email'
            'dir'      => $request->string('dir')->toString(),      // 'asc'|'desc'
        ]);

        return view('admin.users.index', [
            'totalUsers'    => $stats['totalUsers'],
            'activeClients' => $stats['activeClients'],
            'totalCoaches'  => $stats['totalCoaches'],
            'users'         => $users,
        ]);
    }
}
