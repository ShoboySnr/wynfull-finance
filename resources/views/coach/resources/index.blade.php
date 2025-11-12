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
                <a href="{{ route('coach.resources') }}" class="resource-tab active">All Collections</a>
            </div>
            <button class="btn-primary" id="addResourceBtn">
                <i class="fas fa-plus"></i>
                Add New Collection
            </button>
        </div>

        <div class="resources-grid">
            @forelse ($resourceCollections as $collection)
                <div class="resource-collection-card">
                    <div>
                        <div class="collection-icon">
                            <i class="fas {{ $collection->icon_class ?? 'fa-folder' }}"></i>
                        </div>
                    </div>
                    <div class="collection-info">
                        <h3>{{ $collection->title }}</h3>
                        <p>{{ Str::limit($collection->description, 100) }}</p>
                        <div class="collection-meta">
                            <span class="module-count">{{ $collection->modules_count }} Modules</span>
                            <span
                                class="status-badge status-{{ $collection->status }}">{{ Str::ucfirst($collection->status) }}</span>
                            @if($collection->visibility === 'global')
                                <span class="status-badge status-admin">Admin</span>
                            @endif
                        </div>
                    </div>
                    <div class="collection-actions">
                        <a href="{{ route('coach.resources.collection.edit', $collection) }}" class="btn-primary">
                            Manage Modules
                        </a>
                        @if($collection->visibility !== 'global')
                            <button class="btn-secondary editCollectionBtn"
                                    data-id="{{ $collection->id }}"
                                    data-title="{{ $collection->title }}"
                                    data-description="{{ $collection->description }}"
                                    data-icon_class="{{ $collection->icon_class }}"
                                    data-action="{{ route('coach.resources.collection.update', $collection) }}">
                                Edit Details
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="no-resources-message">
                    <p>You haven't created any resource collections yet. Click "Add New Collection" to get started!</p>
                </div>
            @endforelse
        </div>
        {{-- Pagination Links --}}
        <div class="pagination-container">
            {{ $resourceCollections->links() }}
        </div>
    </div>


    {{-- START: Refactored Add Collection Modal --}}
    <div class="modal-overlay" id="addResourceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Resource Collection</h2>
                <button class="modal-close" id="addResourceModalClose">&times;</button>
            </div>
            <div class="modal-body">
                {{-- Point this form to the route for storing COLLECTIONS --}}
                <form action="{{ route('coach.resources.collection.store') }}" method="POST" id="addResourceForm">
                    @csrf
                    @if ($errors->any() && old('form_type') === 'add_collection')
                        <input type="hidden" name="has_add_errors" value="true">
                    @endif
                    <input type="hidden" name="form_type" value="add_collection"> {{-- To identify form on error --}}

                    <div class="form-group">
                        <label for="add_title">Collection Title</label>
                        <input type="text" id="add_title" name="title" class="form-input"
                               placeholder="e.g., Budgeting Basics" value="{{ old('title') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="add_description">Collection Description</label>
                        <textarea id="add_description" name="description" class="form-textarea" rows="3"
                                  placeholder="A brief summary of what this collection covers.">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="add_icon_class">Icon Class (Font Awesome)</label>
                        <input type="text" id="add_icon_class" name="icon_class" class="form-input"
                               placeholder="e.g., fa-piggy-bank" value="{{ old('icon_class', 'fa-folder-open') }}">
                        <small>Find icons at <a href="https://fontawesome.com/icons" target="_blank"
                                                rel="noopener noreferrer">Font Awesome</a> (use free icons).</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="addResourceModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Create Collection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: Refactored Add Collection Modal --}}

    {{-- START: Edit Collection Modal (Similar to Add, but with PUT method) --}}
    <div class="modal-overlay" id="editResourceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Resource Collection</h2>
                <button class="modal-close" id="editResourceModalClose">&times;</button>
            </div>
            <div class="modal-body">
                {{-- Action will be set dynamically by JS --}}
                <form method="POST" id="editResourceForm">
                    @csrf
                    @method('PUT')
                    @if ($errors->any() && old('form_type') === 'edit_collection')
                        <input type="hidden" name="has_edit_errors" value="true">
                    @endif
                    <input type="hidden" name="form_type" value="edit_collection">

                    <div class="form-group">
                        <label for="edit_title">Collection Title</label>
                        <input type="text" id="edit_title" name="title" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Collection Description</label>
                        <textarea id="edit_description" name="description" class="form-textarea" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_icon_class">Icon Class (Font Awesome)</label>
                        <input type="text" id="edit_icon_class" name="icon_class" class="form-input"
                               placeholder="e.g., fa-piggy-bank">
                        <small>Find icons at <a href="https://fontawesome.com/icons" target="_blank"
                                                rel="noopener noreferrer">Font Awesome</a> (use free icons).</small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="editResourceModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: Edit Collection Modal --}}
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/coach-script.js') }}"></script>
    <script src="{{ asset('assets/js/resources.js') }}"></script>
@endpush

