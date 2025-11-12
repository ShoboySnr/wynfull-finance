@extends('layouts.app') {{-- Use your coach layout --}}

@section('title', 'Client Profile - ' . $client->name)
@section('page-title', 'Client Profile')

@section('content')
    <div class="page-content" id="client-profile-page">

        {{-- START: Back Link --}}
        <div class="collection-header-bar" style="background: none; border: none; box-shadow: none; padding: 0 0 1.5rem 0; margin-bottom: 0;">
            <a href="{{ route('coach.clients') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to All Clients</a>
        </div>
        {{-- END: Back Link --}}

        {{-- START: Profile Header Card (Reusing admin-user-profile styles) --}}
        <div class="profile-header-card">
            <div class="profile-avatar-large">
                <img src="{{ $profile?->avatar_path ? asset('storage/' . $profile->avatar_path) : 'https://placehold.co/80x80/EBF0FF/0E4DA4?text=' . strtoupper(substr($client->name, 0, 1)) }}" alt="{{ $client->name }}">
                <span class="status-dot-large online"></span>
            </div>
            <div class="profile-info-main">
                <h1 class="profile-name">{{ $client->name }}</h1>
                <div class="profile-meta-details">
                    <span><i class="fas fa-envelope"></i> {{ $client->email }}</span>
                    <span><i class="fas fa-phone"></i> {{ $profile?->phone ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        {{-- END: Profile Header Card --}}

        {{-- START: Client Stats Grid (Reusing client dashboard styles) --}}
        <h2 class="section-title">Client's Financial Snapshot</h2>
        <div class="dashboard-grid">
            <div class="metric-card confidence-card">
                <h3>Confidence Score</h3>
                <div class="confidence-circle">
                    @php
                        $score = $confidence['score'] ?? 0;
                        $degree = round($score * 3.6);
                    @endphp
                    <div class="confidence-progress" style="background-image: conic-gradient(var(--success-green) 0deg {{ $degree }}deg, #E5E7EB {{ $degree }}deg 360deg)">
                        <span class="confidence-value">{{ $score }}</span>
                    </div>
                </div>
            </div>

            <div class="metric-card debt-progress-card">
                <h3>Debt Journey</h3>
                <div class="debt-status">
                    <div class="debt-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="debt-content">
                        <span class="debt-text">{{ $journey['label'] ?? '' }}</span>
                        <div class="debt-progress-bar">
                            <div class="debt-progress-fill" style="width: {{ $journey['score'] ?? 0 }}%"></div>
                        </div>
                        <span class="debt-subtitle">{{ $journey['badge']['text'] ?? '' }}</span>
                    </div>
                </div>
            </div>

            <div class="metric-card knowledge-card">
                <h3>Investing Knowledge</h3>
                <div class="knowledge-content">
                    <div class="knowledge-level">
                        <div class="knowledge-icon"><i class="fas fa-brain"></i></div>
                        <div class="knowledge-info">
                            <span class="knowledge-text">{{ $financialKnowledge['experience_label'] ?? '' }}</span>
                            {{-- <x-knowledge-dots :score="$financialKnowledge['score'] ?? 0"/> --}}
                            <span class="knowledge-subtitle">Client's self-assessment</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- END: Client Stats Grid --}}


        {{-- START: Client Activity Feed (Reusing coach dashboard styles) --}}
        <div class="client-activity" style="grid-column: 1 / -1;">
            <h2 class="section-title">Recent Activity</h2>
            <div class="activity-list" id="activityList">
                @forelse($recentActivities as $activity)
                    @php
                        $properties = json_decode($activity->properties, true) ?? [];
                    @endphp
                    <div class="activity-item">
                        <div class="activity-avatar">
                            <img src="{{ $profile?->avatar_path ? asset('storage/' . $profile->avatar_path) : 'https://placehold.co/40x40/EBF0FF/0E4DA4?text=' . strtoupper(substr($client->name, 0, 1)) }}" alt="{{ $client->name }}">
                        </div>
                        <div class="activity-content">
                            <h4>{{ $client->name }}</h4>
                            <p>{{ $activity->description }}</p>
                            <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
{{--                        <button class="activity-action view-activity-details"--}}
{{--                                data-description="{{ $activity->description }}"--}}
{{--                                data-client-name="{{ $client->name }}"--}}
{{--                                data-timestamp="{{ $activity->created_at->format('M d, Y \a\t h:i A') }}"--}}
{{--                                data-ip="{{ $properties['ip'] ?? 'N/A' }}"--}}
{{--                                data-user-agent="{{ $properties['user_agent'] ?? 'N/A' }}">--}}
{{--                            Details--}}
{{--                        </button>--}}
                    </div>
                @empty
                    <div class="activity-item">
                        <div class="activity-avatar"><i class="fas fa-clipboard-list" style="font-size: 1.2rem;"></i></div>
                        <div class="activity-content">
                            <h4>No recent activity</h4>
                            <p>This client has no recorded activity yet.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
        {{-- END: Client Activity Feed --}}

    </div> {{-- End page-content --}}

    {{-- Include the activity detail modal --}}
    @include('coach.partials.activity-detail-modal')

@endsection

