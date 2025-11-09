@extends('layouts.app')

@section('title', 'My Clients')

@section('content')
    <div class="page-content" id="clients">
        <div class="page-header">
            <h1>My Clients</h1>
            <p>Manage and track your client relationships</p>
        </div>

        {{-- START: Filters and Search (Using Links for Filters) --}}
        <div class="clients-controls">
            <div class="filter-tabs">
                {{-- Adjust counts dynamically later if needed --}}
                <a href="{{ route('coach.clients') }}" class="filter-tab {{ !request('filter') ? 'active' : '' }}">All Clients</a>
                <a href="{{ route('coach.clients', ['filter' => 'active']) }}" class="filter-tab {{ request('filter') == 'active' ? 'active' : '' }}">Active</a>
{{--                <a href="{{ route('coach.clients', ['filter' => 'premium']) }}" class="filter-tab {{ request('filter') == 'premium' ? 'active' : '' }}">Premium</a>--}}
{{--                <a href="{{ route('coach.clients', ['filter' => 'growth']) }}" class="filter-tab {{ request('filter') == 'growth' ? 'active' : '' }}">Growth</a>--}}
{{--                <a href="{{ route('coach.clients', ['filter' => 'attention']) }}" class="filter-tab {{ request('filter') == 'attention' ? 'active' : '' }}">Needs Attention</a>--}}
            </div>
            <div class="clients-search">
                <form action="{{ route('coach.clients') }}" method="GET">
                    @if(request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search clients..." value="{{ request('search') }}">
                </form>
            </div>
        </div>
        {{-- END: Filters and Search --}}

        {{-- START: Dynamic Clients Grid --}}
        <div class="clients-grid">
            @forelse ($clients as $client)
                @php
                    $statusClass = match($client['status'] ?? 'active') {
                        'active' => 'online',
                        'inactive' => 'offline',
                        'away' => 'away',
                        default => 'offline'
                    };
                    $planClass = strtolower($client['plan_name'] ?? '');
                @endphp

                <div class="client-card" data-plan="{{ $planClass }}">
                    <div class="client-header">
                        <img src="{{ $client['avatar_path'] ? asset('storage/' . $client['avatar_path']) : 'https://placehold.co/60x60/EBF0FF/0E4DA4?text=' . strtoupper(substr($client['name'], 0, 1)) }}" alt="{{ $client['name'] }}">
                        <div class="client-info">
                            <h3>{{ $client['name'] }}</h3>
                            <p>{{ $client['plan_name'] ?? '' }} • {{ Str::ucfirst($client['status'] ?? 'Active') }}</p>
                            <div class="client-tags">
                                @if($client['plan_name'])
                                    <span class="tag tag-{{ $planClass }}"></span>
                                @endif
                                 <span class="tag">{{ $client['primary_goal_label'] }}</span>
                            </div>
                        </div>
                        <div class="client-status {{ $statusClass }}"></div>
                    </div>
                    <div class="client-stats">
                        <div class="stat">
                            <span class="label">Confidence</span> {{-- Example Stat --}}
                            <span class="value">{{ $client['confidence_score'] ?? 'N/A' }}%</span>
                        </div>
                        <div class="stat">
                            <span class="label">Goal Progress</span> {{-- Example Stat --}}
                            <span class="value">{{ $client['goal_score'] ?? 'N/A' }}%</span>
                        </div>
                        <div class="stat">
                            <span class="label">Last Activity</span> {{-- Example Stat --}}
                            <span class="value">{{ $client['last_activity_ago'] ? $client['last_activity_ago'] : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="client-actions">
                        <a href="{{ route('coach.messages') }}" class="btn-secondary btn-sm"><i class="fas fa-comment-dots"></i> Message</a>
                        <a href="{{ route('coach.client.show', ['user' => $client['id']]) }}" class="btn-primary btn-sm"><i class="fas fa-eye"></i> View Profile</a>
                    </div>
                </div>
            @empty
                <div class="no-clients-message">
                    <p>No clients found matching your criteria.</p>
                </div>
            @endforelse
        </div>
        {{-- END: Dynamic Clients Grid --}}

        {{-- Pagination Links --}}
        <div class="pagination-container mt-4">
{{--            {{ $clients->appends(request()->query())->links() }}--}}
        </div>

    </div>
@endsection

@push('scripts')
     <script src="{{ asset('assets/js/coach-clients.js') }}"></script>
@endpush

