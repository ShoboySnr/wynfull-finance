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
            <span class="coach-badge">Client Portal</span>
        </div>
    </div>

    <nav class="nav-menu">
        <li class="nav-item {{ request()->routeIs('dashboard.client') ? 'active' : '' }}">
            <a href="{{ route('dashboard.client') }}" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('resources.library') ? 'active' : '' }}">
            <a href="{{ route('resources.library') }}" class="nav-link" data-page="resource-library">
                <i class="fas fa-book"></i>
                <span>Resource Library</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('ai.client') ? 'active' : '' }}">
            <a href="{{ route('ai.client') }}" class="nav-link" data-page="wynfull-ai">
                <i class="fas fa-robot"></i>
                <span>Wynfull AI</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('coaching.client') ? 'active' : '' }}">
            <a href="{{ route('coaching.client') }}" class="nav-link" data-page="coaching">
                <i class="fas fa-user-tie"></i>
                <span>Coaching</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('messages.client') ? 'active' : '' }}">
            <a href="{{ route('messages.client') }}" class="nav-link" data-page="messaging">
                <i class="fas fa-comments"></i>
                <span>Messaging</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('plans.client') ? 'active' : '' }}">
            <a href="{{ route('plans.client') }}" class="nav-link" data-page="plans">
                <i class="fas fa-credit-card"></i>
                <span>Plans</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('account.client') ? 'active' : '' }}">
            <a href="{{ route('account.client') }}" class="nav-link" data-page="account">
                <i class="fas fa-user-cog"></i>
                <span>Account</span>
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
            </div>
            <button class="role-switch-btn" id="clientViewBtn" title="Switch to Client View">
                <i class="fas fa-user"></i>
            </button>
        </a>
    </div>
</aside>
