<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Wynfull</title>

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coach-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/client-styles.css') }}">

    @stack('styles')
</head>
<body class="antialiased">
@yield('content')

<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/client.js') }}"></script>
<script src="{{ asset('assets/js/coach.js') }}"></script>

@stack('scripts')
</body>
</html>
