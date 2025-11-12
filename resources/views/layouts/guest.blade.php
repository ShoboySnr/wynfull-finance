<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Wynfull')</title>

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
<body class="antialiased">

@yield('content')

<script src="{{ asset('assets/js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
