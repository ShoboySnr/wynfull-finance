<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Wynfull</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coach-styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">

<div class="coach-dashboard-layout">
    <!-- Sidebar -->
    @include('partials/sidebar-coach')

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="header-right">
                <button class="theme-toggle" id="themeToggle" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="notification-btn" id="notificationBtn" style="display: none;">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </button>
            </div>
        </header>

        <div class="page active">
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </main>
</div>

<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/coach.js') }}"></script>

@stack('scripts')
</body>
</html>
