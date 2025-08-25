@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center space-x-2 mb-4">
            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create New User</h1>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select id="role" 
                        name="role" 
                        required 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror"
                        onchange="toggleRegionField()">
                    <option value="">Select Role</option>
                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin_region" {{ old('role') == 'admin_region' ? 'selected' : '' }}>Regional Admin</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Region -->
            <div id="region-field" class="mb-6" style="display: {{ old('role') == 'admin_region' ? 'block' : 'none' }};">
                <label for="region" class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                <select id="region" 
                        name="region" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('region') border-red-500 @enderror">
                    <option value="">Select Region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region }}" {{ old('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                    @endforeach
                </select>
                @error('region')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Required for Regional Admin role</p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('users.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRegionField() {
    const roleSelect = document.getElementById('role');
    const regionField = document.getElementById('region-field');
    const regionSelect = document.getElementById('region');
    
    if (roleSelect.value === 'admin_region') {
        regionField.style.display = 'block';
        regionSelect.required = true;
    } else {
        regionField.style.display = 'none';
        regionSelect.required = false;
        regionSelect.value = '';
    }
}
</script>
@endsection