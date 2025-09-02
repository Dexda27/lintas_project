@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="text-center">
                    <div class="mx-auto h-24 w-24 rounded-full bg-blue-500 flex items-center justify-center mb-4">
                        <span class="text-white font-bold text-2xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $user->name }}</h2>
                    <p class="text-gray-600 mb-4">{{ $user->email }}</p>

                    <!-- Role Badge -->
                    <div class="mb-4">
                        @if($user->role == 'super_admin')
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                            Super Admin
                        </span>
                        @elseif($user->role == 'admin_region')
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            Regional Admin
                        </span>
                        @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            User
                        </span>
                        @endif
                    </div>

                    <!-- Status Badge -->
                    <!-- <div class="mb-4">
                        @if(isset($user->is_active) && !$user->is_active)
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-rose-100 text-rose-800">
                                Inactive
                            </span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                Active
                            </span>
                        @endif
                    </div> -->
                </div>

                <!-- Contact Information -->
                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Contact Information</h3>
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-600">{{ $user->email }}</span>
                        </div>
                        @if($user->region)
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-600">{{ $user->region }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                        <p class="text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                        <p class="text-gray-900">
                            @if($user->role == 'super_admin')
                            Super Administrator
                            @elseif($user->role == 'admin_region')
                            Regional Administrator
                            @else
                            User
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Region</label>
                        <p class="text-gray-900">{{ $user->region ?? 'N/A' }}</p>
                    </div>
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Status</label>
                        <p class="text-gray-900">
                            @if(isset($user->is_active) && !$user->is_active)
                                Inactive
                            @else
                                Active
                            @endif
                        </p> -->
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Member Since</label>
                    <p class="text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
                <br>
                <hr>

                <!-- Account Activity -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Account Created</p>
                            <p class="text-sm text-gray-500">{{ $user->created_at->timezone('Asia/Makassar')->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i') }}

                            </p>
                        </div>
                        <div class="text-green-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Last Updated</p>
                            <p class="text-sm text-gray-500">{{ $user->updated_at->timezone('Asia/Makassar')->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i') }}</p>
                        </div>
                        <div class="text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                    </div>

                    @if($user->email_verified_at)
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Email Verified</p>
                            <p class="text-sm text-gray-500">{{ $user->email_verified_at->timezone('Asia/Makassar')->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i') }}
                            </p>
                        </div>
                        <div class="text-green-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Account Activity -->

    </div>
</div>
</div>
@endsection