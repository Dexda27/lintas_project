@extends('layouts.app')

@section('title', 'Splitters')

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Splitters</h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-base">Manage fiber optic splitters</p>
        </div>
        <a href="{{ route('splitters.create') }}" class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm sm:text-base text-center">
            <span class="hidden sm:inline">+ Add New Splitter</span>
            <span class="sm:hidden">+ Add Splitter</span>
        </a>
    </div>
</div>
<ul class="border-t border-gray-200 my-4"></ul>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-6 mb-6 sm:mb-8">
    <!-- Total Splitters Card -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-blue-500">
        <div class="sm:hidden text-center">
            <div class="bg-blue-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="git-branch" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Total Splitters</p>
            <h3 class="text-lg font-bold text-gray-900">{{ $statistics['total_splitters'] }}</h3>
        </div>
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-blue-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="git-branch" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Total Splitters</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['total_splitters'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Active Splitters Card -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-green-500">
        <div class="sm:hidden text-center">
            <div class="bg-green-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Active</p>
            <h3 class="text-lg font-bold text-green-600">{{ $statistics['active_splitters'] }}</h3>
        </div>
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-green-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Active Splitters</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['active_splitters'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Problem Splitters Card -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-red-500">
        <div class="sm:hidden text-center">
            <div class="bg-red-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="triangle-alert" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Problem</p>
            <h3 class="text-lg font-bold text-red-600">{{ $statistics['problem_splitters'] }}</h3>
        </div>
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-red-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="triangle-alert" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Problem Splitters</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['problem_splitters'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Total Capacity Card -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-purple-500">
        <div class="sm:hidden text-center">
            <div class="bg-purple-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="layers" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Total Capacity</p>
            <h3 class="text-lg font-bold text-purple-600">{{ $statistics['total_capacity'] }}</h3>
        </div>
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-purple-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Total Capacity</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['total_capacity'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Used Capacity Card -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-orange-500">
        <div class="sm:hidden text-center">
            <div class="bg-orange-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="activity" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Used Capacity</p>
            <h3 class="text-lg font-bold text-orange-600">{{ $statistics['used_capacity'] }}</h3>
        </div>
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-orange-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Used Capacity</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['used_capacity'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Splitters Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Splitters List</h2>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('splitters.index') }}" class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 w-full text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Search Splitter ID, name, location, or region..."
                aria-label="Search splitters">
            <div class="flex gap-2">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg shadow transition-colors text-sm sm:text-base flex-1 sm:flex-initial">
                    Search
                </button>
                @if(request('search'))
                <a
                    href="{{ route('splitters.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 sm:px-4 py-2 rounded-lg shadow transition-colors text-sm sm:text-base flex-1 sm:flex-initial text-center">
                    Clear
                </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Mobile Card View -->
    <div class="lg:hidden">
        @forelse($splitters as $splitter)
        <div class="border-b border-gray-200 p-4 last:border-b-0">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $splitter->splitter_id }}</h3>
                    <p class="text-xs text-gray-600 mt-1 truncate">{{ $splitter->name }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 ml-2 flex-shrink-0">
                    {{ $splitter->region }}
                </span>
            </div>

            <div class="mb-2">
                <p class="text-xs text-gray-600">
                    <span class="font-medium">Location:</span> {{ $splitter->location }}
                </p>
            </div>

            <!-- Capacity Progress Bar -->
            <div class="mb-3">
                @php
                $capacityPercentage = $splitter->capacity > 0 ? ($splitter->used_capacity / $splitter->capacity) * 100 : 0;
                @endphp
                <div class="flex items-center mb-1">
                    <span class="text-xs font-medium text-gray-600 mr-2">Capacity:</span>
                    <span class="text-xs font-medium text-gray-900">
                        {{ $splitter->used_capacity }}/{{ $splitter->capacity }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        style="width: {{ $capacityPercentage }}%"></div>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $splitter->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $splitter->status)) }}
                </span>

                <div class="flex space-x-1">
                    <a href="{{ route('splitters.show', $splitter) }}"
                        class="flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 border border-blue-300 rounded-full transition-colors"
                        title="Detail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>

                    <a href="{{ route('splitters.edit', $splitter) }}"
                        class="flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 border border-yellow-300 rounded-full transition-colors"
                        title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>

                    @if($splitter->used_capacity == 0)
                    <button type="button"
                        onclick="confirmDelete('{{ $splitter->id }}', '{{ addslashes($splitter->name) }}', '{{ $splitter->splitter_id }}')"
                        class="flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 border border-red-300 rounded-full transition-colors"
                        title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    @else
                    <span class="flex items-center justify-center w-8 h-8 text-gray-400 cursor-not-allowed"
                        title="Cannot delete splitter with active connections">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="p-6 sm:p-12 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-300 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <p class="text-gray-500 mb-2 text-sm">No splitters found</p>
                <a href="{{ route('splitters.create') }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                    Create your first splitter
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Splitter ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($splitters as $splitter)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $splitter->splitter_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $splitter->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $splitter->location }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $splitter->region }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @php
                        $capacityPercentage = $splitter->capacity > 0 ? ($splitter->used_capacity / $splitter->capacity) * 100 : 0;
                        @endphp
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ $capacityPercentage }}%"></div>
                            </div>
                            <span class="text-xs font-medium">
                                {{ $splitter->used_capacity }}/{{ $splitter->capacity }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $splitter->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $splitter->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('splitters.show', $splitter) }}"
                                class="flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-100 rounded-full transition-colors"
                                title="Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>

                            <a href="{{ route('splitters.edit', $splitter) }}"
                                class="flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-100 rounded-full transition-colors"
                                title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            @if($splitter->used_capacity == 0)
                            <button type="button"
                                onclick="confirmDelete('{{ $splitter->id }}', '{{ addslashes($splitter->name) }}', '{{ $splitter->splitter_id }}')"
                                class="flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-100 rounded-full transition-colors"
                                title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @else
                            <span class="flex items-center justify-center w-8 h-8 text-gray-400 cursor-not-allowed"
                                title="Cannot delete splitter with active connections">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <p class="text-gray-500 mb-2">No splitters found</p>
                            <a href="{{ route('splitters.create') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                Create your first splitter
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($splitters->hasPages())
    <div class="bg-white px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
        {{ $splitters->links() }}
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 hidden items-center justify-center backdrop-blur-xs z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto">
        <div class="p-4 sm:p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-rose-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Delete Splitter</h3>
            </div>

            <div class="mb-4 sm:mb-6">
                <p class="text-sm sm:text-base text-gray-600 mb-3">Are you sure you want to delete this splitter?</p>
                <div class="bg-gray-50 p-3 rounded border-l-4 border-red-400">
                    <p class="font-semibold text-gray-900 text-sm sm:text-base" id="splitterName"></p>
                    <p class="text-xs sm:text-sm text-gray-600" id="splitterId"></p>
                </div>
                <p class="text-xs sm:text-sm text-red-600 mt-3 flex items-center">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    This action cannot be undone!
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                <button type="button"
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors text-sm sm:text-base order-2 sm:order-1">
                    Cancel
                </button>
                <button type="button"
                    onclick="executeDelete()"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm sm:text-base order-1 sm:order-2">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete(id, name, splitterId) {
        document.getElementById('splitterName').textContent = name;
        document.getElementById('splitterId').textContent = 'ID: ' + splitterId;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
        document.getElementById('deleteForm').action = '/splitters/' + id;
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    function executeDelete() {
        document.getElementById('deleteForm').submit();
    }
</script>

@endsection