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
                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="assessment" {{ old('type') == 'assessment' ? 'selected' : '' }}>Assessment</option>
                    </select>
                </div>

                {{-- START: Video Source Selection (Visible only if type is Video) --}}
                <div class="form-group video-source-field-container" style="display: none;">
                    <label>Video Source</label>
                    <div class="radio-group" style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                        <label style="font-weight: normal; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="video_source" value="link" {{ old('video_source', 'link') == 'link' ? 'checked' : '' }}>
                            External Link (YouTube/Vimeo)
                        </label>
                        <label style="font-weight: normal; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="video_source" value="upload" {{ old('video_source') == 'upload' ? 'checked' : '' }}>
                            Upload Video File
                        </label>
                    </div>
                </div>
                {{-- END: Video Source Selection --}}

                {{-- Standard File Upload (For Template, Word, PDF, Excel) --}}
                <div class="form-group file-field-container" style="display: none;">
                    <label for="add_module_file">Upload Document</label>
                    <div class="file-drop-area">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drag & drop file, or <span class="file-browse-link">browse</span></p>
                        <input type="file" id="add_module_file" name="file" class="file-input">
                        <p class="file-name-display"></p>
                    </div>
                </div>

                {{-- Video Link Field --}}
                <div class="form-group video-link-field-container" style="display: none;">
                    <label for="add_module_video_link">Video Link</label>
                    <input type="url" id="add_module_video_link" name="video_link" class="form-input" placeholder="https://youtube.com/watch?v=..." value="{{ old('video_link') }}">
                </div>

                {{-- START: Video Upload Field --}}
                <div class="form-group video-upload-field-container" style="display: none;">
                    <label for="add_module_video_file">Upload Video File</label>
                    <div class="file-drop-area">
                        <i class="fas fa-film"></i>
                        <p>Drag & drop video, or <span class="file-browse-link">browse</span></p>
                        <input type="file" id="add_module_video_file" name="video_file" class="file-input" accept="video/mp4,video/x-m4v,video/*">
                        <p class="file-name-display"></p>
                    </div>
                </div>
                {{-- END: Video Upload Field --}}

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="addModuleModalCancel">Cancel</button>
                    <button type="submit" class="btn-primary">Add Module</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END: Add New Module Modal --}}
