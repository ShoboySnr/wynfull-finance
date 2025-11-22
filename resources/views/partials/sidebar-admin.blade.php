<aside class="sidebar coach-sidebar">
    <div class="sidebar-header">
        <a href="/" class="logo">
            <div class="logo-icon">
                <img src="{{ asset('assets/img/wynfull-logo.png') }}" alt="Wynfull Logo" style="width: 32px; height: 32px;">
            </div>
            <div class="logo-text">
                <h2>Wynfull</h2>
                <span>Finance Platform</span>
            </div>
        </a>
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
        <li class="nav-item {{ request()->routeIs('admin.schedules') ? 'active' : '' }}">
            <a href="{{ route('admin.schedules') }}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <span>Schedule</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <a href="{{ route('admin.users') }}" class="nav-link">
                <i class="fas fa-user-group"></i>
                <span>Users</span>
            </a>
        </li>

        <!-- Logout Button -->
        <li class="nav-item nav-item-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </li>
    </nav>

    <div class="sidebar-footer">
        <!-- User info moved to header -->
    </div>
</aside>
