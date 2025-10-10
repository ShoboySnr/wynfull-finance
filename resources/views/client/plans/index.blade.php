@extends('layouts.client')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <!-- Plans Page -->
    <div class="" id="plans">
        <div class="page-content">
            <div class="page-header">
                <h1>Your Plans</h1>
                <p>Manage your subscription and explore upgrade options to unlock advanced features.</p>
            </div>

            <div class="plans-grid">
                <div class="plan-card current">
                    <div class="plan-badge current-badge">Current Plan</div>
                    <div class="plan-header">
                        <h3>Core Access</h3>
                        <div class="plan-price">
                            <span class="price">$20</span>
                            <span class="period">/month</span>
                        </div>
                    </div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Full access to 4-phase curriculum & resource library (videos, templates, reference guide)</li>
                        <li><i class="fas fa-check"></i> Community access + monthly group coaching and Q&A session</li>
                        <li><i class="fas fa-check"></i> Wynfull AI research access</li>
                    </ul>
                    <button class="btn-secondary plan-btn current-plan-btn" disabled>Current Plan</button>
                </div>

                <div class="plan-card featured">
                    <div class="plan-badge">Most Popular</div>
                    <div class="plan-header">
                        <h3>Guided Growth</h3>
                        <div class="plan-price">
                            <span class="price">$30</span>
                            <span class="period">/month</span>
                        </div>
                        <div class="price-difference">+$10/month</div>
                    </div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Everything in your current Core Access plan plus:</li>
                        <li><i class="fas fa-star"></i> Two 45-minute 1:1 coaching session per month (non-dedicated coach)</li>
                        <li><i class="fas fa-star"></i> Personalized feedback on budgets and debt strategies</li>
                        <li><i class="fas fa-star"></i> Access to bonus live workshops (credit repair, investing, etc)</li>
                    </ul>
                    <button class="btn-primary plan-btn">Upgrade to Guided Growth</button>
                </div>

                <div class="plan-card premium">
                    <div class="plan-badge">Best Value</div>
                    <div class="plan-header">
                        <h3>Financial Freedom Plus</h3>
                        <div class="plan-price">
                            <span class="price">$40</span>
                            <span class="period">/month</span>
                        </div>
                        <div class="price-difference">+$20/month</div>
                    </div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> Everything in Guided Growth plan plus:</li>
                        <li><i class="fas fa-crown"></i> Dedicated coach assignment from day one</li>
                        <li><i class="fas fa-crown"></i> Weekly or as needed 45-minute 1:1 coaching session (up to 4/month)</li>
                        <li><i class="fas fa-crown"></i> Exclusive event access and new products access</li>
                    </ul>
                    <button class="btn-secondary plan-btn">Upgrade to Financial Freedom Plus</button>
                </div>
            </div>

            <div class="upgrade-benefits">
                <h2>Why Upgrade?</h2>
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <i class="fas fa-user-tie"></i>
                        <h3>Expert Coaching</h3>
                        <p>Work directly with certified coaches to gain confidence and optimize your
                            financial goals.</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-chart-line"></i>
                        <h3>Advanced Strategies</h3>
                        <p>Track investments, analyze portfolio performance, and plan for long-term growth.</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-comments"></i>
                        <h3>Personalized Feedback</h3>
                        <p>Get feedback on budgets and debt strategies</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Priority Support</h3>
                        <p>Get faster responses and dedicated assistance when you need help.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

