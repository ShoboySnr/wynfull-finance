<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserAdminService
{
    /**
     * Get top-line stats for the Users page.
     */
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
     * Paginated users list with optional search/filters.
     *
     * @param  array{q?:string,role?:string,status?:string,per_page?:int,sort?:string,dir?:'asc'|'desc'} $filters
     */
    public function listUsers(array $filters = []): LengthAwarePaginator
    {
        $q        = $filters['q']        ?? null;                 // search term
        $role     = $filters['role']     ?? null;                 // 'admin'|'coach'|'client'
        $status   = $filters['status']   ?? null;                 // 'active'|'inactive'
        $perPage  = (int)($filters['per_page'] ?? 15);
        $sort     = $filters['sort']     ?? 'created_at';
        $dir      = strtolower($filters['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query = User::query()
            ->with(['roles']) // eager-load to avoid N+1
            ->when($q, function ($builder) use ($q) {
                $builder->where(function ($b) use ($q) {
                    $b->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($role, function ($builder) use ($role) {
                $builder->whereHas('roles', fn($r) => $r->where('name', $role)->where('guard_name', 'web'));
            })
            ->when($status, function ($builder) use ($status) {
                $builder->where('is_active', $status === 'active');
            })
            ->orderBy($sort, $dir);

        return $query->paginate($perPage)->withQueryString();
    }
}
