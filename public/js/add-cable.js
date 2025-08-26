document.addEventListener("DOMContentLoaded", function () {
    const totalTubesInput = document.getElementById("total_tubes");
    const totalCoresInput = document.getElementById("total_cores");
    const coresPerTubeDisplay = document.getElementById(
        "cores-per-tube-display"
    );
    const coreNumberingPreview = document.getElementById(
        "core-numbering-preview"
    );
    const numberingSummary = document.getElementById("numbering-summary");
    const form = document.getElementById("cable-form");

    function updateCoreNumberingPreview() {
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
            distributionText += ` (${extraCores} tube${
                extraCores > 1 ? "s" : ""
            } have ${baseCoresPerTube + 1} cores)`;
        }
        coresPerTubeDisplay.textContent = distributionText;

        // Generate sequential core numbering preview
        let previewHtml = "";
        let currentCoreNumber = 1;
        let tubeData = [];
        const maxTubesToShow = 5; // Show first 5 tubes in detail

        for (let tube = 1; tube <= tubes; tube++) {
            const coresInThisTube =
                baseCoresPerTube + (tube <= extraCores ? 1 : 0);
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

        // Generate summary
        const summaryHtml = `
            <strong>Summary:</strong> Total ${totalCores} cores (numbered 1 to ${totalCores}) across ${tubes} tubes.<br>
            <strong>Key benefit:</strong> Each core has a unique sequential number - no duplicates within the cable.
        `;
        numberingSummary.innerHTML = summaryHtml;

        // Add hidden input for core structure (for backend processing)
        addCoreStructureToForm(tubeData);
    }

    function addCoreStructureToForm(tubeData) {
        // Remove existing core structure input
        const existingInput = document.getElementById("core_structure");
        if (existingInput) {
            existingInput.remove();
        }

        // Create hidden input with core structure data
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "core_structure";
        hiddenInput.id = "core_structure";
        hiddenInput.value = JSON.stringify(tubeData);

        form.appendChild(hiddenInput);
    }

    // Event listeners
    totalTubesInput.addEventListener("input", updateCoreNumberingPreview);
    totalCoresInput.addEventListener("input", updateCoreNumberingPreview);

    // Initial preview
    updateCoreNumberingPreview();
});
