<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserAdminService
{
    public function getStats(): array
    {
        $totalUsers    = User::count();

        $activeClients = User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn($q) => $q->where('name', 'client')->where('guard_name', 'web'))
            ->count();

        $totalCoaches  = User::query()
            ->whereHas('roles', fn($q) => $q->where('name', 'coach')->where('guard_name', 'web'))
            ->count();

        return compact('totalUsers', 'activeClients', 'totalCoaches');
    }

    /**
     * @param array{q?:string,role?:string,status?:string,per_page?:int,sort?:string,dir?:string} $filters
     */
    public function listUsers(array $filters = []): LengthAwarePaginator
    {
        $q        = $filters['q']   ?? null;
        $role     = $filters['role'] ?? null;
        $status   = $filters['status'] ?? null;

        $perPage  = (int)($filters['per_page'] ?? 15);
        if ($perPage < 5)   $perPage = 15;
        if ($perPage > 100) $perPage = 100;

        // whitelist sortable columns
        $allowedSorts = ['created_at', 'name', 'email', 'is_active'];
        $sortInput    = $filters['sort'] ?? '';
        $sort         = in_array($sortInput, $allowedSorts, true) ? $sortInput : 'created_at';

        // only asc|desc
        $dirInput = strtolower($filters['dir'] ?? '');
        $dir      = $dirInput === 'asc' ? 'asc' : 'desc';

        $query = User::query()
            ->with('roles')
            ->when($q, fn($b) => $b->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            }))
            ->when($role, fn($b) => $b->whereHas('roles', fn($r) => $r
                ->where('name', $role)->where('guard_name', 'web')))
            ->when($status, fn($b) => $b->where('is_active', $status === 'active'))
            ->orderBy($sort, $dir);

        return $query->paginate($perPage)->withQueryString();
    }
}
