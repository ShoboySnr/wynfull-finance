@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <section class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white border rounded-xl p-6 shadow-sm">
            <h1 class="text-2xl font-semibold">Welcome back</h1>
            <p class="mt-1 text-sm text-gray-600">Sign in to your account.</p>

            <form class="mt-6 space-y-4" method="POST" action="#">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" required class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-0">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-0">
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                        Remember me
                    </label>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Forgot password?</a>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md bg-gray-900 px-5 py-3 text-white hover:bg-black">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                No account? <a href="#" class="text-gray-900 hover:underline">Create one</a>
            </div>
        </div>
    </section>
@endsection
