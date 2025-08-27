<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600 mt-2 text-sm sm:text-base">Overview of fiber core infrastructure</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-500 text-white p-3 rounded-lg">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Total Cores</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalCores) }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
            <div class="bg-green-500 text-white p-3 rounded-lg">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Active Cores</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($activeCores) }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 shadow hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
            <div class="bg-gray-500 text-white p-3 rounded-lg">
                <i data-lucide="circle-minus" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Inactive Cores</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($inactiveCores) }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 shadow hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
            <div class="bg-red-500 text-white p-3 rounded-lg">
                <i data-lucide="triangle-alert" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Problems Cores</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($problemCores) }}</h3>
            </div>
        </div>
    </div>
    
    <!-- Tambahkan 2 card lainnya dengan warna sesuai status -->
</div>


@if(auth()->user()->isSuperAdmin() && $regionalData->count() > 0)
<!-- Regional Overview -->
<div class="bg-white rounded-lg shadow mb-6 sm:mb-8">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Regional Overview</h2>
    </div>
    <div class="p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($regionalData as $region)
            <div class="border rounded-lg p-3 sm:p-4">
                <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $region->region }}</h3>
                <div class="mt-2 space-y-1">
                    <p class="text-xs sm:text-sm text-gray-600">Cables: {{ $region->total_cables }}</p>
                    <p class="text-xs sm:text-sm text-gray-600">Total Cores: {{ number_format($region->total_cores) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Cables Table -->
<div class="bg-white rounded-lg shadow">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-2 sm:space-y-0">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Fiber Cables</h2>
        <a href="{{ route('cables.create') }}" class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm sm:text-base text-center">
            <span class="hidden sm:inline">Add New Cable</span>
            <span class="sm:hidden">Add Cable</span>
        </a>
    </div>

    <!-- Mobile Card View (visible on screens smaller than lg) -->
    <div class="lg:hidden">
        @forelse($cables as $cable)
        <div class="border-b border-gray-200 p-4 last:border-b-0">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">{{ $cable->cable_id }}</h3>
                    <p class="text-xs text-gray-600 mt-1">{{ $cable->name }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ $cable->region }}
                </span>
            </div>

            <div class="mb-2">
                <p class="text-xs text-gray-600">
                    <span class="font-medium">Route:</span> {{ $cable->source_site }} → {{ $cable->destination_site }}
                </p>
            </div>

            <div class="mb-3">
                <div class="flex flex-wrap gap-1 mb-1">
                    <span class="text-green-600 text-xs">{{ $cable->active_cores_count }} Active</span>
                    <span class="text-gray-500 text-xs">{{ $cable->inactive_cores_count }} Inactive</span>
                    @if($cable->problem_cores_count > 0)
                    <span class="text-red-600 text-xs">{{ $cable->problem_cores_count }} Problems</span>
                    @endif
                </div>
                <div class="text-xs text-gray-500">Total: {{ $cable->total_cores }}</div>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <span class="px-2 py-1 text-xs font-semibold {{ $cable->status }}">
                        {{ ucfirst($cable->status) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold {{ $cable->usage}}">
                        {{ ucfirst($cable->usage) }}
                    </span>
                </div>

                <div class="flex space-x-1">
                    <a href="{{ route('cables.show', $cable) }}" class="text-indigo-600 hover:text-indigo-900 text-xs">View</a>
                    <a href="{{ route('cables.cores', $cable) }}" class="text-blue-600 hover:text-blue-900 text-xs">Cores</a>
                    <a href="{{ route('cables.edit', $cable) }}" class="text-yellow-600 hover:text-yellow-900 text-xs">Edit</a>
                </div>
            </div>
        </div>
        @empty
        <div class="p-4 text-center">
            <p class="text-sm text-gray-500">
                No cables found. <a href="{{ route('cables.create') }}" class="text-blue-600 hover:text-blue-900">Create your first cable</a>
            </p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table View (visible on lg screens and up) -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cable ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cores</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cables as $cable)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $cable->cable_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $cable->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $cable->region }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $cable->source_site }} → {{ $cable->destination_site }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex space-x-2">
                            <span class="text-green-600">{{ $cable->active_cores_count }} Active</span>
                            <span class="text-gray-500">{{ $cable->inactive_cores_count }} Inactive</span>
                            @if($cable->problem_cores_count > 0)
                            <span class="text-red-600">{{ $cable->problem_cores_count }} Problems</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">Total: {{ $cable->total_cores }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cable->status}}">
                            {{ ucfirst($cable->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cable->usage === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($cable->usage) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                        <a href="{{ route('cables.show', $cable) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                            <i data-lucide="eye" class="w-4 h-4"></i> View
                        </a>
                        <a href="{{ route('cables.cores', $cable) }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-1">
                            <i data-lucide="layers" class="w-4 h-4"></i> Cores
                        </a>
                        <a href="{{ route('cables.edit', $cable) }}" class="text-yellow-600 hover:text-yellow-900 flex items-center gap-1">
                            <i data-lucide="edit" class="w-4 h-4"></i> Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        No cables found. <a href="{{ route('cables.create') }}" class="text-blue-600 hover:text-blue-900">Create your first cable</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Responsive Pagination Section -->
    @if($cables->hasPages())
    <div class="bg-white px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <!-- Showing Results Info -->
            <div class="text-center sm:text-left">
                <p class="text-xs sm:text-sm text-gray-700">
                    <span class="hidden sm:inline">Showing</span>
                    <span class="font-medium">{{ $cables->firstItem() }}</span>
                    <span class="hidden sm:inline">to</span>
                    <span class="sm:hidden">-</span>
                    <span class="font-medium">{{ $cables->lastItem() }}</span>
                    <span class="hidden sm:inline">of</span>
                    <span class="sm:hidden">/</span>
                    <span class="font-medium">{{ $cables->total() }}</span>
                    <span class="hidden sm:inline">results</span>
                </p>
            </div>

            <!-- Pagination Links -->
            <div class="flex justify-center sm:justify-end">
                <div class="flex items-center space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($cables->onFirstPage())
                    <span class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-l-md">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    @else
                    <a href="{{ $cables->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    @endif

                    {{-- Mobile: Show only current page and total pages --}}
                    <div class="sm:hidden flex items-center">
                        <span class="relative inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-blue-600">
                            {{ $cables->currentPage() }} / {{ $cables->lastPage() }}
                        </span>
                    </div>

                    {{-- Desktop: Show all page numbers --}}
                    <div class="hidden sm:flex items-center space-x-1">
                        @php
                        $start = max(1, $cables->currentPage() - 2);
                        $end = min($cables->lastPage(), $cables->currentPage() + 2);
                        @endphp

                        @if($start > 1)
                        <a href="{{ $cables->url(1) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">1</a>
                        @if($start > 2)
                        <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300">...</span>
                        @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if ($page==$cables->currentPage())
                            <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 border border-blue-600">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $cables->url($page) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                {{ $page }}
                            </a>
                            @endif
                            @endfor

                            @if($end < $cables->lastPage())
                                @if($end < $cables->lastPage() - 1)
                                    <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300">...</span>
                                    @endif
                                    <a href="{{ $cables->url($cables->lastPage()) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">{{ $cables->lastPage() }}</a>
                                    @endif
                    </div>

                    {{-- Next Page Link --}}
                    @if ($cables->hasMorePages())
                    <a href="{{ $cables->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    @else
                    <span class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-r-md">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
