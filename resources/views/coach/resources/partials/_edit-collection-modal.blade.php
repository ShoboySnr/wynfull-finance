{{-- START: Edit Collection Modal --}}
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
                    <input type="text" id="edit_icon_class" name="icon_class" class="form-input" placeholder="e.g., fa-piggy-bank">
                    <small>Find icons at <a href="https://fontawesome.com/icons" target="_blank" rel="noopener noreferrer">Font Awesome</a> (use free icons).</small>
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
