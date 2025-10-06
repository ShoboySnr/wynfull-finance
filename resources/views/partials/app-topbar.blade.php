<header class="h-16 border-b bg-white">
    <div class="h-full mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <button class="lg:hidden p-2 rounded hover:bg-gray-100" x-on:click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                <!-- icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="{{ route('home') }}" class="font-semibold text-lg">Wynfull</a>
        </div>

        <div class="flex items-center gap-3">
            <input type="search" class="hidden sm:block w-64 rounded-md border-gray-300 focus:border-gray-900 focus:ring-0 text-sm"
                   placeholder="Search...">
            <a href="{{ route('dashboard.client') }}" class="text-sm text-gray-600 hover:text-gray-900">Client</a>
            <a href="{{ route('dashboard.coach') }}" class="text-sm text-gray-600 hover:text-gray-900">Coach</a>
            <a href="{{ route('login') }}" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Login</a>
        </div>
    </div>
</header>
