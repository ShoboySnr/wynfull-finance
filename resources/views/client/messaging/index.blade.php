@extends('layouts.client')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')
    {{-- Added page-messaging class for styling consistency --}}
    <div class="page-content page-messaging" id="messaging">
        <div class="page-header">
            <h1>Messages</h1>
            <p>Connect directly with your coach</p>
        </div>

        {{-- START: Added Auth User ID for JS --}}
        <script>
            // Pass the authenticated user's ID to JavaScript
            const authUserId = {{ auth()->id() }};
        </script>
        {{-- END: Added Auth User ID for JS --}}

        <div class="coach-messaging-container">
            <div class="coaches-sidebar">
                <div class="coaches-header">
                    <h3>Your Coach</h3>
                </div>
                {{-- Add Search if needed --}}
                {{-- <div class="conversation-search"> ... </div> --}}
                {{-- Use conversations-list ID for consistency with JS --}}
                <div class="coaches-list" id="conversationsList">
                    {{-- START: Loop through assignments --}}
                    @forelse ($assignments as $assignment)
                        @php
                            // Get the coach from the assignment
                            $coach = $assignment->coach;
                            // Placeholder for last message/time
                            $lastMessage = $assignment->latestMessage()->first(); // Optimize this query
                            $unreadCount = $assignment->unreadMessagesCount(auth()->id()); // Needs model method
                        @endphp
                        {{-- Use conversation-item class for consistency --}}
                        <div class="conversation-item {{-- $loop->first ? 'active' : '' --}}"
                             data-assignment-id="{{ $assignment->id }}"
                             data-coach-name="{{ $coach->name }}"
                             data-coach-avatar="{{ $coach->profile->avatar_path ? asset('storage/' . $coach->profile->avatar_path) : 'https://placehold.co/50x50/0E4DA4/FFFFFF?text=' . strtoupper(substr($coach->name, 0, 1)) }}"
                             data-coach-title="{{ $coach->profile->professional_title ?? 'Financial Coach' }}"
                             {{-- Add coach online status if available from presence channels or backend --}}
                             data-coach-status="Online"
                        >
                            {{-- Use conversation-avatar class --}}
                            <div class="conversation-avatar">
                                <img src="{{ $coach->profile->avatar_path ? asset('storage/' . $coach->profile->avatar_path) : 'https://placehold.co/50x50/0E4DA4/FFFFFF?text=' . strtoupper(substr($coach->name, 0, 1)) }}" alt="{{ $coach->name }}">
                                {{-- Add dynamic status dot --}}
                                <div class="status-dot online"></div>
                            </div>
                            {{-- Use conversation-details class --}}
                            <div class="conversation-details">
                                {{-- Use conversation-header class --}}
                                <div class="conversation-header">
                                    <h4 class="client-name">{{ $coach->name }}</h4>
                                    <span class="message-time">{{ $lastMessage?->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="coach-specialty">{{ $coach->profile->title ?? 'Financial Coach' }}</p> {{-- Added title --}}
                                <p class="last-message">{{ Str::limit($lastMessage?->body, 30) ?: 'No messages yet' }}</p>
                            </div>
                            @if($unreadCount > 0)
                                <div class="unread-indicator">
                                    <span class="unread-count">{{ $unreadCount }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="no-conversations">You are not currently assigned to a coach.</p>
                    @endforelse
                    {{-- END: Loop through assignments --}}
                </div>
            </div>

            {{-- Use coach-chat-area class --}}
            <div class="coach-chat-area">
                {{-- Use chat-header ID --}}
                <div class="chat-header" id="chatHeader">
                    <div class="client-profile-placeholder">
                        Select your coach to start chatting.
                    </div>
                </div>

                {{-- Use coach-messages-area class --}}
                <div class="coach-messages-area">
                    {{-- Use messages-list ID --}}
                    <div class="messages-list" id="messagesList">
                        <div class="no-conversation-selected">
                            <i class="fas fa-comments"></i>
                            <p>Select your coach from the left sidebar.</p>
                        </div>
                    </div>

                    {{-- Use message-input-container ID, initially hidden --}}
                    <div class="message-input-container" id="messageInputContainer" style="display: none;">
                        {{-- Wrap input in a form --}}
                        <form id="messageForm">
                            {{-- Use message-input-area class --}}
                            <div class="message-input-area">
                                <button type="button" class="btn-icon attachment-btn" title="Attach file">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                {{-- Use message-input ID --}}
                                <textarea placeholder="Type your message..." class="message-input" id="messageInput" name="body" rows="1"></textarea>
                                {{-- Use send-btn ID, initially disabled --}}
                                <button type="submit" class="btn-icon send-btn" id="sendBtn" title="Send message" disabled>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- Messaging Overlay for Mobile --}}
        <div class="messaging-overlay"></div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/client-messages.js') }}"></script> {{-- New JS file --}}
@endpush
