document.addEventListener('DOMContentLoaded', function () {
    // --- DOM Elements ---
    const conversationsList = document.getElementById('conversationsList');
    const messagesList = document.getElementById('messagesList');
    const chatHeader = document.getElementById('chatHeader');
    const messageInputContainer = document.getElementById('messageInputContainer');
    const messageForm = document.getElementById('messageForm'); // Form element
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentAssignmentId = null; // Track the currently active assignment
    let currentChannelName = null;  // Track the current Echo channel name

    // --- Helper Functions ---
    const showLoadingMessages = () => {
        messagesList.innerHTML = '<div class="loading-messages"><p>Loading messages...</p></div>';
    };

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

    const scrollToBottom = () => {
        // Debounce or throttle this if it causes performance issues
        requestAnimationFrame(() => {
            messagesList.scrollTop = messagesList.scrollHeight;
        });
    };

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

    const renderMessage = (message, isOwnMessage) => {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', isOwnMessage ? 'message-coach' : 'message-client');
        messageDiv.dataset.messageId = message.id; // Add message ID for potential future use

        let avatarHtml = '';
        // If it's not our message and sender info is available (check your API response structure)
        if (!isOwnMessage && message.sender?.profile?.avatar_path) {
            // Assuming sender relation is loaded with profile in your API response
            const avatarSrc = message.sender.profile.avatar_path.startsWith('http')
                ? message.sender.profile.avatar_path
                : `/storage/${message.sender.profile.avatar_path}`; // Construct URL correctly
            avatarHtml = `<img src="${avatarSrc}" alt="${message.sender.name}" class="message-avatar">`;
        } else if (!isOwnMessage && message.sender?.name) {
            // Fallback to placeholder if avatar URL is missing
            const initial = message.sender.name.charAt(0).toUpperCase();
            avatarHtml = `<img src="https://placehold.co/40x40/EBF0FF/0E4DA4?text=${initial}" alt="${message.sender.name}" class="message-avatar">`;
        }

        // Basic check for attachment (enhance later)
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
        // Append or Prepend? Append for initial load, maybe prepend for new messages?
        // Let's stick to append for simplicity now.
        messagesList.appendChild(messageDiv);
    };

    // Simple HTML escaping function
    const escapeHtml = (unsafe) => {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    const renderMessages = (messages) => {
        messagesList.innerHTML = ''; // Clear existing
        if (!messages || messages.length === 0) {
            messagesList.innerHTML = '<div class="no-messages"><p>No messages in this conversation yet. Send one!</p></div>';
            return;
        }
        // Messages from API are newest first (orderByDesc), render them oldest first
        messages.slice().reverse().forEach(msg => renderMessage(msg, parseInt(msg.sender_id) === parseInt(authUserId)));
        scrollToBottom();
    };

    // Updates the chat header using data from the selected conversation item
    const updateChatHeader = (conversationItem) => {
        const clientName = conversationItem.dataset.clientName || 'Client';
        const clientAvatar = conversationItem.dataset.clientAvatar || 'https://placehold.co/50x50/EBF0FF/0E4DA4?text=?';
        const clientPlan = conversationItem.dataset.clientPlan || '';
        const clientStatus = conversationItem.dataset.clientStatus || 'Offline'; // Fetch real status later

        chatHeader.innerHTML = `
            <div class="client-profile">
                <img src="${clientAvatar}" alt="${clientName}">
                <div class="client-details">
                    <h3>${clientName}</h3>
                    <p>${clientPlan} • ${clientStatus}</p>
                </div>
            </div>
            <div class="chat-actions">
                <button class="btn-icon" title="More options"><i class="fas fa-ellipsis-v"></i></button>
            </div>`;
    };

    const updateConversationPreview = (assignmentId, messageBody, timestamp) => {
        const conversationItem = conversationsList.querySelector(`.conversation-item[data-assignment-id="${assignmentId}"]`);
        if (conversationItem) {
            const lastMessageEl = conversationItem.querySelector('.last-message');
            const timeEl = conversationItem.querySelector('.message-time');
            if (lastMessageEl) lastMessageEl.textContent = messageBody ? messageBody.substring(0, 30) + (messageBody.length > 30 ? '...' : '') : 'Attachment';
            if (timeEl) timeEl.textContent = formatTimestamp(timestamp);
            conversationsList.prepend(conversationItem); // Move to top
        }
    };

    // Mark messages as read via API
    const markMessagesRead = async (assignmentId) => {
        if (!assignmentId) return;
        try {
            await fetch(`/chat/${assignmentId}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            console.log(`Marked assignment ${assignmentId} as read.`);
            // Optionally update UI immediately (e.g., clear unread count visually)
            const conversationItem = conversationsList.querySelector(`.conversation-item[data-assignment-id="${assignmentId}"]`);
            const unreadIndicator = conversationItem?.querySelector('.unread-indicator');
            if(unreadIndicator) unreadIndicator.style.display = 'none';

        } catch (error) {
            console.error("Error marking messages as read:", error);
        }
    };

    // --- Core Logic ---

    const loadConversation = async (assignmentId, conversationItem) => {
        if (!assignmentId || assignmentId === currentAssignmentId) return;

        console.log(`Loading assignment ID: ${assignmentId}`);
        currentAssignmentId = assignmentId;

        // Update Sidebar Visual State
        conversationsList.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('active');
        });
        conversationItem.classList.add('active');
        // Clear unread indicator visually immediately
        const unreadIndicator = conversationItem.querySelector('.unread-indicator');
        if(unreadIndicator) unreadIndicator.style.display = 'none';


        // Show Loading, Update Header, Show Input
        showLoadingMessages();
        updateChatHeader(conversationItem); // Use data from the clicked item
        messageInputContainer.style.display = 'block'; // Use block for form
        messageInput.value = '';
        messageInput.style.height = 'auto'; // Reset textarea height
        sendBtn.disabled = true;
        messageInput.focus();

        try {
            // Fetch Messages
            const response = await fetch(`/chat/${assignmentId}`); // Use correct endpoint
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json(); // Expecting { ok: true, data: messages[], meta: {...} }

            // Render Messages
            renderMessages(data.data);

            // Leave previous Echo channel
            if (currentChannelName) {
                window.Echo.leave(currentChannelName);
                console.log(`Left channel: ${currentChannelName}`);
            }

            // Join new Echo channel
            currentChannelName = `chat.assignment.${assignmentId}`;

            console.log(`Joining channel: ${currentChannelName}`);
            window.Echo.private(currentChannelName)
                .listen('.message.sent', (event) => {
                    console.log('Message received via Echo:', event);

                    const message = { /* Map eventData to message object */
                        id: event.id, sender_id: event.sender_id, body: event.body,
                        attachment_path: event.attachment, created_at: event.created_at,
                        coach_client_assignment_id: assignmentId
                    };

                    if (message.coach_client_assignment_id == currentAssignmentId && parseInt(message.sender_id) !== parseInt(authUserId)) {
                        renderMessage(message, false);
                        scrollToBottom();
                        // Mark as read immediately if the conversation is open
                        markMessagesRead(currentAssignmentId);
                    }
                    // Update preview regardless of active chat
                    updateConversationPreview(
                        message.coach_client_assignment_id,
                        message.body || 'Attachment',
                        message.created_at
                    );

                    // Show unread indicator if the conversation is NOT active
                    if (message.coach_client_assignment_id != currentAssignmentId && parseInt(message.sender_id) !== parseInt(authUserId)) {
                        const targetConvItem = conversationsList.querySelector(`.conversation-item[data-assignment-id="${message.coach_client_assignment_id}"]`);
                        let indicator = targetConvItem?.querySelector('.unread-indicator');
                        let countEl = targetConvItem?.querySelector('.unread-count');

                        if(targetConvItem && !indicator){ // Create indicator if it doesn't exist
                            indicator = document.createElement('div');
                            indicator.className = 'unread-indicator';
                            countEl = document.createElement('span');
                            countEl.className = 'unread-count';
                            countEl.textContent = '0';
                            indicator.appendChild(countEl);
                            targetConvItem.appendChild(indicator); // Append to item
                        }

                        if(indicator && countEl){
                            indicator.style.display = 'flex';
                            let count = parseInt(countEl.textContent || '0') + 1;
                            countEl.textContent = count;
                        }
                    }
                });
            console.log(`Listening on channel: ${currentChannelName}`);

            // Mark messages as read after loading
            markMessagesRead(assignmentId);


        } catch (error) {
            console.error("Error loading conversation:", error);
            messagesList.innerHTML = '<div class="error-messages"><p>Could not load messages. Please try again.</p></div>';
            messageInputContainer.style.display = 'none';
        }
    };

    // Function to send a message (using FormData for potential attachments)
    const sendMessage = async (event) => {
        event.preventDefault();
        const messageBody = messageInput.value.trim();
        if (!messageBody || !currentAssignmentId) return;

        sendBtn.disabled = true;
        const originalMessage = messageBody;

        // Optimistic UI update
        const tempMessage = {
            id: `temp-${Date.now()}`, // Temporary ID
            body: messageBody,
            created_at: new Date().toISOString(),
            sender_id: authUserId
        };
        renderMessage(tempMessage, true);
        scrollToBottom();
        messageInput.value = '';
        messageInput.style.height = 'auto'; // Reset height


        // Prepare form data (allows for file uploads later)
        const formData = new FormData();
        formData.append('body', messageBody);
        // formData.append('attachment', fileInputElement.files[0]); // If adding attachments

        try {
            const response = await fetch(`/chat/${currentAssignmentId}`, { // Use correct endpoint
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                    // 'Content-Type': 'multipart/form-data' // Browser sets this automatically with FormData
                },
                body: formData
            });

            if (!response.ok) {
                // Try to parse error message from backend if available
                let errorMsg = `HTTP error! status: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMsg = errorData.message || JSON.stringify(errorData.errors);
                } catch(e) {}
                throw new Error(errorMsg);
            }

            const result = await response.json(); // Expecting { ok: true, data: { message details } }
            console.log('Message sent successfully:', result.data);

            // Update conversation preview
            updateConversationPreview(currentAssignmentId, result.data.body || 'Attachment', result.data.created_at);

            // Optional: Update the temporary message bubble with the real ID from result.data.id
            const tempBubble = messagesList.querySelector(`[data-message-id="${tempMessage.id}"]`);
            if(tempBubble) tempBubble.dataset.messageId = result.data.id;


        } catch (error) {
            console.error("Error sending message:", error);
            messageInput.value = originalMessage; // Put text back
            // Mark the temp bubble as failed or remove it
            const tempBubble = messagesList.querySelector(`[data-message-id="${tempMessage.id}"]`);
            if(tempBubble) tempBubble.classList.add('message-failed'); // Add styling for .message-failed
            alert(`Failed to send message: ${error.message}`);
        } finally {
            sendBtn.disabled = messageInput.value.trim().length === 0; // Re-enable based on input
            messageInput.focus();
        }
    };


    // --- Event Listeners ---

    // Conversation selection
    conversationsList.addEventListener('click', (event) => {
        const conversationItem = event.target.closest('.conversation-item');
        if (conversationItem && conversationItem.dataset.assignmentId) {
            loadConversation(parseInt(conversationItem.dataset.assignmentId), conversationItem);
            // Mobile sidebar close logic (if applicable)
            const sidebar = document.querySelector('.coach-clients-sidebar');
            const overlay = document.querySelector('.messaging-overlay');
            if(sidebar && overlay && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
        }
    });

    // Enable/Disable send button based on input
    messageInput.addEventListener('input', () => {
        sendBtn.disabled = messageInput.value.trim().length === 0;
    });

    // Send message on form submit (handles button click and Enter)
    messageForm.addEventListener('submit', sendMessage);

    // Prevent newline on Enter unless Shift is pressed
    messageInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            // Trigger form submit instead of calling sendMessage directly
            // This ensures any other form logic runs
            if (!sendBtn.disabled) { // Only submit if send is enabled
                messageForm.requestSubmit(); // Modern way to trigger submit
                // messageForm.submit(); // Older way
            }
        }
    });

    // Textarea auto-resize
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });


    // --- Initial State ---
    showPlaceholder();

    // Optional: Load the first conversation automatically if one exists
    const firstConversation = conversationsList.querySelector('.conversation-item');
    if (firstConversation && firstConversation.dataset.assignmentId) {
        // loadConversation(parseInt(firstConversation.dataset.assignmentId), firstConversation);
    }


    // --- Mobile Sidebar Toggle ---
    const sidebar = document.querySelector('.coach-clients-sidebar');
    const headerProfileArea = document.querySelector('.chat-header .client-profile'); // Clickable area in header
    const overlay = document.querySelector('.messaging-overlay');

    if (sidebar && headerProfileArea && overlay ) {
        const openSidebar = () => {
            sidebar.classList.add('open');
            overlay.classList.add('active');
        };
        const closeSidebar = () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        };

        headerProfileArea.addEventListener('click', (event)=>{
            if(!sidebar.classList.contains('open')){
                openSidebar();
            }
        });
        overlay.addEventListener('click', closeSidebar);
    }

});

