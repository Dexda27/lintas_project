<!-- resources/views/closures/index.blade.php -->
@extends('layouts.app')

@section('title', 'Joint Closures')

@push('scripts')
<script src="{{ asset('js/jc-index.js') }}"></script>
@endpush

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Joint Closures</h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-base">Manage fiber optic joint closures</p>
        </div>
        <a href="{{ route('closures.create') }}" class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm sm:text-base text-center">
            <span class="hidden sm:inline">+ Add New Closure</span>
            <span class="sm:hidden">+ Add Closure</span>
        </a>
    </div>
</div>
<ul class="border-t border-gray-200 my-4"></ul>


<!-- Statistics Cards - Dual Layout (Mobile Vertical, Desktop Horizontal) -->
<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
    <!-- Total Closures Card - Compact Mobile Version -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-blue-500">
        <!-- Mobile: Vertical Layout -->
        <div class="sm:hidden text-center">
            <div class="bg-blue-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="layers" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Total Closures</p>
            <h3 class="text-lg font-bold text-gray-900">{{ $statistics['total_closures'] ?? $closures->total() }}</h3>
        </div>

        <!-- Desktop/Tablet: Horizontal Layout -->
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-blue-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Total Closures</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['total_closures'] ?? $closures->total() }}</h3>
            </div>
        </div>
    </div>

    <!-- Active Closures Card - Compact Mobile Version -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-green-500">
        <!-- Mobile: Vertical Layout -->
        <div class="sm:hidden text-center">
            <div class="bg-green-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Active Closures</p>
            <h3 class="text-lg font-bold text-green-600">{{ $statistics['active_closures'] ?? 0 }}</h3>
        </div>

        <!-- Desktop/Tablet: Horizontal Layout -->
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-green-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Active Closures</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['active_closures'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Total Connections Card - Compact Mobile Version -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-gray-500">
        <!-- Mobile: Vertical Layout -->
        <div class="sm:hidden text-center">
            <div class="bg-gray-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="cable" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Total Connections</p>
            <h3 class="text-lg font-bold text-blue-600">{{ $statistics['total_connections'] ?? 0 }}</h3>
        </div>

        <!-- Desktop/Tablet: Horizontal Layout -->
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-gray-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="cable" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Total Connections</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['total_connections'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Problem Closures Card - Compact Mobile Version -->
    <div class="bg-white rounded-lg sm:rounded-xl p-3 sm:p-6 shadow hover:shadow-lg transition-shadow duration-200 border-t-4 border-red-500">
        <!-- Mobile: Vertical Layout -->
        <div class="sm:hidden text-center">
            <div class="bg-red-500 text-white p-2 rounded-lg inline-flex mb-2">
                <i data-lucide="triangle-alert" class="w-4 h-4"></i>
            </div>
            <p class="text-gray-600 text-xs font-medium mb-1">Problem Closures</p>
            <h3 class="text-lg font-bold text-red-600">{{ $statistics['problem_closures'] ?? 0 }}</h3>
        </div>

        <!-- Desktop/Tablet: Horizontal Layout -->
        <div class="hidden sm:flex items-center space-x-4">
            <div class="bg-red-500 text-white p-3 rounded-lg shrink-0">
                <i data-lucide="triangle-alert" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-gray-600 text-sm font-medium">Problem Closures</p>
                <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $statistics['problem_closures'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>


<!-- Closures Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Joint Closures</h2>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('closures.index') }}" class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 w-full text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Search Closure ID, name, location, or region..."
                aria-label="Search closures">
            <div class="flex gap-2">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg shadow transition-colors text-sm sm:text-base flex-1 sm:flex-initial">
                    Search
                </button>
                @if(request('search'))
                <a
                    href="{{ route('closures.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 sm:px-4 py-2 rounded-lg shadow transition-colors text-sm sm:text-base flex-1 sm:flex-initial text-center">
                    Clear
                </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Mobile Card View (visible on screens smaller than lg) -->
    <div class="lg:hidden">
        @forelse($closures as $closure)
        <div class="border-b border-gray-200 p-4 last:border-b-0">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $closure->closure_id }}</h3>
                    <p class="text-xs text-gray-600 mt-1 truncate">{{ $closure->name }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 ml-2 flex-shrink-0">
                    {{ $closure->region }}
                </span>
            </div>

            <div class="mb-2">
                <p class="text-xs text-gray-600">
                    <span class="font-medium">Location:</span> {{ $closure->location }}
                </p>
            </div>

            <!-- Capacity Progress Bar -->
            <div class="mb-3">
                @php
                $capacityPercentage = $closure->capacity > 0 ? ($closure->used_capacity / $closure->capacity) * 100 : 0;
                @endphp
                <div class="flex items-center mb-1">
                    <span class="text-xs font-medium text-gray-600 mr-2">Capacity:</span>
                    <span class="text-xs font-medium text-gray-900">
                        {{ $closure->used_capacity }}/{{ $closure->capacity }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        style="width: {{ $capacityPercentage }}%"
                        role="progressbar"
                        aria-valuenow="{{ $capacityPercentage }}"
                        aria-valuemin="0"
                        aria-valuemax="100"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $closure->core_connections_count }} connections
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $closure->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ ucfirst(str_replace('_', ' ', $closure->status)) }}
                    </span>
                </div>

                <div class="flex space-x-1">
                    <a
                        href="{{ route('closures.connections', $closure) }}"
                        class="flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 border border-blue-300 rounded-full transition-colors"
                        title="Detail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                    <a
                        href="{{ route('closures.edit', $closure) }}"
                        class="flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 border border-yellow-300 rounded-full transition-colors"
                        title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    @if($closure->core_connections_count == 0)
                    <button
                        type="button"
                        onclick="confirmDelete('{{ $closure->id }}', '{{ addslashes($closure->name) }}', '{{ $closure->closure_id }}')"
                        class="flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 border border-red-300 rounded-full transition-colors"
                        title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    @else
                    <span
                        class="flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-900 hover:bg-gray-50 border border-gray-300 rounded-full transition-colors cursor-not-allowed"
                        title="Cannot delete closure with active connections">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-500 mb-2 text-sm">No joint closures found</p>
                <a
                    href="{{ route('closures.create') }}"
                    class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                    Create your first closure
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table View (visible on lg screens and up) -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Closure ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($closures as $closure)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $closure->closure_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $closure->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $closure->location }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $closure->region }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @php
                        $capacityPercentage = $closure->capacity > 0 ? ($closure->used_capacity / $closure->capacity) * 100 : 0;
                        @endphp
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div
                                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ $capacityPercentage }}%"
                                    role="progressbar"
                                    aria-valuenow="{{ $capacityPercentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                            <span class="text-xs font-medium">
                                {{ $closure->used_capacity }}/{{ $closure->capacity }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $closure->core_connections_count }} connections
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $closure->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $closure->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a
                                href="{{ route('closures.connections', $closure) }}"
                                class="flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-100 rounded-full transition-colors"
                                title="Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a
                                href="{{ route('closures.edit', $closure) }}"
                                class="flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-100 rounded-full transition-colors"
                                title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @if($closure->core_connections_count == 0)
                            <button
                                type="button"
                                onclick="confirmDelete('{{ $closure->id }}', '{{ addslashes($closure->name) }}', '{{ $closure->closure_id }}')"
                                class="flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-100 rounded-full transition-colors"
                                title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @else
                            <span
                                class="flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors cursor-not-allowed"
                                title="Cannot delete closure with active connections">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-500 mb-2">No joint closures found</p>
                            <a
                                href="{{ route('closures.create') }}"
                                class="text-blue-600 hover:text-blue-900 font-medium">
                                Create your first closure
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Responsive Pagination Section -->
    @if($closures->hasPages())
    <div class="bg-white px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <!-- Showing Results Info -->
            <div class="text-center sm:text-left">
                <p class="text-xs sm:text-sm text-gray-700">
                    <span class="hidden sm:inline">Showing</span>
                    <span class="font-medium">{{ $closures->firstItem() ?: 0 }}</span>
                    <span class="hidden sm:inline">to</span>
                    <span class="sm:hidden">-</span>
                    <span class="font-medium">{{ $closures->lastItem() ?: 0 }}</span>
                    <span class="hidden sm:inline">of</span>
                    <span class="sm:hidden">/</span>
                    <span class="font-medium">{{ $closures->total() }}</span>
                    <span class="hidden sm:inline">results</span>
                </p>
            </div>

            <!-- Pagination Links -->
            <div class="flex justify-center sm:justify-end">
                <div class="flex items-center space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($closures->onFirstPage())
                    <span class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-l-md">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Previous</span>
                    </span>
                    @else
                    <a href="{{ $closures->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Previous</span>
                    </a>
                    @endif

                    {{-- Mobile: Show only current page and total pages --}}
                    <div class="sm:hidden flex items-center">
                        <span class="relative inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-blue-600">
                            {{ $closures->currentPage() }} / {{ $closures->lastPage() }}
                        </span>
                    </div>

                    {{-- Desktop: Show all page numbers --}}
                    <div class="hidden sm:flex items-center space-x-1">
                        @php
                        $start = max(1, $closures->currentPage() - 2);
                        $end = min($closures->lastPage(), $closures->currentPage() + 2);
                        @endphp

                        @if($start > 1)
                        <a href="{{ $closures->appends(request()->query())->url(1) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                            1
                        </a>
                        @if($start > 2)
                        <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300">
                            ...
                        </span>
                        @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if ($page==$closures->currentPage())
                            <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 border border-blue-600">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $closures->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                                {{ $page }}
                            </a>
                            @endif
                            @endfor

                            @if($end < $closures->lastPage())
                                @if($end < $closures->lastPage() - 1)
                                    <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300">
                                        ...
                                    </span>
                                    @endif
                                    <a href="{{ $closures->appends(request()->query())->url($closures->lastPage()) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                                        {{ $closures->lastPage() }}
                                    </a>
                                    @endif
                    </div>

                    {{-- Next Page Link --}}
                    @if ($closures->hasMorePages())
                    <a href="{{ $closures->appends(request()->query())->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Next</span>
                    </a>
                    @else
                    <span class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-r-md">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Next</span>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto transform transition-all">
        <div class="p-4 sm:p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div>
                    <h3 id="modal-title" class="text-base sm:text-lg font-semibold text-gray-900">Delete Joint Closure</h3>
                </div>
            </div>

            <div class="mb-4 sm:mb-6">
                <p class="text-sm sm:text-base text-gray-600 mb-3">Are you sure you want to delete this joint closure?</p>
                <div class="bg-gray-50 p-3 rounded border-l-4 border-red-400">
                    <p class="font-semibold text-gray-900 text-sm sm:text-base" id="closureName"></p>
                    <p class="text-xs sm:text-sm text-gray-600" id="closureId"></p>
                </div>
                <p class="text-xs sm:text-sm text-red-600 mt-3 flex items-center">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    This action cannot be undone!
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-sm sm:text-base order-2 sm:order-1">
                    Cancel
                </button>
                <button
                    type="button"
                    onclick="executeDelete()"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-sm sm:text-base order-1 sm:order-2">
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

@push('styles')
<style>
    /* Smooth transitions */
    .transition-colors {
        transition-property: color, background-color, border-color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    /* Ensure modal appears above everything with backdrop */
    #deleteModal {
        backdrop-filter: blur(4px);
        background-color: rgba(0, 0, 0, 0.5);
        transition: all 0.15s ease;
    }

    /* Custom focus styles for better accessibility */
    .focus\:ring-2:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
    }

    /* Progress bar animation */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    /* Mobile-specific adjustments */
    @media (max-width: 640px) {
        #deleteModal .transform {
            transform: scale(0.95);
        }

        /* Ensure touch targets are large enough */
        button,
        a {
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Better spacing for mobile */
        .space-x-1>*+* {
            margin-left: 0.25rem;
        }

        /* Improve readability on small screens */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }

    /* Tablet adjustments */
    @media (min-width: 641px) and (max-width: 1023px) {

        /* Optimize for tablet portrait/landscape */
        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Desktop enhancements */
    @media (min-width: 1024px) {

        /* Hover effects only on desktop */
        .hover\:bg-gray-50:hover {
            background-color: #f9fafb;
        }

        .hover\:text-blue-900:hover {
            color: #1e3a8a;
        }

        .hover\:text-yellow-900:hover {
            color: #78350f;
        }

        .hover\:text-red-900:hover {
            color: #7f1d1d;
        }
    }
</style>
@endpush
@endsection