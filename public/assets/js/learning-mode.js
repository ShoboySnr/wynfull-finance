document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const markCompleteBtns = document.querySelectorAll('.mark-complete-btn');

    markCompleteBtns.forEach(btn => {
        btn.addEventListener('click', async function (e) {
            const moduleId = this.dataset.moduleId;
            if (!moduleId) return;

            // We don't prevent default here because we WANT the navigation to happen.
            // We just fire the completion request.

            try {
                await fetch(`/modules/${moduleId}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({})
                });
                console.log(`Module ${moduleId} completion recorded.`);
            } catch (error) {
                console.error('Background completion failed:', error);
                // Navigation still happens, so it's fine.
            }
        });
    });
});
