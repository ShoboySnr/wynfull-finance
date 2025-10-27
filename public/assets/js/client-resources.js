document.addEventListener('DOMContentLoaded', function () {
    const phasesContainer = document.getElementById('phasesContainer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'); // Get CSRF token

    if (!phasesContainer || !csrfToken) {
        if (!csrfToken) console.error('CSRF token meta tag not found!');
        return;
    }

    // --- Tab Switching Logic ---
    const tabs = document.querySelectorAll('.resource-tab');
    const tabContents = document.querySelectorAll('.resource-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.dataset.tab;

            // Deactivate all tabs and content
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Activate clicked tab and corresponding content
            tab.classList.add('active');
            const activeContent = document.getElementById(targetTab);
            if (activeContent) {
                activeContent.classList.add('active');
            }
        });
    });


    // --- Module Completion Logic ---
    phasesContainer.addEventListener('click', async function (event) {
        const completeButton = event.target.closest('a.mark-complete-btn'); // Target the link

        if (completeButton) {
            // Prevent default link behavior ONLY IF we successfully mark complete via JS
            // Allow the link to open normally otherwise or on error.

            const moduleId = completeButton.dataset.moduleId;
            const moduleItem = completeButton.closest('.module-item');
            const collectionCard = completeButton.closest('.phase-card');

            if (!moduleId || !moduleItem || !collectionCard) return;

            // Avoid re-marking if already visually complete (can refine this)
            if (moduleItem.classList.contains('completed')) {
                console.log(`Module ${moduleId} already visually marked complete.`);
                // Allow link to open without sending request again
                return;
            }

            // Visual feedback: Indicate processing (optional)
            completeButton.textContent = 'Opening...';
            completeButton.style.opacity = '0.7';


            try {
                // Send request to backend to mark module as complete
                // ** IMPORTANT: Replace with your actual endpoint **
                const response = await fetch(`/modules/${moduleId}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({})
                });

                if (!response.ok) {
                    // If backend fails, log error but still let the link open
                    console.error(`Failed to mark module ${moduleId} complete. Status: ${response.status}`);
                    completeButton.textContent = 'Open Resource'; // Reset button text
                    completeButton.style.opacity = '1';
                    // Allow default link behavior to proceed
                    return;
                }

                // If backend succeeds:
                console.log(`Module ${moduleId} marked complete.`);
                event.preventDefault(); // Prevent default link navigation ONLY on success

                // 1. Update the Module UI
                moduleItem.classList.add('completed');
                completeButton.textContent = 'View Again';
                completeButton.classList.remove('primary');
                completeButton.classList.add('secondary'); // Change button style
                completeButton.style.opacity = '1'; // Reset opacity


                // 2. Update Collection Progress
                updateCollectionProgress(collectionCard);

                // Open link manually AFTER successful update
                window.open(completeButton.href, '_blank');


            } catch (error) {
                // Network error etc. - log error, let the link open
                console.error('Error marking module complete:', error);
                completeButton.textContent = 'Open Resource'; // Reset button text
                completeButton.style.opacity = '1';
                // Allow default link behavior to proceed
            }
        }
    });

    // --- Helper Function to Update Collection UI ---
    function updateCollectionProgress(collectionCard) {
        if (!collectionCard) return;

        const collectionId = collectionCard.dataset.collectionId;
        const moduleItems = collectionCard.querySelectorAll('.module-item');
        const completedModules = collectionCard.querySelectorAll('.module-item.completed');
        const progressBarFill = collectionCard.querySelector('.progress-fill');
        const progressText = collectionCard.querySelector('.progress-text');
        const headerStatusDiv = collectionCard.querySelector('.phase-status'); // Status div in header
        const headerStatusIcon = headerStatusDiv?.querySelector('i');
        const headerStatusSpan = headerStatusDiv?.querySelector('span');


        const totalModules = moduleItems.length;
        const completedCount = completedModules.length;
        const isCollectionComplete = (totalModules > 0 && completedCount === totalModules);
        const completionPercentage = (totalModules > 0) ? Math.round((completedCount / totalModules) * 100) : 0;

        // Update progress bar and text
        if (progressBarFill) progressBarFill.style.width = `${completionPercentage}%`;
        if (progressText) progressText.textContent = `${completedCount}/${totalModules} modules • ${completionPercentage}% complete`;

        // Update Header Status (if not already complete or locked)
        if (headerStatusDiv && !collectionCard.classList.contains('locked') && !collectionCard.classList.contains('completed')) {
            if (isCollectionComplete) {
                collectionCard.classList.remove('current', 'not-started');
                collectionCard.classList.add('completed');
                headerStatusDiv.classList.remove('current', 'not-started');
                headerStatusDiv.classList.add('completed');
                if(headerStatusIcon) headerStatusIcon.className = 'fas fa-check-circle';
                if(headerStatusSpan) headerStatusSpan.textContent = 'Completed';
            } else if (completedCount > 0 && !collectionCard.classList.contains('current')) {
                // Mark as "In Progress" if at least one is done and it wasn't already
                collectionCard.classList.remove('not-started');
                collectionCard.classList.add('current');
                headerStatusDiv.classList.remove('not-started');
                headerStatusDiv.classList.add('current');
                if(headerStatusIcon) headerStatusIcon.className = 'fas fa-play-circle';
                if(headerStatusSpan) headerStatusSpan.textContent = 'In Progress';
            }
            // Handle 'Not Started' case if needed (usually set on initial load)
        }


        // Unlock Next Collection if this one is now complete
        if (isCollectionComplete) {
            unlockNextCollection(collectionCard);
        }
    }

    // --- Helper Function to Unlock Next Collection ---
    function unlockNextCollection(completedCollectionCard) {
        let nextCollection = completedCollectionCard.nextElementSibling;
        // Skip non-element nodes (like text nodes)
        while(nextCollection && nextCollection.nodeType !== 1) {
            nextCollection = nextCollection.nextElementSibling;
        }


        if (nextCollection && nextCollection.classList.contains('phase-card') && nextCollection.classList.contains('locked')) {
            console.log(`Unlocking collection: ${nextCollection.dataset.collectionId}`);
            nextCollection.classList.remove('locked');

            // Update status in the header
            const headerStatusDiv = nextCollection.querySelector('.phase-status');
            const headerStatusIcon = headerStatusDiv?.querySelector('i');
            const headerStatusSpan = headerStatusDiv?.querySelector('span');
            if(headerStatusDiv) headerStatusDiv.classList.remove('locked');
            // Determine if it should be 'Not Started' or 'In Progress' (likely Not Started)
            headerStatusDiv?.classList.add('not-started'); // Assume not started initially
            if(headerStatusIcon) headerStatusIcon.className = 'fas fa-circle'; // Not started icon
            if(headerStatusSpan) headerStatusSpan.textContent = 'Not Started';


            // Update progress text
            const progressText = nextCollection.querySelector('.progress-text');
            const totalModules = nextCollection.querySelectorAll('.module-item').length;
            if (progressText) progressText.textContent = `0/${totalModules} modules • 0% complete`; // Reset progress text


            // Update modules within the unlocked collection
            const modulesDiv = nextCollection.querySelector('.phase-modules');
            if(modulesDiv) modulesDiv.classList.remove('locked');
            nextCollection.querySelectorAll('.module-item').forEach(moduleItem => {
                moduleItem.classList.remove('locked');
                const button = moduleItem.querySelector('.module-btn');
                if (button && button.classList.contains('locked')) {
                    button.disabled = false;
                    button.classList.remove('locked');
                    button.classList.add('primary'); // Or appropriate class
                    button.textContent = 'Start Module'; // Or 'Open Resource'
                    // Convert button to link if it wasn't already
                    if (button.tagName === 'BUTTON') {
                        const link = document.createElement('a');
                        link.href = button.dataset.href || '#'; // Need to store href on button or get dynamically
                        link.target = '_blank';
                        link.className = button.className; // Copy classes
                        link.dataset.moduleId = button.dataset.moduleId;
                        link.innerHTML = button.innerHTML;
                        button.parentNode.replaceChild(link, button);
                    } else {
                        // It's already a link, just update text/class
                        button.textContent = 'Start Module';
                    }
                }
            });
        } else {
            console.log("No next locked collection found or already unlocked.");
        }
    }

});
