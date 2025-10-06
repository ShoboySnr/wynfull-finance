@extends('layouts.guest')

@section('title', 'Level up with coaching')

@section('content')
    <section class="bg-gradient-to-b from-white to-gray-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                    Learn faster with a <span class="text-gray-700">Coach</span>
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Clients (students) match with expert coaches (mentors) to follow structured plans,
                    book sessions, exchange resources, and keep momentum with messaging & calls.
                </p>
                <div class="mt-8 flex gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-white hover:bg-black">
                        Get Started
                    </a>
                    <a href="#features" class="inline-flex items-center rounded-md border border-gray-300 px-5 py-3 hover:bg-gray-50">
                        Explore Features
                    </a>
                </div>
            </div>

            <div class="bg-white border rounded-xl p-6 shadow-sm">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="text-2xl font-semibold">1:1</div>
                        <div class="text-sm text-gray-500">Coaching</div>
                    </div>
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="text-2xl font-semibold">Calendar</div>
                        <div class="text-sm text-gray-500">Scheduling</div>
                    </div>
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="text-2xl font-semibold">Chat</div>
                        <div class="text-sm text-gray-500">Messaging</div>
                    </div>
                    <div class="p-4 rounded-lg bg-gray-50 col-span-3">
                        <div class="text-2xl font-semibold">Resources</div>
                        <div class="text-sm text-gray-500">Uploads & Docs</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold">Everything you need to learn</h2>
            <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ([
                  ['title'=>'Structured Plans','desc'=>'Follow step-by-step programs defined by your coach.'],
                  ['title'=>'Smart Scheduling','desc'=>'See coach availability in your timezone and book instantly.'],
                  ['title'=>'Messaging & Calls','desc'=>'Stay connected with chat and integrated calls.'],
                  ['title'=>'Resource Library','desc'=>'Exchange documents, links, and notes securely.'],
                  ['title'=>'Progress Tracking','desc'=>'Track sessions, goals, and milestones.'],
                  ['title'=>'Coach Matching','desc'=>'Get paired based on your plan and goals.'],
                ] as $f)
                    <div class="bg-white border rounded-xl p-5 shadow-sm">
                        <div class="text-lg font-medium">{{ $f['title'] }}</div>
                        <p class="mt-2 text-sm text-gray-600">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
