@extends('layouts.app')
@section('title', 'Coach Dashboard')

@section('sidebar')
    @include('partials.sidebar-coach')
@endsection

@section('content')
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Coach Dashboard</h1>
        <div class="text-sm text-gray-500">Welcome back, <span class="font-medium">Coach</span></div>
    </div>

    {{-- Stat cards --}}
    <section class="mt-6 grid md:grid-cols-3 gap-4">
        @foreach ([
          ['kpi'=>'Assigned Clients','val'=>'8','hint'=>'active'],
          ['kpi'=>'Upcoming Sessions','val'=>'4','hint'=>'next 7 days'],
          ['kpi'=>'Pending Approvals','val'=>'2','hint'=>'resources awaiting review'],
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
        {{-- Availability --}}
        <div class="lg:col-span-2 bg-white border rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium">Availability</h2>
                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Manage</a>
            </div>
            <div class="mt-4 grid sm:grid-cols-2 gap-4">
                @foreach ([['day'=>'Tue','slots'=>['09:00','10:30','14:00']],
                           ['day'=>'Thu','slots'=>['11:00','13:00']]] as $a)
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-500">{{ $a['day'] }}</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($a['slots'] as $slot)
                                <span class="px-2 py-1 text-xs border rounded-md bg-gray-50">{{ $slot }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Add Slot</button>
                <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Clear Day</button>
            </div>
        </div>

        {{-- Messages --}}
        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <h2 class="text-lg font-medium">Messages</h2>
            <ul class="mt-3 space-y-3">
                @foreach ([['name'=>'Client Joy','snippet'=>'Thank you for the feedback!'],
                           ['name'=>'Client Dayo','snippet'=>'Shared the draft document.']] as $m)
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
              ['title'=>'Week 2 Worksheet.pdf','status'=>'approved'],
              ['title'=>'Reading List','status'=>'submitted_for_review'],
              ['title'=>'Project Brief.docx','status'=>'draft'],
            ] as $r)
                <article class="border rounded-lg p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-medium">{{ $r['title'] }}</div>
                            <div class="text-xs text-gray-500 mt-1">Updated recently</div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-md border
              @class([
                'bg-green-50 border-green-200 text-green-700' => $r['status']==='approved',
                'bg-yellow-50 border-yellow-200 text-yellow-700' => $r['status']==='submitted_for_review',
                'bg-gray-50 border-gray-200 text-gray-700' => $r['status']==='draft',
              ])
            ">{{ str_replace('_',' ',$r['status']) }}</span>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">View</button>
                        <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Edit</button>
                        <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Submit</button>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-4">
            <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">Upload New</button>
        </div>
    </section>
@endsection
