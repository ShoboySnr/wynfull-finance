<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Wynfull')</title>

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    
    <script>
        // Apply theme immediately to prevent flash
        (function() {
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const systemTheme = systemPrefersDark ? 'dark' : 'light';
            const savedTheme = localStorage.getItem('wynfullTheme') || systemTheme;
            
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.body.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body class="antialiased">

<!-- Theme Toggle Button -->
<button class="theme-toggle-guest" id="themeToggle" title="Toggle Dark/Light Mode">
    <i class="fas fa-moon"></i>
</button>

@yield('content')

<script src="{{ asset('assets/js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
