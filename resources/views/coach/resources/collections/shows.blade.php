@extends('layouts.app')

@section('title', 'Manage Modules - ' . $resourceCollection->title)

@section('content')
    <div class="page-content" id="manage-modules">
        {{-- START: Back Link and Collection Header --}}
        <div class="collection-header-bar">
            <a href="{{ route('coach.resources') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Collections</a>
            <div class="collection-info-header">
                <div class="collection-icon-large">
                    <i class="fas {{ $resourceCollection->icon_class ?? 'fa-folder-open' }}"></i>
                </div>
                <div class="collection-details-header">
                    <h1>{{ $resourceCollection->title }}</h1>
                    <p>{{ $resourceCollection->description }}</p>
                    {{-- Button to trigger the Edit Collection modal (from resources.blade.php) --}}
                    <button class="btn-secondary btn-sm editCollectionBtn"
                            data-id="{{ $resourceCollection->id }}"
                            data-title="{{ $resourceCollection->title }}"
                            data-description="{{ $resourceCollection->description }}"
                            data-icon_class="{{ $resourceCollection->icon_class }}"
                            data-action="{{ route('coach.resources.collection.update', $resourceCollection) }}">
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
                @forelse ($resourceCollection->modules as $module)
                    @php
                        // Determine the icon class based on module type
                        $iconClass = match ($module->type) {
                            'template' => 'fa-file-alt', // Generic template icon
                            'pdf' => 'fa-file-pdf',
                            'word' => 'fa-file-word',
                            'excel' => 'fa-file-excel',
                            'video' => 'fa-video',
                            default => 'fa-file', // Default icon for unknown types
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
                                    data-action="{{ route('coach.resources.modules.update', [$resourceCollection, $module]) }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('coach.resources.modules.destroy', [$resourceCollection, $module]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this module?');"> {{-- Route needed --}}
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

    {{-- START: Add New Module Modal --}}
    <div class="modal-overlay" id="addModuleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Module to "{{ $resourceCollection->title }}"</h2>
                <button class="modal-close" id="addModuleModalClose">&times;</button>
            </div>
            <div class="modal-body">
                {{-- Point this form to the route for storing MODULES --}}
                <form action="{{ route('coach.resources.modules.store', $resourceCollection) }}" method="POST" enctype="multipart/form-data" id="addModuleForm">
                    @csrf
                    {{-- Hidden input to detect validation errors for this specific modal --}}
                    @if ($errors->any() && old('form_type') === 'add_module')
                        <input type="hidden" name="has_add_module_errors" value="true">
                    @endif
                    <input type="hidden" name="form_type" value="add_module">

                    <div class="form-group">
                        <label for="add_module_title">Module Title</label>
                        <input type="text" id="add_module_title" name="title" class="form-input" placeholder="e.g., Understanding Your Paycheck" value="{{ old('title') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="add_module_description">Description</label>
                        <textarea id="add_module_description" name="description" class="form-textarea" rows="3" placeholder="A brief summary of this module.">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="add_module_type">Module Type</label>
                        <select id="add_module_type" name="type" class="form-select module-type-select" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="template" {{ old('type') == 'template' ? 'selected' : '' }}>Template</option>
                            <option value="word" {{ old('type') == 'word' ? 'selected' : '' }}>Word Document</option>
                            <option value="pdf" {{ old('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="excel" {{ old('type') == 'excel' ? 'selected' : '' }}>Excel</option>
                            <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video Link</option>
                            {{-- Add other types if needed --}}
                        </select>
                    </div>

                    {{-- File Upload Field (Conditional) --}}
                    <div class="form-group file-field-container" style="display: none;"> {{-- Initially hidden --}}
                        <label for="add_module_file">Upload File</label>
                        <div class="file-drop-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop file, or <span class="file-browse-link">browse</span></p>
                            <input type="file" id="add_module_file" name="file" class="file-input">
                            <p class="file-name-display"></p>
                        </div>
                    </div>
                    {{-- Video Link Field (Conditional) --}}
                    <div class="form-group video-link-field-container" style="display: none;"> {{-- Initially hidden --}}
                        <label for="add_module_video_link">Video Link</label>
                        <input type="url" id="add_module_video_link" name="video_link" class="form-input" placeholder="https://youtube.com/watch?v=..." value="{{ old('video_link') }}">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="addModuleModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Add Module</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: Add New Module Modal --}}

    {{-- START: Edit Module Modal --}}
    <div class="modal-overlay" id="editModuleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Module</h2>
                <button class="modal-close" id="editModuleModalClose">&times;</button>
            </div>
            <div class="modal-body">
                {{-- Action set dynamically by JS --}}
                <form method="POST" enctype="multipart/form-data" id="editModuleForm">
                    @csrf
                    @method('PUT')
                    @if ($errors->any() && old('form_type') === 'edit_module')
                        <input type="hidden" name="has_edit_module_errors" value="true">
                    @endif
                    <input type="hidden" name="form_type" value="edit_module">

                    <div class="form-group">
                        <label for="edit_module_title">Module Title</label>
                        <input type="text" id="edit_module_title" name="title" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_module_description">Description</label>
                        <textarea id="edit_module_description" name="description" class="form-textarea" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_module_type">Module Type</label>
                        <select id="edit_module_type" name="type" class="form-select module-type-select" required>
                            <option value="template">Template</option>
                            <option value="word">Word Document</option>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="video">Video Link</option>
                        </select>
                    </div>

                    {{-- File Upload Field (Conditional) --}}
                    <div class="form-group file-field-container" style="display: none;">
                        <label for="edit_module_file">Upload New File (Optional)</label>
                        <p class="current-file-info">Current file: <span id="edit_current_file_name">None</span></p>
                        <div class="file-drop-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop to replace, or <span class="file-browse-link">browse</span></p>
                            <input type="file" id="edit_module_file" name="file" class="file-input">
                            <p class="file-name-display"></p>
                        </div>
                    </div>
                    {{-- Video Link Field (Conditional) --}}
                    <div class="form-group video-link-field-container" style="display: none;">
                        <label for="edit_module_video_link">Video Link</label>
                        <input type="url" id="edit_module_video_link" name="video_link" class="form-input" placeholder="https://youtube.com/watch?v=...">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="editModuleModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: Edit Module Modal --}}

    @include('coach.resources.partials._edit-collection-modal')

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/manage-modules.js') }}"></script>
     <script src="{{ asset('assets/js/resources.js') }}"></script>
@endpush
