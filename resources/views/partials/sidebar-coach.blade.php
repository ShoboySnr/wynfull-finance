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
            <span class="coach-badge">Coach Portal</span>
        </div>
    </div>

    <nav class="nav-menu">
        <li class="nav-item {{ request()->routeIs('coach.dashboard') ? 'active' : '' }}">
            <a href="{{ route('coach.dashboard') }}" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('coach.clients') ? 'active' : '' }}">
            <a href="{{ route('coach.clients') }}" class="nav-link">
                <i class="fas fa-users"></i>
                <span>My Clients</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('coach.messages') ? 'active' : '' }}">
            <a href="{{ route('coach.messages') }}" class="nav-link">
                <i class="fas fa-comments"></i>
                <span>Messages</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('coach.schedule') ? 'active' : '' }}">
            <a href="{{ route('coach.schedule') }}" class="nav-link">
                <i class="fas fa-calendar-alt"></i>
                <span>Schedule</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('coach.resources') ? 'active' : '' }}">
            <a href="{{ route('coach.resources') }}" class="nav-link">
                <i class="fas fa-folder-open"></i>
                <span>Resources</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('coach.profile') ? 'active' : '' }}">
            <a href="{{ route('coach.profile') }}" class="nav-link">
                <i class="fas fa-user-cog"></i>
                <span>Profile</span>
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
        <a href="{{ route('profile.show') }}" class="user-info" style="text-decoration: none; cursor: pointer;">
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format" alt="Coach Avatar" class="user-avatar">
            <div class="user-details">
                <span class="user-name">{{ $authUser->name ?? 'Coach Name' }}</span>
                <span class="user-role">{{ $authUser->profile->specialities ?? '' }}</span>
            </div>
            <button class="role-switch-btn" id="clientViewBtn" title="Switch to Client View">
                <i class="fas fa-user"></i>
            </button>
        </a>
    </div>
</aside>
