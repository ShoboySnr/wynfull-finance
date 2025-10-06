<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Wynfull')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<header class="border-b bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="font-semibold text-lg">Wynfull</a>
        <nav class="space-x-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Login</a>
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="mt-24 border-t bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 text-sm text-gray-500">
        © {{ date('Y') }} Wynfull. All rights reserved.
    </div>
</footer>

@stack('scripts')
</body>
</html>
