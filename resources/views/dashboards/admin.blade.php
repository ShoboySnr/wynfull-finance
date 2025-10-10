@extends('layouts.admin')
@section('title', 'Admin Dashboard')

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
                        <p class="stat-value">24</p>
                        <span class="stat-change positive">+3 this month</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Sessions This Week</h3>
                        <p class="stat-value">12</p>
                        <span class="stat-change neutral">3 remaining</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Unread Messages</h3>
                        <p class="stat-value">8</p>
                        <span class="stat-change">From 6 clients</span>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="coach-sections">
                <div class="today-schedule">
                    <h2>Today's Schedule</h2>
                    <div class="schedule-list">
                        <div class="schedule-item">
                            <div class="schedule-time">
                                <span class="time">10:00 AM</span>
                                <span class="duration">60 min</span>
                            </div>
                            <div class="schedule-details">
                                <h4>John Doe - Financial Review</h4>
                                <p>Quarterly portfolio assessment and goal adjustment</p>
                                <div class="client-tags">
                                    <span class="tag">Premium</span>
                                    <span class="tag">Investment Focus</span>
                                </div>
                            </div>
                            <div class="schedule-actions">
                                <button class="btn-secondary">Join Call</button>
                            </div>
                        </div>

                        <div class="schedule-item">
                            <div class="schedule-time">
                                <span class="time">2:00 PM</span>
                                <span class="duration">45 min</span>
                            </div>
                            <div class="schedule-details">
                                <h4>Maria Rodriguez - Debt Strategy</h4>
                                <p>Review debt consolidation plan and next steps</p>
                                <div class="client-tags">
                                    <span class="tag">Growth</span>
                                    <span class="tag">Debt Management</span>
                                </div>
                            </div>
                            <div class="schedule-actions">
                                <button class="btn-secondary">Join Call</button>
                            </div>
                        </div>

                        <div class="schedule-item upcoming">
                            <div class="schedule-time">
                                <span class="time">4:30 PM</span>
                                <span class="duration">30 min</span>
                            </div>
                            <div class="schedule-details">
                                <h4>David Kim - Business Planning</h4>
                                <p>S-Corp vs LLC discussion and tax implications</p>
                                <div class="client-tags">
                                    <span class="tag">Premium</span>
                                    <span class="tag">Business</span>
                                </div>
                            </div>
                            <div class="schedule-actions">
                                <button class="btn-primary">Join Call</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Client Activity -->
                <div class="client-activity">
                    <h2>Recent Client Activity</h2>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <img
                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face&auto=format"
                                    alt="John Doe">
                            </div>
                            <div class="activity-content">
                                <h4>John Doe completed Emergency Fund worksheet</h4>
                                <p>Calculated $12,000 target for 6-month emergency fund</p>
                                <span class="activity-time">2 hours ago</span>
                            </div>
                            <button class="activity-action">Review</button>
                        </div>

                        <div class="activity-item">
                            <div class="activity-avatar">
                                <img
                                    src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face&auto=format"
                                    alt="Lisa Wang">
                            </div>
                            <div class="activity-content">
                                <h4>Lisa Wang sent a message</h4>
                                <p>"Should I start looking at pre-approval options?"</p>
                                <span class="activity-time">4 hours ago</span>
                            </div>
                            <button class="activity-action">Reply</button>
                        </div>

                        <div class="activity-item">
                            <div class="activity-avatar">
                                <img
                                    src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face&auto=format"
                                    alt="David Kim">
                            </div>
                            <div class="activity-content">
                                <h4>David Kim uploaded business plan</h4>
                                <p>Business_Plan_v2.pdf ready for review</p>
                                <span class="activity-time">Yesterday</span>
                            </div>
                            <button class="activity-action">View</button>
                        </div>
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
                        <button class="btn-secondary">Upload</button>
                    </div>
                    <div class="quick-action-card">
                        <i class="fas fa-calendar-plus"></i>
                        <h3>Schedule Session</h3>
                        <p>Book a new coaching session</p>
                        <button class="btn-secondary">Schedule</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/coach-script.js') }}"></script>
@endpush
