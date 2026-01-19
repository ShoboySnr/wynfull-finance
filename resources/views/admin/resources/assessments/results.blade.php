@extends('layouts.admin')

@section('title', 'Assessment Results - ' . $module->title)

@section('content')
<div class="admin-container">
    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('admin.modules.assessments.index', $module) }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Questions
            </a>
            <h1>
                Assessment Results
            </h1>
            <h3 class="subtitle">{{ $module->title }}</h3>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="submissions-section">
        <h2 class="section-title">Client Submissions</h2>

        @if($submissions->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Submissions Yet</h3>
                <p>No clients have completed this assessment yet.</p>
            </div>
        @else
            <div class="submissions-grid">
                @foreach($submissions as $submission)
                    <div class="submission-card">
                        <div class="submission-header">
                            <div class="user-info">
                                @if($submission->user->clientProfile && $submission->user->clientProfile->avatar_path)
                                    <img src="{{ Storage::url($submission->user->clientProfile->avatar_path) }}" 
                                         alt="{{ $submission->user->name }}" 
                                         class="user-avatar">
                                @else
                                    <div class="user-avatar-placeholder">
                                        {{ substr($submission->user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="user-details">
                                    <span class="user-name">{{ $submission->user->name }}</span>
                                    <span class="user-email">{{ $submission->user->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="submission-body">
                            <div class="submission-info">
                                <i class="fas fa-calendar-check"></i>
                                <div>
                                    <span class="info-label">Submitted</span>
                                    <span class="info-value">{{ $submission->submitted_at->format('M d, Y \a\t g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="submission-footer">
                            <a href="{{ route('admin.modules.assessments.submissions.view', [$module, $submission]) }}" 
                               class="btn-view">
                                View Responses
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>

.page-header h3 {
    font-size: 1.5rem;
}

/* Section Title */
.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

/* Empty State */
.empty-state {
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 3rem 2rem;
    text-align: center;
}

.empty-state i {
    font-size: 3rem;
    color: var(--text-tertiary);
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-secondary);
    margin: 0;
}

/* Submissions Grid */
.submissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

/* Submission Card */
.submission-card {
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
}

.submission-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

[data-theme="dark"] .submission-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.submission-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-light);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.user-avatar-placeholder {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--wynfull-blue);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.25rem;
}

.user-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.user-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 1rem;
}

.user-email {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.submission-body {
    padding: 1.5rem;
}

.submission-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.submission-info i {
    font-size: 1.5rem;
    color: var(--wynfull-blue);
}

.submission-info > div {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-secondary);
    letter-spacing: 0.05em;
}

.info-value {
    font-size: 0.9375rem;
    color: var(--text-primary);
    font-weight: 500;
}

.submission-footer {
    padding: 1rem 1.5rem;
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-light);
}

.btn-view {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: var(--wynfull-blue);
    color: white;
    border-radius: 6px;
    font-size: 0.9375rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    width: 100%;
    justify-content: center;
}

.btn-view:hover {
    background: #0d3d84;
    transform: translateY(-1px);
}

.btn-view i {
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .submissions-grid {
        grid-template-columns: 1fr;
    }
    
    .submission-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush
