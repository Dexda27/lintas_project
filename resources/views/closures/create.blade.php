<!-- resources/views/closures/create.blade.php -->
@extends('layouts.app')

@section('title', 'Add New Joint Closure')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Joint Closure</h1>
            <p class="text-gray-600 mt-2">Create a new fiber optic joint closure</p>
        </div>
        <a href="{{ route('closures.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Back to Closures
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <form method="POST" action="{{ route('closures.store') }}" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Closure ID -->
            <div>
                <label for="closure_id" class="block text-sm font-medium text-gray-700 mb-2">Closure ID *</label>
                <input type="text" 
                       id="closure_id" 
                       name="closure_id" 
                       value="{{ old('closure_id') }}" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('closure_id') border-red-500 @enderror"
                       placeholder="e.g., JC-JKT-001">
                @error('closure_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Closure Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Closure Name *</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                       placeholder="e.g., Main Distribution Closure">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                <input type="text" 
                       id="location" 
                       name="location" 
                       value="{{ old('location') }}" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location') border-red-500 @enderror"
                       placeholder="e.g., Jl. Sudirman No. 123">
                @error('location')
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
                @endif
                @error('region')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Capacity -->
            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity *</label>
                <input type="number" 
                       id="capacity" 
                       name="capacity" 
                       value="{{ old('capacity', 24) }}" 
                       required 
                       min="1" 
                       max="1000"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('capacity') border-red-500 @enderror"
                       placeholder="Maximum number of connections">
                @error('capacity')
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

            <!-- Latitude -->
            <div>
                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                <input type="number" 
                       id="latitude" 
                       name="latitude" 
                       value="{{ old('latitude') }}" 
                       step="0.000001"
                       min="-90" 
                       max="90"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('latitude') border-red-500 @enderror"
                       placeholder="e.g., -6.200000">
                @error('latitude')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Longitude -->
            <div>
                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                <input type="number" 
                       id="longitude" 
                       name="longitude" 
                       value="{{ old('longitude') }}" 
                       step="0.000001"
                       min="-180" 
                       max="180"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('longitude') border-red-500 @enderror"
                       placeholder="e.g., 106.816666">
                @error('longitude')
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
                      placeholder="Additional notes about this closure...">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('closures.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Create Closure
            </button>
        </div>
    </form>
</div>
@endsection