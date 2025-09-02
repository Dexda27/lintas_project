@extends('layouts.app')

@section('title', 'Add New Cable')

@push('scripts')
<script>
    // Pass configuration from backend to frontend
    window.cableConfig = {
        maxTubes: {{ $config['max_tubes'] ?? 8 }},
        maxCores: {{ $config['max_cores'] ?? 96 }},
        maxCoresPerTube: {{ $config['max_cores_per_tube'] ?? 12 }}
    };
</script>
<script src="{{ asset('js/add-cable.js') }}"></script>
@endpush

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Cable</h1>
            <p class="text-gray-600 mt-2">Create a new fiber optic cable</p>
        </div>
        <a href="{{ route('cables.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Cables
        </a>
    </div>
</div>

<!-- Dynamic Alert Container (will be populated by JavaScript) -->
<div id="validation-alerts" class="mb-4"></div>

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
                <label for="total_tubes" class="block text-sm font-medium text-gray-700 mb-2">
                    Total Tubes *
                    <span class="text-gray-500 font-normal">(Max: {{ $config['max_tubes'] ?? 8 }})</span>
                </label>
                <input type="number"
                       id="total_tubes"
                       name="total_tubes"
                       value="{{ old('total_tubes') }}"
                       min="1"
                       max="{{ $config['max_tubes'] ?? 8 }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_tubes') border-red-500 @enderror">
                @error('total_tubes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Cores -->
            <div>
                <label for="total_cores" class="block text-sm font-medium text-gray-700 mb-2">
                    Total Cores *
                    <span class="text-gray-500 font-normal">(Max: {{ $config['max_cores'] ?? 96 }})</span>
                </label>
                <input type="number"
                       id="total_cores"
                       name="total_cores"
                       value="{{ old('total_cores') }}"
                       min="1"
                       max="{{ $config['max_cores'] ?? 96 }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_cores') border-red-500 @enderror">
                @error('total_cores')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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

        <!-- Core Distribution Preview Section -->
        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Core Distribution Preview</h3>

            <!-- Cores per tube display -->
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Distribution:</p>
                <p id="cores-per-tube-display" class="text-gray-600">Enter tube and core counts</p>
            </div>

            <!-- Core numbering preview -->
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Core Numbering Preview:</p>
                <div id="core-numbering-preview" class="bg-gray-100 rounded-md p-3 space-y-1 min-h-[60px]">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Numbering summary -->
            <div id="numbering-summary" class="text-sm text-gray-600">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('cables.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 flex items-center gap-1">
                <i data-lucide="x" class="w-4 h-4"></i> Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-1">
                <i data-lucide="plus" class="w-4 h-4"></i> Create Cable
            </button>
        </div>
    </form>
</div>

@if(session('warning'))
    <div class="fixed bottom-4 right-4 bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-md shadow-lg max-w-md">
        <div class="flex items-center space-x-2">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            <span class="text-sm">{{ session('warning') }}</span>
        </div>
    </div>
@endif

@endsection
