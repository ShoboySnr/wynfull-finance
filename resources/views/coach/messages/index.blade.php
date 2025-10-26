@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <div class="page-content page-messaging" id="coach-messages">
        <div class="page-header">
            <h1>Messages</h1>
            <p>Communicate directly with your clients</p>
        </div>

        <script>
        const authUserId = {{ auth()->id() }};
        </script>

        <div class="coach-messaging-container">
            <!-- Conversations List Sidebar -->
            <div class="coach-clients-sidebar">
                <div class="clients-header">
                    <h3>Conversations</h3>
                    {{-- <button class="btn-secondary new-message-btn btn-sm"><i class="fas fa-plus"></i> New</button> --}}
                </div>
                <div class="conversation-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search conversations...">
                </div>
                <div class="conversations-list" id="conversationsList">
                    {{-- START: Loop through assignments --}}
                    @forelse ($assignments as $assignment)
                        @php
                            // Get the client from the assignment
                            $client = $assignment->client;
                            $lastMessage = $assignment->latestMessage()->first();
                            $unreadCount = $assignment->unreadMessagesCount(auth()->id());
                        @endphp
                        <div class="conversation-item {{-- $loop->first ? 'active' : '' --}}"
                             data-assignment-id="{{ $assignment->id }}"
                             data-client-name="{{ $client->name }}"
                             data-client-avatar="{{ $client->profile->avatar_path ? asset('storage/' . $client->profile->avatar_path) : 'https://placehold.co/50x50/EBF0FF/0E4DA4?text=' . strtoupper(substr($client->name, 0, 1)) }}"
                             data-client-status="{{-- $client->status ?? 'Online' --}}"
                             data-client-plan="{{-- $client->plan_name ?? '' --}}"
                        >
                            <div class="conversation-avatar">
                                <img src="{{ $client->profile->avatar_path ? asset('storage/' . $client->profile->avatar_path) : 'https://placehold.co/50x50/EBF0FF/0E4DA4?text=' . strtoupper(substr($client->name, 0, 1)) }}" alt="{{ $client->name }}">
                                {{-- Add dynamic status dot based on client presence if available --}}
                                <div class="status-dot online"></div>
                            </div>
                            <div class="conversation-details">
                                <div class="conversation-header">
                                    <h4 class="client-name">{{ $client->name }}</h4>
                                    <span class="message-time">{{ $lastMessage?->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="last-message">{{ Str::limit($lastMessage?->body, 30) ?: 'No messages yet' }}</p>
                            </div>
                            @if($unreadCount > 0)
                                <div class="unread-indicator">
                                    <span class="unread-count">{{ $unreadCount }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="no-conversations">No active client assignments.</p>
                    @endforelse
                    {{-- END: Loop through assignments --}}
                </div>
            </div>

            <!-- Chat Area -->
            <div class="coach-chat-area">
                {{-- START: Updated Chat Header Placeholder --}}
                <div class="chat-header" id="chatHeader">
                    <div class="client-profile-placeholder">
                        Select a conversation to start chatting.
                    </div>
                </div>
                {{-- END: Updated Chat Header Placeholder --}}

                <div class="coach-messages-area">
                    {{-- START: Updated Messages List Placeholder --}}
                    <div class="messages-list" id="messagesList">
                        <div class="no-conversation-selected">
                            <i class="fas fa-comments"></i>
                            <p>Select a client conversation from the left sidebar.</p>
                        </div>
                    </div>
                    {{-- END: Updated Messages List Placeholder --}}

                    {{-- START: Message Input - Hidden Initially --}}
                    <div class="message-input-container" id="messageInputContainer" style="display: none;">
                        <form id="messageForm"> {{-- Wrap input in a form --}}
                            <div class="message-input-area">
                                <button type="button" class="btn-icon attachment-btn" title="Attach file">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <textarea placeholder="Type your message..." class="message-input" id="messageInput" name="body" rows="1"></textarea>
                                <button type="submit" class="btn-icon send-btn" id="sendBtn" title="Send message" disabled>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
{{--                             <input type="file" name="attachment" style="display:none;" id="attachmentInput">--}}
                        </form>
                    </div>
                    {{-- END: Message Input - Hidden Initially --}}
                </div>
            </div>
        </div>
        <div class="messaging-overlay"></div>
    </div>

@endsection

@push('scripts')
     <script src="{{ asset('assets/js/coach-messages.js') }}"></script>
@endpush
