let currentCableId = null;

function showDeleteModal(cableId, cableName, cableIdText) {
    console.log("Opening delete modal for cable:", cableId); // Debug log
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
    } else {
        console.error("Delete modal element not found");
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
    console.log("Execute delete called with ID:", currentCableId); // Debug log

    if (!currentCableId) {
        console.error("No cable ID selected for deletion");
        alert("Error: No cable selected for deletion");
        return;
    }

    const form = document.getElementById("deleteForm");
    if (!form) {
        console.error("Delete form not found");
        alert("Error: Delete form not found");
        return;
    }

    try {
        // Set the form action to the delete endpoint
        form.action = `/cables/${currentCableId}`;

        // Ensure the form method is set to POST (for Laravel DELETE routes)
        form.method = "POST";

        // Add CSRF token if using Laravel
        let csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            let csrfInput = form.querySelector('input[name="_token"]');
            if (!csrfInput) {
                csrfInput = document.createElement("input");
                csrfInput.type = "hidden";
                csrfInput.name = "_token";
                form.appendChild(csrfInput);
            }
            csrfInput.value = csrfToken.getAttribute("content");
        }

        // Add method spoofing for DELETE request (Laravel)
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement("input");
            methodInput.type = "hidden";
            methodInput.name = "_method";
            methodInput.value = "DELETE";
            form.appendChild(methodInput);
        }

        console.log("Submitting form to:", form.action); // Debug log
        form.submit();
    } catch (error) {
        console.error("Error during form submission:", error);
        alert("Error occurred during deletion");
    }
}

// Alternative function using fetch API (more modern approach)
async function executeDeleteWithFetch() {
    console.log("Execute delete (fetch) called with ID:", currentCableId);

    if (!currentCableId) {
        console.error("No cable ID selected for deletion");
        alert("Error: No cable selected for deletion");
        return;
    }

    try {
        // Get CSRF token
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        const response = await fetch(`/cables/${currentCableId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (response.ok) {
            closeDeleteModal();
            // Reload page or remove element from DOM
            window.location.reload();
            // Or alternatively, remove the row from table without reload:
            // const cableRow = document.querySelector(`tr[data-cable-id="${currentCableId}"]`);
            // if (cableRow) cableRow.remove();
        } else {
            const errorData = await response.json();
            console.error("Delete failed:", errorData);
            alert(
                "Failed to delete cable: " +
                    (errorData.message || "Unknown error")
            );
        }
    } catch (error) {
        console.error("Network error:", error);
        alert("Network error occurred during deletion");
    }
}

// Initialize event listeners when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM loaded, initializing modal events"); // Debug log

    const modal = document.getElementById("deleteModal");

    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    } else {
        console.error("Delete modal not found during initialization");
    }

    // Close modal with Escape key
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeDeleteModal();
        }
    });

    // Add event listener to delete button if it exists
    const deleteButton = document.getElementById("confirmDeleteBtn");
    if (deleteButton) {
        deleteButton.addEventListener("click", function (e) {
            e.preventDefault();
            executeDelete(); // or executeDeleteWithFetch() for modern approach
        });
    }
});
