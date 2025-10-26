document.addEventListener('DOMContentLoaded', function () {
    // Check for Echo
    if (typeof window.Echo === 'undefined') {
        console.error('Laravel Echo is not initialized.');
        // Display error in UI
        const messagesList = document.getElementById('messagesList');
        if(messagesList) messagesList.innerHTML = '<div class="error-messages"><p>Real-time connection failed. Please refresh.</p></div>';
        return;
    }

    // --- DOM Elements ---
    const conversationsList = document.getElementById('conversationsList'); // Matches ID in Blade
    const messagesList = document.getElementById('messagesList');
    const chatHeader = document.getElementById('chatHeader');
    const messageInputContainer = document.getElementById('messageInputContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentAssignmentId = null;
    let currentChannelName = null;

    // --- Helper Functions (Mostly reused from coach-messages.js) ---
    const showLoadingMessages = () => { messagesList.innerHTML = '<div class="loading-messages"><p>Loading messages...</p></div>'; };
    const showPlaceholder = () => {
        messagesList.innerHTML = `
            <div class="no-conversation-selected">
                <i class="fas fa-comments"></i>
                <p>Select a client conversation from the left sidebar.</p>
            </div>`;
        chatHeader.innerHTML = '<div class="client-profile-placeholder">Select a conversation to start chatting.</div>';
        messageInputContainer.style.display = 'none';
        currentAssignmentId = null;
    };
    const scrollToBottom = () => { requestAnimationFrame(() => { messagesList.scrollTop = messagesList.scrollHeight; }); };
    const formatTimestamp = (isoString) => {
        if (!isoString) return '';
        // Consider using Day.js or Luxon for robust date formatting
        try {
            const date = new Date(isoString);
            return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        } catch (e) {
            console.error("Error formatting date:", isoString, e);
            return '';
        }
    };
    const escapeHtml = (unsafe) => {

        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    // Render message - adjusted slightly for client perspective
    const renderMessage = (message, isOwnMessage) => {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', isOwnMessage ? 'message-client' : 'message-coach'); // Swapped classes
        messageDiv.dataset.messageId = message.id;

        let avatarHtml = '';
        if (!isOwnMessage && message.sender?.profile?.avatar_path) {
            const avatarSrc = message.sender.profile.avatar_path.startsWith('http')
                ? message.sender.profile.avatar_path
                : `/storage/${message.sender.profile.avatar_path}`;
            avatarHtml = `<img src="${avatarSrc}" alt="${message.sender.name}" class="message-avatar">`;
        } else if (!isOwnMessage && message.sender?.name) {
            const initial = message.sender.name.charAt(0).toUpperCase();
            avatarHtml = `<img src="https://placehold.co/40x40/0E4DA4/FFFFFF?text=${initial}" alt="${message.sender.name}" class="message-avatar">`;
        }
        // Don't show client's own avatar

        const attachmentHtml = message.attachment_path
            ? `<div class="message-attachment"><i class="fas fa-paperclip"></i> <span>Attachment</span> <a href="/storage/${message.attachment_path}" target="_blank" class="attachment-download" title="Download"><i class="fas fa-download"></i></a></div>`
            : '';

        messageDiv.innerHTML = `
            ${avatarHtml}
            <div class="message-bubble">
                ${message.body ? `<div class="message-text">${escapeHtml(message.body)}</div>` : ''}
                ${attachmentHtml}
                <div class="message-time">${formatTimestamp(message.created_at)}</div>
            </div>
        `;
        messagesList.appendChild(messageDiv);
    };

    const renderMessages = (messages) => {
        messagesList.innerHTML = '';
        if (!messages || messages.length === 0) {
            messagesList.innerHTML = '<div class="no-messages"><p>No messages in this conversation yet. Send one!</p></div>';
            return;
        }
        messages.slice().reverse().forEach(msg => renderMessage(msg, msg.sender_id === authUserId));
        scrollToBottom();
    };

    // Update chat header using data from the selected coach item
    const updateChatHeader = (coachItem) => {
        const coachName = coachItem.dataset.coachName || 'Coach';
        const coachAvatar = coachItem.dataset.coachAvatar || 'https://placehold.co/50x50/EBF0FF/0E4DA4?text=?';
        const coachTitle = coachItem.dataset.coachTitle || '';
        const coachStatus = coachItem.dataset.coachStatus || 'Offline';

        chatHeader.innerHTML = `
            <div class="client-profile">
                <img src="${coachAvatar}" alt="${coachName}">
                <div class="client-details">
                    <h3>${coachName}</h3>
                    <p>${coachTitle} • ${coachStatus}</p>
                </div>
            </div>
            <div class="chat-actions">
                <button class="btn-icon" title="More options"><i class="fas fa-ellipsis-v"></i></button>
            </div>`;
    };

    // Update conversation preview in the sidebar
    const updateConversationPreview = (assignmentId, messageBody, timestamp) => {
        const conversationItem = conversationsList.querySelector(`.conversation-item[data-assignment-id="${assignmentId}"]`);
        if (conversationItem) {
            const lastMessageEl = conversationItem.querySelector('.last-message');
            const timeEl = conversationItem.querySelector('.message-time'); // Make sure this class exists
            if (lastMessageEl) lastMessageEl.textContent = messageBody ? messageBody.substring(0, 30) + (messageBody.length > 30 ? '...' : '') : 'Attachment';
            // Need correct selector for time in client view list item
            const headerTimeEl = conversationItem.querySelector('.conversation-header .message-time');
            if (headerTimeEl) headerTimeEl.textContent = formatTimestamp(timestamp);
            conversationsList.prepend(conversationItem);
        }
    };

    // Mark messages as read
    const markMessagesRead = async (assignmentId) => {
        if (!assignmentId) return;
        try {
            await fetch(`/chat/${assignmentId}/read`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            console.log(`Marked assignment ${assignmentId} as read.`);
            const conversationItem = conversationsList.querySelector(`.conversation-item[data-assignment-id="${assignmentId}"]`);
            const unreadIndicator = conversationItem?.querySelector('.unread-indicator');
            if(unreadIndicator) unreadIndicator.style.display = 'none';

        } catch (error) {
            console.error("Error marking messages as read:", error);
        }
    };

    // --- Core Logic ---
    const loadConversation = async (assignmentId, coachItem) => {
        if (!assignmentId || assignmentId === currentAssignmentId) return;

        console.log(`Loading assignment ID: ${assignmentId}`);
        currentAssignmentId = assignmentId;

        // Update Sidebar Visual State
        conversationsList.querySelectorAll('.conversation-item').forEach(item => item.classList.remove('active'));
        coachItem.classList.add('active');
        const unreadIndicator = coachItem.querySelector('.unread-indicator');
        if(unreadIndicator) unreadIndicator.style.display = 'none';


        // Show Loading, Update Header, Show Input
        showLoadingMessages();
        updateChatHeader(coachItem);
        messageInputContainer.style.display = 'block';
        messageInput.value = '';
        messageInput.style.height = 'auto';
        sendBtn.disabled = true;
        messageInput.focus();

        try {
            // Fetch Messages (Client uses the same endpoint)
            const response = await fetch(`/chat/${assignmentId}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();

            // Render Messages
            renderMessages(data.data); // data.data has messages

            // Leave previous Echo channel
            if (currentChannelName) {
                window.Echo.leave(currentChannelName);
                console.log(`Left channel: ${currentChannelName}`);
            }

            // Join new Echo channel (Client uses the same channel name)
            currentChannelName = `chat.assignment.${assignmentId}`;

            console.log(`Joining channel: ${currentChannelName}`);
            window.Echo.private(currentChannelName)
                .listen('.message.sent', (eventData) => {
                    console.log('Message received via Echo:', eventData);

                    const message = { /* Map eventData to message object */
                        id: eventData.id, sender_id: eventData.sender_id, body: eventData.body,
                        attachment_path: eventData.attachment, created_at: eventData.created_at,
                        coach_client_assignment_id: assignmentId
                    };

                    if (message.coach_client_assignment_id == currentAssignmentId && message.sender_id !== authUserId) {
                        renderMessage(message, false); // Render coach's message
                        scrollToBottom();
                        markMessagesRead(currentAssignmentId);
                    }
                    updateConversationPreview(
                        message.coach_client_assignment_id,
                        message.body || 'Attachment',
                        message.created_at
                    );
                    // Handle unread indicator if conversation is not active
                    if (message.coach_client_assignment_id !== currentAssignmentId && message.sender_id !== authUserId) {
                        const targetConvItem = conversationsList.querySelector(`.conversation-item[data-assignment-id="${message.coach_client_assignment_id}"]`);
                    }
                });
            console.log(`Listening on channel: ${currentChannelName}`);

            await markMessagesRead(assignmentId);

        } catch (error) {
            console.error("Error loading conversation:", error);
            messagesList.innerHTML = '<div class="error-messages"><p>Could not load messages. Please try again.</p></div>';
            messageInputContainer.style.display = 'none';
        }
    };

    // Send message (Client uses the same endpoint)
    const sendMessage = async (event) => {
        event.preventDefault();
        const messageBody = messageInput.value.trim();
        if (!messageBody || !currentAssignmentId) return;

        sendBtn.disabled = true;
        const originalMessage = messageBody;
        const tempId = `temp-${Date.now()}`;

        // Optimistic UI update (render client's message)
        const tempMessage = { id: tempId, body: messageBody, created_at: new Date().toISOString(), sender_id: authUserId };
        renderMessage(tempMessage, true);
        scrollToBottom();
        messageInput.value = '';
        messageInput.style.height = 'auto';

        const formData = new FormData();
        formData.append('body', messageBody);

        try {
            const response = await fetch(`/chat/${currentAssignmentId}`, { // POST to assignment endpoint
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: formData
            });
            if (!response.ok) { /* ... error handling ... */ throw new Error('Failed to send'); }
            const result = await response.json();
            updateConversationPreview(currentAssignmentId, result.data.body || 'Attachment', result.data.created_at);
            const tempBubble = messagesList.querySelector(`[data-message-id="${tempId}"]`);
            if(tempBubble) tempBubble.dataset.messageId = result.data.id;
        } catch (error) {
            console.error("Error sending message:", error);
            messageInput.value = originalMessage;
            const tempBubble = messagesList.querySelector(`[data-message-id="${tempId}"]`);
            if(tempBubble) tempBubble.classList.add('message-failed'); // Mark failed
            alert(`Failed to send message: ${error.message}`);
        } finally {
            sendBtn.disabled = messageInput.value.trim().length === 0;
            messageInput.focus();
        }
    };

    // --- Event Listeners ---
    // Conversation selection (use .conversation-item class)
    conversationsList.addEventListener('click', (event) => {
        const coachItem = event.target.closest('.conversation-item'); // Changed selector
        if (coachItem && coachItem.dataset.assignmentId) {
            loadConversation(parseInt(coachItem.dataset.assignmentId), coachItem);
            // Mobile sidebar close (if applicable)
        }
    });

    // Input handling
    messageInput.addEventListener('input', () => {
        sendBtn.disabled = messageInput.value.trim().length === 0;
    });
    messageForm.addEventListener('submit', sendMessage);
    messageInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            if (!sendBtn.disabled) messageForm.requestSubmit();
        }
    });
    messageInput.addEventListener('input', function() { /* auto-resize */ });

    // --- Initial State ---
    showPlaceholder();

    // Automatically load the first conversation if available
    const firstConversation = conversationsList.querySelector('.conversation-item');
    if (firstConversation && firstConversation.dataset.assignmentId) {
        loadConversation(parseInt(firstConversation.dataset.assignmentId), firstConversation);
    }


});
