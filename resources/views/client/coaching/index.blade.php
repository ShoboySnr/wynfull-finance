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
                        @foreach($upcoming as $session)
                            @php
                                $tz = optional($session->coach->availabilitySetting)->timezone ?? config('app.timezone');

                                $dateStr = $session->starts_at
                                    ? $session->starts_at->clone()->setTimezone($tz)->format('F j, Y')
                                    : '—';

                                $timeStr = $session->starts_at
                                    ? $session->starts_at->clone()->setTimezone($tz)->format('g:i A T')
                                    : '—';
                            @endphp
                            <div class="workshop-item">
                                <div class="workshop-info">
                                    <h3>{{ $session->title ?? 'Requested Session' }}</h3>
                                    <p>{{ $session->notes ?? '' }}</p>
                                    <div class="workshop-meta">
                                        <span><i class="fas fa-calendar"></i> {{ $dateStr }}</span>
                                        <span><i class="fas fa-clock"></i> {{ $timeStr }}</span>
                                    </div>
                                </div>
                                @if (!empty($session->location_url))
                                    <a class="btn-secondary" href="{{ $session->location_url }}" target="_blank" rel="noopener">Join</a>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="coaching-section" style="display: none;">
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
                <h2>Book a Session with {{ $coach->name ?? 'your coach' }}</h2>
                <button class="modal-close" id="bookSessionModalClose">&times;</button>
            </div>
            <div class="modal-body">
               @if($coach)
                    <form action="{{ route('client.booking.store', $coach->id) }}" method="POST" id="bookSessionForm">
                        @csrf
                        @if($coach)
                            <input type="hidden" name="coach_id" value="{{ $coach->id }}">
                        @endif
                        <input type="hidden" name="starts_at" id="starts_at_utc">
                        <input type="hidden" name="ends_at" id="ends_at_utc">

                        <div class="form-group">
                            <label for="session_date">1. Select a Date</label>
                            <input type="date" id="session_date" name="date" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label>2. Select an Available Time</label>
                            <div class="time-slots-container" id="timeSlotsContainer">
                                <p class="time-slot-placeholder">Please select a date to see available times.</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="session_notes">3. Notes for your coach (Optional)</label>
                            <textarea id="session_notes" name="notes" class="form-textarea" rows="3" placeholder="What would you like to discuss?"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" id="bookSessionModalCancel">Cancel</button>
                            <button type="submit" class="btn-primary" id="requestSessionBtn" disabled>Request Session</button>
                        </div>
                    </form>
               @endif
            </div>
        </div>
    </div>
    {{-- END: New Book a Session Modal --}}
@endsection

@push('scripts')
    <script>
        const coachAvailability = @json($availability ?? []);
    </script>
    <script src="{{ asset('assets/js/client-coaching.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}" defer></script>
@endpush
