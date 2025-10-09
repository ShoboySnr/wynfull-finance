<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Wynfull</title>

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coach-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/client-styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
