@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
    <div class="page-header">
        <h1>User Administration</h1>
        <p>Manage all users and their roles on the platform.</p>
    </div>

    {{-- START: Feedback Messages --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
            <div>
                <strong class="font-bold">Oops! Something went wrong.</strong>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    {{-- END: Feedback Messages --}}

    {{-- Helper for JS to re-open password modal on error --}}
    @if ($errors->any() && old('form_type') === 'change_password')
        <input type="hidden" id="password_error_user_id" value="{{ old('user_id') }}">
    @endif

    <!-- User Stats Grid -->
    <div class="coach-stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3>Total Users</h3>
                <p class="stat-value">{{ $totalUsers ?? 152 }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-check"></i></div>
            <div class="stat-content">
                <h3>Active Clients</h3>
                <p class="stat-value">{{ $activeClients ?? 124 }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-content">
                <h3>Coaches</h3>
                <p class="stat-value">{{ $totalCoaches ?? 8 }}</p>
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
                <button class="btn-primary" id="addUserBtn">
                    <i class="fas fa-plus"></i>
                    Add New User
                </button>
            </div>
        </div>

        <div class="responsive-table-wrapper">
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
                        <td data-label="User">
                            <div class="user-cell">
                                <img src="{{ $user->profile?->avatar_path ? asset('storage/' . $user->profile->avatar_path) : 'https://placehold.co/40x40/EBF0FF/0E4DA4?text=' . strtoupper(substr($user->name, 0, 1)) }}" alt="{{ $user->name }}" class="user-avatar-small">
                                <div>
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Role">
                            @php
                                $role = 'Client';
                                if (isset($user->roles)) $role = $user->roles->pluck('name')->first();
                                $roleClass = 'badge-' . strtolower($role ?? 'client');
                            @endphp
                            <span class="badge {{ $roleClass }}">{{ ucfirst($role ?? 'Client') }}</span>
                        </td>
                        <td data-label="Status">
                            @if($user->is_active)
                                <span class="badge badge-active">Active</span>
                            @else
                                <span class="badge badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td data-label="Joined">{{ $user->created_at->format('M d, Y') }}</td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="action-btn view" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <div class="dropdown-container" style="display: inline-block; position: relative;">
                                    <button class="action-btn dropdown-toggle" onclick="toggleDropdown('userActions{{ $user->id }}')" title="More Actions">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu" id="userActions{{ $user->id }}">
                                        <button type="button" class="dropdown-item change-password-btn"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-action="{{ route('admin.users.password.update', $user->id) }}">
                                            Change Password
                                        </button>

                                        @if(!$user->hasRole('admin') && $user->id !== auth()->id())
                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                                  onsubmit="return confirm('This will permanently delete {{ $user->name }} and all their data. This action cannot be undone. Are you absolutely sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item danger">
                                                    Delete User
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
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
        </div>

        <div class="table-pagination">
            {{ $users->links() }}
        </div>
    </div>

    {{-- START: Add New User Modal --}}
    <div class="modal-overlay" id="addUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New User</h2>
                <button class="modal-close" id="addUserModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{-- route('admin.users.store') --}}" method="POST" id="addUserForm">
                    @csrf
                    {{-- Identification for error handling --}}
                    <input type="hidden" name="form_type" value="add_user">

                    {{-- Only show this input if THIS form failed --}}
                    @if ($errors->any() && old('form_type') === 'add_user')
                        <input type="hidden" name="has_add_errors" value="true">
                    @endif

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
                        @error('name') <span class="text-danger small" style="color: #dc2626; font-size: 0.85rem; display: block; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required>
                        @error('email') <span class="text-danger small" style="color: #dc2626; font-size: 0.85rem; display: block; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-input" required>
                            @error('password') <span class="text-danger small" style="color: #dc2626; font-size: 0.85rem; display: block; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="role">Assign Role</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select a role...</option>
                            <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client</option>
                            <option value="coach" {{ old('role') == 'coach' ? 'selected' : '' }}>Coach</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role') <span class="text-danger small" style="color: #dc2626; font-size: 0.85rem; display: block; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group form-group-checkbox">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active">Activate this user immediately</label>
                        <small class="checkbox-hint">If unchecked, the user will need to be activated later.</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="addUserModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: Add New User Modal --}}

    {{-- START: Change Password Modal --}}
    <div class="modal-overlay" id="changePasswordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Change Password for <span id="cpUserName">User</span></h2>
                <button class="modal-close" id="changePasswordModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" id="changePasswordForm">
                    @csrf
                    @method('PUT')
                    {{-- Identification for error handling --}}
                    <input type="hidden" name="form_type" value="change_password">
                    <input type="hidden" name="user_id" id="cp_hidden_user_id" value="{{ old('user_id') }}">

                    <div class="form-group">
                        <label for="cp_password">New Password</label>
                        <input type="password" id="cp_password" name="password" class="form-input" required>
                        @error('password') <span class="text-danger small" style="color: #dc2626; font-size: 0.85rem; display: block; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="cp_password_confirmation">Confirm New Password</label>
                        <input type="password" id="cp_password_confirmation" name="password_confirmation" class="form-input" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="changePasswordModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: Change Password Modal --}}

@endsection
@push('scripts')
    <script>
        // Dropdown functionality
        window.toggleDropdown = function(dropdownId) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if(menu.id !== dropdownId) menu.classList.remove('show');
            });
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) dropdown.classList.toggle('show');
        };

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    </script>
    <script src="{{ asset('assets/js/admin-users.js') }}"></script>
@endpush
