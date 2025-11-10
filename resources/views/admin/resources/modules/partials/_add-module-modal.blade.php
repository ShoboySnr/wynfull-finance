{{-- START: Add New Module Modal --}}
<div class="modal-overlay" id="addModuleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Module to "{{ $collection->title }}"</h2>
            <button class="modal-close" id="addModuleModalClose">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.resources.collection.modules.store', $collection->id) }}" method="POST" enctype="multipart/form-data" id="addModuleForm">
                @csrf
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
                        <option value="" disabled {{ !old('type') ? 'selected' : '' }}>Select Type</option>
                        <option value="template" {{ old('type') == 'template' ? 'selected' : '' }}>Template</option>
                        <option value="word" {{ old('type') == 'word' ? 'selected' : '' }}>Word Document</option>
                        <option value="pdf" {{ old('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="excel" {{ old('type') == 'excel' ? 'selected' : '' }}>Excel</option>
                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video Link</option>
                    </select>
                </div>

                {{-- File Upload Field (Conditional) --}}
                <div class="form-group file-field-container" style="display: none;">
                    <label for="add_module_file">Upload File</label>
                    <div class="file-drop-area">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drag & drop file, or <span class="file-browse-link">browse</span></p>
                        <input type="file" id="add_module_file" name="file" class="file-input">
                        <p class="file-name-display"></p>
                    </div>
                </div>
                {{-- Video Link Field (Conditional) --}}
                <div class="form-group video-link-field-container" style="display: none;">
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
