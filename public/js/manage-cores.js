// Core management functions (harus di luar DOMContentLoaded agar bisa diakses dari onclick)
function editCore(coreId) {
    document.getElementById("core-id").value = coreId;
    document.getElementById("edit-core-modal").classList.remove("hidden");
}

function closeEditModal() {
    document.getElementById("edit-core-modal").classList.add("hidden");
}

function joinCore(coreId) {
    document.getElementById("join-core-id").value = coreId;
    loadJCs();
    document.getElementById("join-core-modal").classList.remove("hidden");
}

function closeJoinModal() {
    document.getElementById("join-core-modal").classList.add("hidden");
    document.getElementById("join-core-form").reset();
    ["target-cable", "target-tube", "target-core"].forEach((id) => {
        document.getElementById(id).disabled = true;
    });
}
function closeModal() {
    const modal = document.getElementById("my-modal-id");
    modal.classList.add("hidden"); // sembunyikan modal
}

function loadJCs() {
    fetch("/connections/joint-closures")
        .then((response) => response.json())
        .then((data) => {
            const select = document.getElementById("jc-selection");
            select.innerHTML = '<option value="">Select JC...</option>';
            data.forEach((jc) => {
                const available =
                    jc.capacity - jc.used_capacity ||
                    jc.available_capacity ||
                    0;
                select.innerHTML += `<option value="${jc.id}">${jc.name} (${jc.location}) - ${available}/${jc.capacity} available</option>`;
            });
        })
        .catch((error) => console.error("Error loading JCs:", error));
}

function disconnectCore(connectionId) {
    if (!confirm("Are you sure you want to disconnect this core connection?"))
        return;

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
            console.error("Error:", error);
            alert("Error disconnecting core");
        });
}

// Filter functionality dan event listeners
document.addEventListener("DOMContentLoaded", function () {
    // Initialize filters
    const filters = {
        tube: document.getElementById("tube-filter"),
        status: document.getElementById("status-filter"),
        usage: document.getElementById("usage-filter"),
    };

    // Apply filters function
    function applyFilters() {
        const values = {
            tube: filters.tube.value,
            status: filters.status.value,
            usage: filters.usage.value,
        };

        const coreCards = document.querySelectorAll(".core-card");
        const tubeSections = document.querySelectorAll(".tube-section");
        const visibleTubes = new Set();

        tubeSections.forEach((section) => (section.style.display = "none"));

        coreCards.forEach((card) => {
            const cardData = {
                tube: card.dataset.tube,
                status: card.dataset.status,
                usage: card.dataset.usage,
                core: card.dataset.core,
                description: (card.dataset.description || "").toLowerCase(),
            };

            const shouldShow =
                (!values.tube || cardData.tube === values.tube) &&
                (!values.status || cardData.status === values.status) &&
                (!values.usage || cardData.usage === values.usage);

            card.style.display = shouldShow ? "block" : "none";
            if (shouldShow) visibleTubes.add(cardData.tube);
        });

        tubeSections.forEach((section) => {
            if (visibleTubes.has(section.dataset.tube))
                section.style.display = "block";
        });
    }

    // Add event listeners to filters
    Object.values(filters).forEach((filter) => {
        if (filter) {
            filter.addEventListener("change", applyFilters);
        }
    });

    // Clear filters button
    const clearButton = document.getElementById("clear-filters");
    if (clearButton) {
        clearButton.addEventListener("click", () => {
            Object.values(filters).forEach((filter) => {
                if (filter) filter.value = "";
            });
            applyFilters();
        });
    }

    // Cascading dropdown handlers
    const jcSelection = document.getElementById("jc-selection");
    if (jcSelection) {
        jcSelection.addEventListener("change", function () {
            const jcId = this.value;
            const cableSelect = document.getElementById("target-cable");

            if (jcId) {
                fetch(`/connections/joint-closures/${jcId}/cables`)
                    .then((response) => response.json())
                    .then((data) => {
                        cableSelect.innerHTML =
                            '<option value="">Select Cable...</option>';
                        data.forEach((cable) => {
                            // Note: currentCableId should be passed from the Blade template
                            if (cable.id !== window.currentCableId) {
                                cableSelect.innerHTML += `<option value="${cable.id}">${cable.name} (${cable.cable_id})</option>`;
                            }
                        });
                        cableSelect.disabled = false;
                    })
                    .catch((error) =>
                        console.error("Error loading cables:", error)
                    );
            } else {
                cableSelect.disabled = true;
            }

            ["target-tube", "target-core"].forEach((id) => {
                const element = document.getElementById(id);
                if (element) element.disabled = true;
            });
        });
    }

    const targetCable = document.getElementById("target-cable");
    if (targetCable) {
        targetCable.addEventListener("change", function () {
            const cableId = this.value;
            const tubeSelect = document.getElementById("target-tube");

            if (cableId) {
                fetch(`/connections/cables/${cableId}/tubes`)
                    .then((response) => response.json())
                    .then((data) => {
                        tubeSelect.innerHTML =
                            '<option value="">Select Tube...</option>';
                        for (let i = 1; i <= data.total_tubes; i++) {
                            tubeSelect.innerHTML += `<option value="${i}">Tube ${i}</option>`;
                        }
                        tubeSelect.disabled = false;
                    })
                    .catch((error) =>
                        console.error("Error loading tubes:", error)
                    );
            } else {
                tubeSelect.disabled = true;
            }

            const targetCore = document.getElementById("target-core");
            if (targetCore) targetCore.disabled = true;
        });
    }

    const targetTube = document.getElementById("target-tube");
    if (targetTube) {
        targetTube.addEventListener("change", function () {
            const cableId = document.getElementById("target-cable").value;
            const tubeNumber = this.value;
            const coreSelect = document.getElementById("target-core");

            if (cableId && tubeNumber) {
                fetch(
                    `/connections/cables/${cableId}/tubes/${tubeNumber}/cores`
                )
                    .then((response) => response.json())
                    .then((data) => {
                        coreSelect.innerHTML =
                            '<option value="">Select Core...</option>';
                        data.forEach((core) => {
                            coreSelect.innerHTML += `<option value="${
                                core.id
                            }" ${
                                core.status !== "ok"
                                    ? 'style="color: #ef4444"'
                                    : ""
                            }>Core ${core.core_number}${
                                core.status !== "ok" ? " (!)" : ""
                            }</option>`;
                        });
                        coreSelect.disabled = false;
                    })
                    .catch((error) =>
                        console.error("Error loading cores:", error)
                    );
            } else {
                coreSelect.disabled = true;
            }
        });
    }

    // Form submission handlers
    const joinForm = document.getElementById("join-core-form");
    if (joinForm) {
        joinForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData();
            const fieldMappings = {
                "join-core-id": "source_core_id",
                "target-core": "target_core_id",
                "jc-selection": "joint_closure_id",
                "connection-type": "connection_type",
                "connection-loss": "connection_loss",
                "connection-notes": "connection_notes",
            };

            Object.entries(fieldMappings).forEach(([elementId, fieldName]) => {
                const element = document.getElementById(elementId);
                if (element) {
                    formData.append(fieldName, element.value);
                }
            });

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append("_token", csrfToken.getAttribute("content"));
            }

            fetch("/connections", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Connection created successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + (data.message || "Unknown error"));
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Error creating connection");
                });
        });
    }

    const editForm = document.getElementById("edit-core-form");
    if (editForm) {
        editForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const coreId = document.getElementById("core-id").value;
            const formData = new FormData();

            const fieldMappings = {
                "core-status": "status",
                "core-usage": "usage",
                "core-attenuation": "attenuation",
                "core-description": "description",
            };

            Object.entries(fieldMappings).forEach(([elementId, fieldName]) => {
                const element = document.getElementById(elementId);
                if (element) {
                    formData.append(fieldName, element.value);
                }
            });

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append("_token", csrfToken.getAttribute("content"));
            }
            formData.append("_method", "PUT");

            fetch(`/cores/${coreId}`, {
                method: "POST",
                body: formData,
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
                    console.error("Error:", error);
                    alert("Error updating core");
                });
        });
    }
});
