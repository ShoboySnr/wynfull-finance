@extends('layouts.client')

@section('title', 'Resource Library')
@section('page-title', 'Resource Library')

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
                        $totalModules = $collection->modules->count();
                        $completedModules = $collection->modules->filter(fn($module) => !$module->completions->isEmpty())->count();
                        $completionPercentage = ($totalModules > 0) ? round(($completedModules / $totalModules) * 100) : 0;
                        $isCollectionComplete = ($totalModules > 0 && $completedModules === $totalModules);
                        $isLocked = !$previousCollectionComplete;

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
                                <div class="progress-fill" style="width: {{ $isLocked ? 0 : $completionPercentage }}%"></div>
                            </div>
                            <span class="progress-text">
                                @if($isLocked)
                                    Complete previous phase to unlock
                                @else
                                    {{ $completedModules }}/{{ $totalModules }} modules • {{ $completionPercentage }}% complete
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
                                    $linkUrl = '#';
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
                                        <span class="resource-tag {{ strtolower($module->type) }}">{{ Str::ucfirst($module->type) }}</span>
                                    </div>
                                    @if($isLocked)
                                        <button class="module-btn locked" disabled>Locked</button>
                                    @else
                                        <a href="{{ $linkUrl }}"
                                           target="_blank"
                                           class="module-btn {{ $isModuleComplete ? 'secondary' : 'primary' }} mark-complete-btn"
                                           data-module-id="{{ $module->id }}"
                                           data-type="{{ $module->type }}"
                                           data-title="{{ $module->title }}">
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
            </div>
        </div>

        <div id="tools-templates" class="resource-tab-content">
            <div class="tools-grid">
                @forelse ($toolsAndTemplates as $module)
                    @php
                        $isModuleComplete = !$module->completions->isEmpty();
                        $moduleIconClass = match ($module->type) {
                            'template' => 'fa-file-alt', 'pdf' => 'fa-file-pdf',
                            'word' => 'fa-file-word', 'excel' => 'fa-file-excel',
                            default => 'fa-tools',
                        };
                        $linkUrl = '#';
                        if ($module->type === 'video' && $module->video_link) {
                            $linkUrl = $module->video_link;
                        } elseif ($module->file_path) {
                            $linkUrl = asset('storage/' . $module->file_path);
                        }
                    @endphp
                    <div class="tool-card {{ $isModuleComplete ? 'completed' : '' }}" data-module-id="{{ $module->id }}">
                        <div class="tool-accent"></div>
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
                        <a href="{{ $linkUrl }}"
                           target="_blank"
                           class="tool-download-btn mark-complete-btn {{ $isModuleComplete ? 'secondary' : 'primary' }}"
                           data-module-id="{{ $module->id }}"
                           data-type="{{ $module->type }}"
                           data-title="{{ $module->title }}">
                            <i class="fas fa-eye"></i>
                            {{ $isModuleComplete ? 'View Again' : 'View' }}
                        </a>
                    </div>
                @empty
                    <div class="no-resources-message" style="grid-column: 1 / -1;">
                        <p>No tools or templates are available at this time.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Resource Viewer Modal --}}
    <div class="modal-overlay" id="resourceViewerModal">
        <div class="modal-content viewer-modal-content">
            <div class="modal-header">
                <h2 id="viewerTitle">Resource Title</h2>
                <button class="modal-close" id="closeViewerModal">&times;</button>
            </div>
            <div class="modal-body viewer-body">
                <div id="viewerContainer" class="viewer-container"></div>
                <div id="viewerFallback" style="display:none; text-align: center; padding: 20px;">
                    <p class="mb-3">This file type cannot be previewed directly.</p>
                    <a href="#" id="fallbackDownloadLink" class="btn-primary" target="_blank" download>
                        <i class="fas fa-download"></i> Download File
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/client-resources.js') }}"></script>
@endpush
