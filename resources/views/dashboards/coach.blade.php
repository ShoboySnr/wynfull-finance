@extends('layouts.app')
@section('title', 'Coach Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Manage your clients and coaching sessions')
@section('breadcrumb', 'Coach Dashboard')

@section('content')
        <!-- Coach Dashboard Page -->
        <div class="page active" id="coach-dashboard">
            <div class="page-content">
                <div class="page-header">
                    <h1>Welcome back, {{ $user->name }}! 👋</h1>
                    <p>Here's what's happening with your clients today</p>
                </div>

                <!-- Coach Stats -->
                <div class="coach-stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Active Clients</h3>
                            <p class="stat-value">{{ $activeClientsCount }}</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Sessions This Week</h3>
                            <p class="stat-value">{{ $weeklySessionsCount }}</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Unread Messages</h3>
                            <p class="stat-value">0</p>
                        </div>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="coach-sections">
                    <div class="today-schedule">
                        <h2>This week Schedule</h2>
                        <div class="schedule-list">
                            @foreach($weeklyScheduleEntries as $schedule)
                                <div class="schedule-item">
                                    <div class="schedule-time">
                                        <span class="time">{{ $schedule['date'] }}</span>
                                        <span class="duration">{{ $schedule['starts_at'] }} - {{ $schedule['ends_at'] }}</span>
                                    </div>
                                    <div class="schedule-details">
                                        <h4>{{ $schedule['client'] }} - {{ $schedule['title'] }}</h4>
                                        <p>{{ $schedule['notes'] }}</p>
                                        <div class="client-tags">
                                            <span class="tag">{{ $schedule['type'] }}</span>
                                        </div>
                                    </div>
                                    @if($schedule['join_url'])
                                        <div class="schedule-actions">
                                            <a href="{{ $schedule['join_url'] }}" class="btn-secondary" style="text-decoration: none">Join Call</a>
                                        </div>
                                    @endif

                                </div>
                            @endforeach

                        </div>
                    </div>

                    <!-- Recent Client Activity -->
                    <div class="client-activity">
                        <h2>Recent Client Activity</h2>
                        <div class="activity-list" id="activityList">
                            @forelse($recentActivities as $activity)
                                @php
                                    $properties = json_decode($activity->properties, true) ?? [];
                                    $client = $activity->subject;
                                @endphp
                                <div class="activity-item">
                                    <div class="activity-avatar">
                                        <img src="{{ $client->profile?->avatar_path ? asset('storage/'. $client->profile->avatar_path) : 'https://placehold.co/40x40/EBF0FF/0E4DA4?text=' . strtoupper(substr($client->name, 0, 1)) }}" alt="{{ $client->name }}">
                                    </div>
                                    <div class="activity-content">
                                        <h4>{{ $client->name }}</h4>
                                        <p>{{ $activity->description }}</p>
                                        <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                    <button class="activity-action view-activity-details"
                                            data-description="{{ $activity->description }}"
                                            data-client-name="{{ $client->name }}"
                                            data-timestamp="{{ $activity->created_at->format('M d, Y \a\t h:i A') }}"
                                            data-ip="{{ $properties['ip'] ?? 'N/A' }}"
                                            data-user-agent="{{ $properties['user_agent'] ?? 'N/A' }}">
                                        Details
                                    </button>
                                </div>
                            @empty
                                <div class="activity-item">
                                    <div class="activity-avatar">
                                        <i class="fas fa-clipboard-list" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4>No recent activity</h4>
                                        <p>Client activities will appear here as they happen.</p>
                                    </div>
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="coach-quick-actions">
                    <h2>Quick Actions</h2>
                    <div class="quick-actions-grid">
                        <div class="quick-action-card">
                            <i class="fas fa-file-alt"></i>
                            <h3>Create Resource</h3>
                            <p>Upload new educational materials</p>
                            <a href="{{ route('coach.resources') }}" class="btn-secondary">Upload</a>
                        </div>
                        <div class="quick-action-card">
                            <i class="fas fa-calendar-plus"></i>
                            <h3>Schedule Session</h3>
                            <p>Book a new coaching session</p>
                            <a href="{{ route('coach.schedule') }}" class="btn-secondary">Schedule</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/coach-script.js') }}"></script>
@endpush
