@extends('layouts.app')
@section('title', 'Client Dashboard')

@section('sidebar')
    @include('partials.sidebar-client')
@endsection

@section('content')
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Client Dashboard</h1>
        <div class="text-sm text-gray-500">Welcome back, <span class="font-medium">Client</span></div>
    </div>

    {{-- Stat cards --}}
    <section class="mt-6 grid md:grid-cols-3 gap-4">
        @foreach ([
          ['kpi'=>'Next Session','val'=>'Tue 2:00 PM','hint'=>'with Coach Ada'],
          ['kpi'=>'Unread Messages','val'=>'3','hint'=>'from 2 threads'],
          ['kpi'=>'Resources','val'=>'12','hint'=>'approved & available'],
        ] as $c)
            <div class="bg-white border rounded-xl p-5 shadow-sm">
                <div class="text-sm text-gray-500">{{ $c['kpi'] }}</div>
                <div class="mt-1 text-2xl font-semibold">{{ $c['val'] }}</div>
                <div class="text-xs text-gray-500">{{ $c['hint'] }}</div>
            </div>
        @endforeach
    </section>

    {{-- Two-column main --}}
    <section class="mt-6 grid lg:grid-cols-3 gap-6">
        {{-- Schedule --}}
        <div class="lg:col-span-2 bg-white border rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium">Schedule</h2>
                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">View calendar</a>
            </div>
            <div class="mt-4 grid sm:grid-cols-2 gap-4">
                @foreach ([['day'=>'Tue','time'=>'2:00 PM','coach'=>'Ada'],
                           ['day'=>'Thu','time'=>'11:00 AM','coach'=>'Ada']] as $s)
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-500">{{ $s['day'] }}</div>
                        <div class="mt-1 text-lg font-medium">{{ $s['time'] }}</div>
                        <div class="text-xs text-gray-500">with Coach {{ $s['coach'] }}</div>
                        <div class="mt-3 flex gap-2">
                            <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Join Call</button>
                            <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Reschedule</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Messages --}}
        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <h2 class="text-lg font-medium">Messages</h2>
            <ul class="mt-3 space-y-3">
                @foreach ([['name'=>'Coach Ada','snippet'=>'Great progress! Let’s review your assignment…'],
                           ['name'=>'Coach Ben','snippet'=>'Reminder: upload your worksheet.']] as $m)
                    <li class="p-3 rounded-lg border hover:bg-gray-50">
                        <div class="font-medium">{{ $m['name'] }}</div>
                        <div class="text-sm text-gray-600 truncate">{{ $m['snippet'] }}</div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4">
                <a href="#" class="inline-flex items-center rounded-md border px-3 py-1.5 text-sm hover:bg-gray-50">Open Inbox</a>
            </div>
        </div>
    </section>

    {{-- Resources --}}
    <section class="mt-6 bg-white border rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium">Resources</h2>
            <div class="flex gap-2">
                <input type="search" class="w-56 rounded-md border-gray-300 focus:border-gray-900 focus:ring-0 text-sm" placeholder="Search resources…">
                <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Filter</button>
            </div>
        </div>
        <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ([
              ['title'=>'Week 2 Worksheet.pdf','meta'=>'PDF • 320 KB • Coach Ada'],
              ['title'=>'Reading List','meta'=>'Link • Updated yesterday'],
              ['title'=>'Project Brief.docx','meta'=>'DOCX • 54 KB • Coach Ada'],
            ] as $r)
                <article class="border rounded-lg p-4">
                    <div class="font-medium">{{ $r['title'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $r['meta'] }}</div>
                    <div class="mt-3 flex gap-2">
                        <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">View</button>
                        <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Download</button>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
