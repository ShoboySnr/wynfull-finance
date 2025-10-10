@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
    <div class="page-header">
        <h1>User Administration</h1>
        <p>Manage all users and their roles on the platform.</p>
    </div>

    <!-- User Stats Grid -->
    <div class="coach-stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3>Total Users</h3>
                <p class="stat-value">{{ $totalUsers ?? 152 }}</p>
                <span class="stat-change positive">+12 this month</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-check"></i></div>
            <div class="stat-content">
                <h3>Active Clients</h3>
                <p class="stat-value">{{ $activeClients ?? 124 }}</p>
                <span class="stat-change neutral">89% of total</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-content">
                <h3>Coaches</h3>
                <p class="stat-value">{{ $totalCoaches ?? 8 }}</p>
                <span class="stat-change">2 admins</span>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="users-table-container">
        <div class="table-header">
            <h3>All Users</h3>
            <div class="table-controls">
                <div class="clients-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search users...">
                </div>
                <button class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Add New User
                </button>
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-cell">
                            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=40&h=40&fit=crop&crop=face&auto=format" alt="{{ $user->name }}" class="user-avatar-small">
                            <div>
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $role = 'Client'; // Default role
                            if (isset($user->role)) $role = $user->role; // Check for a 'role' property
                            $roleClass = 'badge-' . strtolower($role);
                        @endphp
                        <span class="badge {{ $roleClass }}">{{ $role }}</span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn view" title="View Profile"><i class="fas fa-eye"></i></button>
                            <button class="action-btn edit" title="Edit User"><i class="fas fa-pencil-alt"></i></button>
                            <button class="action-btn delete" title="Delete User"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem;">No users found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="table-pagination">
            {{ $users->links() }}
        </div>
    </div>
@endsection
