@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <div class="page-content page-messaging" id="coach-messages"> {{-- Added page-messaging class --}}
        {{-- Page Header remains the same --}}
        <div class="page-header">
            <h1>Messages</h1>
            <p>Communicate directly with your clients</p>
        </div>

        <div class="coach-messaging-container">
            <!-- Conversations List Sidebar -->
            <div class="coach-clients-sidebar">
                <div class="clients-header">
                    <h3>Conversations</h3>
                    {{-- Consider making this a modal later --}}
                    <button class="btn-secondary new-message-btn btn-sm">
                        <i class="fas fa-plus"></i> New
                    </button>
                </div>
                {{-- START: Added Search Bar --}}
                <div class="conversation-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search conversations...">
                </div>
                {{-- END: Added Search Bar --}}
                <div class="conversations-list">
                    {{-- Loop through conversations from backend --}}
                    {{-- Example Item (Active) --}}
                    <div class="conversation-item active">
                        <div class="conversation-avatar">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face&auto=format" alt="John Doe">
                            <div class="status-dot online"></div>
                        </div>
                        <div class="conversation-details"> {{-- Renamed class --}}
                            <div class="conversation-header">
                                <h4 class="client-name">John Doe</h4>
                                <span class="message-time">11:45 AM</span>
                            </div>
                            <p class="last-message">Thank you! I'll review the plan today...</p>
                        </div>
                        <div class="unread-indicator"> {{-- Renamed class --}}
                            <span class="unread-count">2</span>
                        </div>
                    </div>
                    {{-- Example Item (Inactive) --}}
                    <div class="conversation-item">
                        <div class="conversation-avatar">
                            <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face&auto=format" alt="Lisa Wang">
                            <div class="status-dot away"></div>
                        </div>
                        <div class="conversation-details">
                            <div class="conversation-header">
                                <h4 class="client-name">Lisa Wang</h4>
                                <span class="message-time">Yesterday</span>
                            </div>
                            <p class="last-message">Should I start looking at pre-approval...</p>
                        </div>
                        {{-- No unread indicator needed here --}}
                    </div>
                    {{-- More items... --}}
                </div>
            </div>

            <!-- Chat Area -->
            <div class="coach-chat-area">
                {{-- Chat Header remains mostly the same --}}
                <div class="chat-header">
                    <div class="client-profile">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face&auto=format" alt="John Doe">
                        <div class="client-details">
                            <h3>John Doe</h3>
                            <p>Premium Plan • Online</p> {{-- Consider making status dynamic --}}
                        </div>
                    </div>
                    <div class="chat-actions">
                        {{-- Removed Schedule button - better placed elsewhere? --}}
                        <button class="btn-icon" title="More options">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>

                <div class="coach-messages-area">
                    {{-- Messages List - Refined Structure --}}
                    <div class="messages-list" id="messagesList">
                        {{-- Messages will be loaded here dynamically or from backend --}}
                        <div class="message-date"><span>Today</span></div>

                        {{-- Client Message Example --}}
                        <div class="message message-client">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face&auto=format" alt="John Doe" class="message-avatar">
                            <div class="message-bubble">
                                <div class="message-text">Thank you! I'll review the plan today. Quick question - should I prioritize the credit card with 18% APR or the personal loan at 12%?</div>
                                <div class="message-time">11:15 AM</div>
                            </div>
                        </div>

                        {{-- Coach Message Example --}}
                        <div class="message message-coach">
                            {{-- No avatar needed for self --}}
                            <div class="message-bubble">
                                <div class="message-text">Great question! Always prioritize the highest interest rate first. Focus on the 18% credit card while maintaining minimum payments on the personal loan. This will save you more money in the long run.</div>
                                <div class="message-time">11:45 AM</div>
                            </div>
                        </div>
                        {{-- More messages... --}}
                    </div>

                    {{-- Message Input Area - Refined Structure --}}
                    <div class="message-input-container">
                        <div class="message-input-area">
                            <button class="btn-icon attachment-btn" title="Attach file">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <textarea placeholder="Type your message to John..." class="message-input" rows="1"></textarea>
                            <button class="btn-icon send-btn" title="Send message">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
     <script src="{{ asset('assets/js/coach-messages.js') }}"></script>
@endpush
