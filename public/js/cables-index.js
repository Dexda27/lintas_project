let currentCableId = null;

function showDeleteModal(cableId, cableName, cableIdText) {
    currentCableId = cableId;

    const cableNameElement = document.getElementById("cableName");
    const cableIdElement = document.getElementById("cableId");
    const modal = document.getElementById("deleteModal");

    if (cableNameElement) {
        cableNameElement.textContent = cableName;
    }

    if (cableIdElement) {
        cableIdElement.textContent = `Cable ID: ${cableIdText}`;
    }

    if (modal) {
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    }
}

function closeDeleteModal() {
    const modal = document.getElementById("deleteModal");

    if (modal) {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }

    currentCableId = null;
}

function executeDelete() {
    if (currentCableId) {
        const form = document.getElementById("deleteForm");
        if (form) {
            // Construct the delete URL - adjust this based on your route structure
            form.action = `/cables/${currentCableId}`;
            form.submit();
        }
    }
}

// Initialize event listeners when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("deleteModal");

    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeDeleteModal();
        }
    });
});
