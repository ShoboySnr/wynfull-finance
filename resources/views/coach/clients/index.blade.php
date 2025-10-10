@extends('layouts.app')
@section('title', 'Coach Dashboard')

@section('content')
    <!-- My Clients Page -->
    <div class="" id="clients">
        <div class="page-content">
            <div class="page-header">
                <h1>My Clients</h1>
                <p>Manage and track your client relationships</p>
            </div>

            <div class="clients-filters">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">All Clients (24)</button>
                    <button class="filter-tab" data-filter="active">Active (18)</button>
                    <button class="filter-tab" data-filter="premium">Premium (8)</button>
                    <button class="filter-tab" data-filter="growth">Growth (10)</button>
                    <button class="filter-tab" data-filter="needs-attention">Needs Attention (3)</button>
                </div>
                <div class="clients-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search clients...">
                </div>
            </div>

            <div class="clients-grid">
                <div class="client-card" data-status="active" data-plan="premium">
                    <div class="client-header">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=60&h=60&fit=crop&crop=face&auto=format" alt="John Doe">
                        <div class="client-info">
                            <h3>John Doe</h3>
                            <p>Premium Plan • Active</p>
                            <div class="client-tags">
                                <span class="tag premium">Premium</span>
                                <span class="tag">Investment Focus</span>
                            </div>
                        </div>
                        <div class="client-status online"></div>
                    </div>
                    <div class="client-stats">
                        <div class="stat">
                            <span class="label">Confidence Score</span>
                            <span class="value">$45,230</span>
                        </div>
                        <div class="stat">
                            <span class="label">Goal Progress</span>
                            <span class="value">67%</span>
                        </div>
                    </div>
                    <div class="client-actions">
                        <button class="btn-secondary">Message</button>
                        <button class="btn-primary">View Profile</button>
                    </div>
                </div>

                <div class="client-card" data-status="active" data-plan="growth">
                    <div class="client-header">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=60&h=60&fit=crop&crop=face&auto=format" alt="Lisa Wang">
                        <div class="client-info">
                            <h3>Lisa Wang</h3>
                            <p>Growth Plan • Active</p>
                            <div class="client-tags">
                                <span class="tag growth">Growth</span>
                                <span class="tag">Real Estate</span>
                            </div>
                        </div>
                        <div class="client-status away"></div>
                    </div>
                    <div class="client-stats">
                        <div class="stat">
                            <span class="label">Confidence Score</span>
                            <span class="value">$28,500</span>
                        </div>
                        <div class="stat">
                            <span class="label">Goal Progress</span>
                            <span class="value">45%</span>
                        </div>
                    </div>
                    <div class="client-actions">
                        <button class="btn-secondary">Message</button>
                        <button class="btn-primary">View Profile</button>
                    </div>
                </div>

                <div class="client-card" data-status="needs-attention" data-plan="premium">
                    <div class="client-header">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face&auto=format" alt="David Kim">
                        <div class="client-info">
                            <h3>David Kim</h3>
                            <p>Premium Plan • Needs Attention</p>
                            <div class="client-tags">
                                <span class="tag premium">Premium</span>
                                <span class="tag attention">Business</span>
                            </div>
                        </div>
                        <div class="client-status offline"></div>
                    </div>
                    <div class="client-stats">
                        <div class="stat">
                            <span class="label">Confidence Score</span>
                            <span class="value">$125K</span>
                        </div>
                        <div class="stat">
                            <span class="label">Last Contact</span>
                            <span class="value">5 days</span>
                        </div>
                    </div>
                    <div class="client-actions">
                        <button class="btn-secondary">Message</button>
                        <button class="btn-primary">View Profile</button>
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
