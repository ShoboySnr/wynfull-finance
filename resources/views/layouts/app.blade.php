<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }" xmlns:x-on="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Wynfull</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="h-screen bg-gray-50 text-gray-900 antialiased">
{{-- Topbar --}}
@include('partials.app-topbar')

<div class="flex h-[calc(100vh-64px)]">
    {{-- Sidebar --}}
    <aside class="hidden lg:block w-64 border-r bg-white">
        @yield('sidebar')
    </aside>

    {{-- Mobile sidebar --}}
    <div class="lg:hidden" x-show="sidebarOpen" x-transition>
        <div class="fixed inset-0 z-40 flex">
            <div class="fixed inset-0 bg-black/40" x-on:click="sidebarOpen=false"></div>
            <aside class="relative z-50 w-72 max-w-full bg-white border-r">
                @yield('sidebar')
            </aside>
        </div>
    </div>

    {{-- Content --}}
    <main class="flex-1 overflow-y-auto">
        <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
