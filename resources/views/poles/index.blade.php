@extends('layouts.app')

@section('title', 'Tiang')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tiang</h1>
            <p class="text-gray-600 mt-1 text-sm">Manage all fiber optic poles</p>
        </div>
        <a href="{{ route('poles.create') }}"
           class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium shadow-sm w-full sm:w-auto">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Tiang
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <!-- Total Poles Card -->
    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border-l-4 border-blue-500 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-600 font-medium">Total Tiang</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $poles->total() }}</h3>
            </div>
            <div class="bg-blue-500 text-white p-3 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Steel Poles Card -->
    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border-l-4 border-gray-500 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-600 font-medium">Steel</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $poles->where('type', 'besi')->count() }}</h3>
            </div>
            <div class="bg-gray-500 text-white p-3 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Concrete Poles Card -->
    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border-l-4 border-gray-800 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-600 font-medium">Concrete</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $poles->where('type', 'beton')->count() }}</h3>
            </div>
            <div class="bg-gray-800 text-white p-3 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Status OK Card -->
    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border-l-4 border-green-500 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-600 font-medium">Status OK</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $poles->where('status', 'ok')->count() }}</h3>
            </div>
            <div class="bg-green-500 text-white p-3 rounded-lg ml-2 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Table Container -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <!-- Table Header -->
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Tiang List</h2>
    </div>

    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('poles.index') }}" class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-white">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="sm:col-span-2 lg:col-span-1 xl:col-span-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="border border-gray-300 rounded-lg px-4 py-2.5 w-full text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search Pole ID, JC, Splitter...">
            </div>

            <!-- Region Filter -->
            <div>
                <select name="region" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Regions</option>
                    <option value="Bali" {{ request('region') == 'Bali' ? 'selected' : '' }}>Bali</option>
                    <option value="NTB" {{ request('region') == 'NTB' ? 'selected' : '' }}>NTB</option>
                    <option value="NTT" {{ request('region') == 'NTT' ? 'selected' : '' }}>NTT</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <select name="type" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="besi" {{ request('type') == 'besi' ? 'selected' : '' }}>Steel</option>
                    <option value="beton" {{ request('type') == 'beton' ? 'selected' : '' }}>Concrete</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="ok" {{ request('status') == 'ok' ? 'selected' : '' }}>OK</option>
                    <option value="not_ok" {{ request('status') == 'not_ok' ? 'selected' : '' }}>Not OK</option>
                </select>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-col sm:flex-row gap-2 mt-3">
            <button type="submit" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Search
            </button>
            @if(request()->hasAny(['search', 'region', 'type', 'status']))
            <a href="{{ route('poles.index') }}" class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Clear Filters
            </a>
            @endif
        </div>
    </form>

    <!-- Table - Desktop & Tablet -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Pole ID</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Name</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Location</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Region</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Type</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Height</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">JC</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Splitter</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Status</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($poles as $pole)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $pole->pole_id }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                        {{ $pole->name }}
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-900">
                        <div class="max-w-xs truncate" title="{{ $pole->location }}">
                            {{ Str::limit($pole->location, 30) }}
                        </div>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $pole->region }}
                        </span>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                        @if($pole->type == 'besi')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                            Steel
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-800 text-white">
                            Concrete
                        </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                        {{ $pole->height }}m
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-600 text-white min-w-[2rem]">
                            {{ $pole->jointClosures->count() }}
                        </span>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-semibold rounded-full bg-green-600 text-white min-w-[2rem]">
                            {{ $pole->splitters->count() }}
                        </span>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $pole->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $pole->status === 'ok' ? 'OK' : 'Not OK' }}
                        </span>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('poles.show', $pole) }}"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>

                            <a href="{{ route('poles.edit', $pole) }}"
                                class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <button type="button"
                                onclick="confirmDelete('{{ $pole->id }}', '{{ addslashes($pole->name) }}', '{{ $pole->pole_id }}')"
                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-500 mb-3">No poles found</p>
                            <a href="{{ route('poles.create') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create your first pole
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($poles->hasPages())
    <div class="px-4 sm:px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs sm:text-sm text-gray-700 text-center sm:text-left">
                Showing <span class="font-medium">{{ $poles->firstItem() }}</span> to <span class="font-medium">{{ $poles->lastItem() }}</span> of <span class="font-medium">{{ $poles->total() }}</span> results
            </div>
            <div class="w-full sm:w-auto flex justify-center">
                {{ $poles->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" onclick="if(event.target === this) closeDeleteModal()">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all" onclick="event.stopPropagation()">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-rose-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1-1.964-1-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Delete Pole</h3>
            </div>

            <!-- Modal Body -->
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-3">Are you sure you want to delete this pole? This action cannot be undone.</p>
                <div class="bg-gray-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="font-semibold text-gray-900 text-sm mb-1" id="poleName"></p>
                    <p class="text-xs text-gray-600" id="poleId"></p>
                </div>
                <div class="mt-3 flex items-center text-xs text-red-600">
                    <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1-1.964-1-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>All associated data will be removed</span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3">
                <button type="button"
                    onclick="closeDeleteModal()"
                    class="w-full sm:flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                    Cancel
                </button>
                <button type="button"
                    onclick="executeDelete()"
                    class="w-full sm:flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
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

@push('scripts')
<script>
    function confirmDelete(id, name, poleId) {
        document.getElementById('poleName').textContent = name;
        document.getElementById('poleId').textContent = 'ID: ' + poleId;
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('deleteForm').action = '/poles/' + id;
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function executeDelete() {
        document.getElementById('deleteForm').submit();
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endpush

@endsection
