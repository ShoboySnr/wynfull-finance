@extends('layouts.app')

@section('title', 'My Resources')

@section('content')
    <div class="page-content" id="resources">
        <div class="page-header">
            <h1>Resources</h1>
            <p>Manage educational materials and templates for your clients</p>
        </div>

        {{-- START: Success and Error Messages --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

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


        <div class="resources-controls">
            <div class="resource-tabs">
                <a href="{{ route('coach.resources') }}" class="resource-tab {{ !request('filter') || request('filter') === 'all' ? 'active' : '' }}">All Resources</a>
                <a href="{{ route('coach.resources', ['filter' => 'template']) }}" class="resource-tab {{ request('filter') == 'template' ? 'active' : '' }}">Templates</a>
                <a href="{{ route('coach.resources', ['filter' => 'word']) }}" class="resource-tab {{ request('filter') == 'word' ? 'active' : '' }}">Word</a>
                <a href="{{ route('coach.resources', ['filter' => 'pdf']) }}" class="resource-tab {{ request('filter') == 'pdf' ? 'active' : '' }}">PDF</a>
                <a href="{{ route('coach.resources', ['filter' => 'excel']) }}" class="resource-tab {{ request('filter') == 'excel' ? 'active' : '' }}">Excel</a>
                <a href="{{ route('coach.resources', ['filter' => 'video']) }}" class="resource-tab {{ request('filter') == 'video' ? 'active' : '' }}">Videos</a>
            </div>
            <button class="btn-primary" id="addResourceBtn">
                <i class="fas fa-plus"></i>
                Add Resource
            </button>
        </div>

        <div class="resources-grid">
            @forelse ($resources as $resource)
                <div class="resource-item" data-type="{{ $resource->type }}">
                    <div class="resource-icon">
                        <i class="fas {{ $resource->icon_class }}"></i>
                    </div>
                    <div class="resource-info">
                        <h3>{{ $resource->title }}</h3>
                        <p>{{ Str::limit($resource->description, 100) }}</p>
                        <div class="resource-meta">
                            <span class="resource-type">{{ Str::ucfirst($resource->type) }}</span>
                            <span class="status-badge status-{{ $resource->status }}">{{ Str::ucfirst($resource->status) }}</span>
                        </div>
                    </div>
                    <div class="resource-actions">
                        <button class="btn-secondary editResourceBtn"
                                data-id="{{ $resource->id }}"
                                data-title="{{ $resource->title }}"
                                data-description="{{ $resource->description }}"
                                data-type="{{ $resource->type }}"
                                data-video_link="{{ $resource->video_link }}"
                                data-action="{{ route('coach.resources.update', $resource) }}">
                            Edit
                        </button>
                        {{-- Add a share button if needed --}}
                    </div>
                </div>
            @empty
                <div class="no-resources-message">
                    <p>You haven't uploaded any resources yet. Click "Add Resource" to get started!</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Links --}}
        <div class="pagination-container">
            {{ $resources->links() }}
        </div>
    </div>


    <!-- START: Add Resource Modal -->
    <div class="modal-overlay" id="addResourceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Resource</h2>
                <button class="modal-close" id="addResourceModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('coach.resources.store') }}" method="POST" enctype="multipart/form-data" id="addResourceForm">
                    @csrf

                    {{-- START: Hidden input to detect validation errors --}}
                    @if ($errors->any())
                        <input type="hidden" name="has_add_errors" value="true">
                    @endif
                    {{-- END: Hidden input to detect validation errors --}}

                    <div class="form-group">
                        <label for="add_title">Title</label>
                        <input type="text" id="add_title" name="title" class="form-input" placeholder="e.g., Monthly Budget Template" value="{{ old('title') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="add_description">Description</label>
                        <textarea id="add_description" name="description" class="form-textarea" rows="3" placeholder="A brief summary of what this resource is for.">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="add_type">Resource Type</label>
                        <select id="add_type" name="type" class="form-select" required>
                            <option value="template" {{ old('type') == 'template' ? 'selected' : '' }}>Template</option>
                            <option value="word" {{ old('type') == 'word' ? 'selected' : '' }}>Word Document</option>
                            <option value="pdf" {{ old('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="excel" {{ old('type') == 'excel' ? 'selected' : '' }}>Excel</option>
                            <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                        </select>
                    </div>

                    <div class="form-group file-field-container">
                        <label for="add_file">Upload File</label>
                        <div class="file-drop-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop your file here, or <span class="file-browse-link">browse</span></p>
                            <input type="file" id="add_file" name="file" class="file-input">
                            <p class="file-name-display"></p>
                        </div>
                    </div>
                    <div class="form-group video-link-field-container" style="display: none;">
                        <label for="add_video_link">Video Link</label>
                        <input type="url" id="add_video_link" name="video_link" class="form-input" placeholder="https://youtube.com/watch?v=..." value="{{ old('video_link') }}">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="addResourceModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Upload Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: Add Resource Modal -->

    <!-- START: Edit Resource Modal -->
    <div class="modal-overlay" id="editResourceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Resource</h2>
                <button class="modal-close" id="editResourceModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" id="editResourceForm">
                    @csrf
                    @method('PUT')

                    {{-- START: Hidden input to detect validation errors --}}
                    @if ($errors->any())
                        <input type="hidden" name="has_edit_errors" value="true">
                    @endif
                    {{-- END: Hidden input to detect validation errors --}}

                    <div class="form-group">
                        <label for="edit_title">Title</label>
                        <input type="text" id="edit_title" name="title" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea id="edit_description" name="description" class="form-textarea" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_type">Resource Type</label>
                        <select id="edit_type" name="type" class="form-select" required>
                            <option value="template">Template</option>
                            <option value="word">Word Document</option>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="video">Video</option>
                        </select>
                    </div>

                    <div class="form-group file-field-container">
                        <label for="edit_file">Upload New File (Optional)</label>
                        <div class="file-drop-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop a new file, or <span class="file-browse-link">browse</span> to replace</p>
                            <input type="file" id="edit_file" name="file" class="file-input">
                            <p class="file-name-display"></p>
                        </div>
                    </div>
                    <div class="form-group video-link-field-container" style="display: none;">
                        <label for="edit_video_link">Video Link</label>
                        <input type="url" id="edit_video_link" name="video_link" class="form-input" placeholder="https://youtube.com/watch?v=...">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="editResourceModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: Edit Resource Modal -->
@endsection

@push('scripts')
{{--    <script src="{{ asset('assets/js/coach-script.js') }}"></script>--}}
    <script src="{{ asset('assets/js/resources.js') }}"></script>
@endpush

