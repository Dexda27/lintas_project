<!-- resources/views/closures/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Joint Closure')

@push('scripts')
<script src="{{ asset('js/jc-edit.js') }}"></script>
@endpush

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Joint Closure</h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-base">Update joint closure information</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('closures.show', $closure) }}" class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm sm:text-base text-center">
                <span class="hidden sm:inline">View Details</span>
                <span class="sm:hidden">Details</span>
            </a>
            <a href="{{ route('closures.index') }}" class="bg-gray-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-sm sm:text-base text-center">
                <span class="hidden sm:inline">Back to List</span>
                <span class="sm:hidden">Back</span>
            </a>
        </div>
    </div>
</div>

<!-- Edit Form -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Closure Information</h2>
    </div>

    <form method="POST" action="{{ route('closures.update', $closure) }}" class="p-4 sm:p-6 space-y-4 sm:space-y-6"
          data-used-capacity="{{ $closure->used_capacity }}"
          data-original-closure-id="{{ $closure->closure_id }}"
          data-user-region="{{ Auth::user()->region }}"
          data-is-admin-region="{{ Auth::user()->isAdminRegion() ? 'true' : 'false' }}">
        @csrf
        @method('PUT')

        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Basic Information Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Closure ID -->
            <div>
                <label for="closure_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Closure ID <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="closure_id"
                    name="closure_id"
                    value="{{ old('closure_id', $closure->closure_id) }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('closure_id') border-red-500 @enderror"
                    placeholder="Enter unique closure ID">
                @error('closure_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Closure Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $closure->name) }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="Enter closure name">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div class="lg:col-span-2">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Location <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="location"
                    name="location"
                    value="{{ old('location', $closure->location) }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror"
                    placeholder="Enter closure location">
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Region -->
            <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-2">
                    Region <span class="text-red-500">*</span>
                </label>
                <select
                    id="region"
                    name="region"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('region') border-red-500 @enderror">
                    <option value="">Select Region</option>
                    @php
                        $regions = ['Bali', 'NTT', 'NTB'];
                        $userRegion = Auth::user()->region;
                        $isAdminRegion = Auth::user()->isAdminRegion();
                    @endphp
                    @foreach($regions as $region)
                        @if(!$isAdminRegion || $region == $userRegion)
                            <option value="{{ $region }}" {{ old('region', $closure->region) == $region ? 'selected' : '' }}>
                                {{ $region }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('region')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                    <option value="">Select Status</option>
                    <option value="ok" {{ old('status', $closure->status) == 'ok' ? 'selected' : '' }}>OK</option>
                    <option value="not_ok" {{ old('status', $closure->status) == 'not_ok' ? 'selected' : '' }}>Not OK</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Latitude -->
            <div>
                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                    Latitude
                </label>
                <input
                    type="number"
                    id="latitude"
                    name="latitude"
                    value="{{ old('latitude', $closure->latitude) }}"
                    step="0.000001"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('latitude') border-red-500 @enderror"
                    placeholder="e.g., -6.200000">
                @error('latitude')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Longitude -->
            <div>
                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                    Longitude
                </label>
                <input
                    type="number"
                    id="longitude"
                    name="longitude"
                    value="{{ old('longitude', $closure->longitude) }}"
                    step="0.000001"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('longitude') border-red-500 @enderror"
                    placeholder="e.g., 106.816667">
                @error('longitude')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description Section -->
        <div class="border-t border-gray-200 pt-4 sm:pt-6">
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                    placeholder="Enter additional description or notes about this closure">{{ old('description', $closure->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <!-- Form Actions -->
        <div class="border-t border-gray-200 pt-4 sm:pt-6">
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:justify-end">
                <a
                    href="{{ route('closures.index') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-center text-sm sm:text-base">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-sm sm:text-base">
                    Update Closure
                </button>
            </div>
        </div>
    </form>
</div>



@endsection
