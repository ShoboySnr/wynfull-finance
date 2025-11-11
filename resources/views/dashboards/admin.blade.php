@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Manage users, resources, and system settings')
@section('breadcrumb', 'Administration')

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
                        <h3>Coaches</h3>
                        <p class="stat-value">{{ $totals['coaches'] }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Clients</h3>
                        <p class="stat-value">{{ $totals['clients'] }}</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Admin</h3>
                        <p class="stat-value">{{ $totals['admins'] }}</p>
                    </div>
                </div>
            </div>


            <!-- Recent Client Activity -->
            <div class="client-activity">
                <h2>Recent Client Activity</h2>
                <div class="activity-list">
                    @foreach($activities as $activity)
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <img
                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face&auto=format"
                                    alt="John Doe">
                            </div>
                            <div class="activity-content">
                                <h4>{{ $activity['event'] }}</h4>
                                <p>{{ $activity['description'] }}</p>
                                <span class="activity-time">{{ $activity['time_ago'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/coach-script.js') }}"></script>
@endpush
