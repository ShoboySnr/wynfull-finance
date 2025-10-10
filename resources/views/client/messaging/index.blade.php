@extends('layouts.client')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <!-- Messaging Page -->
    <div class="" id="messaging">
        <div class="page-content">
            <div class="page-header">
                <h1>Messages</h1>
                <p>Connect with your coach</p>
            </div>

            <div class="messaging-container">
                <!-- Coaches List Sidebar -->
                <div class="coaches-sidebar">
                    <div class="coaches-header">
                        <h3>Your Coaches</h3>
                    </div>
                    <div class="coaches-list">
                        <div class="coach-item active" data-coach="sarah-chen">
                            <div class="coach-avatar">
                                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=50&h=50&fit=crop&crop=face&auto=format" alt="Sarah Chen">
                                <div class="status-dot online"></div>
                            </div>
                            <div class="coach-info">
                                <h4>Sarah Chen, CFP</h4>
                                <p class="coach-specialty">Debt & Investment Planning</p>
                                <div class="last-message">
                                    <span class="message-preview">Great question! Always prioritize...</span>
                                    <span class="message-time">11:45 AM</span>
                                </div>
                            </div>
                            <div class="unread-badge">2</div>
                        </div>

                        <div class="coach-item" data-coach="michael-torres">
                            <div class="coach-avatar">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face&auto=format" alt="Michael Torres">
                                <div class="status-dot away"></div>
                            </div>
                            <div class="coach-info">
                                <h4>Michael Torres, CPA</h4>
                                <p class="coach-specialty">Tax & Retirement Planning</p>
                                <div class="last-message">
                                    <span class="message-preview">I've reviewed your 401k...</span>
                                    <span class="message-time">Yesterday</span>
                                </div>
                            </div>
                        </div>

                        <div class="coach-item" data-coach="lisa-wang">
                            <div class="coach-avatar">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face&auto=format" alt="Lisa Wang">
                                <div class="status-dot offline"></div>
                            </div>
                            <div class="coach-info">
                                <h4>Lisa Wang, CFP</h4>
                                <p class="coach-specialty">Real Estate & Insurance</p>
                                <div class="last-message">
                                    <span class="message-preview">The home buying checklist...</span>
                                    <span class="message-time">Mar 10</span>
                                </div>
                            </div>
                        </div>

                        <div class="coach-item" data-coach="david-kim">
                            <div class="coach-avatar">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face&auto=format" alt="David Kim">
                                <div class="status-dot online"></div>
                            </div>
                            <div class="coach-info">
                                <h4>David Kim, MBA</h4>
                                <p class="coach-specialty">Business & Entrepreneurship</p>
                                <div class="last-message">
                                    <span class="message-preview">Your business plan looks solid...</span>
                                    <span class="message-time">Mar 8</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="chat-area">
                    <!-- Coach Info Header -->
                    <div class="coach-header">
                        <div class="coach-profile">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=60&h=60&fit=crop&crop=face&auto=format" alt="Sarah Chen" class="coach-avatar">
                            <div class="coach-details">
                                <h3>Sarah Chen, CFP</h3>
                                <p>Your Coach</p>
                                <div class="coach-status online">
                                    <span class="status-indicator"></span>
                                    <span>Online</span>
                                </div>
                            </div>
                        </div>
                        <div class="coach-actions">
                            <button class="btn-secondary">
                                <i class="fas fa-calendar"></i>
                                Schedule
                            </button>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="messages-area">
                        <div class="messages-list" id="messagesList">
                            <!-- Today's Messages -->
                            <div class="message-date">
                                <span>Today</span>
                            </div>

                            <div class="message coach-message">
                                <div class="message-avatar">
                                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format" alt="Sarah Chen">
                                </div>
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">Sarah Chen</span>
                                        <span class="message-time">10:30 AM</span>
                                    </div>
                                    <div class="message-text">
                                        Hi! I've reviewed your budget from our last session. Great progress on reducing your dining out expenses! 🎉
                                    </div>
                                </div>
                            </div>

                            <div class="message coach-message">
                                <div class="message-avatar">
                                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format" alt="Sarah Chen">
                                </div>
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">Sarah Chen</span>
                                        <span class="message-time">10:32 AM</span>
                                    </div>
                                    <div class="message-text">
                                        I've attached a new debt consolidation strategy that could save you $150/month. Take a look when you have a chance.
                                    </div>
                                    <div class="message-attachment">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>Debt_Consolidation_Plan.pdf</span>
                                        <button class="attachment-download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="message user-message">
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">You</span>
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
                                        <span class="message-sender">Sarah Chen</span>
                                        <span class="message-time">11:45 AM</span>
                                    </div>
                                    <div class="message-text">
                                        Great question! Always prioritize the highest interest rate first. Focus on the 18% credit card while maintaining minimum payments on the personal loan. This will save you more money in the long run.
                                    </div>
                                </div>
                            </div>

                            <!-- Yesterday's Messages -->
                            <div class="message-date">
                                <span>Yesterday</span>
                            </div>

                            <div class="message user-message">
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">You</span>
                                        <span class="message-time">3:20 PM</span>
                                    </div>
                                    <div class="message-text">
                                        Hi Sarah! I completed the emergency fund worksheet. I think I need about $12,000 for 6 months of expenses. Is this realistic to achieve in 18 months?
                                    </div>
                                </div>
                            </div>

                            <div class="message coach-message">
                                <div class="message-avatar">
                                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format" alt="Sarah Chen">
                                </div>
                                <div class="message-content">
                                    <div class="message-header">
                                        <span class="message-sender">Sarah Chen</span>
                                        <span class="message-time">4:10 PM</span>
                                    </div>
                                    <div class="message-text">
                                        Absolutely! $12,000 in 18 months means saving about $667 per month. Based on your current budget, this is very achievable. Let's discuss this in our next session on Friday.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="message-input-container">
                            <div class="message-input-area">
                                <button class="attachment-btn">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <textarea
                                    placeholder="Type your message to Sarah..."
                                    class="message-input"
                                    rows="1"
                                    id="messageInput"
                                ></textarea>
                                <button class="send-btn" id="sendBtn">
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

