@extends('layouts.app')

@section('title', 'Edit Cable - ' . $cable->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Cable</h1>
            <p class="text-gray-600 mt-2">Update cable information</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('cables.show', $cable) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Cable Details
            </a>
            <a href="{{ route('cables.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back to Cables
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <form id="cable-form" method="POST" action="{{ route('cables.update', $cable) }}" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cable ID -->
            <div>
                <label for="cable_id" class="block text-sm font-medium text-gray-700 mb-2">Cable ID *</label>
                <input type="text" 
                       id="cable_id" 
                       name="cable_id" 
                       value="{{ old('cable_id', $cable->cable_id) }}" 
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
                       value="{{ old('name', $cable->name) }}" 
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
                        <option value="Bali" {{ old('region', $cable->region) === 'Bali' ? 'selected' : '' }}>Bali </option>
                        <option value="NTT" {{ old('region', $cable->region) === 'NTT' ? 'selected' : '' }}>NTT</option>
                        <option value="NTB" {{ old('region', $cable->region) === 'NTB' ? 'selected' : '' }}>Bandung</option>
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
                       value="{{ old('source_site', $cable->source_site) }}" 
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
                       value="{{ old('destination_site', $cable->destination_site) }}" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destination_site') border-red-500 @enderror"
                       placeholder="e.g., Surabaya Data Center">
                @error('destination_site')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cable Information (Read-only for edit) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Tubes</label>
                <input type="text" 
                       value="{{ $cable->total_tubes }}" 
                       disabled 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                <p class="mt-1 text-xs text-gray-500">Cannot be modified after creation</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Cores</label>
                <input type="text" 
                       value="{{ $cable->total_cores }}" 
                       disabled 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                <p class="mt-1 text-xs text-gray-500">Cannot be modified after creation</p>
            </div>

            <!-- OTDR Length -->
            <div>
                <label for="otdr_length" class="block text-sm font-medium text-gray-700 mb-2">OTDR Length (meters)</label>
                <input type="number" 
                       id="otdr_length" 
                       name="otdr_length" 
                       value="{{ old('otdr_length', $cable->otdr_length) }}" 
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
                    <option value="ok" {{ old('status', $cable->status) === 'ok' ? 'selected' : '' }}>OK</option>
                    <option value="not_ok" {{ old('status', $cable->status) === 'not_ok' ? 'selected' : '' }}>Not OK</option>
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
                    <option value="inactive" {{ old('usage', $cable->usage) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="active" {{ old('usage', $cable->usage) === 'active' ? 'selected' : '' }}>Active</option>
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
                      placeholder="Additional notes about this cable...">{{ old('description', $cable->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('cables.show', $cable) }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Update Cable
            </button>
        </div>
    </form>
</div>
@endsection