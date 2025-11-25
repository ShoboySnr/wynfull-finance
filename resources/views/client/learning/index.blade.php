@extends('layouts.client')

@section('title', $module->title . ' - ' . $collection->title)
@section('page-title', 'Learning Center')

@section('content')
    <div class="learning-wrapper">
        {{-- Sidebar: Table of Contents --}}
        <aside class="learning-sidebar">
            <div class="sidebar-header">
                <a href="{{ route('resources.library') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Library
                </a>
                <h2 class="collection-title">{{ $collection->title }}</h2>
                <div class="progress-container">
                    @php
                        $total = $collection->modules->count();
                        $completed = $collection->modules->filter(fn($m) => $m->completions->isNotEmpty())->count();
                        $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                    @endphp
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $percent }}%"></div>
                    </div>
                    <span class="progress-text">{{ $percent }}% Complete</span>
                </div>
            </div>

            <div class="module-list-nav">
                @foreach($collection->modules as $mod)
                    @php
                        $isActive = $mod->id === $module->id;
                        $isComplete = $mod->completions->isNotEmpty();
                        $icon = $isComplete ? 'fa-check-circle text-green' : ($isActive ? 'fa-play-circle' : 'fa-circle');
                        $itemClass = $isActive ? 'active' : '';
                    @endphp
                    <a href="{{ route('resources.learn', ['resourceCollection' => $collection->id, 'resourceModule' => $mod->id]) }}"
                       class="module-nav-item {{ $itemClass }} {{ $isComplete ? 'completed' : '' }}">
                        <div class="status-icon">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <div class="module-meta">
                            <span class="module-title">{{ $mod->title }}</span>
                            <span class="module-type">{{ ucfirst($mod->type) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </aside>

        {{-- Main Content Area --}}
        <main class="learning-content">
            <div class="content-header">
                <h1>{{ $module->title }}</h1>
                @if($module->description)
                    <p>{{ $module->description }}</p>
                @endif
            </div>

            <div class="content-viewer" id="contentViewer">
                {{-- Dynamic Content Injection --}}
                @php
                    $fileUrl = $module->file_path ? asset('storage/' . $module->file_path) : null;
                    // Handle localhost URL
                    $isLocal = in_array(request()->getHost(), ['localhost', '127.0.0.1']);
                @endphp

                @if($module->type === 'video')
                    @if($module->video_link)
                        {{-- Embed Logic for YouTube/Vimeo --}}
                        @if(Str::contains($module->video_link, ['youtube.com', 'youtu.be']))
                            @php
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $module->video_link, $match);
                                $videoId = $match[1] ?? null;
                            @endphp
                            <div class="video-wrapper">
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                            </div>
                        @elseif(Str::contains($module->video_link, 'vimeo.com'))
                            @php $videoId = (int) substr(parse_url($module->video_link, PHP_URL_PATH), 1); @endphp
                            <div class="video-wrapper">
                                <iframe src="https://player.vimeo.com/video/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                            </div>
                        @endif
                    @elseif($fileUrl)
                        <video controls class="native-video">
                            <source src="{{ $fileUrl }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif

                @elseif($module->type === 'pdf' && $fileUrl)
                    <iframe src="{{ $fileUrl }}#toolbar=0" class="doc-frame"></iframe>

                @elseif(in_array($module->type, ['word', 'excel', 'template']) && $fileUrl)
                    @if($isLocal)
                        <div class="viewer-fallback">
                            <i class="fas fa-file-alt"></i>
                            <h3>Preview unavailable in Local Development</h3>
                            <p>Microsoft Office Viewer requires a public URL.</p>
                            <a href="{{ $fileUrl }}" class="btn-primary" download>Download File</a>
                        </div>
                    @else
                        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}" class="doc-frame"></iframe>
                    @endif
                @endif
            </div>

            <div class="content-footer">
                <div class="nav-buttons">
                    @if($prevModule)
                        <a href="{{ route('resources.learn', ['resourceCollection' => $collection->id, 'resourceModule' => $prevModule->id]) }}" class="btn-secondary">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    @else
                        <div></div> {{-- Spacer --}}
                    @endif

                    @if($nextModule)
                        {{-- The Next button triggers completion via JS --}}
                        <a href="{{ route('resources.learn', ['resourceCollection' => $collection->id, 'resourceModule' => $nextModule->id]) }}"
                           class="btn-primary mark-complete-btn"
                           data-module-id="{{ $module->id }}">
                            Next Lesson <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <a href="{{ route('resources.library') }}"
                           class="btn-primary mark-complete-btn"
                           data-module-id="{{ $module->id }}">
                            Finish Course <i class="fas fa-check"></i>
                        </a>
                    @endif
                </div>
            </div>
        </main>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/learning-mode.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/learning-mode.js') }}"></script>
@endpush
