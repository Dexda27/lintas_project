// Core Management - Simplified Version
class CoreManager {
    constructor() {
        this.currentEditingCore = null;
        this.coreColorMap = [
            "#0000ff",
            "#ff7f00",
            "#00ff00",
            "#964b00",
            "#808080",
            "#ffffff",
            "#ff0000",
            "#000000",
            "#ffff00",
            "#8f00ff",
            "#ff00ff",
            "#00ffff",
        ];
        this.init();
    }

    // === CORE EDITING ===
    openEditModal(coreId) {
        this.currentEditingCore = coreId;
        const coreData = this.getCoreDataFromDOM(coreId);

        if (!coreData) {
            this.showNotification("Core not found on page", "error");
            return;
        }

        this.populateEditModal(coreData);
        this.showModal("core-edit-modal");
    }

    getCoreDataFromDOM(coreId) {
        const coreCard = document.querySelector(`[data-core="${coreId}"]`);
        if (!coreCard) return null;

        const coreNumber = coreCard
            .querySelector("h4")
            .textContent.replace("Core ", "");
        const attenuationEl = coreCard.querySelector(
            ".space-y-2 .flex:nth-child(3) span:last-child"
        );
        const attenuation = attenuationEl?.textContent.includes("dB")
            ? attenuationEl.textContent.replace(" dB", "")
            : "";

        return {
            id: coreId,
            core_number: parseInt(coreNumber),
            tube_number: parseInt(coreCard.dataset.tube),
            status: coreCard.dataset.status,
            usage: coreCard.dataset.usage,
            attenuation,
            description: coreCard.dataset.description || "",
            cable: { name: document.querySelector("h1").textContent },
        };
    }

    populateEditModal(core) {
        const elements = {
            "edit-core-id": core.id,
            "edit-core-status": core.status || "ok",
            "edit-core-usage": core.usage || "inactive",
            "edit-core-attenuation": core.attenuation || "",
            "edit-core-description": core.description || "",
        };

        Object.entries(elements).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.value = value;
        });

        // Update modal header
        this.updateElement(
            document.getElementById("modal-core-title"),
            `Core ${core.core_number}`
        );
        this.updateElement(
            document.getElementById("modal-core-location"),
            `Tube ${core.tube_number} • Cable: ${core.cable.name}`
        );
    }

    async submitEdit(formData) {
        if (!this.currentEditingCore) {
            this.showNotification("No core selected for editing", "error");
            return;
        }

        const submitBtn = document.querySelector(
            '#core-edit-form button[type="submit"]'
        );
        this.setButtonLoading(submitBtn, true);

        try {
            const response = await this.makeRequest(
                `/cores/${this.currentEditingCore}`,
                "PUT",
                formData
            );

            if (response.success) {
                this.showNotification("Core updated successfully!", "success");
                this.updateCoreCardInDOM(this.currentEditingCore, formData);
                setTimeout(() => this.closeModal("core-edit-modal"), 500);
            } else {
                throw new Error(response.message || "Update failed");
            }
        } catch (error) {
            console.error("Error updating core:", error);
            this.showNotification(
                "Failed to update core: " + error.message,
                "error"
            );
        } finally {
            this.setButtonLoading(submitBtn, false);
        }
    }

    updateCoreCardInDOM(coreId, formData) {
        const coreCard = document.querySelector(`[data-core="${coreId}"]`);
        if (!coreCard) return;

        // Animation
        coreCard.style.transform = "scale(0.98)";
        coreCard.style.transition = "all 0.2s ease";

        setTimeout(() => {
            // Update attributes
            Object.assign(coreCard.dataset, {
                status: formData.status,
                usage: formData.usage,
                description: formData.description || "",
            });

            this.updateStatusIndicators(coreCard, formData);
            this.updateDetailSection(coreCard, formData);
            this.updateDescription(coreCard, formData.description);
            this.updateStatistics();
            this.applyFiltersToCard(coreCard);

            // Reset animation
            coreCard.style.transform = "scale(1)";
            coreCard.style.boxShadow = "0 4px 6px -1px rgba(0, 0, 0, 0.1)";
            setTimeout(() => {
                coreCard.style.boxShadow = "";
                coreCard.style.transition = "";
            }, 1000);
        }, 100);
    }

    updateStatusIndicators(coreCard, formData) {
        const indicators = coreCard.querySelectorAll(".flex.space-x-1 span");
        if (indicators.length < 2) return;

        // Status indicator - check if exists
        if (indicators[1]) {
            indicators[1].className = `w-3 h-3 rounded-full ${
                formData.status === "ok" ? "bg-green-500" : "bg-red-500"
            }`;
            indicators[1].title = `Status: ${
                formData.status === "ok" ? "OK" : "Not OK"
            }`;
        }

        // Usage indicator - check if exists
        if (indicators[2]) {
            indicators[2].className = `w-3 h-3 rounded-full ${
                formData.usage === "active" ? "bg-blue-500" : "bg-gray-400"
            }`;
            indicators[2].title = `Usage: ${
                formData.usage.charAt(0).toUpperCase() + formData.usage.slice(1)
            }`;
        }
    }

    updateDetailSection(coreCard, formData) {
        const detailSection = coreCard.querySelector(".space-y-2");
        if (!detailSection) return;

        const flexItems = detailSection.querySelectorAll(
            ".flex.justify-between"
        );
        const updates = {
            "Status:": {
                text: formData.status === "ok" ? "OK" : "Not OK",
                class: `font-medium ${
                    formData.status === "ok" ? "text-green-600" : "text-red-600"
                }`,
            },
            "Usage:": {
                text:
                    formData.usage.charAt(0).toUpperCase() +
                    formData.usage.slice(1),
                class: `font-medium ${
                    formData.usage === "active"
                        ? "text-blue-600"
                        : "text-gray-600"
                }`,
            },
            "Attenuation:": formData.attenuation
                ? {
                      text: `${formData.attenuation} dB`,
                      class: "font-medium",
                  }
                : null,
        };

        flexItems.forEach((item) => {
            const label = item.querySelector("span:first-child");
            const value = item.querySelector("span:last-child");

            if (label && value && updates[label.textContent.trim()]) {
                const update = updates[label.textContent.trim()];
                if (update) {
                    value.textContent = update.text;
                    value.className = update.class;
                } else {
                    item.remove();
                }
            }
        });

        // Add attenuation if needed
        if (formData.attenuation && !this.hasAttenuationRow(flexItems)) {
            this.addAttenuationRow(detailSection, formData.attenuation);
        }
    }

    // === MODAL MANAGEMENT ===
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove("hidden");

        setTimeout(() => {
            const content = modal.querySelector(".bg-white");
            if (content) {
                content.style.transform = "scale(1)";
                content.style.opacity = "1";
            }
        }, 10);

        // Focus first input
        const firstInput = modal.querySelector("select, input");
        if (firstInput) firstInput.focus();
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector(".bg-white");

        if (content) {
            content.style.transform = "scale(0.95)";
            content.style.opacity = "0.5";
        }

        setTimeout(() => {
            modal.classList.add("hidden");
            if (modalId === "core-edit-modal") this.resetEditModal();
            if (modalId === "join-core-modal") this.resetJoinModal();
        }, 150);
    }

    resetEditModal() {
        this.currentEditingCore = null;
        document.getElementById("core-edit-form")?.reset();
        document.getElementById("edit-core-id").value = "";
    }

    resetJoinModal() {
        document.getElementById("join-core-form")?.reset();
        ["target-cable", "target-tube", "target-core"].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.disabled = true;
        });
    }

    // === STATISTICS & FILTERING ===
    updateStatistics() {
        const allCards = document.querySelectorAll(".core-card");
        const stats = {
            total: allCards.length,
            active: 0,
            inactive: 0,
            problems: 0,
        };

        allCards.forEach((card) => {
            if (card.dataset.usage === "active") stats.active++;
            if (card.dataset.usage === "inactive") stats.inactive++;
            if (card.dataset.status === "not_ok") stats.problems++;
        });

        // Update main statistics
        const statsCards = document.querySelectorAll(
            ".grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 .bg-white.rounded-lg.shadow"
        );
        if (statsCards.length >= 4) {
            this.updateElement(
                statsCards[1].querySelector(".text-2xl.font-bold"),
                stats.active
            );
            this.updateElement(
                statsCards[2].querySelector(".text-2xl.font-bold"),
                stats.inactive
            );
            this.updateElement(
                statsCards[3].querySelector(".text-2xl.font-bold"),
                stats.problems
            );
        }

        // Update tube statistics
        this.updateTubeStatistics();
    }

    updateTubeStatistics() {
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
                tubeStats[0].innerHTML = `<span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>Active: ${stats.active}`;
                tubeStats[1].innerHTML = `<span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>Inactive: ${stats.inactive}`;
                tubeStats[2].innerHTML = `<span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>Problems: ${stats.problems}`;
            }
        });
    }

    applyFilters() {
        const filters = {
            tube: document.getElementById("tube-filter")?.value || "",
            status: document.getElementById("status-filter")?.value || "",
            usage: document.getElementById("usage-filter")?.value || "",
        };

        const coreCards = document.querySelectorAll(".core-card");
        const tubeSections = document.querySelectorAll(".tube-section");
        const visibleTubes = new Set();

        // Hide all tube sections initially
        tubeSections.forEach((section) => (section.style.display = "none"));

        coreCards.forEach((card) => {
            const shouldShow =
                (!filters.tube || card.dataset.tube === filters.tube) &&
                (!filters.status || card.dataset.status === filters.status) &&
                (!filters.usage || card.dataset.usage === filters.usage);

            card.style.display = shouldShow ? "block" : "none";
            if (shouldShow) visibleTubes.add(card.dataset.tube);
        });

        // Show tube sections with visible cards
        tubeSections.forEach((section) => {
            if (visibleTubes.has(section.dataset.tube)) {
                section.style.display = "block";
            }
        });
    }

    applyFiltersToCard(coreCard) {
        const filters = {
            tube: document.getElementById("tube-filter")?.value || "",
            status: document.getElementById("status-filter")?.value || "",
            usage: document.getElementById("usage-filter")?.value || "",
        };

        const shouldShow =
            (!filters.tube || coreCard.dataset.tube === filters.tube) &&
            (!filters.status || coreCard.dataset.status === filters.status) &&
            (!filters.usage || coreCard.dataset.usage === filters.usage);

        coreCard.style.display = shouldShow ? "block" : "none";

        const tubeSection = coreCard.closest(".tube-section");
        if (tubeSection) {
            const visibleCards = tubeSection.querySelectorAll(
                '.core-card[style="display: block"], .core-card:not([style*="display: none"])'
            );
            tubeSection.style.display =
                visibleCards.length > 0 ? "block" : "none";
        }
    }

    // === CONNECTION MANAGEMENT ===
    async loadJointClosures() {
        try {
            const data = await this.makeRequest(
                "/connections/joint-closures",
                "GET"
            );
            const select = document.getElementById("jc-selection");
            select.innerHTML = '<option value="">Select JC...</option>';

            data.forEach((jc) => {
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

    async disconnectCore(connectionId) {
        if (
            !confirm(
                "Are you sure you want to disconnect this core connection?"
            )
        )
            return;

        try {
            const data = await this.makeRequest(
                `/connections/${connectionId}`,
                "DELETE"
            );
            if (data.success) {
                location.reload();
            } else {
                this.showNotification(
                    "Error: " + (data.message || "Unknown error"),
                    "error"
                );
            }
        } catch (error) {
            console.error("Error:", error);
            this.showNotification("Error disconnecting core", "error");
        }
    }

    // === UTILITY FUNCTIONS ===
    async makeRequest(url, method, formData = null) {
        const options = {
            method: method === "PUT" || method === "DELETE" ? "POST" : method,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": this.getCSRFToken(),
            },
        };

        if (formData) {
            const submitData = new FormData();
            submitData.append("_token", this.getCSRFToken());

            if (method === "PUT" || method === "DELETE") {
                submitData.append("_method", method);
            }

            Object.entries(formData).forEach(([key, value]) => {
                if (value !== null && value !== "") {
                    submitData.append(key, value);
                }
            });

            options.body = submitData;
        }

        const response = await fetch(url, options);
        if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    }

    getCSRFToken() {
        return (
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || ""
        );
    }

    setButtonLoading(button, isLoading) {
        if (!button) return;

        if (isLoading) {
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Updating...
            `;
            button.disabled = true;
        } else {
            button.innerHTML = button.dataset.originalText || button.innerHTML;
            button.disabled = false;
        }
    }

    updateElement(element, content, styles = null) {
        if (!element) return;
        if (content !== undefined) element.textContent = content;
        if (styles) Object.assign(element.style, styles);
    }

    showNotification(message, type = "info") {
        const colors = {
            success: "bg-green-500 text-white",
            error: "bg-red-500 text-white",
            warning: "bg-yellow-500 text-white",
            info: "bg-blue-500 text-white",
        };

        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full ${
            colors[type] || colors.info
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => (notification.style.transform = "translateX(0)"), 10);
        setTimeout(() => {
            notification.style.transform = "translateX(full)";
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    hasAttenuationRow(flexItems) {
        return Array.from(flexItems).some((item) => {
            const label = item.querySelector("span:first-child");
            return label && label.textContent.trim() === "Attenuation:";
        });
    }

    addAttenuationRow(detailSection, attenuation) {
        const newRow = document.createElement("div");
        newRow.className = "flex justify-between";
        newRow.innerHTML = `
            <span class="text-gray-600">Attenuation:</span>
            <span class="font-medium">${attenuation} dB</span>
        `;
        detailSection.appendChild(newRow);
    }

    updateDescription(coreCard, description) {
        const existingDesc = coreCard.querySelector(
            ".text-xs.text-gray-600.italic"
        );
        const detailSection = coreCard.querySelector(".space-y-2");

        if (description?.trim()) {
            if (existingDesc) {
                existingDesc.textContent = description;
            } else {
                const descDiv = document.createElement("div");
                descDiv.className = "mt-2";
                descDiv.innerHTML = `<p class="text-xs text-gray-600 italic">${description}</p>`;
                detailSection.appendChild(descDiv);
            }
        } else if (existingDesc) {
            existingDesc.closest(".mt-2")?.remove();
        }
    }

    // === CASCADE DROPDOWN HANDLERS ===
    setupCascadeDropdowns() {
        const jcSelect = document.getElementById("jc-selection");
        const cableSelect = document.getElementById("target-cable");
        const tubeSelect = document.getElementById("target-tube");
        const coreSelect = document.getElementById("target-core");

        if (jcSelect) {
            jcSelect.addEventListener("change", async () => {
                if (jcSelect.value) {
                    try {
                        const data = await this.makeRequest(
                            `/connections/joint-closures/${jcSelect.value}/cables`
                        );
                        cableSelect.innerHTML =
                            '<option value="">Select Cable...</option>';
                        data.forEach((cable) => {
                            if (cable.id !== window.currentCableId) {
                                cableSelect.innerHTML += `<option value="${cable.id}">${cable.name} (${cable.cable_id})</option>`;
                            }
                        });
                        cableSelect.disabled = false;
                    } catch (error) {
                        console.error("Error loading cables:", error);
                    }
                }
                [tubeSelect, coreSelect].forEach((el) => (el.disabled = true));
            });
        }

        if (cableSelect) {
            cableSelect.addEventListener("change", async () => {
                if (cableSelect.value) {
                    try {
                        const data = await this.makeRequest(
                            `/connections/cables/${cableSelect.value}/tubes`
                        );
                        tubeSelect.innerHTML =
                            '<option value="">Select Tube...</option>';
                        for (let i = 1; i <= data.total_tubes; i++) {
                            tubeSelect.innerHTML += `<option value="${i}">Tube ${i}</option>`;
                        }
                        tubeSelect.disabled = false;
                    } catch (error) {
                        console.error("Error loading tubes:", error);
                    }
                }
                coreSelect.disabled = true;
            });
        }

        if (tubeSelect) {
            tubeSelect.addEventListener("change", async () => {
                if (cableSelect.value && tubeSelect.value) {
                    try {
                        const data = await this.makeRequest(
                            `/connections/cables/${cableSelect.value}/tubes/${tubeSelect.value}/cores`
                        );
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
                    } catch (error) {
                        console.error("Error loading cores:", error);
                    }
                }
            });
        }
    }

    // === INITIALIZATION ===
    init() {
        document.addEventListener("DOMContentLoaded", () => {
            this.initializeModals();
            this.setupFilters();
            this.setupForms();
            this.setupCascadeDropdowns();
            this.setupKeyboardHandlers();
        });
    }

    initializeModals() {
        const modalContent = document.querySelector(
            "#core-edit-modal .bg-white"
        );
        if (modalContent) {
            modalContent.style.transform = "scale(0.95)";
            modalContent.style.opacity = "0.5";
            modalContent.style.transition = "all 0.15s ease-out";
        }
    }

    setupFilters() {
        const filterIds = ["tube-filter", "status-filter", "usage-filter"];
        const filters = {};

        filterIds.forEach((id) => {
            filters[id] = document.getElementById(id);
            if (filters[id]) {
                filters[id].addEventListener("change", () =>
                    this.applyFilters()
                );
            }
        });

        const clearButton = document.getElementById("clear-filters");
        if (clearButton) {
            clearButton.addEventListener("click", () => {
                Object.values(filters).forEach((filter) => {
                    if (filter) filter.value = "";
                });
                this.applyFilters();
            });
        }
    }

    setupForms() {
        // Core Edit Form
        const coreEditForm = document.getElementById("core-edit-form");
        if (coreEditForm) {
            coreEditForm.addEventListener("submit", (e) => {
                e.preventDefault();
                const formData = {
                    status: document.getElementById("edit-core-status").value,
                    usage: document.getElementById("edit-core-usage").value,
                    attenuation:
                        document.getElementById("edit-core-attenuation")
                            .value || null,
                    description:
                        document.getElementById("edit-core-description")
                            .value || null,
                };
                this.submitEdit(formData);
            });
        }

        // Join Form
        const joinForm = document.getElementById("join-core-form");
        if (joinForm) {
            joinForm.addEventListener("submit", async (e) => {
                e.preventDefault();

                const fieldMappings = {
                    "join-core-id": "source_core_id",
                    "target-core": "target_core_id",
                    "jc-selection": "joint_closure_id",
                    "connection-type": "connection_type",
                    "connection-loss": "connection_loss",
                    "connection-notes": "connection_notes",
                };

                const formData = {};
                Object.entries(fieldMappings).forEach(
                    ([elementId, fieldName]) => {
                        const element = document.getElementById(elementId);
                        if (element) formData[fieldName] = element.value;
                    }
                );

                try {
                    const data = await this.makeRequest(
                        "/connections",
                        "POST",
                        formData
                    );
                    if (data.success) {
                        this.showNotification(
                            "Connection created successfully!",
                            "success"
                        );
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        this.showNotification(
                            "Error: " + (data.message || "Unknown error"),
                            "error"
                        );
                    }
                } catch (error) {
                    console.error("Error:", error);
                    this.showNotification("Error creating connection", "error");
                }
            });
        }
    }

    setupKeyboardHandlers() {
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                const editModal = document.getElementById("core-edit-modal");
                const joinModal = document.getElementById("join-core-modal");

                if (!editModal.classList.contains("hidden")) {
                    this.closeModal("core-edit-modal");
                } else if (!joinModal.classList.contains("hidden")) {
                    this.closeModal("join-core-modal");
                }
            }
        });
    }
}

// Initialize Core Manager
const coreManager = new CoreManager();

// Global functions for backward compatibility
function openCoreEditModal(coreId) {
    coreManager.openEditModal(coreId);
}
function closeCoreEditModal() {
    coreManager.closeModal("core-edit-modal");
}
function joinCore(coreId) {
    document.getElementById("join-core-id").value = coreId;
    coreManager.loadJointClosures();
    coreManager.showModal("join-core-modal");
}
function closeJoinModal() {
    coreManager.closeModal("join-core-modal");
}
function disconnectCore(connectionId) {
    coreManager.disconnectCore(connectionId);
}
