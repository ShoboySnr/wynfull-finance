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
        <!-- User info moved to header -->
    </div>
</aside>
