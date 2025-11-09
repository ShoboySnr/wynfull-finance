{{-- START: Activity Detail Modal --}}
<div class="modal-overlay" id="activityDetailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Activity Details</h2>
            <button class="modal-close" id="activityModalClose">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Client</label>
                <p id="modalClientName" class="modal-data-field"></p>
            </div>
            <div class="form-group">
                <label>Action</label>
                <p id="modalActivityDescription" class="modal-data-field"></p>
            </div>
            <div class="form-group">
                <label>Date & Time</label>
                <p id="modalActivityTimestamp" class="modal-data-field"></p>
            </div>

            <h3 class="modal-subtitle">Technical Details</h3>
            <div class="activity-detail-list">
                <dt>IP Address</dt>
                <dd id="modalActivityIp"></dd>

                <dt>Device / Browser</dt>
                <dd id="modalActivityUserAgent"></dd>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" id="activityModalCancel">Close</button>
        </div>
    </div>
</div>
{{-- END: Activity Detail Modal --}}
