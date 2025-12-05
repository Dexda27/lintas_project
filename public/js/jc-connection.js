// JC Connection Manager - Multiple Connections Support
class JCConnectionManager {
    constructor() {
        this.currentClosureId = null;
        this.connectionRowCounter = 0;
        this.init();
    }

    init() {
        console.log("JCConnectionManager initialized");
        const closureIdElement = document.getElementById("current-closure-id");
        if (closureIdElement) {
            this.currentClosureId = closureIdElement.value;
            console.log("Current Closure ID:", this.currentClosureId);
        }
        this.setupAddConnectionButton();
    }

    setupAddConnectionButton() {
        const addBtn = document.getElementById("add-connection-btn");
        if (addBtn) {
            addBtn.addEventListener("click", () => {
                console.log("Add connection button clicked");
                this.addConnectionRow();
            });
        }
    }

    addConnectionRow() {
        this.connectionRowCounter++;
        const rowId = this.connectionRowCounter;
        console.log(`Adding connection row #${rowId}`);

        const container = document.getElementById("connection-rows-container");
        if (!container) {
            console.error("Connection rows container not found");
            return;
        }

        const row = document.createElement("div");
        row.className = "connection-row border-2 border-gray-200 rounded-lg p-3 sm:p-4 mb-4 bg-white";
        row.dataset.rowId = rowId;

        row.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-sm sm:text-base font-semibold text-gray-700">Connection #${rowId}</h4>
                ${rowId > 1 ? `<button type="button" onclick="jcConnectionManager.removeConnectionRow(${rowId})" class="text-red-600 hover:text-red-800 p-1">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>` : ''}
            </div>

            <!-- Connection Steps Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                <!-- Core A Selection -->
                <div class="bg-blue-50 rounded-lg p-3 md:p-4">
                    <h5 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                        <span class="bg-blue-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center mr-2">A</span>
                        First Core
                    </h5>

                    <!-- Cable A -->
                    <div class="space-y-2 mb-3">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700">
                            <span class="bg-gray-200 text-gray-700 rounded px-2 py-1 text-xs mr-1">1</span>
                            Select Cable
                        </label>
                        <select id="cable_a_${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Choose cable...</option>
                        </select>
                    </div>

                    <!-- Tube A -->
                    <div class="space-y-2 mb-3">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700">
                            <span class="bg-gray-200 text-gray-700 rounded px-2 py-1 text-xs mr-1">2</span>
                            Select Tube
                        </label>
                        <select id="tube_a_${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required disabled>
                            <option value="">Select cable first...</option>
                        </select>
                    </div>

                    <!-- Core A -->
                    <div class="space-y-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700">
                            <span class="bg-gray-200 text-gray-700 rounded px-2 py-1 text-xs mr-1">3</span>
                            Select Core
                        </label>
                        <select id="core_a_${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required disabled>
                            <option value="">Select tube first...</option>
                        </select>
                    </div>
                </div>

                <!-- Core B Selection -->
                <div class="bg-green-50 rounded-lg p-3 md:p-4">
                    <h5 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                        <span class="bg-green-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center mr-2">B</span>
                        Second Core
                    </h5>

                    <!-- Cable B -->
                    <div class="space-y-2 mb-3">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700">
                            <span class="bg-gray-200 text-gray-700 rounded px-2 py-1 text-xs mr-1">1</span>
                            Select Cable
                        </label>
                        <select id="cable_b_${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500" required>
                            <option value="">Choose cable...</option>
                        </select>
                    </div>

                    <!-- Tube B -->
                    <div class="space-y-2 mb-3">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700">
                            <span class="bg-gray-200 text-gray-700 rounded px-2 py-1 text-xs mr-1">2</span>
                            Select Tube
                        </label>
                        <select id="tube_b_${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500" required disabled>
                            <option value="">Select cable first...</option>
                        </select>
                    </div>

                    <!-- Core B -->
                    <div class="space-y-2">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700">
                            <span class="bg-gray-200 text-gray-700 rounded px-2 py-1 text-xs mr-1">3</span>
                            Select Core
                        </label>
                        <select id="core_b_${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500" required disabled>
                            <option value="">Select tube first...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Connection Details -->
            <div class="bg-gray-50 rounded-lg p-3 md:p-4 mt-4">
                <h5 class="text-sm font-semibold text-gray-800 mb-3">Connection Details</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Splice Loss (dB)</label>
                        <input type="number" id="splice_loss_${rowId}" step="0.001" min="0" max="10" class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="e.g., 0.15">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" id="description_${rowId}" maxlength="500" class="w-full px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Optional notes...">
                    </div>
                </div>
            </div>
        `;

        container.appendChild(row);
        console.log("Row HTML inserted");

        // Setup event listeners for this row
        this.setupRowEventListeners(rowId);

        // Load cables immediately for both sides
        this.loadCablesForRow(rowId, 'a');
        this.loadCablesForRow(rowId, 'b');

        // Update submit button text
        this.updateSubmitButtonText();

        console.log(`Row #${rowId} added successfully`);
    }

    setupRowEventListeners(rowId) {
        // Cable A
        const cableASelect = document.getElementById(`cable_a_${rowId}`);
        if (cableASelect) {
            cableASelect.addEventListener("change", async () => {
                const cableId = cableASelect.value;
                console.log(`Cable A selected for row ${rowId}:`, cableId);
                if (cableId) {
                    this.resetDownstreamSelects(rowId, 'a', 'tube');
                    await this.loadTubesForRow(rowId, cableId, 'a');
                } else {
                    this.resetDownstreamSelects(rowId, 'a', 'tube');
                }
                this.updateCableAvailability(rowId);
            });
        }

        // Tube A
        const tubeASelect = document.getElementById(`tube_a_${rowId}`);
        if (tubeASelect) {
            tubeASelect.addEventListener("change", async () => {
                const tubeId = tubeASelect.value;
                const cableId = cableASelect?.value;
                console.log(`Tube A selected for row ${rowId}:`, tubeId);
                if (tubeId && cableId) {
                    this.resetDownstreamSelects(rowId, 'a', 'core');
                    await this.loadCoresForRow(rowId, cableId, tubeId, 'a');
                } else {
                    this.resetDownstreamSelects(rowId, 'a', 'core');
                }
            });
        }

        // Cable B
        const cableBSelect = document.getElementById(`cable_b_${rowId}`);
        if (cableBSelect) {
            cableBSelect.addEventListener("change", async () => {
                const cableId = cableBSelect.value;
                console.log(`Cable B selected for row ${rowId}:`, cableId);
                if (cableId) {
                    this.resetDownstreamSelects(rowId, 'b', 'tube');
                    await this.loadTubesForRow(rowId, cableId, 'b');
                } else {
                    this.resetDownstreamSelects(rowId, 'b', 'tube');
                }
                this.updateCableAvailability(rowId);
            });
        }

        // Tube B
        const tubeBSelect = document.getElementById(`tube_b_${rowId}`);
        if (tubeBSelect) {
            tubeBSelect.addEventListener("change", async () => {
                const tubeId = tubeBSelect.value;
                const cableId = cableBSelect?.value;
                console.log(`Tube B selected for row ${rowId}:`, tubeId);
                if (tubeId && cableId) {
                    this.resetDownstreamSelects(rowId, 'b', 'core');
                    await this.loadCoresForRow(rowId, cableId, tubeId, 'b');
                } else {
                    this.resetDownstreamSelects(rowId, 'b', 'core');
                }
            });
        }
    }

    removeConnectionRow(rowId) {
        const row = document.querySelector(`[data-row-id="${rowId}"]`);
        if (row) {
            row.remove();
            console.log(`Row ${rowId} removed`);
            this.updateSubmitButtonText();
        }
    }

    resetDownstreamSelects(rowId, side, startFrom) {
        const selects = {
            tube: [`tube_${side}_${rowId}`, `core_${side}_${rowId}`],
            core: [`core_${side}_${rowId}`],
        };

        const toReset = selects[startFrom] || [];
        toReset.forEach((selectId) => {
            const select = document.getElementById(selectId);
            if (select) {
                select.innerHTML = '<option value="">Select...</option>';
                select.disabled = true;
            }
        });
    }

    async loadCablesForRow(rowId, side) {
        const select = document.getElementById(`cable_${side}_${rowId}`);
        if (!select) return;

        // Use available cables data from window
        if (window.availableCablesData) {
            select.innerHTML = '<option value="">Choose cable...</option>';
            window.availableCablesData.forEach((cable) => {
                const option = document.createElement("option");
                option.value = cable.id;
                option.textContent = `${cable.name} (${cable.cable_id})`;
                select.appendChild(option);
            });
        } else {
            console.error('availableCablesData not found in window object');
        }
    }

    async loadTubesForRow(rowId, cableId, side) {
        try {
            const response = await fetch(`/connections/cables/${cableId}/tubes`);
            const data = await response.json();
            const select = document.getElementById(`tube_${side}_${rowId}`);

            if (select && data) {
                select.innerHTML = '<option value="">Select tube...</option>';

                if (data.available_tubes && data.available_tubes.length > 0) {
                    data.available_tubes.forEach((tubeNumber) => {
                        const option = document.createElement("option");
                        option.value = tubeNumber;
                        option.textContent = `Tube ${tubeNumber}`;
                        select.appendChild(option);
                    });
                } else if (data.total_tubes) {
                    for (let i = 1; i <= data.total_tubes; i++) {
                        const option = document.createElement("option");
                        option.value = i;
                        option.textContent = `Tube ${i}`;
                        select.appendChild(option);
                    }
                }
                select.disabled = false;
            }
        } catch (error) {
            console.error(`Error loading tubes for row ${rowId} side ${side}:`, error);
        }
    }

    async loadCoresForRow(rowId, cableId, tubeNumber, side) {
        try {
            const response = await fetch(`/connections/cables/${cableId}/tubes/${tubeNumber}/cores`);
            const cores = await response.json();
            const select = document.getElementById(`core_${side}_${rowId}`);

            if (select && cores) {
                select.innerHTML = '<option value="">Select core...</option>';
                cores.forEach((core) => {
                    const option = document.createElement("option");
                    option.value = core.id;
                    option.textContent = `Core ${core.core_number}`;
                    if (core.attenuation) {
                        option.textContent += ` (${core.attenuation}dB)`;
                    }
                    select.appendChild(option);
                });
                select.disabled = false;
            }
        } catch (error) {
            console.error(`Error loading cores for row ${rowId} side ${side}:`, error);
        }
    }

    updateCableAvailability(rowId) {
        const cableASelect = document.getElementById(`cable_a_${rowId}`);
        const cableBSelect = document.getElementById(`cable_b_${rowId}`);

        if (!cableASelect || !cableBSelect) return;

        const selectedCableA = cableASelect.value;
        const selectedCableB = cableBSelect.value;

        // Prevent same cable selection
        Array.from(cableBSelect.options).forEach((option) => {
            if (option.value && option.value === selectedCableA) {
                option.disabled = true;
                option.style.color = "#9CA3AF";
            } else {
                option.disabled = false;
                option.style.color = "";
            }
        });

        Array.from(cableASelect.options).forEach((option) => {
            if (option.value && option.value === selectedCableB) {
                option.disabled = true;
                option.style.color = "#9CA3AF";
            } else {
                option.disabled = false;
                option.style.color = "";
            }
        });
    }

    updateSubmitButtonText() {
        const rows = document.querySelectorAll(".connection-row");
        const submitBtn = document.getElementById("submit-connection");
        if (submitBtn) {
            const originalText = submitBtn.textContent.trim();
            if (rows.length > 1) {
                submitBtn.textContent = `Create ${rows.length} Connections`;
            } else {
                submitBtn.textContent = 'Create Connection';
            }
        }
    }

    async submitConnections() {
        const rows = document.querySelectorAll(".connection-row");
        const closureId = this.currentClosureId;

        console.log("=== SUBMIT CONNECTIONS ===");
        console.log("Closure ID:", closureId);
        console.log("Total rows found:", rows.length);

        if (!closureId) {
            alert("Closure ID not found");
            return false;
        }

        if (rows.length === 0) {
            alert("Please add at least one connection");
            return false;
        }

        const connections = [];
        let hasError = false;

        rows.forEach((row, index) => {
            const rowId = row.dataset.rowId;
            console.log(`Processing row #${index + 1}, rowId:`, rowId);

            const coreAId = document.getElementById(`core_a_${rowId}`)?.value;
            const coreBId = document.getElementById(`core_b_${rowId}`)?.value;
            const spliceLoss = document.getElementById(`splice_loss_${rowId}`)?.value;
            const description = document.getElementById(`description_${rowId}`)?.value;

            console.log(`Row ${index + 1} values:`, {
                coreAId,
                coreBId,
                spliceLoss,
                description
            });

            if (!coreAId || !coreBId) {
                console.error(`Row ${index + 1} validation failed - missing required fields`);
                hasError = true;
                row.classList.add("border-red-500");
                return;
            }

            if (coreAId === coreBId) {
                console.error(`Row ${index + 1} validation failed - same core selected`);
                alert(`Connection #${index + 1}: Cannot connect a core to itself`);
                hasError = true;
                row.classList.add("border-red-500");
                return;
            }

            const connectionData = {
                source_core_id: parseInt(coreAId),
                target_core_id: parseInt(coreBId),
                joint_closure_id: parseInt(closureId),
                connection_type: "splice",
                connection_loss: spliceLoss ? parseFloat(spliceLoss) : null,
                notes: description || null
            };

            console.log(`Adding connection #${index + 1}:`, connectionData);
            connections.push(connectionData);

            row.classList.remove("border-red-500");
        });

        console.log("Total connections to send:", connections.length);
        console.log("Connections array:", JSON.stringify(connections, null, 2));

        if (hasError) {
            return false;
        }

        if (connections.length === 0) {
            alert("No valid connections to create");
            return false;
        }

        const submitBtn = document.getElementById("submit-connection");
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            `;
        }

        try {
            console.log("Sending request to /connections/bulk");
            const response = await fetch("/connections/bulk", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ connections: connections })
            });

            const result = await response.json();
            console.log("Server response:", result);

            if (result.success) {
                alert(result.message || "Connections created successfully!");
                window.location.reload();
            } else {
                console.error("Server returned error:", result.message);
                alert(`Error: ${result.message}`);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = `Create ${rows.length} Connection${rows.length > 1 ? 's' : ''}`;
                }
            }
        } catch (error) {
            console.error("Request failed:", error);
            alert("Error creating connections: " + error.message);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = `Create ${rows.length} Connection${rows.length > 1 ? 's' : ''}`;
            }
        }

        return false;
    }
}

// Initialize manager
const jcConnectionManager = new JCConnectionManager();

document.addEventListener("DOMContentLoaded", function () {
    initializeConnectionForm();
});

function initializeConnectionForm() {
    // This function is kept for backward compatibility but most logic moved to JCConnectionManager
    console.log("Legacy initializeConnectionForm called");
}

// Global helper functions
function resetForm() {
    // Reset modal form when reopening
    const container = document.getElementById("connection-rows-container");
    if (container) {
        container.innerHTML = "";
        jcConnectionManager.connectionRowCounter = 0;
        jcConnectionManager.addConnectionRow();
    }
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
    if (modal) {
        modal.classList.add("hidden");
    }
    // Clear all connection rows
    const container = document.getElementById("connection-rows-container");
    if (container) {
        container.innerHTML = "";
        jcConnectionManager.connectionRowCounter = 0;
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
