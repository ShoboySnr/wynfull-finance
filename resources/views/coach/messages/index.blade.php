@extends('layouts.app')
@section('title', 'Coach Dashboard')

@section('content')
    <!-- Coach Messages Page -->
    <div class="page" id="coach-messages">
        <div class="page-content">
            <div class="page-header">
                <h1>Messages</h1>
                <p>Communicate with your clients</p>
            </div>

            <div class="coach-messaging-container">
                <!-- Clients List Sidebar -->
                <div class="coach-clients-sidebar">
                    <div class="clients-header">
                        <h3>Conversations</h3>
                        <button class="btn-primary new-message-btn">
                            <i class="fas fa-plus"></i>
                            New Message
                        </button>
                    </div>
                    <div class="conversations-list">
                        <div class="conversation-item active">
                            <div class="conversation-avatar">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face&auto=format" alt="John Doe">
                                <div class="status-dot online"></div>
                            </div>
                            <div class="conversation-info">
                                <h4>John Doe</h4>
                                <p class="last-message">Thank you! I'll review the plan today...</p>
                                <span class="message-time">11:45 AM</span>
                            </div>
                            <div class="unread-count">2</div>
                        </div>

                        <div class="conversation-item">
                            <div class="conversation-avatar">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face&auto=format" alt="Lisa Wang">
                                <div class="status-dot away"></div>
                            </div>
                            <div class="conversation-info">
                                <h4>Lisa Wang</h4>
                                <p class="last-message">Should I start looking at pre-approval...</p>
                                <span class="message-time">Yesterday</span>
                            </div>
                        </div>

                        <div class="conversation-item">
                            <div class="conversation-avatar">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face&auto=format" alt="David Kim">
                                <div class="status-dot offline"></div>
                            </div>
                            <div class="conversation-info">
                                <h4>David Kim</h4>
                                <p class="last-message">Your business plan looks solid...</p>
                                <span class="message-time">Mar 8</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="coach-chat-area">
                    <div class="chat-header">
                        <div class="client-profile">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face&auto=format" alt="John Doe">
                            <div class="client-details">
                                <h3>John Doe</h3>
                                <p>Premium Plan • Online</p>
                            </div>
                        </div>
                        <div class="chat-actions">
                            <button class="btn-secondary">
                                <i class="fas fa-calendar"></i>
                                Schedule
                            </button>
                        </div>
                    </div>

                    <div class="coach-messages-area">
                        <div class="messages-list">
                            <div class="message-date">
                                <span>Today</span>
                            </div>

                            <div class="message client-message">
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">John Doe</span>
                                        <span class="message-time">11:15 AM</span>
                                    </div>
                                    <div class="message-text">
                                        Thank you! I'll review the plan today. Quick question - should I prioritize the credit card with 18% APR or the personal loan at 12%?
                                    </div>
                                </div>
                            </div>

                            <div class="message coach-message">
                                <div class="message-avatar">
                                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format" alt="Sarah Chen">
                                </div>
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">You</span>
                                        <span class="message-time">11:45 AM</span>
                                    </div>
                                    <div class="message-text">
                                        Great question! Always prioritize the highest interest rate first. Focus on the 18% credit card while maintaining minimum payments on the personal loan. This will save you more money in the long run.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="message-input-container">
                            <div class="message-input-area">
                                <button class="attachment-btn">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <textarea placeholder="Type your message to John..." class="message-input" rows="1"></textarea>
                                <button class="send-btn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
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
