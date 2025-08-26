document.addEventListener("DOMContentLoaded", function () {
    const coreASelect = document.getElementById("core_a_id");
    const coreBSelect = document.getElementById("core_b_id");

    // Prevent selecting cores from the same cable
    function updateCoreOptions() {
        const selectedCableA =
            coreASelect.options[coreASelect.selectedIndex]?.dataset.cable;
        const selectedCableB =
            coreBSelect.options[coreBSelect.selectedIndex]?.dataset.cable;

        // Update Core B options
        Array.from(coreBSelect.options).forEach((option) => {
            if (option.value && option.dataset.cable === selectedCableA) {
                option.disabled = true;
                option.style.color = "#9CA3AF";
            } else {
                option.disabled = false;
                option.style.color = "";
            }
        });

        // Update Core A options
        Array.from(coreASelect.options).forEach((option) => {
            if (option.value && option.dataset.cable === selectedCableB) {
                option.disabled = true;
                option.style.color = "#9CA3AF";
            } else {
                option.disabled = false;
                option.style.color = "";
            }
        });
    }

    // Add event listeners for dynamic option updates
    if (coreASelect) {
        coreASelect.addEventListener("change", updateCoreOptions);
    }

    if (coreBSelect) {
        coreBSelect.addEventListener("change", updateCoreOptions);
    }
});

function showConnectModal() {
    const modal = document.getElementById("connect-modal");
    if (modal) {
        modal.classList.remove("hidden");
    }
}

function closeConnectModal() {
    const modal = document.getElementById("connect-modal");
    const form = document.getElementById("connect-form");

    if (modal) {
        modal.classList.add("hidden");
    }

    if (form) {
        form.reset();
    }

    // Reset any disabled options when closing modal
    const coreASelect = document.getElementById("core_a_id");
    const coreBSelect = document.getElementById("core_b_id");

    if (coreASelect) {
        Array.from(coreASelect.options).forEach((option) => {
            option.disabled = false;
            option.style.color = "";
        });
    }

    if (coreBSelect) {
        Array.from(coreBSelect.options).forEach((option) => {
            option.disabled = false;
            option.style.color = "";
        });
    }
}

function disconnectConnection(connectionId) {
    if (!confirm("Are you sure you want to disconnect this core connection?")) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert("CSRF token not found");
        return;
    }

    fetch(`/connections/${connectionId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
            Accept: "application/json",
            "Content-Type": "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                location.reload();
            } else {
                alert(
                    "Error disconnecting cores: " +
                        (data.message || "Unknown error")
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("Error disconnecting cores. Please try again.");
        });
}
