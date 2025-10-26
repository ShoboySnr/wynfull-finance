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
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Clients</h3>
                        <p class="stat-value">24</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Active Clients</h3>
                        <p class="stat-value">24</p>
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
