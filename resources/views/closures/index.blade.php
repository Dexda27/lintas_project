<!-- resources/views/closures/index.blade.php -->
@extends('layouts.app')

@section('title', 'Joint Closures')

@push('scripts')
<script src="{{ asset('js/jc-index.js') }}"></script>
@endpush

@section('content')
<!-- Header Section -->
<div class="mb-10">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <h1 class="text-4xl font-light text-gray-900 tracking-tight">Joint Closures</h1>
            <p class="text-gray-500 text-lg">Fiber optic infrastructure management</p>
        </div>
        <a href="{{ route('closures.create') }}"
           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md group">
            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Closure
        </a>
    </div>
</div>

<!-- Statistics Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Total Closures -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl border border-slate-200 hover:border-slate-300 transition-all duration-300 hover:shadow-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-slate-900 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-light text-slate-900">{{ $statistics['total_closures'] ?? $closures->total() }}</p>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-slate-900/5 group-hover:to-slate-900/10 transition-all duration-300"></div>
    </div>

    <!-- Active Closures -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl border border-emerald-200 hover:border-emerald-300 transition-all duration-300 hover:shadow-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-600 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-light text-emerald-900">{{ $statistics['active_closures'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-emerald-600 uppercase tracking-wider">Active</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-emerald-900/5 group-hover:to-emerald-900/10 transition-all duration-300"></div>
    </div>

    <!-- Total Connections -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border border-blue-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-600 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-light text-blue-900">{{ $statistics['total_connections'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">Connected</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-blue-900/5 group-hover:to-blue-900/10 transition-all duration-300"></div>
    </div>

    <!-- Problem Closures -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-red-50 to-red-100 rounded-2xl border border-red-200 hover:border-red-300 transition-all duration-300 hover:shadow-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-600 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-light text-red-900">{{ $statistics['problem_closures'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-red-600 uppercase tracking-wider">Issues</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-red-900/5 group-hover:to-red-900/10 transition-all duration-300"></div>
    </div>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Search Section -->
    <div class="p-6 border-b border-gray-100">
        <form method="GET" action="{{ route('closures.index') }}">
            <div class="flex gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all duration-200 text-sm"
                        placeholder="Search by Closure ID, name, location, or region..."
                        aria-label="Search closures">
                </div>
                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium text-sm">
                    Search
                </button>
                @if(request('search'))
                <a
                    href="{{ route('closures.index') }}"
                    class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium text-sm">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Region</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Capacity</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($closures as $closure)
                <tr class="hover:bg-gray-50 group transition-colors duration-150">
                    <td class="px-6 py-5">
                        <div class="font-mono text-sm font-medium text-gray-900">{{ $closure->closure_id }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm font-medium text-gray-900">{{ $closure->name }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm text-gray-600 flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $closure->location }}
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                            {{ $closure->region }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        @php
                        $capacityPercentage = $closure->capacity > 0 ? ($closure->used_capacity / $closure->capacity) * 100 : 0;
                        $colorClass = $capacityPercentage >= 80 ? 'bg-red-500' : ($capacityPercentage >= 60 ? 'bg-yellow-500' : 'bg-emerald-500');
                        @endphp
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $closure->used_capacity }}/{{ $closure->capacity }}
                                </span>
                                <span class="text-xs text-gray-500">{{ round($capacityPercentage) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div
                                    class="{{ $colorClass }} h-full rounded-full transition-all duration-500 ease-out"
                                    style="width: {{ $capacityPercentage }}%">
                                </div>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $closure->core_connections_count }} connections
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        @php
                        $statusConfig = [
                            'ok' => ['bg-emerald-100', 'text-emerald-800', 'border-emerald-200'],
                            'problem' => ['bg-red-100', 'text-red-800', 'border-red-200'],
                            'maintenance' => ['bg-yellow-100', 'text-yellow-800', 'border-yellow-200']
                        ];
                        $status = $closure->status ?? 'ok';
                        $config = $statusConfig[$status] ?? $statusConfig['ok'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border {{ implode(' ', $config) }}">
                            <div class="w-1.5 h-1.5 rounded-full mr-2 {{ str_replace('bg-', 'bg-', $config[0]) === 'bg-emerald-100' ? 'bg-emerald-500' : (str_replace('bg-', 'bg-', $config[0]) === 'bg-red-100' ? 'bg-red-500' : 'bg-yellow-500') }}"></div>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center space-x-3">
                            <a
                                href="{{ route('closures.connections', $closure) }}"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-150">
                                View
                            </a>
                            <a
                                href="{{ route('closures.edit', $closure) }}"
                                class="text-yellow-600 hover:text-yellow-800 text-sm font-medium transition-colors duration-150">
                                Edit
                            </a>
                            @if($closure->core_connections_count == 0)
                            <button
                                type="button"
                                onclick="confirmDelete('{{ $closure->id }}', '{{ addslashes($closure->name) }}', '{{ $closure->closure_id }}')"
                                class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-150">
                                Delete
                            </button>
                            @else
                            <span
                                class="text-gray-300 cursor-not-allowed text-sm"
                                title="Cannot delete closure with active connections">
                                Delete
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-600 font-medium">No joint closures found</p>
                                <p class="text-gray-400 text-sm">Get started by creating your first closure</p>
                            </div>
                            <a
                                href="{{ route('closures.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-all duration-200 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create First Closure
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    @if($closures->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
        <div class="flex-1 flex justify-between items-center">
            <!-- Showing Results Info -->
            <div>
                <p class="text-sm text-gray-600">
                    Showing <span class="font-medium text-gray-900">{{ $closures->firstItem() ?: 0 }}</span>
                    to <span class="font-medium text-gray-900">{{ $closures->lastItem() ?: 0 }}</span>
                    of <span class="font-medium text-gray-900">{{ $closures->total() }}</span> results
                </p>
            </div>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-1">
                {{-- Previous Page Link --}}
                @if ($closures->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    Previous
                </span>
                @else
                <a href="{{ $closures->appends(request()->query())->previousPageUrl() }}"
                   class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    Previous
                </a>
                @endif

                {{-- Pagination Elements --}}
                @php
                $start = max(1, $closures->currentPage() - 2);
                $end = min($closures->lastPage(), $closures->currentPage() + 2);
                @endphp

                @if($start > 1)
                <a href="{{ $closures->appends(request()->query())->url(1) }}"
                   class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    1
                </a>
                @if($start > 2)
                <span class="px-3 py-2 text-sm text-gray-400">...</span>
                @endif
                @endif

                @for($page = $start; $page <= $end; $page++)
                    @if ($page == $closures->currentPage())
                    <span class="px-3 py-2 text-sm text-white bg-gray-900 border border-gray-900 rounded-lg">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $closures->appends(request()->query())->url($page) }}"
                       class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        {{ $page }}
                    </a>
                    @endif
                @endfor

                @if($end < $closures->lastPage())
                    @if($end < $closures->lastPage() - 1)
                    <span class="px-3 py-2 text-sm text-gray-400">...</span>
                    @endif
                    <a href="{{ $closures->appends(request()->query())->url($closures->lastPage()) }}"
                       class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        {{ $closures->lastPage() }}
                    </a>
                @endif

                {{-- Next Page Link --}}
                @if ($closures->hasMorePages())
                <a href="{{ $closures->appends(request()->query())->nextPageUrl() }}"
                   class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    Next
                </a>
                @else
                <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    Next
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 hidden items-center justify-center z-50 bg-black/50" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all" id="modalContent">
        <div class="p-6">
            <div class="flex items-start mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 id="modal-title" class="text-xl font-semibold text-gray-900 mb-2">Delete Joint Closure</h3>
                    <p class="text-gray-600 mb-4">This action cannot be undone. Are you sure you want to delete this closure?</p>

                    <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-red-400">
                        <p class="font-semibold text-gray-900" id="closureName"></p>
                        <p class="text-sm text-gray-600 font-mono" id="closureId"></p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="px-6 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button
                    type="button"
                    onclick="executeDelete()"
                    class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all duration-200 font-medium">
                    Delete
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



@endsection
