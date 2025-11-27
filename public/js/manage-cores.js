// Simplified Core Management System - ALLOW DUPLICATE TARGET CORES
class CoreManager {
    constructor() {
        this.currentCableId = null;
        this.connectionRowCounter = 0;
        this.editingCoreId = null;
        this.disconnectingConnectionId = null;
        this.init();
    }

    init() {
        console.log("CoreManager initialized");
        const cableIdElement = document.getElementById("current-cable-id");
        if (cableIdElement) {
            this.currentCableId = cableIdElement.value;
            console.log("Current Cable ID:", this.currentCableId);
        }
        this.setupEventListeners();
        this.setupForms();
        this.setupFilters();
    }

    getCurrentCableId() {
        return this.currentCableId;
    }

    // === EVENT LISTENERS ===
    setupEventListeners() {
        const cableIdElement = document.getElementById("current-cable-id");
        if (cableIdElement) {
            cableIdElement.addEventListener("change", (e) => {
                this.currentCableId = e.target.value;
                console.log("Cable ID changed to:", this.currentCableId);
            });
        }
    }

    setupForms() {
        // Join form submission
        const joinForm = document.getElementById("join-core-form");
        if (joinForm) {
            joinForm.addEventListener("submit", async (e) => {
                e.preventDefault();
                await this.submitJoinForm();
            });
        }

        // Edit form submission
        const editForm = document.getElementById("core-edit-form");
        if (editForm) {
            editForm.addEventListener("submit", async (e) => {
                e.preventDefault();
                await this.submitEdit(this.getEditFormData());
            });
        }

        // Setup add connection button
        this.setupAddConnectionButton();
    }

    setupFilters() {
        const tubeFilter = document.getElementById("tube-filter");
        const statusFilter = document.getElementById("status-filter");
        const usageFilter = document.getElementById("usage-filter");

        if (tubeFilter) {
            tubeFilter.addEventListener("change", () => this.applyFilters());
        }
        if (statusFilter) {
            statusFilter.addEventListener("change", () => this.applyFilters());
        }
        if (usageFilter) {
            usageFilter.addEventListener("change", () => this.applyFilters());
        }
    }

    applyFilters() {
        const tubeValue = document.getElementById("tube-filter")?.value || "all";
        const statusValue = document.getElementById("status-filter")?.value || "all";
        const usageValue = document.getElementById("usage-filter")?.value || "all";

        const cards = document.querySelectorAll(".core-card");
        let visibleCount = 0;

        cards.forEach((card) => {
            const tube = card.dataset.tube;
            const status = card.dataset.status;
            const usage = card.dataset.usage;

            const tubeMatch = tubeValue === "all" || tube === tubeValue;
            const statusMatch = statusValue === "all" || status === statusValue;
            const usageMatch = usageValue === "all" || usage === usageValue;

            if (tubeMatch && statusMatch && usageMatch) {
                card.style.display = "block";
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });

        console.log(`Filtered: ${visibleCount} cores visible`);
    }

    // === CORE EDITING ===
    openEditModal(coreId) {
        console.log("Opening edit modal for core:", coreId);
        this.editingCoreId = coreId;

        const core = this.getCoreData(coreId);
        if (!core) {
            console.error("Core not found:", coreId);
            return;
        }

        this.populateModal(core);

        const modal = document.getElementById("core-edit-modal");
        if (modal) {
            modal.classList.remove("hidden");
        }
    }

    getCoreData(coreId) {
        const card = document.querySelector(`[data-core="${coreId}"]`);
        if (!card) {
            console.error("Core card not found for ID:", coreId);
            return null;
        }

        // Extract core number from card title
        const coreTitle = card.querySelector('h4');
        const coreNumber = coreTitle ? coreTitle.textContent.replace('Core ', '').trim() : '';

        // Extract tube number from subtitle
        const tubeText = card.querySelector('p.text-xs.text-gray-500');
        const tubeNumber = tubeText ? tubeText.textContent.replace('Tube ', '').trim() : '';

        return {
            id: coreId,
            coreNumber: coreNumber,
            tubeNumber: tubeNumber,
            status: card.dataset.status,
            usage: card.dataset.usage,
            attenuation: this.extractAttenuation(card),
            description: card.dataset.description || "",
        };
    }

    extractAttenuation(card) {
        // Look for attenuation in the card details
        const detailsDiv = card.querySelector('.space-y-2');
        if (!detailsDiv) return "";

        const flexDivs = detailsDiv.querySelectorAll('.flex.justify-between');
        for (let div of flexDivs) {
            const label = div.querySelector('.text-gray-600');
            if (label && label.textContent.includes('Attenuation')) {
                const value = div.querySelector('.font-medium');
                if (value) {
                    return value.textContent.replace(' dB', '').trim();
                }
            }
        }
        return "";
    }

    populateModal(core) {
        document.getElementById("edit-core-id").value = core.id;
        document.getElementById("edit-core-status").value = core.status;
        document.getElementById("edit-core-usage").value = core.usage;
        document.getElementById("edit-core-attenuation").value = core.attenuation;
        document.getElementById("edit-core-description").value = core.description;

        console.log("Modal populated with data:", core);
    }

    async submitEdit(formData) {
        const coreId = document.getElementById("edit-core-id")?.value;
        if (!coreId) {
            console.error("No core ID found");
            return;
        }

        console.log("Submitting edit for core:", coreId, "with data:", formData);

        // Get submit button and add loading state
        const submitBtn = document.querySelector('#core-edit-form button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
            `;
        }

        try {
            const response = await fetch(`/cores/${coreId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            console.log("Server response:", result);

            if (result.success) {
                this.showNotification("Core updated successfully!", "success");
                setTimeout(() => location.reload(), 500);
            } else {
                this.showNotification(`Error: ${result.message || 'Failed to update core'}`, "error");
                // Reset button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Core
                    `;
                }
            }
        } catch (error) {
            console.error("Error updating core:", error);
            this.showNotification("Error updating core: " + error.message, "error");
            // Reset button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Core
                `;
            }
        }
    }

    updateCoreCard(coreId, formData) {
        const card = document.querySelector(`[data-core-id="${coreId}"]`);
        if (!card) return;

        this.animateUpdate(card, () => {
            card.dataset.status = formData.status;
            card.dataset.usage = formData.usage;
            this.updateStatusIndicators(card, formData);
            this.updateDetails(card, formData);
            this.updateDescription(card, formData.description);
        });
    }

    animateUpdate(element, callback) {
        element.style.transition = "all 0.3s ease";
        element.style.opacity = "0.5";
        setTimeout(() => {
            callback();
            element.style.opacity = "1";
        }, 150);
    }

    updateStatusIndicators(card, formData) {
        const statusBadge = card.querySelector(".status-badge");
        const usageBadge = card.querySelector(".usage-badge");

        if (statusBadge) {
            statusBadge.className = `status-badge px-2 py-1 rounded text-xs font-medium ${
                formData.status === "ok" ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
            }`;
            statusBadge.textContent = this.capitalize(formData.status);
        }

        if (usageBadge) {
            const usageColors = {
                active: "bg-blue-100 text-blue-800",
                inactive: "bg-gray-100 text-gray-800",
                reserved: "bg-yellow-100 text-yellow-800",
            };
            usageBadge.className = `usage-badge px-2 py-1 rounded text-xs font-medium ${usageColors[formData.usage]}`;
            usageBadge.textContent = this.capitalize(formData.usage);
        }
    }

    updateDetails(card, formData) {
        const detailSection = card.querySelector(".core-details");
        if (!detailSection) return;

        const flexItems = detailSection.querySelectorAll(".flex.justify-between");
        const attRow = this.findAttenuationRow(flexItems);

        if (formData.attenuation) {
            if (attRow) {
                const attValue = attRow.querySelector(".font-medium");
                if (attValue) attValue.textContent = `${formData.attenuation} dB`;
            } else {
                this.addAttenuationRow(detailSection, formData.attenuation);
            }
        } else if (attRow) {
            attRow.remove();
        }
    }

    updateDescription(card, description) {
        const descElement = card.querySelector(".description-text");
        if (descElement) {
            descElement.textContent = description || "No description";
        }
    }

    // === CONNECTION MANAGEMENT ===
    openJoinModal(coreId) {
        console.log("Opening join modal for core:", coreId);
        document.getElementById("join-core-id").value = coreId;

        // Reset connection rows
        const container = document.getElementById("connection-rows-container");
        if (container) {
            container.innerHTML = "";
            this.connectionRowCounter = 0;
        }

        // Populate source core info
        this.populateSourceCoreInfo(coreId);

        // Add first connection row
        this.addConnectionRow();

        const modal = document.getElementById("join-core-modal");
        if (modal) {
            modal.classList.remove("hidden");
        }
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

    populateSourceCoreInfo(coreId) {
        const core = this.getCoreData(coreId);
        if (!core) return;

        const infoDiv = document.getElementById("source-core-info");
        if (infoDiv) {
            infoDiv.innerHTML = `
                <div class="bg-blue-50 p-3 sm:p-4 rounded-lg">
                    <p class="text-xs sm:text-sm font-medium text-blue-900">Source Core</p>
                    <p class="text-sm sm:text-base text-blue-700">Core ${core.coreNumber} - Tube ${core.tubeNumber}</p>
                </div>
            `;
        }
    }

    addConnectionRow() {
        this.connectionRowCounter++;
        const rowId = this.connectionRowCounter;
        console.log(`Adding row # ${rowId}`);

        const container = document.getElementById("connection-rows-container");
        if (!container) {
            console.error("Connection rows container not found");
            return;
        }

        const row = document.createElement("div");
        row.className = "connection-row border-2 border-gray-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4 bg-white";
        row.dataset.rowId = rowId;

        row.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-sm sm:text-base font-semibold text-gray-700">Connection #${rowId}</h4>
                ${rowId > 1 ? `<button type="button" onclick="coreManager.removeConnectionRow(${rowId})" class="text-red-600 hover:text-red-800 p-1">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>` : ''}
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Joint Closure *</label>
                    <select id="jc-${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select JC</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Target Cable *</label>
                    <select id="cable-${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required disabled>
                        <option value="">Select Cable</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Target Tube *</label>
                    <select id="tube-${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required disabled>
                        <option value="">Select Tube</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                        Target Core *
                        <span class="text-xs text-gray-500 hidden sm:inline">(Can be same)</span>
                    </label>
                    <select id="core-${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required disabled>
                        <option value="">Select Core</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Connection Loss (dB)</label>
                    <input type="number" id="loss-${rowId}" step="0.001" min="0" max="10" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0.000">
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" id="notes-${rowId}" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Optional notes">
                </div>
            </div>
        `;

        container.appendChild(row);
        console.log("Row HTML inserted");

        // Setup event listeners for this row
        this.setupRowEventListeners(rowId);

        // Load JCs immediately
        this.loadJCsForRow(rowId);

        // Update submit button text
        this.updateSubmitButtonText();

        console.log(`Row # ${rowId} added successfully`);
    }

    setupRowEventListeners(rowId) {
        const jcSelect = document.getElementById(`jc-${rowId}`);
        const cableSelect = document.getElementById(`cable-${rowId}`);
        const tubeSelect = document.getElementById(`tube-${rowId}`);
        const coreSelect = document.getElementById(`core-${rowId}`);

        if (jcSelect) {
            jcSelect.addEventListener("change", async () => {
                const jcId = jcSelect.value;
                console.log(`JC selected for row ${rowId}:`, jcId);
                if (jcId) {
                    this.resetDownstreamSelects(rowId, "cable");
                    await this.loadCablesForRow(rowId, jcId);
                } else {
                    this.resetDownstreamSelects(rowId, "cable");
                }
            });
        }

        if (cableSelect) {
            cableSelect.addEventListener("change", async () => {
                const cableId = cableSelect.value;
                console.log(`Cable selected for row ${rowId}:`, cableId);
                if (cableId) {
                    this.resetDownstreamSelects(rowId, "tube");
                    await this.loadTubesForRow(rowId, cableId);
                } else {
                    this.resetDownstreamSelects(rowId, "tube");
                }
            });
        }

        if (tubeSelect) {
            tubeSelect.addEventListener("change", async () => {
                const tubeId = tubeSelect.value;
                const cableId = cableSelect?.value;
                console.log(`Tube selected for row ${rowId}:`, tubeId);
                if (tubeId && cableId) {
                    this.resetDownstreamSelects(rowId, "core");
                    await this.loadCoresForRow(rowId, cableId, tubeId);
                } else {
                    this.resetDownstreamSelects(rowId, "core");
                }
            });
        }

        // ❌ REMOVED: validateUniqueTargetCores() call
        // Now allows duplicate target cores
        if (coreSelect) {
            coreSelect.addEventListener("change", () => {
                console.log(`Core selected for row ${rowId}:`, coreSelect.value);
                // No validation - allow duplicates
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

    resetDownstreamSelects(rowId, startFrom) {
        const selects = {
            cable: [`cable-${rowId}`, `tube-${rowId}`, `core-${rowId}`],
            tube: [`tube-${rowId}`, `core-${rowId}`],
            core: [`core-${rowId}`],
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

    async loadJCsForRow(rowId) {
        try {
            const jcs = await this.apiRequest("/connections/joint-closures");
            const select = document.getElementById(`jc-${rowId}`);

            if (select && jcs) {
                select.innerHTML = '<option value="">Select JC</option>';
                jcs.forEach((jc) => {
                    const option = document.createElement("option");
                    option.value = jc.id;
                    option.textContent = `${jc.name || jc.closure_id} (${jc.available_capacity}/${jc.capacity} available)`;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error(`Error loading JCs for row ${rowId}:`, error);
        }
    }

    async loadCablesForRow(rowId, jcId) {
        try {
            const cables = await this.apiRequest(`/connections/joint-closures/${jcId}/cables`);
            const select = document.getElementById(`cable-${rowId}`);

            if (select && cables) {
                // Get source cable ID to exclude it from target options
                const sourceCableId = this.currentCableId || window.currentCableId;

                select.innerHTML = '<option value="">Select Cable</option>';
                cables.forEach((cable) => {
                    // Skip if this is the source cable
                    if (cable.id == sourceCableId) {
                        console.log(`Excluding source cable from row ${rowId}:`, cable.name);
                        return;
                    }

                    const option = document.createElement("option");
                    option.value = cable.id;
                    option.textContent = cable.name;
                    select.appendChild(option);
                });
                select.disabled = false;
            }
        } catch (error) {
            console.error(`Error loading cables for row ${rowId}:`, error);
        }
    }

    async loadTubesForRow(rowId, cableId) {
        try {
            const data = await this.apiRequest(`/connections/cables/${cableId}/tubes`);
            const select = document.getElementById(`tube-${rowId}`);

            if (select && data && data.available_tubes) {
                select.innerHTML = '<option value="">Select Tube</option>';
                data.available_tubes.forEach((tube) => {
                    const option = document.createElement("option");
                    option.value = tube;
                    option.textContent = `Tube ${tube}`;
                    select.appendChild(option);
                });
                select.disabled = false;
            }
        } catch (error) {
            console.error(`Error loading tubes for row ${rowId}:`, error);
        }
    }

    async loadCoresForRow(rowId, cableId, tubeId) {
        try {
            const cores = await this.apiRequest(`/connections/cables/${cableId}/tubes/${tubeId}/cores`);
            const select = document.getElementById(`core-${rowId}`);

            if (select && cores) {
                select.innerHTML = '<option value="">Select Core</option>';
                cores.forEach((core) => {
                    const option = document.createElement("option");
                    option.value = core.id;
                    option.textContent = `Core ${core.core_number}`;
                    select.appendChild(option);
                });
                select.disabled = false;
            }
        } catch (error) {
            console.error(`Error loading cores for row ${rowId}:`, error);
        }
    }

    updateSubmitButtonText() {
        const rows = document.querySelectorAll(".connection-row");
        const submitBtn = document.querySelector('#join-core-form button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('span');
            if (btnText) {
                btnText.textContent = `Create ${rows.length} Connection${rows.length > 1 ? 's' : ''}`;
            }
        }
    }

    // ❌ REMOVED: validateUniqueTargetCores() method completely
    // Now allows same target core in multiple connections

    async submitJoinForm() {
        const sourceCoreId = document.getElementById("join-core-id")?.value;
        const rows = document.querySelectorAll(".connection-row");

        console.log("=== SUBMIT JOIN FORM ===");
        console.log("Source Core ID:", sourceCoreId);
        console.log("Total rows found:", rows.length);

        if (!sourceCoreId) {
            this.showNotification("Source core ID not found", "error");
            return;
        }

        if (rows.length === 0) {
            this.showNotification("Please add at least one connection", "error");
            return;
        }

        const connections = [];
        let hasError = false;

        rows.forEach((row, index) => {
            const rowId = row.dataset.rowId;
            console.log(`Processing row #${index + 1}, rowId:`, rowId);

            const jcId = document.getElementById(`jc-${rowId}`)?.value;
            const cableId = document.getElementById(`cable-${rowId}`)?.value;
            const tubeId = document.getElementById(`tube-${rowId}`)?.value;
            const coreId = document.getElementById(`core-${rowId}`)?.value;
            const loss = document.getElementById(`loss-${rowId}`)?.value;
            const notes = document.getElementById(`notes-${rowId}`)?.value;

            console.log(`Row ${index + 1} values:`, {
                jcId,
                cableId,
                tubeId,
                coreId,
                loss,
                notes
            });

            if (!jcId || !coreId) {
                console.error(`Row ${index + 1} validation failed - missing required fields`);
                hasError = true;
                row.classList.add("border-red-500");
                return;
            }

            const connectionData = {
                source_core_id: parseInt(sourceCoreId),
                target_core_id: parseInt(coreId),
                joint_closure_id: parseInt(jcId),
                connection_type: "splice",
                connection_loss: loss ? parseFloat(loss) : null,
                notes: notes || null
            };

            console.log(`Adding connection #${index + 1}:`, connectionData);
            connections.push(connectionData);

            row.classList.remove("border-red-500");
        });

        console.log("Total connections to send:", connections.length);
        console.log("Connections array:", JSON.stringify(connections, null, 2));

        if (hasError) {
            this.showNotification("Please fill all required fields", "error");
            return;
        }

        if (connections.length === 0) {
            this.showNotification("No valid connections to create", "error");
            return;
        }

        const submitBtn = document.querySelector('#join-core-form button[type="submit"]');
        this.toggleLoading(submitBtn, true);

        try {
            console.log("Sending request to /connections/bulk");
            const result = await this.apiRequest("/connections/bulk", "POST", {
                connections: connections
            });

            console.log("Server response:", result);

            if (result.success) {
                this.showNotification(result.message, "success");
                this.closeModal("join-core-modal");
                setTimeout(() => location.reload(), 1000);
            } else {
                console.error("Server returned error:", result.message);
                this.showNotification(`Error: ${result.message}`, "error");
            }
        } catch (error) {
            console.error("Request failed:", error);
            this.showNotification("Error creating connections: " + error.message, "error");
        } finally {
            this.toggleLoading(submitBtn, false);
        }
    }

    // === DISCONNECT MODAL ===
    showDisconnectModal(connectionId, sourceCoreNum, sourceTubeNum, targetCableName, targetCoreNum, targetTubeNum, closureName) {
        console.log("Opening disconnect modal", {
            connectionId,
            sourceCoreNum,
            sourceTubeNum,
            targetCableName,
            targetCoreNum,
            targetTubeNum,
            closureName
        });

        this.disconnectingConnectionId = connectionId;

        // Update source info
        const sourceInfoElement = document.getElementById("disconnect-source-info");
        if (sourceInfoElement) {
            sourceInfoElement.textContent = `Core ${sourceCoreNum} (Tube ${sourceTubeNum})`;
        }

        // Update target info
        const targetInfoElement = document.getElementById("disconnect-target-info");
        if (targetInfoElement) {
            targetInfoElement.textContent = `${targetCableName} - Core ${targetCoreNum} (Tube ${targetTubeNum})`;
        }

        // Show closure info if provided
        if (closureName) {
            const closureInfo = document.createElement('div');
            closureInfo.className = 'flex items-center justify-between text-sm mt-2 pt-2 border-t border-gray-200';
            closureInfo.innerHTML = `
                <span class="font-medium text-gray-700">Via Joint Closure:</span>
                <span class="text-gray-900">${closureName}</span>
            `;

            const container = targetInfoElement?.parentElement?.parentElement;
            if (container) {
                const existingClosureInfo = container.querySelector('.border-t.border-gray-200');
                if (existingClosureInfo) {
                    existingClosureInfo.remove();
                }
                container.appendChild(closureInfo);
            }
        }

        const modal = document.getElementById("disconnect-confirmation-modal");
        if (modal) {
            modal.classList.remove("hidden");
        }
    }

    closeDisconnectModal() {
        this.disconnectingConnectionId = null;
        const modal = document.getElementById("disconnect-confirmation-modal");
        if (modal) {
            modal.classList.add("hidden");
        }
    }

    async confirmDisconnect() {
        if (!this.disconnectingConnectionId) {
            console.error("No connection ID to disconnect");
            return;
        }

        console.log("Confirming disconnect for connection:", this.disconnectingConnectionId);

        const confirmBtn = document.querySelector('#disconnect-confirmation-modal button[onclick="confirmDisconnect()"]');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Disconnecting...
            `;
        }

        try {
            const result = await this.apiRequest(
                `/connections/${this.disconnectingConnectionId}`,
                "DELETE"
            );

            console.log("Disconnect result:", result);

            if (result.success) {
                alert("Connection deleted successfully!");
                this.closeDisconnectModal();
                setTimeout(() => location.reload(), 500);
            } else {
                alert("Error: " + (result.message || "Failed to delete connection"));
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Disconnect
                    `;
                }
            }
        } catch (error) {
            console.error("Error deleting connection:", error);
            alert("Error: " + error.message);
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Disconnect
                `;
            }
        }
    }

    // === UTILITY METHODS ===
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add("hidden");
        }
    }

    async apiRequest(url, method = "GET", data = null) {
        const options = {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content,
            },
        };

        if (data) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        return await response.json();
    }

    showNotification(message, type = "info") {
        alert(message); // Simple alert for now
    }

    toggleLoading(button, isLoading) {
        if (!button) return;

        if (isLoading) {
            button.disabled = true;
            button.classList.add("opacity-50", "cursor-not-allowed");
            const btnText = button.querySelector('span');
            if (btnText) {
                btnText.dataset.originalText = btnText.textContent;
                btnText.textContent = 'Processing...';
            }
        } else {
            button.disabled = false;
            button.classList.remove("opacity-50", "cursor-not-allowed");
            const btnText = button.querySelector('span');
            if (btnText && btnText.dataset.originalText) {
                btnText.textContent = btnText.dataset.originalText;
            }
        }
    }

    getEditFormData() {
        return {
            status: document.getElementById("edit-core-status")?.value,
            usage: document.getElementById("edit-core-usage")?.value,
            attenuation: document.getElementById("edit-core-attenuation")?.value,
            description: document.getElementById("edit-core-description")?.value,
        };
    }

    setText(element, text) {
        if (element) {
            element.textContent = text;
        }
    }

    capitalize(str) {
        if (!str) return "";
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    findAttenuationRow(flexItems) {
        return Array.from(flexItems).find((item) => {
            const label = item.querySelector("span:first-child");
            return label && label.textContent.trim() === "Attenuation:";
        });
    }

    addAttenuationRow(detailSection, attenuation) {
        const attRow = document.createElement("div");
        attRow.className = "flex justify-between";
        attRow.innerHTML = `
            <span class="text-gray-600">Attenuation:</span>
            <span class="font-medium attenuation-value">${attenuation} dB</span>
        `;

        const connectionInfo = detailSection.querySelector(".mt-2.p-2.bg-blue-50");
        if (connectionInfo) {
            detailSection.insertBefore(attRow, connectionInfo);
        } else {
            detailSection.appendChild(attRow);
        }
    }
}

// Initialize
const coreManager = new CoreManager();

// Global functions
window.joinCore = (coreId) => coreManager.openJoinModal(coreId);
window.closeJoinModal = () => coreManager.closeModal("join-core-modal");
window.openCoreEditModal = (coreId) => coreManager.openEditModal(coreId);
window.closeCoreEditModal = () => coreManager.closeModal("core-edit-modal");
window.showDisconnectModal = (connectionId, sourceCoreNum, sourceTubeNum, targetCableName, targetCoreNum, targetTubeNum, closureName) =>
    coreManager.showDisconnectModal(connectionId, sourceCoreNum, sourceTubeNum, targetCableName, targetCoreNum, targetTubeNum, closureName);
window.closeDisconnectModal = () => coreManager.closeDisconnectModal();
window.confirmDisconnect = () => coreManager.confirmDisconnect();
