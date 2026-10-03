<!-- Global Button Loading Logic -->
<script>
    document.addEventListener('submit', function(e) {
        // Find the submit button inside the submitting form
        const btn = e.target.querySelector('button[type="submit"], input[type="submit"]');
        
        if (btn && !btn.hasAttribute('data-no-loader')) {
            // Skip if the event was prevented (e.g., validation failed/AJAX) or if it's inside a modal
            if (e.defaultPrevented || btn.closest('.modal')) {
                return;
            }

            // Check if it's one of our action buttons
            const isActionBtn = btn.classList.contains('save-btn') || 
                              btn.classList.contains('save_btn') || 
                              btn.classList.contains('add_list_btn') ||
                              btn.classList.contains('save-btn') ||
                              btn.innerText.toLowerCase().includes('save') ||
                              btn.innerText.toLowerCase().includes('update') ||
                              btn.innerText.toLowerCase().includes('apply');

            if (isActionBtn) {
                // Disable button to prevent double submission
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';

                // Inject Spinner and Processing Text
                btn.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <span>Processing...</span>
                        <svg class="animate-spin" style="width: 16px; height: 16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                `;
            }
        }
    });

    // Global Spinner Animation CSS (if tailwind is not active in any page)
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    `;
    document.head.appendChild(style);
</script>
