document.addEventListener("DOMContentLoaded", function () {
    const totalTubesInput = document.getElementById("total_tubes");
    const totalCoresInput = document.getElementById("total_cores");
    const coresPerTubeDisplay = document.getElementById("cores-per-tube-display");
    const coreNumberingPreview = document.getElementById("core-numbering-preview");
    const numberingSummary = document.getElementById("numbering-summary");
    const form = document.getElementById("cable-form");

    // Check if required elements exist
    if (!totalTubesInput || !totalCoresInput || !coresPerTubeDisplay || !coreNumberingPreview || !numberingSummary || !form) {
        console.error("Required DOM elements not found. Make sure all elements with proper IDs exist in the HTML.");
        return;
    }

    // Get configuration from backend (passed via window.cableConfig)
    const MAX_TUBES = window.cableConfig?.maxTubes || 8;
    const MAX_CORES = window.cableConfig?.maxCores || 96;
    const MAX_CORES_PER_TUBE = window.cableConfig?.maxCoresPerTube || 12;

    // Flag to track if user has started interacting
    let hasUserInteracted = false;

    // Create alert container
    function createAlertContainer() {
        let alertContainer = document.getElementById("validation-alerts");
        if (!alertContainer) {
            alertContainer = document.createElement("div");
            alertContainer.id = "validation-alerts";
            alertContainer.className = "mb-4";

            // Insert after the header section
            const headerSection = document.querySelector(".mb-8");
            headerSection.parentNode.insertBefore(alertContainer, headerSection.nextSibling);
        }
        return alertContainer;
    }

    // Show alert message - only if user has interacted
    function showAlert(message, type = "error") {
        if (!hasUserInteracted) return; // Don't show alerts on initial load

        const alertContainer = createAlertContainer();
        const alertClass = type === "error" ? "bg-red-50 border-red-200 text-red-800" : "bg-yellow-50 border-yellow-200 text-yellow-800";
        const iconClass = type === "error" ? "text-red-400" : "text-yellow-400";

        alertContainer.innerHTML = `
            <div class="border ${alertClass} px-4 py-3 rounded-md flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${iconClass}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium">${type === "error" ? "Input Error" : "Warning"}</h3>
                    <div class="mt-1 text-sm">${message}</div>
                </div>
            </div>
        `;

        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertContainer.innerHTML = "";
        }, 5000);
    }

    // Hide alert
    function hideAlert() {
        const alertContainer = document.getElementById("validation-alerts");
        if (alertContainer) {
            alertContainer.innerHTML = "";
        }
    }

    // Validate tubes input
    function validateTubes(showAlerts = true) {
        const tubes = parseInt(totalTubesInput.value) || 0;

        if (tubes < 1) {
            if (showAlerts) showAlert("Number of tubes must be at least 1.");
            totalTubesInput.classList.add("border-red-500");
            return false;
        }

        if (tubes > MAX_TUBES) {
            if (showAlerts) showAlert(`Maximum number of tubes is ${MAX_TUBES}. Please enter a value between 1 and ${MAX_TUBES}.`);
            totalTubesInput.classList.add("border-red-500");
            totalTubesInput.value = MAX_TUBES;
            return false;
        }

        totalTubesInput.classList.remove("border-red-500");
        return true;
    }

    // Validate cores input
    function validateCores(showAlerts = true) {
        const totalCores = parseInt(totalCoresInput.value) || 0;
        const tubes = parseInt(totalTubesInput.value) || 1;

        if (totalCores < 1) {
            if (showAlerts) showAlert("Number of cores must be at least 1.");
            totalCoresInput.classList.add("border-red-500");
            return false;
        }

        if (totalCores > MAX_CORES) {
            if (showAlerts) showAlert(`Maximum number of cores is ${MAX_CORES}. Please enter a value between 1 and ${MAX_CORES}.`);
            totalCoresInput.classList.add("border-red-500");
            totalCoresInput.value = MAX_CORES;
            return false;
        }

        // Calculate cores per tube
        const baseCoresPerTube = Math.floor(totalCores / tubes);
        const extraCores = totalCores % tubes;
        const maxCoresPerTube = baseCoresPerTube + (extraCores > 0 ? 1 : 0);

        if (maxCoresPerTube > MAX_CORES_PER_TUBE) {
            if (showAlerts) showAlert(`With ${tubes} tubes and ${totalCores} cores, some tubes would have ${maxCoresPerTube} cores. Maximum cores per tube is ${MAX_CORES_PER_TUBE}. Either increase the number of tubes or decrease the total cores.`);
            totalCoresInput.classList.add("border-red-500");
            return false;
        }

        // Warning for uneven distribution - only show if user has interacted
        if (extraCores > 0 && showAlerts && hasUserInteracted) {
            const tubesWithExtra = extraCores;
            const tubesWithoutExtra = tubes - extraCores;
            showAlert(`Uneven distribution: ${tubesWithExtra} tube(s) will have ${baseCoresPerTube + 1} cores, ${tubesWithoutExtra} tube(s) will have ${baseCoresPerTube} cores.`, "warning");
        }

        totalCoresInput.classList.remove("border-red-500");
        return true;
    }

    // Enhanced core numbering preview with validation
    function updateCoreNumberingPreview(showAlerts = true) {
        // Check if elements exist before proceeding
        if (!coresPerTubeDisplay || !coreNumberingPreview || !numberingSummary) {
            return;
        }

        // First validate inputs
        const tubesValid = validateTubes(showAlerts);
        const coresValid = validateCores(showAlerts);

        if (!tubesValid || !coresValid) {
            coresPerTubeDisplay.textContent = hasUserInteracted ? "Please fix validation errors" : "Enter tube and core counts";
            coreNumberingPreview.innerHTML = "";
            numberingSummary.innerHTML = "";
            return;
        }

        // Hide alerts if validation passes
        if (hasUserInteracted) {
            hideAlert();
        }

        const tubes = parseInt(totalTubesInput.value) || 1;
        const totalCores = parseInt(totalCoresInput.value) || 0;

        if (totalCores === 0) {
            coresPerTubeDisplay.textContent = "Please enter total cores";
            coreNumberingPreview.innerHTML = "";
            numberingSummary.innerHTML = "";
            return;
        }

        // Calculate distribution
        const baseCoresPerTube = Math.floor(totalCores / tubes);
        const extraCores = totalCores % tubes;

        // Update distribution display
        let distributionText = `${baseCoresPerTube} cores per tube`;
        if (extraCores > 0) {
            distributionText += ` (${extraCores} tube${extraCores > 1 ? "s" : ""} have ${baseCoresPerTube + 1} cores)`;
        }
        coresPerTubeDisplay.textContent = distributionText;

        // Generate sequential core numbering preview
        let previewHtml = "";
        let currentCoreNumber = 1;
        let tubeData = [];
        const maxTubesToShow = 5;

        for (let tube = 1; tube <= tubes; tube++) {
            const coresInThisTube = baseCoresPerTube + (tube <= extraCores ? 1 : 0);
            const startCore = currentCoreNumber;
            const endCore = currentCoreNumber + coresInThisTube - 1;

            tubeData.push({
                tube: tube,
                cores: coresInThisTube,
                startCore: startCore,
                endCore: endCore,
            });

            if (tube <= maxTubesToShow) {
                previewHtml += `
                    <div class="flex justify-between items-center py-1 px-2 bg-white rounded text-sm">
                        <span><strong>Tube ${tube}:</strong> ${coresInThisTube} cores</span>
                        <span class="text-blue-600">Sequential: ${startCore} - ${endCore}</span>
                    </div>
                `;
            } else if (tube === maxTubesToShow + 1) {
                previewHtml += `
                    <div class="text-center py-2 text-gray-500 text-sm">
                        ... ${tubes - maxTubesToShow} more tubes ...
                    </div>
                `;
            }

            currentCoreNumber = endCore + 1;
        }

        // Show last tube if there are many tubes
        if (tubes > maxTubesToShow + 1) {
            const lastTube = tubeData[tubes - 1];
            previewHtml += `
                <div class="flex justify-between items-center py-1 px-2 bg-white rounded text-sm">
                    <span><strong>Tube ${lastTube.tube}:</strong> ${lastTube.cores} cores</span>
                    <span class="text-blue-600">Sequential: ${lastTube.startCore} - ${lastTube.endCore}</span>
                </div>
            `;
        }

        coreNumberingPreview.innerHTML = previewHtml;

        // Generate summary with validation status
        const summaryHtml = `
            <strong>Summary:</strong> Total ${totalCores} cores (numbered 1 to ${totalCores}) across ${tubes} tubes.<br>
            <strong>Status:</strong> <span class="text-green-600">✓ Valid configuration</span><br>
            <strong>Key benefit:</strong> Each core has a unique sequential number - no duplicates within the cable.
        `;
        numberingSummary.innerHTML = summaryHtml;

        // Add hidden input for core structure
        addCoreStructureToForm(tubeData);
    }

    function addCoreStructureToForm(tubeData) {
        const existingInput = document.getElementById("core_structure");
        if (existingInput) {
            existingInput.remove();
        }

        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "core_structure";
        hiddenInput.id = "core_structure";
        hiddenInput.value = JSON.stringify(tubeData);

        form.appendChild(hiddenInput);
    }

    // Form submission validation
    function validateForm(event) {
        // Always validate on form submission regardless of interaction flag
        const tubesValid = validateTubes(true);
        const coresValid = validateCores(true);

        if (!tubesValid || !coresValid) {
            event.preventDefault();
            showAlert("Please fix all validation errors before submitting the form.");
            return false;
        }

        return true;
    }

    // Mark user interaction and enable alerts
    function markUserInteraction() {
        hasUserInteracted = true;
    }

    // Event listeners with debouncing
    let tubesTimeout, coresTimeout;

    // Only add event listeners if elements exist
    if (totalTubesInput) {
        totalTubesInput.addEventListener("input", function() {
            markUserInteraction();
            clearTimeout(tubesTimeout);
            tubesTimeout = setTimeout(() => updateCoreNumberingPreview(true), 300);
        });

        // Real-time validation on blur (only after user interaction)
        totalTubesInput.addEventListener("blur", function() {
            if (hasUserInteracted) validateTubes(true);
        });

        // Mark interaction on focus
        totalTubesInput.addEventListener("focus", markUserInteraction);

        // Enforce max values on input
        totalTubesInput.addEventListener("input", function() {
            if (parseInt(this.value) > MAX_TUBES) {
                this.value = MAX_TUBES;
            }
        });
    }

    if (totalCoresInput) {
        totalCoresInput.addEventListener("input", function() {
            markUserInteraction();
            clearTimeout(coresTimeout);
            coresTimeout = setTimeout(() => updateCoreNumberingPreview(true), 300);
        });

        // Real-time validation on blur (only after user interaction)
        totalCoresInput.addEventListener("blur", function() {
            if (hasUserInteracted) validateCores(true);
        });

        // Mark interaction on focus
        totalCoresInput.addEventListener("focus", markUserInteraction);

        // Enforce max values on input
        totalCoresInput.addEventListener("input", function() {
            if (parseInt(this.value) > MAX_CORES) {
                this.value = MAX_CORES;
            }
        });
    }

    // Form validation on submit
    if (form) {
        form.addEventListener("submit", validateForm);
    }

    // Initial preview WITHOUT showing alerts
    updateCoreNumberingPreview(false);
});
