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

        $filters = [
            'q'        => trim((string) $request->query('q', '')),
            'role'     => trim((string) $request->query('role', '')),
            'status'   => trim((string) $request->query('status', '')),
            'per_page' => (int) $request->query('per_page', 15),
            'sort'     => trim((string) $request->query('sort', '')),
            'dir'      => trim((string) $request->query('dir', '')),
        ];

        $users = $this->service->listUsers($filters);

        return view('admin.users.index', [
            'totalUsers'    => $stats['totalUsers'],
            'activeClients' => $stats['activeClients'],
            'totalCoaches'  => $stats['totalCoaches'],
            'users'         => $users,
        ]);
    }
}
