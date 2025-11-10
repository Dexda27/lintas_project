document.addEventListener("DOMContentLoaded", function () {
    initializeConnectionForm();
});

function initializeConnectionForm() {
    // Get all form elements
    const elements = {
        cableA: document.getElementById("cable_a_id"),
        tubeA: document.getElementById("tube_a_id"),
        coreA: document.getElementById("core_a_id"),
        cableB: document.getElementById("cable_b_id"),
        tubeB: document.getElementById("tube_b_id"),
        coreB: document.getElementById("core_b_id"),
    };

    // Add event listeners
    if (elements.cableA) {
        elements.cableA.addEventListener("change", () =>
            handleCableChange("A")
        );
    }
    if (elements.cableB) {
        elements.cableB.addEventListener("change", () =>
            handleCableChange("B")
        );
    }
    if (elements.tubeA) {
        elements.tubeA.addEventListener("change", () => handleTubeChange("A"));
    }
    if (elements.tubeB) {
        elements.tubeB.addEventListener("change", () => handleTubeChange("B"));
    }
    if (elements.coreA) {
        elements.coreA.addEventListener("change", updateConnectionPreview);
    }
    if (elements.coreB) {
        elements.coreB.addEventListener("change", updateConnectionPreview);
    }

    // Initialize form state
    resetForm();
}

function handleCableChange(side) {
    const cableSelect = document.getElementById(
        `cable_${side.toLowerCase()}_id`
    );
    const tubeSelect = document.getElementById(`tube_${side.toLowerCase()}_id`);
    const coreSelect = document.getElementById(`core_${side.toLowerCase()}_id`);

    // Reset dependent dropdowns
    resetDropdown(tubeSelect, "Select tube...");
    resetDropdown(coreSelect, "Select core...");

    const cableId = cableSelect.value;
    if (!cableId) {
        updateCableAvailability();
        updateSubmitButton();
        return;
    }

    // Load tubes for selected cable
    loadTubes(cableId, side);

    // Update cable availability (prevent selecting same cable)
    updateCableAvailability();
    updateSubmitButton();
}

function handleTubeChange(side) {
    const tubeSelect = document.getElementById(`tube_${side.toLowerCase()}_id`);
    const coreSelect = document.getElementById(`core_${side.toLowerCase()}_id`);
    const cableSelect = document.getElementById(
        `cable_${side.toLowerCase()}_id`
    );

    // Reset core dropdown
    resetDropdown(coreSelect, "Select core...");

    const tubeNumber = tubeSelect.value;
    const cableId = cableSelect.value;

    if (!tubeNumber || !cableId) {
        updateSubmitButton();
        return;
    }

    // Load cores for selected tube
    loadCores(cableId, tubeNumber, side);
}

function loadTubes(cableId, side) {
    const tubeSelect = document.getElementById(`tube_${side.toLowerCase()}_id`);

    showLoading(tubeSelect, "Loading tubes...");

    fetch(`/cables/${cableId}/tubes-data`)
        .then((response) => {
            if (!response.ok) throw new Error("Failed to load tubes");
            return response.json();
        })
        .then((data) => {
            resetDropdown(tubeSelect, "Select tube...");

            // Add tube options based on available tubes
            if (data.available_tubes && data.available_tubes.length > 0) {
                data.available_tubes.forEach((tubeNumber) => {
                    const option = document.createElement("option");
                    option.value = tubeNumber;
                    option.textContent = `Tube ${tubeNumber}`;
                    tubeSelect.appendChild(option);
                });
            } else {
                // If no available_tubes data, use total_tubes
                for (let i = 1; i <= data.total_tubes; i++) {
                    const option = document.createElement("option");
                    option.value = i;
                    option.textContent = `Tube ${i}`;
                    tubeSelect.appendChild(option);
                }
            }
        })
        .catch((error) => {
            console.error("Error loading tubes:", error);
            resetDropdown(tubeSelect, "Error loading tubes");
        });
}

function loadCores(cableId, tubeNumber, side) {
    const coreSelect = document.getElementById(`core_${side.toLowerCase()}_id`);

    showLoading(coreSelect, "Loading cores...");

    fetch(`/cables/${cableId}/tubes/${tubeNumber}/cores-data`)
        .then((response) => {
            if (!response.ok) throw new Error("Failed to load cores");
            return response.json();
        })
        .then((cores) => {
            resetDropdown(coreSelect, "Select core...");

            // Add core options
            cores.forEach((core) => {
                const option = document.createElement("option");
                option.value = core.id;
                option.textContent = `Core ${core.core_number}`;
                if (core.attenuation) {
                    option.textContent += ` (${core.attenuation}dB)`;
                    a;
                }
                option.dataset.cable = cableId;
                option.dataset.tube = tubeNumber;
                option.dataset.coreNumber = core.core_number;
                coreSelect.appendChild(option);
            });

            // Update core availability
            updateCoreAvailability();
            updateSubmitButton();
        })
        .catch((error) => {
            console.error("Error loading cores:", error);
            resetDropdown(coreSelect, "Error loading cores");
        });
}

function updateConnectionPreview() {
    updateSubmitButton();
    updateCoreAvailability();
}

function updateSubmitButton() {
    const submitBtn = document.getElementById("submit-connection");
    const coreA = document.getElementById("core_a_id");
    const coreB = document.getElementById("core_b_id");

    if (!submitBtn || !coreA || !coreB) return;

    // Check if both cores are selected and they are different
    const bothSelected =
        coreA.value && coreB.value && coreA.value !== coreB.value;

    submitBtn.disabled = !bothSelected;

    if (bothSelected) {
        submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
    } else {
        submitBtn.classList.add("opacity-50", "cursor-not-allowed");
    }
}

function updateCableAvailability() {
    const cableA = document.getElementById("cable_a_id");
    const cableB = document.getElementById("cable_b_id");

    if (!cableA || !cableB) return;

    const selectedCableA = cableA.value;
    const selectedCableB = cableB.value;

    // Update Cable B options
    Array.from(cableB.options).forEach((option) => {
        if (option.value && option.value === selectedCableA) {
            option.disabled = true;
            option.style.color = "#9CA3AF";
        } else {
            option.disabled = false;
            option.style.color = "";
        }
    });

    // Update Cable A options
    Array.from(cableA.options).forEach((option) => {
        if (option.value && option.value === selectedCableB) {
            option.disabled = true;
            option.style.color = "#9CA3AF";
        } else {
            option.disabled = false;
            option.style.color = "";
        }
    });
}

function updateCoreAvailability() {
    const coreA = document.getElementById("core_a_id");
    const coreB = document.getElementById("core_b_id");

    if (!coreA || !coreB) return;

    const selectedCoreA = coreA.value;
    const selectedCoreB = coreB.value;

    // Update Core B options
    Array.from(coreB.options).forEach((option) => {
        if (option.value && option.value === selectedCoreA) {
            option.disabled = true;
            option.style.color = "#9CA3AF";
        } else {
            option.disabled = false;
            option.style.color = "";
        }
    });

    // Update Core A options
    Array.from(coreA.options).forEach((option) => {
        if (option.value && option.value === selectedCoreB) {
            option.disabled = true;
            option.style.color = "#9CA3AF";
        } else {
            option.disabled = false;
            option.style.color = "";
        }
    });
}

function resetDropdown(selectElement, placeholder) {
    if (!selectElement) return;

    selectElement.innerHTML = "";
    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = placeholder;
    selectElement.appendChild(defaultOption);
}

function showLoading(selectElement, message) {
    if (!selectElement) return;

    resetDropdown(selectElement, message);
    selectElement.disabled = true;

    // Re-enable after a short delay to prevent UI freeze
    setTimeout(() => {
        selectElement.disabled = false;
    }, 100);
}

function resetForm() {
    // Reset all selects to default state
    const selects = [
        "cable_a_id",
        "tube_a_id",
        "core_a_id",
        "cable_b_id",
        "tube_b_id",
        "core_b_id",
    ];

    selects.forEach((id) => {
        const element = document.getElementById(id);
        if (element) {
            // Reset selection
            element.selectedIndex = 0;

            // Re-enable all options
            Array.from(element.options).forEach((option) => {
                option.disabled = false;
                option.style.color = "";
            });
        }
    });

    // Reset tube and core dropdowns to default state
    const tubeA = document.getElementById("tube_a_id");
    const tubeB = document.getElementById("tube_b_id");
    const coreA = document.getElementById("core_a_id");
    const coreB = document.getElementById("core_b_id");

    if (tubeA) resetDropdown(tubeA, "Select cable first...");
    if (tubeB) resetDropdown(tubeB, "Select cable first...");
    if (coreA) resetDropdown(coreA, "Select tube first...");
    if (coreB) resetDropdown(coreB, "Select tube first...");

    updateSubmitButton();
}

function showConnectModal() {
    const modal = document.getElementById("connect-modal");
    if (modal) {
        modal.classList.remove("hidden");
        resetForm();
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

    resetForm();
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
