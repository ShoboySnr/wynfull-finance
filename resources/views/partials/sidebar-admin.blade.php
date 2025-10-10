<aside class="sidebar coach-sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">
                <img src="{{ asset('assets/img/wynfull-logo.png') }}" alt="Wynfull Logo" style="width: 32px; height: 32px;">
            </div>
            <div class="logo-text">
                <h2>Wynfull</h2>
                <span>Finance Platform</span>
            </div>
            <span class="coach-badge">Admin Portal</span>
        </div>
    </div>

    <nav class="nav-menu">
        <li class="nav-item {{ request()->routeIs('adin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.resources') ? 'active' : '' }}">
            <a href="{{ route('admin.resources') }}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <span>Resources</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <a href="{{ route('admin.users') }}" class="nav-link">
                <i class="fas fa-user-group"></i>
                <span>Users</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
            <a href="{{ route('admin.profiles') }}" class="nav-link">
                <i class="fas fa-user-cog"></i>
                <span>Profile</span>
            </a>
        </li>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('admin.profiles') }}" class="user-info" style="text-decoration: none; cursor: pointer;">
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format" alt="Coach Avatar" class="user-avatar">
            <div class="user-details">
                <span class="user-name">{{ $authUser->name ?? 'Coach Name' }}</span>
                <span class="user-role">Financial Planner</span>
            </div>
            <button class="role-switch-btn" id="clientViewBtn" title="Switch to Client View">
                <i class="fas fa-user"></i>
            </button>
        </a>
    </div>
</aside>
