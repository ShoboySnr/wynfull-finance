@extends('layouts.client')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <!-- Wynfull AI Page -->
    <div class="" id="wynfull-ai">
        <div class="page-content">
            <div class="page-header">
                <h1>Wynfull AI Assistant</h1>
                <p>Get personalized answers to your questions</p>
            </div>

            <div class="ai-chat-container">
                <div class="quick-questions">
                    <h3>Quick Questions</h3>
                    <div class="quick-question-buttons">
                        <button class="quick-question-btn" data-question="How much should I save for retirement?">
                            How much should I save for retirement?
                        </button>
                        <button class="quick-question-btn" data-question="What's the best way to pay off debt?">
                            What's the best way to pay off debt?
                        </button>
                        <button class="quick-question-btn" data-question="Should I invest or save more?">
                            Should I invest or save more?
                        </button>
                        <button class="quick-question-btn" data-question="How can I improve my credit score?">
                            How can I improve my credit score?
                        </button>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <div class="message ai-message">
                        <div class="message-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="message-content">
                            <p>Hello! I'm your Wynfull AI assistant. I'm here to help you with personalized financial advice. What would you like to know?</p>
                        </div>
                    </div>
                </div>

                <div class="chat-input-container">
                    <input type="text" id="chatInput" placeholder="Ask me anything about your finances...">
                    <button id="sendMessage" class="btn-primary">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

