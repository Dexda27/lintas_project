// resources/js/app.js
import "./bootstrap";

// Core Management Functions
window.coreManager = {
    // Edit core modal management
    editCore: function (coreId, currentData = {}) {
        const modal = document.getElementById("edit-core-modal");
        if (!modal) return;

        // Populate form with current data
        document.getElementById("core-id").value = coreId;
        document.getElementById("core-status").value =
            currentData.status || "ok";
        document.getElementById("core-usage").value =
            currentData.usage || "inactive";
        document.getElementById("core-attenuation").value =
            currentData.attenuation || "";
        document.getElementById("core-description").value =
            currentData.description || "";

        modal.classList.remove("hidden");
    },

    closeEditModal: function () {
        const modal = document.getElementById("edit-core-modal");
        if (modal) {
            modal.classList.add("hidden");
        }
    },

    // Update core via AJAX
    updateCore: function (coreId, formData) {
        return fetch(`/cores/${coreId}`, {
            method: "PUT",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        });
    },

    // Disconnect core connection
    disconnectCore: function (connectionId) {
        if (
            !confirm(
                "Are you sure you want to disconnect this core connection?"
            )
        ) {
            return Promise.resolve(false);
        }

        return fetch(`/connections/${connectionId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                Accept: "application/json",
            },
        });
    },
};

// Filter Management
window.filterManager = {
    // Apply filters to core cards
    applyFilters: function (filters) {
        const { tube, status, usage, search } = filters;
        const coreCards = document.querySelectorAll(".core-card");
        const tubeSections = document.querySelectorAll(".tube-section");

        // Hide all tube sections first
        tubeSections.forEach((section) => {
            section.style.display = "none";
        });

        let visibleCards = 0;
        const visibleTubes = new Set();

        coreCards.forEach((card) => {
            const cardTube = card.dataset.tube;
            const cardStatus = card.dataset.status;
            const cardUsage = card.dataset.usage;
            const cardCore = card.dataset.core;
            const cardDescription = (
                card.dataset.description || ""
            ).toLowerCase();

            let shouldShow = true;

            // Apply filters
            if (tube && cardTube !== tube) shouldShow = false;
            if (status && cardStatus !== status) shouldShow = false;
            if (usage && cardUsage !== usage) shouldShow = false;
            if (
                search &&
                !cardCore.includes(search) &&
                !cardDescription.includes(search.toLowerCase())
            ) {
                shouldShow = false;
            }

            if (shouldShow) {
                card.style.display = "block";
                visibleCards++;
                visibleTubes.add(cardTube);
            } else {
                card.style.display = "none";
            }
        });

        // Show tube sections that have visible cards
        tubeSections.forEach((section) => {
            const sectionTube = section.dataset.tube;
            if (visibleTubes.has(sectionTube)) {
                section.style.display = "block";
            }
        });

        return visibleCards;
    },

    // Clear all filters
    clearFilters: function () {
        const filters = [
            "tube-filter",
            "status-filter",
            "usage-filter",
            "search-core",
        ];
        filters.forEach((filterId) => {
            const element = document.getElementById(filterId);
            if (element) {
                element.value = "";
            }
        });

        this.applyFilters({});
    },

    // Initialize filter event listeners
    initialize: function () {
        const tubeFilter = document.getElementById("tube-filter");
        const statusFilter = document.getElementById("status-filter");
        const usageFilter = document.getElementById("usage-filter");
        const searchCore = document.getElementById("search-core");
        const clearFilters = document.getElementById("clear-filters");

        const applyCurrentFilters = () => {
            const filters = {
                tube: tubeFilter?.value || "",
                status: statusFilter?.value || "",
                usage: usageFilter?.value || "",
                search: searchCore?.value || "",
            };
            this.applyFilters(filters);
        };

        // Add event listeners
        if (tubeFilter)
            tubeFilter.addEventListener("change", applyCurrentFilters);
        if (statusFilter)
            statusFilter.addEventListener("change", applyCurrentFilters);
        if (usageFilter)
            usageFilter.addEventListener("change", applyCurrentFilters);
        if (searchCore)
            searchCore.addEventListener(
                "input",
                debounce(applyCurrentFilters, 300)
            );
        if (clearFilters)
            clearFilters.addEventListener("click", () => this.clearFilters());
    },
};

// Connection Management
window.connectionManager = {
    // Show connect cores modal
    showConnectModal: function () {
        const modal = document.getElementById("connect-modal");
        if (modal) {
            modal.classList.remove("hidden");
        }
    },

    // Close connect cores modal
    closeConnectModal: function () {
        const modal = document.getElementById("connect-modal");
        if (modal) {
            modal.classList.add("hidden");
            const form = document.getElementById("connect-form");
            if (form) form.reset();
        }
    },

    // Validate core selection (prevent same cable selection)
    validateCoreSelection: function () {
        const coreASelect = document.getElementById("core_a_id");
        const coreBSelect = document.getElementById("core_b_id");

        if (!coreASelect || !coreBSelect) return;

        const updateCoreOptions = () => {
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
        };

        coreASelect.addEventListener("change", updateCoreOptions);
        coreBSelect.addEventListener("change", updateCoreOptions);
    },

    // Connect two cores
    connectCores: function (closureId, formData) {
        return fetch(`/closures/${closureId}/connect`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        });
    },
};

// Notification System
window.notificationManager = {
    show: function (message, type = "info", duration = 5000) {
        const notification = document.createElement("div");
        notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 ${this.getTypeClasses(
            type
        )}`;

        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${this.getIcon(type)}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="inline-flex text-current hover:opacity-75" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);

        return notification;
    },

    getTypeClasses: function (type) {
        const classes = {
            success: "bg-green-100 text-green-800 border border-green-200",
            error: "bg-red-100 text-red-800 border border-red-200",
            warning: "bg-yellow-100 text-yellow-800 border border-yellow-200",
            info: "bg-blue-100 text-blue-800 border border-blue-200",
        };
        return classes[type] || classes.info;
    },

    getIcon: function (type) {
        const icons = {
            success: `<svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>`,
            error: `<svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>`,
            warning: `<svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>`,
            info: `<svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                   </svg>`,
        };
        return icons[type] || icons.info;
    },
};

// Data Validation
window.dataValidator = {
    validateCableForm: function (formData) {
        const errors = [];

        if (!formData.cable_id || formData.cable_id.trim() === "") {
            errors.push("Cable ID is required");
        }

        if (!formData.name || formData.name.trim() === "") {
            errors.push("Cable name is required");
        }

        if (!formData.total_tubes || formData.total_tubes < 1) {
            errors.push("Total tubes must be at least 1");
        }

        if (!formData.total_cores || formData.total_cores < 1) {
            errors.push("Total cores must be at least 1");
        }

        if (formData.source_site_id === formData.destination_site_id) {
            errors.push("Source and destination sites must be different");
        }

        return errors;
    },

    validateClosureForm: function (formData) {
        const errors = [];

        if (!formData.closure_id || formData.closure_id.trim() === "") {
            errors.push("Closure ID is required");
        }

        if (!formData.name || formData.name.trim() === "") {
            errors.push("Closure name is required");
        }

        if (!formData.location || formData.location.trim() === "") {
            errors.push("Location is required");
        }

        if (!formData.capacity || formData.capacity < 1) {
            errors.push("Capacity must be at least 1");
        }

        if (
            formData.latitude &&
            (formData.latitude < -90 || formData.latitude > 90)
        ) {
            errors.push("Latitude must be between -90 and 90");
        }

        if (
            formData.longitude &&
            (formData.longitude < -180 || formData.longitude > 180)
        ) {
            errors.push("Longitude must be between -180 and 180");
        }

        return errors;
    },
};

// Utility Functions
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

function formatNumber(num, decimals = 0) {
    return new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(num);
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
}

// Global functions for backward compatibility
window.editCore = function (coreId, currentData = {}) {
    window.coreManager.editCore(coreId, currentData);
};

window.closeEditModal = function () {
    window.coreManager.closeEditModal();
};

window.disconnectCore = function (connectionId) {
    window.coreManager
        .disconnectCore(connectionId)
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                window.notificationManager.show(
                    "Core disconnected successfully",
                    "success"
                );
                setTimeout(() => location.reload(), 1000);
            } else {
                window.notificationManager.show(
                    data.message || "Error disconnecting core",
                    "error"
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            window.notificationManager.show(
                "Error disconnecting core",
                "error"
            );
        });
};

window.showConnectModal = function () {
    window.connectionManager.showConnectModal();
};

window.closeConnectModal = function () {
    window.connectionManager.closeConnectModal();
};

window.disconnectConnection = function (connectionId) {
    window.coreManager
        .disconnectCore(connectionId)
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                window.notificationManager.show(
                    "Connection disconnected successfully",
                    "success"
                );
                setTimeout(() => location.reload(), 1000);
            } else {
                window.notificationManager.show(
                    data.message || "Error disconnecting connection",
                    "error"
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            window.notificationManager.show(
                "Error disconnecting connection",
                "error"
            );
        });
};

// DOM Ready Event Listeners
document.addEventListener("DOMContentLoaded", function () {
    // Initialize filter manager
    window.filterManager.initialize();

    // Initialize connection manager validation
    window.connectionManager.validateCoreSelection();

    // Handle edit core form submission
    const editCoreForm = document.getElementById("edit-core-form");
    if (editCoreForm) {
        editCoreForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const coreId = document.getElementById("core-id").value;
            const formData = {
                status: document.getElementById("core-status").value,
                usage: document.getElementById("core-usage").value,
                attenuation: document.getElementById("core-attenuation").value,
                description: document.getElementById("core-description").value,
            };

            window.coreManager
                .updateCore(coreId, formData)
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        window.notificationManager.show(
                            "Core updated successfully",
                            "success"
                        );
                        window.coreManager.closeEditModal();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        window.notificationManager.show(
                            data.message || "Error updating core",
                            "error"
                        );
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    window.notificationManager.show(
                        "Error updating core",
                        "error"
                    );
                });
        });
    }

    // Handle connect form submission
    const connectForm = document.getElementById("connect-form");
    if (connectForm) {
        connectForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const closureId = this.action.split("/").slice(-2)[0]; // Extract closure ID from action URL

            // Convert FormData to JSON
            const jsonData = {};
            for (let [key, value] of formData.entries()) {
                jsonData[key] = value;
            }

            // Validate cores are from different cables
            const coreASelect = document.getElementById("core_a_id");
            const coreBSelect = document.getElementById("core_b_id");
            const cableA =
                coreASelect.options[coreASelect.selectedIndex]?.dataset.cable;
            const cableB =
                coreBSelect.options[coreBSelect.selectedIndex]?.dataset.cable;

            if (cableA === cableB) {
                window.notificationManager.show(
                    "Cannot connect cores from the same cable",
                    "error"
                );
                return;
            }

            // Submit the form normally (not AJAX) to handle validation properly
            this.submit();
        });
    }

    // Handle modal click outside to close
    document.addEventListener("click", function (e) {
        if (
            e.target.classList.contains("fixed") &&
            e.target.classList.contains("inset-0")
        ) {
            const modals = ["edit-core-modal", "connect-modal"];
            modals.forEach((modalId) => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains("hidden")) {
                    modal.classList.add("hidden");
                }
            });
        }
    });

    // Handle ESC key to close modals
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            const modals = ["edit-core-modal", "connect-modal"];
            modals.forEach((modalId) => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains("hidden")) {
                    modal.classList.add("hidden");
                }
            });
        }
    });

    // Initialize tooltips (if using any tooltip library)
    // Example: initializeTooltips();

    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll(
        ".bg-green-200, .bg-red-200, .bg-yellow-200"
    );
    flashMessages.forEach((message) => {
        setTimeout(() => {
            message.style.transition = "opacity 0.5s";
            message.style.opacity = "0";
            setTimeout(() => message.remove(), 500);
        }, 5000);
    });
});

// Export for use in other modules if needed
export { debounce, formatNumber, formatBytes };
