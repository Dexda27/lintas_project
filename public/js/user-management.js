/**
 * User Management Delete Modal Handler
 *
 * This script handles the delete confirmation modal for user management.
 * It replaces the default browser confirm dialog with a custom Tailwind-styled modal.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const modal = document.getElementById('deleteModal');
    const modalUserName = document.getElementById('modal-user-name');
    const modalUserEmail = document.getElementById('modal-user-email');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const modalBackdrop = modal.querySelector('.bg-gray-500');

    let currentForm = null; // Store reference to the form being deleted

    /**
     * Show the delete confirmation modal
     */
    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('modal-backdrop');

        // Add animation classes
        const modalPanel = modal.querySelector('.relative.inline-block');
        modalPanel.classList.add('modal-enter');

        // Trigger animation
        setTimeout(() => {
            modalPanel.classList.remove('modal-enter');
            modalPanel.classList.add('modal-enter-active');
        }, 10);

        // Focus on cancel button for accessibility
        cancelDeleteBtn.focus();

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    /**
     * Hide the delete confirmation modal
     */
    function hideModal() {
        const modalPanel = modal.querySelector('.relative.inline-block');

        // Add exit animation
        modalPanel.classList.remove('modal-enter-active');
        modalPanel.classList.add('modal-exit');

        setTimeout(() => {
            modalPanel.classList.remove('modal-exit');
            modalPanel.classList.add('modal-exit-active');
        }, 10);

        // Hide modal after animation
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('modal-backdrop');
            modalPanel.classList.remove('modal-exit-active');

            // Reset form reference
            currentForm = null;

            // Restore body scroll
            document.body.style.overflow = '';
        }, 150);
    }

    /**
     * Handle delete button clicks
     */
    function handleDeleteClick(event) {
        event.preventDefault();
        event.stopPropagation();

        // Get the delete button and form
        const deleteBtn = event.currentTarget;
        const form = deleteBtn.closest('.delete-form');

        if (!form) {
            console.error('Delete form not found');
            return;
        }

        // Store form reference
        currentForm = form;

        // Get user data from button attributes
        const userName = deleteBtn.dataset.userName || 'Unknown User';
        const userEmail = deleteBtn.dataset.userEmail || 'Unknown Email';

        // Update modal content
        modalUserName.textContent = userName;
        modalUserEmail.textContent = userEmail;

        // Show modal
        showModal();
    }

    /**
     * Handle confirm delete button click
     */
    function handleConfirmDelete() {
        if (currentForm) {
            // Add loading state to button
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = `
                <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>
                Deleting...
            `;

            // Re-initialize Lucide for the new icon
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }

            // Submit the form
            currentForm.submit();
        }
    }

    /**
     * Handle cancel button click
     */
    function handleCancelDelete() {
        hideModal();
    }

    /**
     * Handle keyboard events
     */
    function handleKeyDown(event) {
        if (modal.classList.contains('hidden')) return;

        switch (event.key) {
            case 'Escape':
                event.preventDefault();
                hideModal();
                break;
            case 'Enter':
                if (event.target === confirmDeleteBtn) {
                    event.preventDefault();
                    handleConfirmDelete();
                } else if (event.target === cancelDeleteBtn) {
                    event.preventDefault();
                    hideModal();
                }
                break;
            case 'Tab':
                // Trap focus within modal
                trapFocus(event);
                break;
        }
    }

    /**
     * Trap focus within modal for accessibility
     */
    function trapFocus(event) {
        const focusableElements = modal.querySelectorAll(
            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"]):not([disabled])'
        );

        if (focusableElements.length === 0) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
        } else if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    }

    /**
     * Handle clicks on modal backdrop
     */
    function handleBackdropClick(event) {
        if (event.target === modalBackdrop || event.target === modal) {
            hideModal();
        }
    }

    // Event listeners

    // Add click listeners to all delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', handleDeleteClick);
    });

    // Modal action buttons
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', handleConfirmDelete);
    }

    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', handleCancelDelete);
    }

    // Keyboard events
    document.addEventListener('keydown', handleKeyDown);

    // Backdrop click to close modal
    if (modal) {
        modal.addEventListener('click', handleBackdropClick);
    }

    // Prevent modal content clicks from closing modal
    const modalContent = modal?.querySelector('.relative.inline-block');
    if (modalContent) {
        modalContent.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }

    /**
     * Public API for external use
     */
    window.UserManagementModal = {
        show: showModal,
        hide: hideModal,

        /**
         * Show modal with custom user data
         */
        showForUser: function(userName, userEmail, form) {
            currentForm = form;
            modalUserName.textContent = userName || 'Unknown User';
            modalUserEmail.textContent = userEmail || 'Unknown Email';
            showModal();
        }
    };

    // Handle page navigation cleanup
    window.addEventListener('beforeunload', function() {
        document.body.classList.remove('overflow-hidden');
    });

    console.log('User Management Modal initialized successfully');
});
