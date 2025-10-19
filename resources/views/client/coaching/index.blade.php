@extends('layouts.client')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <!-- Coaching Page -->
    <div class="" id="coaching">
        <div class="page-content">
            <div class="page-header">
                <h1>Coach Access & Workshops</h1>
                <p>Connect with certified coaches for personalized guidance and support</p>
            </div>

            <div class="coaching-sections">
                <div class="coaching-section">
                    <h2>1:1 Coaching Sessions</h2>
                    <div class="session-card">
                        <div class="coach-info">
                            @if($coach && $coach->coachProfile && $coach->profile)
                                <img src="{{ $coach->profile->avatar_url }}" alt="Coach" class="coach-avatar">
                                <div class="coach-details">
                                    <h3>{{ $coach->name }},</h3>
                                    <p>{{ $coach->profile->bio }}</p>
                                </div>
                            @else
                                <p>You are not currently assigned to a coach.</p>
                            @endif
                        </div>
                        @if($coach)
                            <button class="btn-primary" id="bookSessionBtn">Book Session</button>
                        @endif
                    </div>
                </div>

                <div class="coaching-section">
                    <h2>Group Workshops</h2>
                    <div class="workshop-list">
                        <div class="workshop-item">
                            <div class="workshop-info">
                                <h3>Retirement Planning Masterclass</h3>
                                <p>Learn advanced strategies for retirement savings</p>
                                <div class="workshop-meta">
                                    <span><i class="fas fa-calendar"></i> March 15, 2024</span>
                                    <span><i class="fas fa-clock"></i> 2:00 PM EST</span>
                                </div>
                            </div>
                            <button class="btn-secondary">Join</button>
                        </div>
                    </div>
                </div>

                <div class="coaching-section">
                    <h2>Workshop Notes</h2>
                    <div class="notes-container">
                        <div class="note-item">
                            <div class="note-header">
                                <h4>Session with Sarah Chen</h4>
                                <span class="note-date">March 1, 2024</span>
                            </div>
                            <p>Discussed debt consolidation strategy. Recommended focusing on high-interest credit cards first while maintaining minimum payments on student loans.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- START: New Book a Session Modal --}}
    <div class="modal-overlay" id="bookSessionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Book a Session</h2>
                <button class="modal-close" id="bookSessionModalClose">&times;</button>
            </div>
            <div class="modal-body">
                {{-- This form will submit to your new endpoint --}}
                <form action="#" method="POST" id="bookSessionForm">
                    @csrf
                    @if($coach)
                        <input type="hidden" name="coach_id" value="{{ $coach->id }}">
                    @endif

                    <div class="form-group">
                        <label for="session_title">Session Title (Optional)</label>
                        <input type="text" id="session_title" name="title" class="form-input" placeholder="e.g., Quarterly Review">
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="session_date">Date</label>
                            <input type="date" id="session_date" name="date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="session_time">Time</label>
                            <input type="time" id="session_time" name="time" class="form-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="session_notes">Notes for your coach (Optional)</label>
                        <textarea id="session_notes" name="notes" class="form-textarea" rows="4" placeholder="What would you like to discuss?"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="bookSessionModalCancel">Cancel</button>
                        <button type="submit" class="btn-primary">Request Session</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END: New Book a Session Modal --}}
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/client-coaching.js') }}"></script>
@endpush
