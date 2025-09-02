document.addEventListener("DOMContentLoaded", function () {
    const totalTubesInput = document.getElementById("total_tubes");
    const totalCoresInput = document.getElementById("total_cores");
    const form = document.getElementById("cable-form");

    // Check if required elements exist
    if (!totalTubesInput || !totalCoresInput || !form) {
        console.error("Required DOM elements not found. Make sure all elements with proper IDs exist in the HTML.");
        return;
    }

    // Get configuration from backend (passed via window.cableConfig)
    const MAX_TUBES = window.cableConfig?.maxTubes || 8;
    const MAX_CORES = window.cableConfig?.maxCores || 96;
    const MAX_CORES_PER_TUBE = window.cableConfig?.maxCoresPerTube || 12;

    // Visual feedback system - no alerts, only field styling
    const visualFeedback = {
        setFieldState(field, isValid, message = "") {
            if (isValid) {
                field.classList.remove("border-red-500", "bg-red-50");
                field.classList.add("border-gray-300");
                this.clearFieldMessage(field);
            } else {
                field.classList.remove("border-gray-300");
                field.classList.add("border-red-500", "bg-red-50");
                if (message) {
                    this.showFieldMessage(field, message);
                }
            }
        },

        showFieldMessage(field, message) {
            // Remove existing message
            this.clearFieldMessage(field);

            // Create error message element
            const errorElement = document.createElement("p");
            errorElement.className = "mt-1 text-sm text-red-600 field-error-message";
            errorElement.textContent = message;

            // Insert after the field
            field.parentNode.appendChild(errorElement);
        },

        clearFieldMessage(field) {
            const existingError = field.parentNode.querySelector(".field-error-message");
            if (existingError) {
                existingError.remove();
            }
        },

        clearAllMessages() {
            document.querySelectorAll(".field-error-message").forEach(el => el.remove());
        }
    };

    // Enhanced validation functions with visual feedback only
    function validateTubes(showFeedback = true) {
        const tubes = parseInt(totalTubesInput.value);
        const inputValue = totalTubesInput.value.trim();

        // Handle empty input
        if (inputValue === "" || isNaN(tubes)) {
            if (showFeedback && inputValue !== "") {
                visualFeedback.setFieldState(totalTubesInput, false, "Please enter a valid number for tubes.");
            } else if (inputValue === "") {
                visualFeedback.setFieldState(totalTubesInput, true);
            }
            return false;
        }

        // Validate minimum value
        if (tubes < 1) {
            if (showFeedback) {
                visualFeedback.setFieldState(totalTubesInput, false, "Number of tubes must be at least 1.");
            }
            return false;
        }

        // Validate maximum value
        if (tubes > MAX_TUBES) {
            if (showFeedback) {
                visualFeedback.setFieldState(totalTubesInput, false, `Maximum number of tubes is ${MAX_TUBES}.`);
                // Auto-correct the value
                setTimeout(() => {
                    totalTubesInput.value = MAX_TUBES;
                    validateTubes(true);
                }, 1000);
            }
            return false;
        }

        // Validation passed
        visualFeedback.setFieldState(totalTubesInput, true);
        return true;
    }

    function validateCores(showFeedback = true) {
        const totalCores = parseInt(totalCoresInput.value);
        const tubes = parseInt(totalTubesInput.value) || 1;
        const inputValue = totalCoresInput.value.trim();

        // Handle empty input
        if (inputValue === "" || isNaN(totalCores)) {
            if (showFeedback && inputValue !== "") {
                visualFeedback.setFieldState(totalCoresInput, false, "Please enter a valid number for cores.");
            } else if (inputValue === "") {
                visualFeedback.setFieldState(totalCoresInput, true);
            }
            return false;
        }

        // Validate minimum value
        if (totalCores < 1) {
            if (showFeedback) {
                visualFeedback.setFieldState(totalCoresInput, false, "Number of cores must be at least 1.");
            }
            return false;
        }

        // Validate maximum value
        if (totalCores > MAX_CORES) {
            if (showFeedback) {
                visualFeedback.setFieldState(totalCoresInput, false, `Maximum number of cores is ${MAX_CORES}.`);
                // Auto-correct the value
                setTimeout(() => {
                    totalCoresInput.value = MAX_CORES;
                    validateCores(true);
                }, 1000);
            }
            return false;
        }

        // Calculate cores per tube for distribution validation
        const baseCoresPerTube = Math.floor(totalCores / tubes);
        const extraCores = totalCores % tubes;
        const maxCoresPerTube = baseCoresPerTube + (extraCores > 0 ? 1 : 0);

        // Validate cores per tube limit
        if (maxCoresPerTube > MAX_CORES_PER_TUBE) {
            if (showFeedback) {
                const suggestedTubes = Math.ceil(totalCores / MAX_CORES_PER_TUBE);
                visualFeedback.setFieldState(totalCoresInput, false,
                    `Too many cores per tube (${maxCoresPerTube}). Maximum is ${MAX_CORES_PER_TUBE}. Try ${suggestedTubes} tubes.`);
            }
            return false;
        }

        // Validation passed
        visualFeedback.setFieldState(totalCoresInput, true);
        return true;
    }

    // Event listeners with enhanced immediate validation
    if (totalTubesInput) {
        // Immediate validation on input
        totalTubesInput.addEventListener("input", function() {
            // Validate immediately for real-time feedback
            validateTubes(true);
        });

        // Additional validation on blur
        totalTubesInput.addEventListener("blur", function() {
            validateTubes(true);
        });

        // Prevent non-numeric input
        totalTubesInput.addEventListener("keypress", function(e) {
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }

    if (totalCoresInput) {
        // Immediate validation on input
        totalCoresInput.addEventListener("input", function() {
            // Validate immediately for real-time feedback
            validateCores(true);
        });

        // Additional validation on blur
        totalCoresInput.addEventListener("blur", function() {
            validateCores(true);
        });

        // Prevent non-numeric input
        totalCoresInput.addEventListener("keypress", function(e) {
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }

    // Form submission validation
    function validateForm(event) {
        const tubesValid = validateTubes(true);
        const coresValid = validateCores(true);

        if (!tubesValid || !coresValid) {
            event.preventDefault();

            // Focus on first invalid field
            if (!tubesValid) {
                totalTubesInput.focus();
            } else if (!coresValid) {
                totalCoresInput.focus();
            }

            return false;
        }

        return true;
    }

    // Form validation on submit
    if (form) {
        form.addEventListener("submit", validateForm);
    }

    // Add CSS for visual feedback
    const style = document.createElement('style');
    style.textContent = `
        .border-red-500 {
            border-color: #ef4444 !important;
        }

        .bg-red-50 {
            background-color: #fef2f2 !important;
        }

        .field-error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        input:focus.border-red-500 {
            outline: 2px solid #fecaca;
            outline-offset: 2px;
        }
    `;
    document.head.appendChild(style);
});
