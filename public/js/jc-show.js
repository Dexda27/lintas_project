document.addEventListener("DOMContentLoaded", function () {
    const coreASelect = document.getElementById("core_a_id");
    const coreBSelect = document.getElementById("core_b_id");

    function updateCoreOptions() {
        const selectedCableA =
            coreASelect.options[coreASelect.selectedIndex]?.dataset.cable;
        const selectedCableB =
            coreBSelect.options[coreBSelect.selectedIndex]?.dataset.cable;

        Array.from(coreBSelect.options).forEach((option) => {
            option.disabled = option.dataset.cable === selectedCableA;
        });
        Array.from(coreASelect.options).forEach((option) => {
            option.disabled = option.dataset.cable === selectedCableB;
        });
    }

    if (coreASelect && coreBSelect) {
        coreASelect.addEventListener("change", updateCoreOptions);
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
}

function disconnectConnection(connectionId) {
    if (!confirm("Are you sure you want to disconnect this core connection?")) {
        return;
    }

    fetch(`/connections/${connectionId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                location.reload();
            } else {
                alert("Error: " + (data.message || "Unknown error"));
            }
        })
        .catch((error) => {
            console.error("Error disconnecting cores:", error);
            alert("Error disconnecting cores");
        });
}
