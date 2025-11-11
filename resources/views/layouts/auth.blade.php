<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Wynfull Finance')</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @stack('styles')
    
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
<body class="antialiased auth-body">
    <button class="theme-toggle-auth" id="themeToggle" title="Toggle Dark/Light Mode">
        <i class="fas fa-moon"></i>
    </button>
<div class="auth-layout-container">
    <!-- Logo Header -->
    <div class="auth-brand-header">
        <a href="{{ url('/') }}" class="brand-link">
            <img src="{{ asset('assets/img/wynfull-logo.png') }}" alt="Wynfull Finance Logo" class="brand-logo">
            <div class="brand-text">
                <h1>Wynfull</h1>
                <small>Finance</small>
            </div>
        </a>
    </div>

    @yield('content')
</div>

<script src="{{ asset('assets/js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
