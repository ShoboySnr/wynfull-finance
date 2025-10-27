@extends('layouts.admin') {{-- Use your admin layout --}}

@section('title', 'Manage Resources')
@section('page-title', 'Resource Management')

@section('content')
    <div class="page-header">
        <h1>Resource Approval</h1>
        <p>Review and manage resource collections submitted by coaches.</p>
    </div>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filters (Optional) --}}
    <div class="admin-resource-filters">
        <a href="{{ route('admin.resources') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
        <a href="{{ route('admin.resources', ['status' => 'pending']) }}" class="filter-tab {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('admin.resources', ['status' => 'approved']) }}" class="filter-tab {{ request('status') == 'approved' ? 'active' : '' }}">Approved</a>
        <a href="{{ route('admin.resources', ['status' => 'rejected']) }}" class="filter-tab {{ request('status') == 'rejected' ? 'active' : '' }}">Rejected</a>
    </div>

    {{-- Resource Collections Grid --}}
    <div class="admin-resources-grid">
        @forelse ($collections as $collection)
            <div class="admin-resource-card status-{{ $collection->status }}">
                <div class="card-header">
                    <div class="collection-icon">
                        <i class="fas {{ $collection->icon_class ?? 'fa-folder-open' }}"></i>
                    </div>
                    <div class="collection-info">
                        <h3>{{ $collection->title }}</h3>
                        <p class="coach-submitter">Submitted by: {{ $collection->coach->name ?? 'N/A' }}</p>
                        <p class="submission-date">Submitted: {{ $collection->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="status-badge status-{{ $collection->status }}">{{ Str::ucfirst($collection->status) }}</span>
                </div>
                <div class="card-body">
                    <p class="description">{{ $collection->description ?: 'No description provided.' }}</p>
                    {{-- Optional: Module Preview/Count --}}
                    <div class="module-preview">
                        <strong>Modules:</strong>
                        @if($collection->modules->count() > 0)
                            <ul>
                                @foreach($collection->modules as $module)
                                    @php
                                        // Determine icon
                                        $iconClass = match ($module->type) {
                                            'template' => 'fa-file-alt', 'pdf' => 'fa-file-pdf',
                                            'word' => 'fa-file-word', 'excel' => 'fa-file-excel',
                                            'video' => 'fa-video', default => 'fa-file',
                                        };
                                        // Determine link URL
                                        $linkUrl = '#'; // Default
                                        if ($module->type === 'video' && $module->video_link) {
                                            $linkUrl = $module->video_link;
                                        } elseif ($module->file_path) {
                                            $linkUrl = asset('storage/' . $module->file_path);
                                        }
                                    @endphp

                                <li><i class="fas {{ $iconClass }}"></i>
                                    <a href="{{ $linkUrl }}" target="_blank" rel="noopener noreferrer" title="Open {{ $module->type }}">
                                        {{ Str::limit($module->title, 35) }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p>No modules added yet.</p>
                        @endif
                    </div>
                    {{-- Display rejection reason if applicable --}}
                    @if($collection->status === 'rejected' && $collection->rejection_reason)
                        <div class="rejection-reason">
                            <strong>Reason for Rejection:</strong> {{ $collection->rejection_reason }}
                        </div>
                    @endif
                    {{-- Display approval info if applicable --}}
                    @if($collection->status === 'approved' && $collection->approved_at)
                        <div class="approval-info">
                            Approved by {{ $collection->approver->name ?? 'N/A' }} on {{ $collection->approved_at->format('M d, Y') }}
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    {{-- Action Buttons --}}
                    @if($collection->status === 'pending' || $collection->status === 'rejected')
                        <form action="{{-- route('admin.resources.approve', $collection->id) --}}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-success btn-sm"><i class="fas fa-check"></i> Approve</button>
                        </form>
                    @endif
                    @if($collection->status === 'pending' || $collection->status === 'approved')
                        <button class="btn-danger btn-sm rejectResourceBtn"
                                data-action="{{-- route('admin.resources.reject', $collection->id) --}}"
                                data-collection-title="{{ $collection->title }}"> {{-- Pass title to modal --}}
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="no-resources-message">
                <p>No resource collections match the current filter.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination Links --}}
    <div class="table-pagination">
        {{ $collections->links() }}
    </div>

    {{-- START: Rejection Reason Modal --}}
    <div class="modal-overlay" id="rejectReasonModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Reason for Rejection</h2>
                <button class="modal-close" id="rejectReasonModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" id="rejectReasonForm"> {{-- Action set by JS --}}
                    @csrf
                    @method('PATCH')
                    <p>Please provide a reason for rejecting the collection "<strong id="rejectCollectionTitle"></strong>":</p>
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason (Optional but recommended)</label>
                        <textarea id="rejection_reason" name="rejection_reason" class="form-textarea" rows="4" placeholder="Explain why the resource collection is being rejected..."></textarea>
                        @error('rejection_reason') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="rejectReasonModalCancel">Cancel</button>
                        <button type="submit" class="btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: Rejection Reason Modal --}}

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/admin-resources.js') }}"></script>
@endpush
