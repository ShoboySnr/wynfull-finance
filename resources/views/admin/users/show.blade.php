@extends('layouts.admin')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <div class="profile-page-container">
        <div class="profile-main-card">
            <div class="profile-header">
                <div class="profile-avatar-large-container">
                    <img
                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&h=100&fit=crop&crop=face&auto=format"
                        alt="{{ $user->name }}" class="profile-avatar-large">
                    @if($user->is_active)
                        <div class="profile-status online" title="Active"></div>
                    @else
                        <div class="profile-status offline" title="Inactive"></div>
                    @endif
                </div>
                <div class="profile-info">
                    <h1>{{ $user->name }}</h1>
                    <div class="profile-meta">
                        <span><i class="fas fa-envelope"></i> {{ $user->email }}</span>
                        <span><i
                                class="fas fa-calendar-alt"></i> Joined {{ $user->created_at->format('F d, Y') }}</span>
                    </div>
                    <div class="profile-roles">
                        @php
                            $role = 'Client';
                            if (isset($user->roles)) $role = $user->roles->pluck('name')->first();
                            $roleClass = 'badge-' . strtolower($role);
                        @endphp
                        <span class="badge {{ $roleClass }}">{{ $role }}</span>
                    </div>
                </div>
                <div class="profile-main-actions">
                    <button class="btn-secondary"><i class="fas fa-comments"></i> Message</button>
                    @if(strtolower($role) === 'client')
                        <button class="btn-secondary" id="assignCoachBtn"><i class="fas fa-user-plus"></i> Assign Coach
                        </button>
                    @endif
                    <button class="btn-primary"><i class="fas fa-pencil-alt"></i> Edit Profile</button>
                </div>
            </div>
        </div>

        <!-- Profile Stats Grid -->
        <div class="profile-stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-content">
                    <h3>Financial Confidence</h3>
                    <p class="stat-value">75/100</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bullseye"></i></div>
                <div class="stat-content">
                    <h3>Goal Progress</h3>
                    <p class="stat-value">62%</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-history"></i></div>
                <div class="stat-content">
                    <h3>Last Active</h3>
                    <p class="stat-value">2 days ago</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-content">
                    <h3>Total Sessions</h3>
                    <p class="stat-value">14</p>
                </div>
            </div>
        </div>

        <!-- Profile Details Section -->
        <div class="profile-details-section">
            <div class="profile-tabs">
                <button class="profile-tab active" data-tab="activity">Recent Activity</button>
                <button class="profile-tab" data-tab="goals">Goals</button>
                <button class="profile-tab" data-tab="notes">Notes</button>
                <button class="profile-tab" data-tab="settings">Settings</button>
            </div>

            <div class="profile-tab-content active" id="activity">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-avatar">
                            <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Completed 'Debt Payoff' worksheet</h4>
                            <p>Identified a $250/month snowball payment.</p>
                            <span class="activity-time">2 hours ago</span>
                        </div>
                        <button class="activity-action">Review</button>
                    </div>
                    <div class="activity-item">
                        <div class="activity-avatar">
                            <i class="fas fa-comment-dots" style="color: #3b82f6;"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Sent a message to their coach</h4>
                            <p>"I have a question about my 401k options."</p>
                            <span class="activity-time">1 day ago</span>
                        </div>
                        <button class="activity-action">Reply</button>
                    </div>
                    <div class="activity-item">
                        <div class="activity-avatar">
                            <i class="fas fa-video" style="color: #9333ea;"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Scheduled a session</h4>
                            <p>Booked 'Quarterly Check-in' for Oct 15, 2025.</p>
                            <span class="activity-time">3 days ago</span>
                        </div>
                        <button class="activity-action">View</button>
                    </div>
                </div>
            </div>
            <div class="profile-tab-content" id="goals">
                <div class="coming-soon">
                    <i class="fas fa-bullseye"></i>
                    <h3>Client Goals</h3>
                    <p>This section will display the user's financial goals and their progress.</p>
                </div>
            </div>
            <div class="profile-tab-content" id="notes">
                <div class="coming-soon">
                    <i class="fas fa-sticky-note"></i>
                    <h3>Coach Notes</h3>
                    <p>This section will contain private notes and observations about the user.</p>
                </div>
            </div>
            <div class="profile-tab-content" id="settings">
                <div class="coming-soon">
                    <i class="fas fa-cog"></i>
                    <h3>User Settings</h3>
                    <p>This section will allow managing user-specific settings and permissions.</p>
                </div>
            </div>
        </div>


        <!-- Assign Coach Modal -->
        <div class="modal-overlay" id="assignCoachModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Assign Coach to {{ $user->name }}</h3>
                    <button class="modal-close" id="closeAssignCoachModal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Select a coach from the list below to assign to this client.</p>
                    <div class="coach-list">
                        @forelse($coaches as $coach)
                            <div class="coach-list-item">
                                <img
                                    src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format"
                                    alt="{{ $coach->name }}" class="user-avatar-small">
                                <div class="coach-details">
                                    <span class="coach-name">{{ $coach->name }}</span>
                                    <span class="coach-email">{{ $coach->email }}</span>
                                </div>
                                <input type="radio" name="assigned_coach" value="{{ $coach->id }}">
                            </div>
                        @empty
                            <p>No coaches available to assign.</p>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" id="cancelAssignCoach">Cancel</button>
                    <button class="btn-primary">Assign Coach</button>
                </div>
            </div>
        </div>
        @endsection

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const assignCoachBtn = document.getElementById('assignCoachBtn');
                    const assignCoachModal = document.getElementById('assignCoachModal');
                    const closeBtn = document.getElementById('closeAssignCoachModal');
                    const cancelBtn = document.getElementById('cancelAssignCoach');

                    if (assignCoachBtn) {
                        assignCoachBtn.addEventListener('click', () => {
                            assignCoachModal.classList.add('active');
                        });
                    }

                    const closeModal = () => {
                        assignCoachModal.classList.remove('active');
                    };

                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
                    if (assignCoachModal) {
                        assignCoachModal.addEventListener('click', (event) => {
                            if (event.target === assignCoachModal) {
                                closeModal();
                            }
                        });
                    }
                });
            </script>
    @endpush
