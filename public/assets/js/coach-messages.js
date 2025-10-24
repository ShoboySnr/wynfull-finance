document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.coach-clients-sidebar');
    const chatProfile = document.querySelector('.chat-header .client-profile'); // Trigger to open
    const messageContainer = document.querySelector('.coach-messaging-container');

    if (sidebar && chatProfile && messageContainer) {
        // Create an overlay div
        const overlay = document.createElement('div');
        overlay.classList.add('messaging-overlay');
        messageContainer.appendChild(overlay);

        const openSidebar = () => {
            sidebar.classList.add('open');
            overlay.classList.add('active');
        };

        const closeSidebar = () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        };

        // Open sidebar when clicking the client profile in the header
        chatProfile.addEventListener('click', openSidebar);

        // Close sidebar when clicking the overlay
        overlay.addEventListener('click', closeSidebar);

        // Close sidebar when clicking a conversation item (optional)
        const conversationList = sidebar.querySelector('.conversations-list');
        if (conversationList) {
            conversationList.addEventListener('click', (event) => {
                if (event.target.closest('.conversation-item')) {
                    // Add logic here to load the selected conversation
                    // For now, just close the sidebar
                    closeSidebar();
                }
            });
        }
    }

    // Auto-scroll messages to bottom (basic example)
    const messagesList = document.getElementById('messagesList');
    if(messagesList){
        messagesList.scrollTop = messagesList.scrollHeight;
    }

    // Dynamically adjust textarea height (basic example)
    const messageInput = document.querySelector('.message-input');
    if(messageInput){
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto'; // Reset height
            this.style.height = (this.scrollHeight) + 'px'; // Set to scroll height
        });
    }

});

