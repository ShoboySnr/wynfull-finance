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
</head>
<body class="antialiased auth-body">

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

</body>
</html>
