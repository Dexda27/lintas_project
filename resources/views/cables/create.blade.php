@extends('layouts.app')

@section('title', 'Add New Cable')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Cable</h1>
            <p class="text-gray-600 mt-2">Create a new fiber optic cable</p>
        </div>
        <a href="{{ route('cables.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Back to Cables
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <form id="cable-form" method="POST" action="{{ route('cables.store') }}" class="p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cable ID -->
            <div>
                <label for="cable_id" class="block text-sm font-medium text-gray-700 mb-2">Cable ID *</label>
                <input type="text"
                       id="cable_id"
                       name="cable_id"
                       value="{{ old('cable_id') }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cable_id') border-red-500 @enderror"
                       placeholder="e.g., CBL-JKT-SBY-001">
                @error('cable_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cable Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Cable Name *</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                       placeholder="e.g., Jakarta - Surabaya Backbone">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Region -->
            <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-2">Region *</label>
                @if(auth()->user()->isAdminRegion())
                    <input type="text"
                           value="{{ auth()->user()->region }}"
                           disabled
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                    <input type="hidden" name="region" value="{{ auth()->user()->region }}">
                @else
                    <select id="region"
                            name="region"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('region') border-red-500 @enderror">
                        <option value="">Select Region</option>
                        <option value="Bali" {{ old('region') === 'Bali' ? 'selected' : '' }}>Bali</option>
                        <option value="NTT" {{ old('region') === 'NTT' ? 'selected' : '' }}>NTT</option>
                        <option value="NTB" {{ old('region') === 'NTB' ? 'selected' : '' }}>NTB</option>
                    </select>
                    @error('region')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <!-- Source Site -->
            <div>
                <label for="source_site" class="block text-sm font-medium text-gray-700 mb-2">Source Site *</label>
                <input type="text"
                       id="source_site"
                       name="source_site"
                       value="{{ old('source_site') }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('source_site') border-red-500 @enderror"
                       placeholder="e.g., Jakarta Data Center">
                @error('source_site')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Destination Site -->
            <div>
                <label for="destination_site" class="block text-sm font-medium text-gray-700 mb-2">Destination Site *</label>
                <input type="text"
                       id="destination_site"
                       name="destination_site"
                       value="{{ old('destination_site') }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destination_site') border-red-500 @enderror"
                       placeholder="e.g., Surabaya Data Center">
                @error('destination_site')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Tubes -->
            <div>
                <label for="total_tubes" class="block text-sm font-medium text-gray-700 mb-2">Total Tubes *</label>
                <input type="number"
                       id="total_tubes"
                       name="total_tubes"
                       value="{{ old('total_tubes') }}"
                       min="1"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Total Cores -->
            <div>
                <label for="total_cores" class="block text-sm font-medium text-gray-700 mb-2">Total Cores *</label>
                <input type="number"
                       id="total_cores"
                       name="total_cores"
                       value="{{ old('total_cores') }}"
                       min="1"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- OTDR Length -->
            <div>
                <label for="otdr_length" class="block text-sm font-medium text-gray-700 mb-2">OTDR Length (meters)</label>
                <input type="number"
                       id="otdr_length"
                       name="otdr_length"
                       value="{{ old('otdr_length') }}"
                       step="0.01"
                       min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('otdr_length') border-red-500 @enderror"
                       placeholder="e.g., 1250.50">
                @error('otdr_length')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select id="status"
                        name="status"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="ok" {{ old('status', 'ok') === 'ok' ? 'selected' : '' }}>OK</option>
                    <option value="not_ok" {{ old('status') === 'not_ok' ? 'selected' : '' }}>Not OK</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Usage -->
            <div>
                <label for="usage" class="block text-sm font-medium text-gray-700 mb-2">Usage *</label>
                <select id="usage"
                        name="usage"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('usage') border-red-500 @enderror">
                    <option value="inactive" {{ old('usage', 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="active" {{ old('usage') === 'active' ? 'selected' : '' }}>Active</option>
                </select>
                @error('usage')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Enhanced Core Distribution Preview -->
        <div class="col-span-2 mt-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sequential Core Numbering Preview</h3>

                <div id="distribution-summary" class="mb-4 text-sm text-gray-700">
                    <strong>Distribution:</strong> <span id="cores-per-tube-display">-</span>
                </div>

                <div id="core-numbering-preview" class="space-y-2 max-h-60 overflow-y-auto">
                    <!-- Core numbering preview will be populated by JavaScript -->
                </div>

                <div id="numbering-summary" class="mt-3 p-3 bg-blue-100 rounded text-sm text-blue-800">
                    <!-- Summary will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description"
                      name="description"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                      placeholder="Additional notes about this cable...">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('cables.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Create Cable
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalTubesInput = document.getElementById('total_tubes');
    const totalCoresInput = document.getElementById('total_cores');
    const coresPerTubeDisplay = document.getElementById('cores-per-tube-display');
    const coreNumberingPreview = document.getElementById('core-numbering-preview');
    const numberingSummary = document.getElementById('numbering-summary');
    const form = document.getElementById('cable-form');

    function updateCoreNumberingPreview() {
        const tubes = parseInt(totalTubesInput.value) || 1;
        const totalCores = parseInt(totalCoresInput.value) || 0;

        if (totalCores === 0) {
            coresPerTubeDisplay.textContent = 'Please enter total cores';
            coreNumberingPreview.innerHTML = '';
            numberingSummary.innerHTML = '';
            return;
        }

        // Calculate distribution
        const baseCoresPerTube = Math.floor(totalCores / tubes);
        const extraCores = totalCores % tubes;

        // Update distribution display
        let distributionText = `${baseCoresPerTube} cores per tube`;
        if (extraCores > 0) {
            distributionText += ` (${extraCores} tube${extraCores > 1 ? 's' : ''} have ${baseCoresPerTube + 1} cores)`;
        }
        coresPerTubeDisplay.textContent = distributionText;

        // Generate sequential core numbering preview
        let previewHtml = '';
        let currentCoreNumber = 1;
        let tubeData = [];
        const maxTubesToShow = 5; // Show first 5 tubes in detail

        for (let tube = 1; tube <= tubes; tube++) {
            const coresInThisTube = baseCoresPerTube + (tube <= extraCores ? 1 : 0);
            const startCore = currentCoreNumber;
            const endCore = currentCoreNumber + coresInThisTube - 1;

            tubeData.push({
                tube: tube,
                cores: coresInThisTube,
                startCore: startCore,
                endCore: endCore
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
        const existingInput = document.getElementById('core_structure');
        if (existingInput) {
            existingInput.remove();
        }

        // Create hidden input with core structure data
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'core_structure';
        hiddenInput.id = 'core_structure';
        hiddenInput.value = JSON.stringify(tubeData);

        form.appendChild(hiddenInput);
    }

    // Event listeners
    totalTubesInput.addEventListener('input', updateCoreNumberingPreview);
    totalCoresInput.addEventListener('input', updateCoreNumberingPreview);

    // Initial preview
    updateCoreNumberingPreview();
});
</script>
@endpush
