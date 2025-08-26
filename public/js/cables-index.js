let currentCableId = null;

function confirmDelete(cableId, cableName, cableIdText) {
    currentCableId = cableId;
    document.getElementById("cableName").textContent = cableName;
    document.getElementById("cableId").textContent = `Cable ID: ${cableIdText}`;
    document.getElementById("deleteModal").classList.remove("hidden");
    document.getElementById("deleteModal").classList.add("flex");
}

function closeDeleteModal() {
    document.getElementById("deleteModal").classList.add("hidden");
    document.getElementById("deleteModal").classList.remove("flex");
    currentCableId = null;
}

function executeDelete() {
    if (currentCableId) {
        const form = document.getElementById("deleteForm");
        form.action = `{{ route('cables.index') }}/${currentCableId}`;
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById("deleteModal").addEventListener("click", function (e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        closeDeleteModal();
    }
});
