<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Wynfull</title>
    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coach-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js for data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Prevent flash of wrong theme -->
    <script>
        (function() {
            // Get system preference
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const systemTheme = systemPrefersDark ? 'dark' : 'light';
            
            // Get saved theme or use system preference
            const savedTheme = localStorage.getItem('wynfullTheme') || systemTheme;
            
            // Apply theme immediately to prevent flash
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.body.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body class="antialiased" data-user-role="admin">

<div class="coach-dashboard-layout">
    <!-- Sidebar -->
    @include('partials/sidebar-admin')

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <div class="header-breadcrumb">
                    <span class="breadcrumb-item">@yield('breadcrumb', 'Admin Dashboard')</span>
                </div>
                <div class="header-title-section">
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    <p class="page-subtitle">@yield('page-subtitle', 'Manage users, resources, and system settings')</p>
                </div>
            </div>
            <div class="header-right">
                <div class="header-user-info" id="userDropdownToggle">
                    <div class="user-welcome">
                        <span class="welcome-text">Welcome back,</span>
                        <span class="user-name">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="user-avatar-container">
                        <img src="{{ auth()->user()->profile?->avatar_path ? asset('storage/' . auth()->user()->profile->avatar_path) : 'https://placehold.co/40x40/EBF0FF/0E4DA4?text=' . strtoupper(substr(auth()->user()->name, 0, 1)) }}" 
                             alt="User Avatar" class="header-user-avatar">
                        <div class="user-status-indicator admin-status"></div>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item logout-item" style="width: 100%; background: none; border: none; text-align: left;">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="header-action-btn notification-btn" id="notificationBtn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" style="display: none;">0</span>
                    </button>
                    <button class="header-action-btn theme-toggle" id="themeToggle" title="Toggle Dark/Light Mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </header>

        <div class="page active">
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Notification Panel -->
    <div class="notification-panel" id="notificationPanel">
        <div class="notification-header">
            <h3>Notifications</h3>
            <button class="close-notifications" id="closeNotifications">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="notification-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
        <div class="notification-footer">
            <button class="mark-all-read-btn" id="markAllReadBtn">Mark All as Read</button>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="info-panel" id="infoPanel">
        <div class="info-header">
            <h3 id="infoPanelTitle">Information</h3>
            <button class="close-info" id="closeInfo">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="info-content" id="infoContent">
            <!-- Dynamic content will be loaded here -->
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/coach.js') }}"></script>


@stack('scripts')

<!-- Chatway Widget -->
<script id="chatway" async="true" src="https://cdn.chatway.app/widget.js?id=d0OdliXFm4Bl"></script>
</body>
</html>
