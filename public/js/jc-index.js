document.addEventListener("DOMContentLoaded", function () {
    let currentClosureId = null;
    const deleteModal = document.getElementById("deleteModal");
    const deleteForm = document.getElementById("deleteForm");

    // Make functions available globally
    window.confirmDelete = function (closureId, closureName, closureIdText) {
        currentClosureId = closureId;
        document.getElementById("closureName").textContent = closureName;
        document.getElementById(
            "closureId"
        ).textContent = `Closure ID: ${closureIdText}`;
        deleteModal.classList.remove("hidden");
        deleteModal.classList.add("flex");

        // Focus the first button for accessibility
        setTimeout(() => {
            deleteModal.querySelector("button").focus();
        }, 100);
    };

    window.closeDeleteModal = function () {
        deleteModal.classList.add("hidden");
        deleteModal.classList.remove("flex");
        currentClosureId = null;
    };

    window.executeDelete = function () {
        if (currentClosureId) {
            deleteForm.action = `{{ route('closures.index') }}/${currentClosureId}`;
            deleteForm.submit();
        }
    };

    // Close modal when clicking outside
    deleteModal.addEventListener("click", function (e) {
        if (e.target === this) {
            window.closeDeleteModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && !deleteModal.classList.contains("hidden")) {
            window.closeDeleteModal();
        }
    });
});
