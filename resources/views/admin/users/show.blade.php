@extends('layouts.admin')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <div class="profile-page-container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-main-card">
            <div class="profile-header">
                <div class="profile-avatar-large-container">
                    <img
                        src="{{ $profile?->avatar_path ? asset('storage/' . $profile->avatar_path) : 'https://placehold.co/40x40/EBF0FF/0E4DA4?text=' . strtoupper(substr($user->name, 0, 1)) }}"
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
                    @if(strtolower($role) === 'client')
                        <button class="btn-secondary" id="assignCoachBtn"><i class="fas fa-user-plus"></i> Assign Coach
                        </button>
                    @endif

                    @if($user->is_active)
                        <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST"
                              style="display: inline;">
                            @csrf
                            <button class="btn-primary">Deactivate</button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.activate', $user->id) }}" method="POST"
                              style="display: inline;">
                            @csrf
                            <button class="btn-primary">Activate</button>
                        </form>
                    @endif

                    <!-- Delete User Button -->
                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                          style="display: inline;" 
                          onsubmit="return confirm('This will permanently delete {{ $user->name }} and all their data. This action cannot be undone. Are you absolutely sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" style="margin-left: 8px;">
                            Delete User
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- Profile Details Section -->
        <div class="profile-details-section">
            <div class="profile-tabs">
                @if(strtolower($role) === 'client')
                    <button class="profile-tab active" data-tab="dashboard">Dashboard</button>
                    <button class="profile-tab" data-tab="activity">Recent Activity</button>
                    <button class="profile-tab" data-tab="assignments">Coach Assignments</button>
                @else
                    <button class="profile-tab active" data-tab="activity">Recent Activity</button>
                @endif
            </div>

            @if(strtolower($role) === 'client' && $dashboardData)
                <div class="profile-tab-content active" id="dashboard">
                    @include('admin.users.partials.client-dashboard-preview', [
                        'user' => $user,
                        'personalFinanceConfidence' => $dashboardData['personalFinanceConfidence'],
                        'emergencyReadiness' => $dashboardData['emergencyReadiness'],
                        'investingHabit' => $dashboardData['investingHabit'],
                        'confidence' => $dashboardData['confidence'],
                        'journey' => $dashboardData['journey'],
                        'financialKnowledge' => $dashboardData['financialKnowledge'],
                        'wealthCards' => $dashboardData['wealthCards'],
                        'financialSituations' => $dashboardData['financialSituations'],
                        'investingStatus' => $dashboardData['investingStatus'],
                        'onboardings' => $dashboardData['onboardings'],
                        'budgetConfidenceChart' => $dashboardData['budgetConfidenceChart'],
                        'personalFinanceConfidenceChart' => $dashboardData['personalFinanceConfidenceChart'],
                        'debtKnowledgeChart' => $dashboardData['debtKnowledgeChart'],
                        'investingKnowledgeChart' => $dashboardData['investingKnowledgeChart'],
                        'emergencyReadinessChart' => $dashboardData['emergencyReadinessChart'],
                        'investingHabitChart' => $dashboardData['investingHabitChart']
                    ])
                </div>
            @endif

            <div class="profile-tab-content {{ strtolower($role) !== 'client' ? 'active' : '' }}" id="activity">
                <div class="activity-list">
                    @forelse($activities as $activity)
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                            </div>
                            <div class="activity-content">
                                <h4>{{ $activity['event'] ?? '—' }}</h4>
                                <p>{{ $activity['description'] ?? 'No description' }}</p>
                                <span class="activity-time">{{ $activity['time_ago'] ?? '' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="activity-empty">
                            <div class="empty-icon">
                                <i class="fas fa-inbox" aria-hidden="true"></i>
                            </div>
                            <div class="empty-copy">
                                <h4>No activity yet</h4>
                            </div>
                        </div>
                    @endforelse

                </div>
            </div>


            {{-- START: New Coach Assignments Tab Content --}}
            @if(strtolower($role) === 'client')
                <div class="profile-tab-content" id="assignments">
                    <div class="coach-assignment-list">
                        @forelse($coachAssignments as $assignment)
{{--                            @dd($assignment)--}}
                            <div class="assignment-item">
                                <img
                                    src="{{ $assignment->coach->profile?->avatar_path ? asset('storage/' . $assignment->coach->profile->avatar_path) : 'https://placehold.co/40x40/EBF0FF/0E4DA4?text=' . strtoupper(substr($assignment->coach->name, 0, 1)) }}"
                                    alt="{{ $assigment->coach->profile->first_name ?? $assignment->coach->name  }}" class="user-avatar-small">

                                <div class="assignment-info">
                                    <span class="coach-name">{{ $assignment->coach->name }}</span>
                                    <span class="assignment-meta">
                                        Assigned on {{ $assignment->assigned_at->format('M d, Y') }}
                                        by {{ $assignment->assignedBy->name ?? 'Admin' }}
                                    </span>
                                </div>

                                <span
                                    class="badge badge-active">active</span>

                                <div class="assignment-actions">
                                        <form action="{{ route('admin.assignments.user.end', ['coach' => $assignment->coach->id, 'client' => $user->id]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to end this assignment?');">
                                            @csrf
                                            <button type="submit" class="btn-danger btn-sm">End Assignment</button>
                                        </form>
                                </div>
                            </div>
                        @empty
                            <div class="activity-empty">
                                <div class="empty-icon">
                                    <i class="fas fa-user-tie" aria-hidden="true"></i>
                                </div>
                                <div class="empty-copy">
                                    <h4>No Coach Assignments</h4>
                                    <p>This client is not currently assigned to any coaches.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
            {{-- END: New Coach Assignments Tab Content --}}
        </div>


        <!-- Assign Coach Modal -->
        <div class="modal-overlay" id="assignCoachModal">
            <form method="POST" action="{{ route('admin.assignments.store') }}">
                @csrf
                <input type="hidden" name="client_id" value="{{ $user->id }}">

                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Assign Coach to {{ $user->name }}</h3>
                        <button type="button" class="modal-close" id="closeAssignCoachModal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Select a coach from the list below to assign to this client.</p>
                        <div class="coach-list">
                            @forelse($coaches as $coach)
                                <label class="coach-list-item">
                                    <img
                                        src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format"
                                        alt="{{ $coach->name }}" class="user-avatar-small">
                                    <div class="coach-details">
                                        <span class="coach-name">{{ $coach->name }}</span>
                                        <span class="coach-email">{{ $coach->email }}</span>
                                    </div>
                                    <input type="radio" name="coach_id" value="{{ $coach->id }}">
                                </label>
                            @empty
                                <p>No coaches available to assign.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="cancelAssignCoach">Cancel</button>
                        <button type="submit" class="btn-primary">Assign Coach</button>
                    </div>
                </div>
            </form>
        </div>
        @endsection

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Assign Coach Modal
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

                // Dropdown functionality
                window.toggleDropdown = function(dropdownId) {
                    const dropdown = document.getElementById(dropdownId);
                    if (dropdown) {
                        dropdown.classList.toggle('show');
                    }
                };

                // Close dropdowns when clicking outside
                document.addEventListener('click', function(event) {
                    if (!event.target.closest('.dropdown-container')) {
                        document.querySelectorAll('.dropdown-menu').forEach(menu => {
                            menu.classList.remove('show');
                        });
                    }
                });
            </script>
            <script src="{{ asset('assets/js/admin-users.js') }}"></script>
    @endpush
