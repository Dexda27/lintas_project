// Simplified Core Management System - FIXED VERSION
class CoreManager {
    constructor() {
        this.currentEditingCore = null;
        this.currentCableId = null; // Add property to track current cable
        this.currentConnectionToDisconnect = null; // Track connection to disconnect
        this.init();
    }

    init() {
        document.addEventListener("DOMContentLoaded", () => {
            this.setupEventListeners();
            this.setupKeyboardShortcuts();
            // Get current cable ID from the page
            this.getCurrentCableId();
        });
    }

    // Get current cable ID from the page
    getCurrentCableId() {
        // Try to get cable ID from URL or page data
        const urlPath = window.location.pathname;
        const cableMatch = urlPath.match(/\/cables\/(\d+)/);
        if (cableMatch) {
            this.currentCableId = cableMatch[1];
        }

        // Alternative: get from a data attribute or meta tag if available
        const cableIdMeta = document.querySelector('meta[name="cable-id"]');
        if (cableIdMeta) {
            this.currentCableId = cableIdMeta.getAttribute("content");
        }

        // Alternative: get from page title or heading
        const heading = document.querySelector("h1");
        if (heading && heading.dataset.cableId) {
            this.currentCableId = heading.dataset.cableId;
        }

        console.log("Current Cable ID:", this.currentCableId);
    }

    // === CORE EDITING ===
    openEditModal(coreId) {
        this.currentEditingCore = coreId;
        const coreData = this.getCoreData(coreId);

        if (!coreData) {
            this.showNotification("Core not found", "error");
            return;
        }

        this.populateModal(coreData);
        this.showModal("core-edit-modal");
    }

    getCoreData(coreId) {
        const card = document.querySelector(`[data-core="${coreId}"]`);
        if (!card) return null;

        // FIXED: Better description extraction
        let description = card.dataset.description || "";

        // Try to get description from the card's description element if data attribute is empty
        if (!description) {
            const descElement = card.querySelector(
                ".text-xs.text-gray-600.italic"
            );
            if (descElement) {
                description = descElement.textContent.trim();
            }
        }

        return {
            id: coreId,
            core_number: parseInt(
                card.querySelector("h4").textContent.replace("Core ", "")
            ),
            tube_number: parseInt(card.dataset.tube),
            status: card.dataset.status,
            usage: card.dataset.usage,
            attenuation: this.extractAttenuation(card),
            description: description,
            cable: { name: document.querySelector("h1").textContent },
        };
    }

    extractAttenuation(card) {
        const attEl = card.querySelector(
            ".space-y-2 .flex:nth-child(3) span:last-child"
        );
        return attEl?.textContent.includes("dB")
            ? attEl.textContent.replace(" dB", "")
            : "";
    }

    populateModal(core) {
        const fields = {
            "edit-core-id": core.id,
            "edit-core-status": core.status || "ok",
            "edit-core-usage": core.usage || "inactive",
            "edit-core-attenuation": core.attenuation,
            "edit-core-description": core.description,
        };

        // Populate form fields
        Object.entries(fields).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.value = value || "";
        });

        // Update modal header
        this.setText("modal-core-title", `Core ${core.core_number}`);
        this.setText(
            "modal-core-location",
            `Tube ${core.tube_number} • Cable: ${core.cable.name}`
        );
    }

    async submitEdit(formData) {
        if (!this.currentEditingCore) {
            this.showNotification("No core selected", "error");
            return;
        }

        const submitBtn = document.querySelector(
            '#core-edit-form button[type="submit"]'
        );
        this.toggleLoading(submitBtn, true);

        try {
            const response = await this.apiRequest(
                `/cores/${this.currentEditingCore}`,
                "PUT",
                formData
            );

            if (response.success) {
                this.showNotification("Core updated successfully!", "success");
                this.updateCoreCard(this.currentEditingCore, formData);
                setTimeout(() => this.closeModal("core-edit-modal"), 500);
            } else {
                throw new Error(response.message || "Update failed");
            }
        } catch (error) {
            this.showNotification(
                `Failed to update core: ${error.message}`,
                "error"
            );
        } finally {
            this.toggleLoading(submitBtn, false);
        }
    }

    updateCoreCard(coreId, formData) {
        const card = document.querySelector(`[data-core="${coreId}"]`);
        if (!card) return;

        // Animate update
        this.animateUpdate(card, () => {
            // Update data attributes
            Object.assign(card.dataset, {
                status: formData.status,
                usage: formData.usage,
                description: formData.description || "",
            });

            this.updateStatusIndicators(card, formData);
            this.updateDetails(card, formData);
            // FIXED: Call updateDescription with proper parameters
            this.updateDescription(card, formData.description);
            this.updateStatistics();
            this.applyFilters();
        });
    }

    animateUpdate(element, callback) {
        element.style.transform = "scale(0.98)";
        element.style.transition = "all 0.2s ease";

        setTimeout(() => {
            callback();
            element.style.transform = "scale(1)";
            setTimeout(() => {
                element.style.transform = "";
                element.style.transition = "";
            }, 200);
        }, 100);
    }

    updateStatusIndicators(card, formData) {
        const indicators = card.querySelectorAll(".flex.space-x-1 span");

        if (indicators[1]) {
            const isOk = formData.status === "ok";
            indicators[1].className = `w-3 h-3 rounded-full ${
                isOk ? "bg-green-500" : "bg-red-500"
            }`;
            indicators[1].title = `Status: ${isOk ? "OK" : "Not OK"}`;
        }

        if (indicators[2]) {
            const isActive = formData.usage === "active";
            indicators[2].className = `w-3 h-3 rounded-full ${
                isActive ? "bg-blue-500" : "bg-gray-400"
            }`;
            indicators[2].title = `Usage: ${this.capitalize(formData.usage)}`;
        }
    }

    updateDetails(card, formData) {
        const detailSection = card.querySelector(".space-y-2");
        if (!detailSection) return;

        const flexItems = detailSection.querySelectorAll(
            ".flex.justify-between"
        );

        flexItems.forEach((item) => {
            const label = item
                .querySelector("span:first-child")
                ?.textContent.trim();
            const valueSpan = item.querySelector("span:last-child");

            if (!valueSpan) return;

            switch (label) {
                case "Status:":
                    const isOk = formData.status === "ok";
                    valueSpan.textContent = isOk ? "OK" : "Not OK";
                    valueSpan.className = `font-medium ${
                        isOk ? "text-green-600" : "text-red-600"
                    }`;
                    break;

                case "Usage:":
                    const isActive = formData.usage === "active";
                    valueSpan.textContent = this.capitalize(formData.usage);
                    valueSpan.className = `font-medium ${
                        isActive ? "text-blue-600" : "text-gray-600"
                    }`;
                    break;

                case "Attenuation:":
                    if (formData.attenuation) {
                        valueSpan.textContent = `${formData.attenuation} dB`;
                    } else {
                        item.remove();
                    }
                    break;
            }
        });

        // Add attenuation row if needed
        if (formData.attenuation && !this.findAttenuationRow(flexItems)) {
            this.addAttenuationRow(detailSection, formData.attenuation);
        }
    }

    // === CONNECTION MANAGEMENT ===
    async loadJointClosures() {
        try {
            const jcs = await this.apiRequest("/connections/joint-closures");
            const select = document.getElementById("jc-selection");

            select.innerHTML = '<option value="">Select JC...</option>';
            jcs.forEach((jc) => {
                const available =
                    jc.capacity - jc.used_capacity ||
                    jc.available_capacity ||
                    0;
                select.innerHTML += `<option value="${jc.id}">${jc.name} (${jc.location}) - ${available}/${jc.capacity} available</option>`;
            });
        } catch (error) {
            console.error("Error loading JCs:", error);
        }
    }

    // NEW: Show disconnect confirmation modal
    showDisconnectModal(connectionId, sourceCoreNum, sourceTubeNum, targetCableName, targetCoreNum, targetTubeNum) {
        // Store connection ID for later use
        this.currentConnectionToDisconnect = connectionId;
        document.getElementById('connection-to-disconnect').value = connectionId;

        // Get current cable name from page
        const currentCableName = document.querySelector('h1').textContent;

        // Populate modal with connection info
        document.getElementById('disconnect-source-info').textContent =
            `${currentCableName} - Tube ${sourceTubeNum}, Core ${sourceCoreNum}`;
        document.getElementById('disconnect-target-info').textContent =
            `${targetCableName} - Tube ${targetTubeNum}, Core ${targetCoreNum}`;

        // Show modal
        this.showModal('disconnect-confirmation-modal');
    }

    // NEW: Close disconnect modal
    closeDisconnectModal() {
        this.closeModal('disconnect-confirmation-modal');
        this.currentConnectionToDisconnect = null;
    }

    // NEW: Confirm disconnect action
    async confirmDisconnect() {
        if (!this.currentConnectionToDisconnect) {
            this.showNotification("No connection selected for disconnection", "error");
            return;
        }

        const disconnectBtn = document.querySelector('#disconnect-confirmation-modal button[onclick="confirmDisconnect()"]');
        this.toggleLoading(disconnectBtn, true);

        try {
            // Try DELETE method first
            let result;
            try {
                result = await this.apiRequest(
                    `/connections/${this.currentConnectionToDisconnect}`,
                    "DELETE"
                );
            } catch (error) {
                // If DELETE fails with 405, try with POST method explicitly
                if (error.message.includes('405')) {
                    console.log("DELETE method failed, trying POST with _method override");

                    result = await fetch(`/connections/${this.currentConnectionToDisconnect}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': this.getCSRFToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new URLSearchParams({
                            '_token': this.getCSRFToken(),
                            '_method': 'DELETE'
                        })
                    });

                    if (!result.ok) {
                        throw new Error(`HTTP ${result.status}: ${result.statusText}`);
                    }

                    result = await result.json();
                } else {
                    throw error;
                }
            }

            if (result.success || result.status === 'success') {
                this.showNotification("Connection disconnected successfully!", "success");
                this.closeDisconnectModal();
                // Reload page to reflect changes
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error(result.message || result.error || "Disconnection failed");
            }
        } catch (error) {
            console.error("Disconnect error:", error);
            this.showNotification(`Failed to disconnect: ${error.message}`, "error");
        } finally {
            this.toggleLoading(disconnectBtn, false);
        }
    }

    // UPDATED: Fixed disconnect function (no longer used directly, replaced by modal)
    async disconnectCore(connectionId) {
        // This function is now deprecated in favor of the modal approach
        // Keeping it for backward compatibility but it should not be called directly
        console.warn("disconnectCore called directly - use showDisconnectModal instead");

        if (!connectionId) {
            this.showNotification("Invalid connection ID", "error");
            return;
        }

        try {
            const result = await this.apiRequest(
                `/connections/${connectionId}`,
                "DELETE"
            );

            if (result.success) {
                this.showNotification("Connection disconnected successfully!", "success");
                location.reload();
            } else {
                this.showNotification(`Error: ${result.message}`, "error");
            }
        } catch (error) {
            this.showNotification("Error disconnecting core", "error");
            console.error("Disconnect error:", error);
        }
    }

    // === MODAL MANAGEMENT ===
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector(".bg-white");

        // Set initial state
        modal.style.backgroundColor = "rgba(0, 0, 0, 0)";
        if (content) {
            content.style.transform = "scale(0.9) translateY(-20px)";
            content.style.opacity = "0";
            content.style.transition =
                "all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)";
        }

        // Remove hidden class and start animation
        modal.classList.remove("hidden");
        modal.style.transition = "background-color 0.3s ease";

        // Use requestAnimationFrame for smoother animation
        requestAnimationFrame(() => {
            modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";

            if (content) {
                content.style.transform = "scale(1) translateY(0)";
                content.style.opacity = "1";
            }
        });

        // Focus first input after animation
        setTimeout(() => {
            modal.querySelector("select, input, button")?.focus();
        }, 100);
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector(".bg-white");

        // Start close animation
        modal.style.backgroundColor = "rgba(0, 0, 0, 0)";

        if (content) {
            content.style.transform = "scale(0.9) translateY(-20px)";
            content.style.opacity = "0";
        }

        // Hide modal after animation completes
        setTimeout(() => {
            modal.classList.add("hidden");
            this.resetModal(modalId);

            // Reset styles for next use
            if (content) {
                content.style.transform = "";
                content.style.opacity = "";
                content.style.transition = "";
            }
            modal.style.backgroundColor = "";
            modal.style.transition = "";
        }, 300);
    }

    resetModal(modalId) {
        if (modalId === "core-edit-modal") {
            this.currentEditingCore = null;
            document.getElementById("core-edit-form")?.reset();
        } else if (modalId === "join-core-modal") {
            document.getElementById("join-core-form")?.reset();
            ["target-cable", "target-tube", "target-core"].forEach((id) => {
                const el = document.getElementById(id);
                if (el) el.disabled = true;
            });
        } else if (modalId === "disconnect-confirmation-modal") {
            this.currentConnectionToDisconnect = null;
            document.getElementById('connection-to-disconnect').value = '';
        }
    }

    // === FILTERING & STATISTICS ===
    applyFilters() {
        const filters = this.getActiveFilters();
        const cards = document.querySelectorAll(".core-card");
        const visibleTubes = new Set();

        // Hide all tube sections first
        document.querySelectorAll(".tube-section").forEach((section) => {
            section.style.display = "none";
        });

        // Show/hide cards based on filters
        cards.forEach((card) => {
            const shouldShow = this.shouldShowCard(card, filters);
            card.style.display = shouldShow ? "block" : "none";

            if (shouldShow) {
                visibleTubes.add(card.dataset.tube);
            }
        });

        // Show tube sections with visible cards
        document.querySelectorAll(".tube-section").forEach((section) => {
            if (visibleTubes.has(section.dataset.tube)) {
                section.style.display = "block";
            }
        });
    }

    getActiveFilters() {
        return {
            tube: document.getElementById("tube-filter")?.value || "",
            status: document.getElementById("status-filter")?.value || "",
            usage: document.getElementById("usage-filter")?.value || "",
        };
    }

    shouldShowCard(card, filters) {
        return (
            (!filters.tube || card.dataset.tube === filters.tube) &&
            (!filters.status || card.dataset.status === filters.status) &&
            (!filters.usage || card.dataset.usage === filters.usage)
        );
    }

    updateStatistics() {
        const cards = document.querySelectorAll(".core-card");
        const stats = {
            total: cards.length,
            active: 0,
            inactive: 0,
            problems: 0,
        };

        cards.forEach((card) => {
            if (card.dataset.usage === "active") stats.active++;
            if (card.dataset.usage === "inactive") stats.inactive++;
            if (card.dataset.status === "not_ok") stats.problems++;
        });

        this.updateMainStats(stats);
        this.updateTubeStats();
    }

    updateMainStats(stats) {
        const statsCards = document.querySelectorAll(
            ".grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 .bg-white.rounded-lg.shadow"
        );
        if (statsCards.length >= 4) {
            this.setText(
                statsCards[1].querySelector(".text-2xl.font-bold"),
                stats.active
            );
            this.setText(
                statsCards[2].querySelector(".text-2xl.font-bold"),
                stats.inactive
            );
            this.setText(
                statsCards[3].querySelector(".text-2xl.font-bold"),
                stats.problems
            );
        }
    }

    updateTubeStats() {
        document.querySelectorAll(".tube-section").forEach((section) => {
            const cards = section.querySelectorAll(".core-card");
            const stats = { active: 0, inactive: 0, problems: 0 };

            cards.forEach((card) => {
                if (card.dataset.usage === "active") stats.active++;
                if (card.dataset.usage === "inactive") stats.inactive++;
                if (card.dataset.status === "not_ok") stats.problems++;
            });

            const tubeStats = section.querySelectorAll(
                ".flex.items-center.space-x-4 span"
            );
            if (tubeStats.length >= 3) {
                tubeStats[0].innerHTML = this.createStatHTML(
                    "green",
                    "Active",
                    stats.active
                );
                tubeStats[1].innerHTML = this.createStatHTML(
                    "gray",
                    "Inactive",
                    stats.inactive
                );
                tubeStats[2].innerHTML = this.createStatHTML(
                    "red",
                    "Problems",
                    stats.problems
                );
            }
        });
    }

    createStatHTML(color, label, count) {
        return `<span class="w-3 h-3 bg-${color}-500 rounded-full mr-2"></span>${label}: ${count}`;
    }

    // === CASCADE DROPDOWNS ===
    setupCascadeDropdowns() {
        const selectors = {
            jc: document.getElementById("jc-selection"),
            cable: document.getElementById("target-cable"),
            tube: document.getElementById("target-tube"),
            core: document.getElementById("target-core"),
        };

        if (selectors.jc) {
            selectors.jc.addEventListener("change", async () => {
                await this.loadCables(selectors.jc.value, selectors.cable);
                this.disableElements([selectors.tube, selectors.core]);
            });
        }

        if (selectors.cable) {
            selectors.cable.addEventListener("change", async () => {
                await this.loadTubes(selectors.cable.value, selectors.tube);
                this.disableElements([selectors.core]);
            });
        }

        if (selectors.tube) {
            selectors.tube.addEventListener("change", async () => {
                await this.loadCores(
                    selectors.cable.value,
                    selectors.tube.value,
                    selectors.core
                );
            });
        }
    }

    // UPDATED: Modified to exclude current cable
    async loadCables(jcId, cableSelect) {
        if (!jcId) return;

        try {
            const cables = await this.apiRequest(
                `/connections/joint-closures/${jcId}/cables`
            );
            cableSelect.innerHTML = '<option value="">Select Cable...</option>';

            cables.forEach((cable) => {
                // Exclude current cable from dropdown
                if (cable.id != this.currentCableId) {
                    cableSelect.innerHTML += `<option value="${cable.id}">${cable.name} (${cable.cable_id})</option>`;
                }
            });

            cableSelect.disabled = false;

            // Show message if no cables available
            if (cableSelect.options.length === 1) {
                cableSelect.innerHTML +=
                    '<option value="" disabled>No other cables available</option>';
            }
        } catch (error) {
            console.error("Error loading cables:", error);
            this.showNotification("Error loading cables", "error");
        }
    }

    async loadTubes(cableId, tubeSelect) {
        if (!cableId) return;

        try {
            const data = await this.apiRequest(
                `/connections/cables/${cableId}/tubes`
            );
            tubeSelect.innerHTML = '<option value="">Select Tube...</option>';

            for (let i = 1; i <= data.total_tubes; i++) {
                tubeSelect.innerHTML += `<option value="${i}">Tube ${i}</option>`;
            }
            tubeSelect.disabled = false;
        } catch (error) {
            console.error("Error loading tubes:", error);
        }
    }

    async loadCores(cableId, tubeId, coreSelect) {
        if (!cableId || !tubeId) return;

        try {
            const cores = await this.apiRequest(
                `/connections/cables/${cableId}/tubes/${tubeId}/cores`
            );
            coreSelect.innerHTML = '<option value="">Select Core...</option>';

            cores.forEach((core) => {
                const isOk = core.status === "ok";
                coreSelect.innerHTML += `<option value="${core.id}" ${
                    !isOk ? 'style="color: #ef4444"' : ""
                }>
                    Core ${core.core_number}${!isOk ? " (!)" : ""}
                </option>`;
            });
            coreSelect.disabled = false;
        } catch (error) {
            console.error("Error loading cores:", error);
        }
    }

    // === EVENT LISTENERS ===
    setupEventListeners() {
        this.setupFilters();
        this.setupForms();
        this.setupCascadeDropdowns();
    }

    setupFilters() {
        const filterIds = ["tube-filter", "status-filter", "usage-filter"];

        filterIds.forEach((id) => {
            const filter = document.getElementById(id);
            if (filter) {
                filter.addEventListener("change", () => this.applyFilters());
            }
        });

        const clearBtn = document.getElementById("clear-filters");
        if (clearBtn) {
            clearBtn.addEventListener("click", () => {
                filterIds.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.value = "";
                });
                this.applyFilters();
            });
        }
    }

    setupForms() {
        // Core Edit Form
        const editForm = document.getElementById("core-edit-form");
        if (editForm) {
            editForm.addEventListener("submit", (e) => {
                e.preventDefault();
                const formData = this.getEditFormData();
                this.submitEdit(formData);
            });
        }

        // Join Core Form
        const joinForm = document.getElementById("join-core-form");
        if (joinForm) {
            joinForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.submitJoinForm();
            });
        }
    }

    getEditFormData() {
        return {
            status: this.getValue("edit-core-status"),
            usage: this.getValue("edit-core-usage"),
            attenuation: this.getValue("edit-core-attenuation") || null,
            description: this.getValue("edit-core-description") || null,
        };
    }

    async submitJoinForm() {
        const formData = {
            source_core_id: this.getValue("join-core-id"),
            target_core_id: this.getValue("target-core"),
            joint_closure_id: this.getValue("jc-selection"),
            connection_type: this.getValue("connection-type"),
            connection_loss: this.getValue("connection-loss"),
            notes: this.getValue("connection-notes"),
        };

        try {
            const result = await this.apiRequest(
                "/connections",
                "POST",
                formData
            );

            if (result.success) {
                this.showNotification(
                    "Connection created successfully!",
                    "success"
                );
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showNotification(`Error: ${result.message}`, "error");
            }
        } catch (error) {
            this.showNotification("Error creating connection", "error");
        }
    }

    setupKeyboardShortcuts() {
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                this.closeVisibleModal();
            }
        });
    }

    closeVisibleModal() {
        const modals = ["core-edit-modal", "join-core-modal", "disconnect-confirmation-modal"];

        for (const modalId of modals) {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains("hidden")) {
                this.closeModal(modalId);
                break;
            }
        }
    }

    // === UTILITY METHODS ===
    async apiRequest(url, method = "GET", data = null) {
        const options = {
            method: method === "PUT" || method === "DELETE" ? "POST" : method,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": this.getCSRFToken(),
            },
        };

        if (data) {
            const formData = new FormData();
            formData.append("_token", this.getCSRFToken());

            if (method === "PUT" || method === "DELETE") {
                formData.append("_method", method);
            }

            Object.entries(data).forEach(([key, value]) => {
                if (value !== undefined) {
                    formData.append(key, value || "");
                }
            });

            options.body = formData;
        }

        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    }

    getCSRFToken() {
        return (
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || ""
        );
    }

    showNotification(message, type = "info") {
        const colors = {
            success: "bg-green-500",
            error: "bg-red-500",
            warning: "bg-yellow-500",
            info: "bg-blue-500",
        };

        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 z-[9999] px-6 py-4 rounded-lg shadow-xl text-white transform transition-all duration-300 ease-out ${
            colors[type] || colors.info
        }`;

        // Set initial position and styling
        notification.style.cssText = `
            min-width: 300px;
            max-width: 400px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.4;
            word-wrap: break-word;
            transform: translateX(100%) scale(0.95);
            opacity: 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        `;

        notification.textContent = message;
        document.body.appendChild(notification);

        // Force reflow then animate in
        notification.offsetHeight;

        requestAnimationFrame(() => {
            notification.style.transform = "translateX(0) scale(1)";
            notification.style.opacity = "1";
        });

        // Animate out and remove
        setTimeout(() => {
            notification.style.transform = "translateX(100%) scale(0.95)";
            notification.style.opacity = "0";

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 4000);
    }

    toggleLoading(button, isLoading) {
        if (!button) return;

        if (isLoading) {
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                ${button.textContent.includes('Disconnect') ? 'Disconnecting...' : 'Processing...'}
            `;
            button.disabled = true;
        } else {
            button.innerHTML = button.dataset.originalText || button.innerHTML;
            button.disabled = false;
        }
    }

    // Helper methods
    setText(elementOrId, text) {
        const el =
            typeof elementOrId === "string"
                ? document.getElementById(elementOrId)
                : elementOrId;
        if (el) el.textContent = text;
    }

    getValue(elementId) {
        return document.getElementById(elementId)?.value || "";
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    disableElements(elements) {
        elements.forEach((el) => {
            if (el) el.disabled = true;
        });
    }

    findAttenuationRow(flexItems) {
        return Array.from(flexItems).some((item) => {
            const label = item.querySelector("span:first-child");
            return label && label.textContent.trim() === "Attenuation:";
        });
    }

    addAttenuationRow(detailSection, attenuation) {
        const row = document.createElement("div");
        row.className = "flex justify-between";
        row.innerHTML = `
            <span class="text-gray-600">Attenuation:</span>
            <span class="font-medium">${attenuation} dB</span>
        `;
        detailSection.appendChild(row);
    }

    // FIXED: Complete rewrite of updateDescription method to match HTML structure
    updateDescription(card, description) {
        console.log("Updating description:", description); // Debug log

        // Find the detail section where description should be
        const detailSection = card.querySelector(".space-y-2");
        if (!detailSection) {
            console.log("Detail section not found");
            return;
        }

        // Look for existing description row with the structure from template
        let existingDescRow = null;
        const flexItems = detailSection.querySelectorAll(
            ".flex.justify-between"
        );

        flexItems.forEach((item) => {
            const label = item.querySelector("span:first-child");
            if (label && label.textContent.trim() === "Description:") {
                existingDescRow = item;
            }
        });

        if (description && description.trim()) {
            if (existingDescRow) {
                // Update existing description
                console.log("Updating existing description row");
                const valueSpan =
                    existingDescRow.querySelector("span:last-child");
                if (valueSpan) {
                    valueSpan.textContent = description.trim();
                }
            } else {
                // Create new description row matching template structure
                console.log("Creating new description row");
                const descRow = document.createElement("div");
                descRow.className = "flex justify-between";
                descRow.innerHTML = `
                    <span class="text-gray-600">Description:</span>
                    <span class="font-medium">${description.trim()}</span>
                `;
                detailSection.appendChild(descRow);
            }
        } else {
            // Remove description row if empty
            if (existingDescRow) {
                console.log("Removing description row");
                existingDescRow.remove();
            }
        }
    }
}

// Initialize and expose global functions
const coreManager = new CoreManager();

// Global functions for backward compatibility
window.openCoreEditModal = (coreId) => coreManager.openEditModal(coreId);
window.closeCoreEditModal = () => coreManager.closeModal("core-edit-modal");
window.joinCore = (coreId) => {
    document.getElementById("join-core-id").value = coreId;
    coreManager.loadJointClosures();
    coreManager.showModal("join-core-modal");
};
window.closeJoinModal = () => coreManager.closeModal("join-core-modal");

// NEW: Global functions for disconnect modal
window.showDisconnectModal = (connectionId, sourceCoreNum, sourceTubeNum, targetCableName, targetCoreNum, targetTubeNum) =>
    coreManager.showDisconnectModal(connectionId, sourceCoreNum, sourceTubeNum, targetCableName, targetCoreNum, targetTubeNum);
window.closeDisconnectModal = () => coreManager.closeDisconnectModal();
window.confirmDisconnect = () => coreManager.confirmDisconnect();

// DEPRECATED: Keep for backward compatibility but not recommended
window.disconnectCore = (connectionId) => {
    console.warn("disconnectCore is deprecated, use showDisconnectModal instead");
    coreManager.disconnectCore(connectionId);
};
