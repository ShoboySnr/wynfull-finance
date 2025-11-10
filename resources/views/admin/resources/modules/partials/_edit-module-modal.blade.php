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
                    <input type="text" id="edit_module_title" name="title" class="form-input" value="{{ old('title') }}" required>
                </div>
                <div class="form-group">
                    <label for="edit_module_description">Description</label>
                    <textarea id="edit_module_description" name="description" class="form-textarea" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="edit_module_type">Module Type</label>
                    <select id="edit_module_type" name="type" class="form-select module-type-select" required>
                        <option value="template" {{ old('type') == 'template' ? 'selected' : '' }}>Template</option>
                        <option value="word" {{ old('type') == 'word' ? 'selected' : '' }}>Word Document</option>
                        <option value="pdf" {{ old('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="excel" {{ old('type') == 'excel' ? 'selected' : '' }}>Excel</option>
                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video Link</option>
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
                    <input type="url" id="edit_module_video_link" name="video_link" class="form-input" placeholder="https://youtube.com/watch?v=..." value="{{ old('video_link') }}">
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
