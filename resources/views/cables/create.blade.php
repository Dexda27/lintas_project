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
                        <option value="Jakarta" {{ old('region') === 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="Surabaya" {{ old('region') === 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                        <option value="Bandung" {{ old('region') === 'Bandung' ? 'selected' : '' }}>Bandung</option>
                        <option value="Medan" {{ old('region') === 'Medan' ? 'selected' : '' }}>Medan</option>
                    </select>
                    @error('region')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <!-- Source Site -->
            <div>
                <label for="source_site_id" class="block text-sm font-medium text-gray-700 mb-2">Source Site *</label>
                <select id="source_site_id" 
                        name="source_site_id" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('source_site_id') border-red-500 @enderror">
                    <option value="">Select Source Site</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ old('source_site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }} ({{ $site->region }})
                        </option>
                    @endforeach
                </select>
                @error('source_site_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Destination Site -->
            <div>
                <label for="destination_site_id" class="block text-sm font-medium text-gray-700 mb-2">Destination Site *</label>
                <select id="destination_site_id" 
                        name="destination_site_id" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destination_site_id') border-red-500 @enderror">
                    <option value="">Select Destination Site</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ old('destination_site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }} ({{ $site->region }})
                        </option>
                    @endforeach
                </select>
                @error('destination_site_id')
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

            <!-- Display Cores Per Tube -->
            <div class="col-span-2 mt-4 text-gray-700 text-sm">
                <strong>Distribution:</strong> <span id="cores-per-tube-display">-</span>
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

    function updateCoresPerTube() {
        const tubes = parseInt(totalTubesInput.value) || 1;
        const cores = parseInt(totalCoresInput.value) || 0;
        const coresPerTube = Math.floor(cores / tubes);
        const remainder = cores % tubes;
        
        let displayText = `${coresPerTube} cores per tube`;
        if (remainder > 0) {
            displayText += ` (${remainder} extra cores)`;
        }
        
        coresPerTubeDisplay.textContent = displayText;
    }

    totalTubesInput.addEventListener('input', updateCoresPerTube);
    totalCoresInput.addEventListener('input', updateCoresPerTube);
    
    updateCoresPerTube();
});
</script>
@endpush
