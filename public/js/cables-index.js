let currentCableId = null;

function confirmDelete(cableId, cableName, cableIdText) {
    currentCableId = cableId;
    document.getElementById("cableName").textContent = cableName;
    document.getElementById("cableId").textContent = `Cable ID: ${cableIdText}`;

    // Show modal with proper classes
    const modal = document.getElementById("deleteModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");

    // Add backdrop blur effect
    document.body.classList.add("modal-open");
}

function closeDeleteModal() {
    const modal = document.getElementById("deleteModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");

    // Remove backdrop effect
    document.body.classList.remove("modal-open");
    currentCableId = null;
}

function executeDelete() {
    if (currentCableId) {
        // Create form dynamically with proper Laravel route
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/cables/${currentCableId}`;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside (backdrop)
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("deleteModal");
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    }
});

// Close modal with Escape key
document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        const modal = document.getElementById("deleteModal");
        if (modal && !modal.classList.contains("hidden")) {
            closeDeleteModal();
        }
    }
});

// Prevent modal content clicks from closing modal
document.addEventListener('DOMContentLoaded', function() {
    const modalContent = document.querySelector("#deleteModal .bg-white");
    if (modalContent) {
        modalContent.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }
});
