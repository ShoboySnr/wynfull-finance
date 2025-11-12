@extends('layouts.client')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <div id="resource-library" class="page-content">
        <div class="resource-header">
            <div class="resource-title">
                <h1>Resource Library</h1>
                <p>Your comprehensive financial education hub</p>
            </div>
            <div class="resource-controls">
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="resource-tabs">
            <button class="resource-tab active" data-tab="learning-phases">
                <i class="fas fa-graduation-cap"></i>
                Learning Phases
            </button>
            <button class="resource-tab" data-tab="tools-templates">
                <i class="fas fa-tools"></i>
                Tools &amp; Templates
            </button>
        </div>

        {{-- Learning Phases Tab --}}
        <div id="learning-phases" class="resource-tab-content active">
            <div class="phases-container" id="phasesContainer">
                @php $previousCollectionComplete = true;  @endphp
                @forelse ($collections as $collection)
                    @php
                        // Calculate Completion
                        $totalModules = $collection->modules->count();
                        $completedModules = $collection->modules->filter(fn($module) => !$module->completions->isEmpty())->count();
                        $completionPercentage = ($totalModules > 0) ? round(($completedModules / $totalModules) * 100) : 0;
                        $isCollectionComplete = ($totalModules > 0 && $completedModules === $totalModules);

                        // Determine Locking Status
                        $isLocked = !$previousCollectionComplete;

                        // Determine Collection Status Text/Icon
                        $statusClass = 'locked';
                        $statusIcon = 'fa-lock';
                        $statusText = 'Locked';
                        if (!$isLocked) {
                            if ($isCollectionComplete) {
                                $statusClass = 'completed'; $statusIcon = 'fa-check-circle'; $statusText = 'Completed';
                            } else {
                                $isInProgress = $completedModules > 0;
                                $statusClass = $isInProgress ? 'current' : 'not-started';
                                $statusIcon = $isInProgress ? 'fa-play-circle' : 'fa-circle';
                                $statusText = $isInProgress ? 'In Progress' : 'Not Started';
                            }
                        }
                    @endphp

                    <div class="phase-card {{ $isLocked ? 'locked' : '' }} {{ $statusClass }}"
                         data-collection-id="{{ $collection->id }}">
                        <div class="phase-header">
                            <div class="phase-icon">
                                <i class="fas {{ $collection->icon_class ?? 'fa-folder-open' }}"></i>
                            </div>
                            <div class="phase-info">
                                <h3>{{ $collection->title }}</h3>
                                <p>{{ $collection->description }}</p>
                            </div>
                            <div class="phase-status {{ $statusClass }}">
                                <i class="fas {{ $statusIcon }}"></i>
                                <span>{{ $statusText }}</span>
                            </div>
                        </div>

                        <div class="phase-progress">
                            <div class="progress-bar">
                                <div class="progress-fill"
                                     style="width: {{ $isLocked ? 0 : $completionPercentage }}%"></div>
                            </div>
                            <span class="progress-text">
                                @if($isLocked)
                                    Complete previous phase to unlock
                                @else
                                    {{ $completedModules }}/{{ $totalModules }} modules • {{ $completionPercentage }}%
                                    complete
                                @endif
                            </span>
                        </div>

                        <div class="phase-modules {{ $isLocked ? 'locked' : '' }}">
                            @forelse ($collection->modules as $module)
                                @php
                                    $isModuleComplete = !$module->completions->isEmpty();
                                    $moduleIconClass = match ($module->type) {
                                        'template' => 'fa-file-alt', 'pdf' => 'fa-file-pdf',
                                        'word' => 'fa-file-word', 'excel' => 'fa-file-excel',
                                        'video' => 'fa-video', default => 'fa-file',
                                    };
                                    $linkUrl = '#'; // Default
                                    if ($module->type === 'video' && $module->video_link) {
                                        $linkUrl = $module->video_link;
                                    } elseif ($module->file_path) {
                                        $linkUrl = asset('storage/' . $module->file_path);
                                    }
                                @endphp
                                <div class="module-item {{ $isModuleComplete ? 'completed' : '' }}"
                                     data-module-id="{{ $module->id }}">
                                    <div class="module-info">
                                        <i class="fas {{ $moduleIconClass }} module-type-icon"></i>
                                        <h4>{{ $module->title }}</h4>
                                        <p>{{ $module->description }}</p>
                                    </div>
                                    <div class="module-resources">
                                        <span
                                            class="resource-tag {{ strtolower($module->type) }}">{{ Str::ucfirst($module->type) }}</span>
                                    </div>
                                    @if($isLocked)
                                        <button class="module-btn locked" disabled>Locked</button>
                                    @else
                                        <a href="{{ $linkUrl }}"
                                           target="_blank"
                                           class="module-btn {{ $isModuleComplete ? 'secondary' : 'primary' }} mark-complete-btn"
                                           data-module-id="{{ $module->id }}">
                                            {{ $isModuleComplete ? 'View Again' : 'Open Resource' }}
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <p class="no-modules">No modules in this collection yet.</p>
                            @endforelse
                        </div>
                    </div>
                    @php $previousCollectionComplete = $isCollectionComplete; @endphp
                @empty
                    <div class="no-resources-message">
                        <p>No learning resources are available at this time.</p>
                    </div>
                @endforelse
                {{-- END: Dynamic Loop for Collections --}}
            </div>
        </div>
        <div id="tools-templates" class="resource-tab-content">
            <div class="tools-grid">
                @forelse ($toolsAndTemplates as $module)
                    @php
                        $isModuleComplete = !$module->completions->isEmpty();
//                        dd($isCollectionComplete);
                        $moduleIconClass = match ($module->type) {
                            'template' => 'fa-file-alt', 'pdf' => 'fa-file-pdf',
                            'word' => 'fa-file-word', 'excel' => 'fa-file-excel',
                            default => 'fa-tools', // Default for tools
                        };
                        $linkUrl = '#'; // Default
                        if ($module->type === 'video' && $module->video_link) {
                            $linkUrl = $module->video_link;
                        } elseif ($module->file_path) {
                            $linkUrl = asset('storage/' . $module->file_path);
                        }
                    @endphp
                    <div class="tool-card {{ $isModuleComplete ? 'completed' : '' }}" data-module-id="{{ $module->id }}">
                        <div class="tool-accent"></div>
{{--                         <button class="walkthrough-indicator"><i class="fas fa-play"></i></button>--}}
                        <div class="tool-icon">
                            <i class="fas {{ $moduleIconClass }}"></i>
                        </div>
                        <div class="tool-content">
                            <h3>{{ $module->title }}</h3>
                            <p>{{ $module->description }}</p>
                            <div class="tool-formats">
                                <span class="format-tag {{ strtolower($module->type) }}">{{ Str::ucfirst($module->type) }}</span>
                            </div>
                        </div>
                        {{-- Updated Download Button --}}
                        <a href="{{ $linkUrl }}"
                           target="_blank"
                           class="tool-download-btn mark-complete-btn {{ $isModuleComplete ? 'secondary' : 'primary' }}"
                           data-module-id="{{ $module->id }}">
                            <i class="fas fa-download"></i>
                            {{ $isModuleComplete ? 'Download Again' : 'Download' }}
                        </a>
                    </div>
{{--                    <div class="tool-card">--}}
{{--                        <div class="tool-accent"></div>--}}
{{--                        <button class="walkthrough-indicator" data-tool="monthly-budget-tracker"--}}
{{--                                title="Watch walkthrough video">--}}
{{--                            <i class="fas fa-play"></i>--}}
{{--                        </button>--}}
{{--                        <div class="tool-icon">--}}
{{--                            <i class="fas fa-file-excel"></i>--}}
{{--                        </div>--}}
{{--                        <div class="tool-content">--}}
{{--                            <h3>Monthly Budget Tracker</h3>--}}
{{--                            <p>Comprehensive Excel template for tracking income, expenses, and savings goals</p>--}}
{{--                            <div class="tool-formats">--}}
{{--                                <span class="format-tag excel">Excel Template</span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <button class="tool-download-btn">--}}
{{--                            <i class="fas fa-download"></i>--}}
{{--                            Download--}}
{{--                        </button>--}}
{{--                    </div>--}}
                @empty
                    <div class="no-resources-message" style="grid-column: 1 / -1;">
                        <p>No tools or templates are available at this time.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/client-resources.js') }}"></script>
@endpush
