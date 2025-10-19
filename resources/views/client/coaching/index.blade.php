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
                            <img src="{{ $coach->profile->avatar_url }}" alt="Coach" class="coach-avatar">
                            <div class="coach-details">
                                <h3>{{ $coach->name }},</h3>
                                <p>{{ $coach->profile->bio }}</p>
                            </div>
                        </div>
                        <button class="btn-primary">Book Session</button>
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
@endsection

