@extends('layouts.admin')

@section('title', 'Manage Modules - ' . $collection->title)
@section('page-title', 'Manage Modules')

@section('content')
    <div class="page-content" id="manage-modules">
        {{-- START: Back Link and Collection Header --}}
        <div class="collection-header-bar">
            <a href="{{ route('admin.resources') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Collections</a>
            <div class="collection-info-header">
                <div class="collection-icon-large">
                    <i class="fas {{ $collection->icon_class ?? 'fa-folder-open' }}"></i>
                </div>
                <div class="collection-details-header">
                    <h1>{{ $collection->title }}</h1>
                    <p>{{ $collection->description }}</p>
                    {{-- Button to trigger the Edit Collection modal --}}
                    <button class="btn-secondary btn-sm editCollectionBtn"
                            data-id="{{ $collection->id }}"
                            data-title="{{ $collection->title }}"
                            data-description="{{ $collection->description }}"
                            data-icon_class="{{ $collection->icon_class }}"
                            data-action="{{ route('admin.resources.collection.update', $collection->id) }}">
                        <i class="fas fa-edit"></i> Edit Collection Details
                    </button>
                </div>
            </div>
            <button class="btn-primary" id="addModuleBtn">
                <i class="fas fa-plus"></i> Add New Module
            </button>
        </div>
        {{-- END: Back Link and Collection Header --}}

        {{-- START: Success and Error Messages --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        {{-- Error display specific to module actions --}}
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong class="font-bold">Oops! Something went wrong.</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- END: Success and Error Messages --}}

        {{-- START: Module List --}}
        <div class="module-list-container">
            <h2>Modules in this Collection</h2>
            <div class="module-list">
                {{-- Use $collection->modules (loaded in the controller) --}}
                @forelse ($collection->modules as $module)
                    @php
                        // Determine the icon class based on module type
                        $iconClass = match ($module->type) {
                            'template' => 'fa-file-alt',
                            'pdf' => 'fa-file-pdf',
                            'word' => 'fa-file-word',
                            'excel' => 'fa-file-excel',
                            'video' => 'fa-video',
                            default => 'fa-file',
                        };
                    @endphp
                    <div class="module-item" data-id="{{ $module->id }}">
                        <div class="module-icon">
                            <i class="fas {{ $iconClass }}"></i>
                        </div>
                        <div class="module-info">
                            <h3>{{ $module->title }}</h3>
                            <p>{{ Str::limit($module->description, 120) }}</p>
                            <span class="resource-type">{{ Str::ucfirst($module->type) }}</span>
                        </div>
                        <div class="module-actions">
                            <button class="btn-secondary btn-sm editModuleBtn"
                                    data-id="{{ $module->id }}"
                                    data-title="{{ $module->title }}"
                                    data-description="{{ $module->description }}"
                                    data-type="{{ $module->type }}"
                                    data-video_link="{{ $module->video_link }}"
                                    data-file_name="{{ $module->file_name }}"
                                    data-action="{{ route('admin.resources.collection.modules.update', [$collection, $module]) }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('admin.resources.collection.modules.destroy', [$collection, $module]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this module?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="no-modules-message">
                        <p>This collection doesn't have any modules yet. Click "Add New Module" to add one.</p>
                    </div>
                @endforelse
            </div>
        </div>
        {{-- END: Module List --}}
    </div>

    {{-- Modals (Add Module, Edit Module, Edit Collection) --}}
    @include('admin.resources.modules.partials._add-module-modal')
    @include('admin.resources.modules.partials._edit-module-modal')
    @include('admin.resources.partials._edit-collection-modal')

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/admin-modules.js') }}"></script>
@endpush
