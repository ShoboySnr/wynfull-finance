document.addEventListener('DOMContentLoaded', function () {
    const resourceLibrary = document.getElementById('resource-library');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Viewer Modal Elements
    const viewerModal = document.getElementById('resourceViewerModal');
    const viewerContainer = document.getElementById('viewerContainer');
    const viewerTitle = document.getElementById('viewerTitle');
    const closeViewerBtn = document.getElementById('closeViewerModal');
    const viewerFallback = document.getElementById('viewerFallback');
    const fallbackLink = document.getElementById('fallbackDownloadLink');

    if (!csrfToken) console.error('CSRF token meta tag not found!');

    // --- Tab Switching Logic ---
    const tabs = document.querySelectorAll('.resource-tab');
    const tabContents = document.querySelectorAll('.resource-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            const activeContent = document.getElementById(targetTab);
            if (activeContent) activeContent.classList.add('active');
        });
    });

    // --- Module Logic (Completion + Viewing) ---
    // if (resourceLibrary) {
    //     resourceLibrary.addEventListener('click', function (event) {
    //         const completeButton = event.target.closest('a.mark-complete-btn, button.mark-complete-btn');
    //
    //         if (completeButton) {
    //             // 1. IMMEDIATELY prevent default link behavior (stopping the download)
    //             event.preventDefault();
    //
    //             const moduleId = completeButton.dataset.moduleId;
    //             const moduleType = completeButton.dataset.type;
    //             const moduleTitle = completeButton.dataset.title;
    //             const resourceUrl = completeButton.href;
    //
    //             const moduleItem = completeButton.closest('[data-module-id]');
    //             const collectionCard = completeButton.closest('.phase-card');
    //
    //             if (!moduleId || !moduleItem) return;
    //
    //             // 2. OPTIMISTIC UI UPDATE (Update visual state instantly)
    //             if (!moduleItem.classList.contains('completed')) {
    //                 // Store original state for revert on error
    //                 const originalBtnText = completeButton.innerHTML;
    //                 const wasPrimary = completeButton.classList.contains('primary');
    //
    //                 // Apply "Completed" styles
    //                 moduleItem.classList.add('completed');
    //                 completeButton.classList.remove('primary');
    //                 completeButton.classList.add('secondary');
    //
    //                 if (moduleItem.classList.contains('tool-card')) {
    //                     completeButton.innerHTML = '<i class="fas fa-eye"></i> View Again';
    //                 } else {
    //                     completeButton.textContent = 'View Again';
    //                 }
    //
    //                 // Update Progress Bar (if part of a collection)
    //                 if (collectionCard) updateCollectionProgress(collectionCard);
    //
    //                 // 3. Send Backend Request (Background "Fire & Forget")
    //                 fetch(`/modules/${moduleId}/complete`, {
    //                     method: 'POST',
    //                     headers: {
    //                         'Content-Type': 'application/json',
    //                         'Accept': 'application/json',
    //                         'X-CSRF-TOKEN': csrfToken
    //                     },
    //                     body: JSON.stringify({})
    //                 }).then(response => {
    //                     if (!response.ok) {
    //                         throw new Error('Network response was not ok');
    //                     }
    //                     console.log(`Module ${moduleId} synced completion.`);
    //                 }).catch(error => {
    //                     console.error('Error marking completion, reverting UI:', error);
    //                     // Revert UI changes on error
    //                     moduleItem.classList.remove('completed');
    //                     if(wasPrimary) {
    //                         completeButton.classList.add('primary');
    //                         completeButton.classList.remove('secondary');
    //                     }
    //                     completeButton.innerHTML = originalText;
    //                     if (collectionCard) updateCollectionProgress(collectionCard);
    //                 });
    //             }
    //
    //             // 4. Open Viewer (Immediately after UI update)
    //             openResourceViewer(resourceUrl, moduleType, moduleTitle);
    //         }
    //     });
    // }

    // --- Viewer Functionality ---
    function openResourceViewer(url, type, title) {
        if (!viewerModal) return;

        viewerTitle.textContent = title;
        viewerContainer.innerHTML = '';
        viewerFallback.style.display = 'none';

        let contentHtml = '';
        const lowerType = type ? type.toLowerCase() : 'file';

        // Detect type and choose viewer
        if (lowerType === 'video') {
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                const videoId = getYoutubeId(url);
                contentHtml = `<iframe class="viewer-frame" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else if (url.includes('vimeo.com')) {
                const videoId = url.split('/').pop();
                contentHtml = `<iframe class="viewer-frame" src="https://player.vimeo.com/video/${videoId}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
            } else {
                contentHtml = `<video class="viewer-video" controls autoplay><source src="${url}" type="video/mp4">Your browser does not support the video tag.</video>`;
            }
            viewerContainer.innerHTML = contentHtml;

        } else if (lowerType === 'pdf') {
            contentHtml = `<iframe class="viewer-frame" src="${url}#toolbar=0" type="application/pdf"></iframe>`;
            viewerContainer.innerHTML = contentHtml;

        } else if (['word', 'excel', 'template', 'powerpoint', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(lowerType)) {
            // Microsoft Office Viewer (Requires Public URL)
            const encodedUrl = encodeURIComponent(url);
            contentHtml = `<iframe class="viewer-frame" src="https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}" frameborder="0"></iframe>`;
            viewerContainer.innerHTML = contentHtml;

            // Setup fallback button just in case
            fallbackLink.href = url;
            // You might want to show this immediately for localhost testing or if viewer fails often
            // viewerFallback.style.display = 'block';

        } else {
            // Unknown/Unsupported type -> Show fallback download
            fallbackLink.href = url;
            viewerFallback.style.display = 'block';
        }

        viewerModal.classList.add('active');
    }

    function getYoutubeId(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    // Close Viewer Logic
    const closeViewer = () => {
        viewerModal.classList.remove('active');
        viewerContainer.innerHTML = ''; // Clears iframe/video to stop playback
    };

    if (closeViewerBtn) closeViewerBtn.addEventListener('click', closeViewer);
    if (viewerModal) {
        viewerModal.addEventListener('click', (e) => {
            if (e.target === viewerModal) closeViewer();
        });
    }


    // --- Collection Progress Helpers ---
    function updateCollectionProgress(collectionCard) {
        if (!collectionCard) return;

        // Re-query DOM to get updated counts (including the one we just marked)
        const moduleItems = collectionCard.querySelectorAll('.module-item');
        const completedModules = collectionCard.querySelectorAll('.module-item.completed');

        const progressBarFill = collectionCard.querySelector('.progress-fill');
        const progressText = collectionCard.querySelector('.progress-text');
        const headerStatusDiv = collectionCard.querySelector('.phase-status');
        const headerStatusIcon = headerStatusDiv?.querySelector('i');
        const headerStatusSpan = headerStatusDiv?.querySelector('span');

        const totalModules = moduleItems.length;
        const completedCount = completedModules.length;
        const isCollectionComplete = (totalModules > 0 && completedCount === totalModules);
        const completionPercentage = (totalModules > 0) ? Math.round((completedCount / totalModules) * 100) : 0;

        // Update Bar width & Text
        if (progressBarFill) progressBarFill.style.width = `${completionPercentage}%`;
        if (progressText) progressText.textContent = `${completedCount}/${totalModules} modules • ${completionPercentage}% complete`;

        // Update Status Badge
        if (headerStatusDiv && !collectionCard.classList.contains('locked')) {
            headerStatusDiv.classList.remove('current', 'not-started', 'completed');
            collectionCard.classList.remove('current', 'not-started', 'completed');

            if (isCollectionComplete) {
                collectionCard.classList.add('completed');
                headerStatusDiv.classList.add('completed');
                if(headerStatusIcon) headerStatusIcon.className = 'fas fa-check-circle';
                if(headerStatusSpan) headerStatusSpan.textContent = 'Completed';
            } else if (completedCount > 0) {
                collectionCard.classList.add('current');
                headerStatusDiv.classList.add('current');
                if(headerStatusIcon) headerStatusIcon.className = 'fas fa-play-circle';
                if(headerStatusSpan) headerStatusSpan.textContent = 'In Progress';
            } else {
                collectionCard.classList.add('not-started');
                headerStatusDiv.classList.add('not-started');
            }
        }

        // Unlock Next Collection immediately
        if (isCollectionComplete) {
            unlockNextCollection(collectionCard);
        }
    }

    function unlockNextCollection(completedCollectionCard) {
        let nextCollection = completedCollectionCard.nextElementSibling;
        // Find next element that is actually a phase card
        while(nextCollection) {
            if (nextCollection.classList.contains('phase-card')) break;
            nextCollection = nextCollection.nextElementSibling;
        }

        if (nextCollection && nextCollection.classList.contains('locked')) {
            // Unlock Card
            nextCollection.classList.remove('locked');

            // Reset Status Badge
            const headerStatusDiv = nextCollection.querySelector('.phase-status');
            if(headerStatusDiv) {
                headerStatusDiv.classList.remove('locked');
                headerStatusDiv.classList.add('not-started');
                const i = headerStatusDiv.querySelector('i'); if(i) i.className = 'fas fa-circle';
                const s = headerStatusDiv.querySelector('span'); if(s) s.textContent = 'Not Started';
            }

            // Reset Progress Text
            const progressText = nextCollection.querySelector('.progress-text');
            const total = nextCollection.querySelectorAll('.module-item').length;
            if(progressText) progressText.textContent = `0/${total} modules • 0% complete`;

            // Unlock Modules
            const modulesDiv = nextCollection.querySelector('.phase-modules');
            if(modulesDiv) modulesDiv.classList.remove('locked');

            nextCollection.querySelectorAll('.module-item.locked').forEach(item => {
                item.classList.remove('locked');
                const btn = item.querySelector('.module-btn');
                if(btn) {
                    btn.disabled = false;
                    btn.classList.remove('locked');
                    btn.classList.add('primary');
                    if(btn.tagName !== 'A') btn.textContent = 'Open Resource';
                }
            });
        }
    }
});
