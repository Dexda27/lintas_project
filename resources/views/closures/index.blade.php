<!-- resources/views/closures/index.blade.php -->
@extends('layouts.app')

@section('title', 'Joint Closures')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Joint Closures</h1>
            <p class="text-gray-600 mt-2">Manage fiber optic joint closures</p>
        </div>
        <a href="{{ route('closures.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Add New Closure
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Closures</p>
                <p class="text-2xl font-bold text-gray-900">{{ $closures->total() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Closures</p>
                <p class="text-2xl font-bold text-green-600">{{ $closures->where('status', 'ok')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Connections</p>
                <p class="text-2xl font-bold text-blue-600">{{ $closures->sum('core_connections_count') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Problem Closures</p>
                <p class="text-2xl font-bold text-red-600">{{ $closures->where('status', 'not_ok')->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Closures Table -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Joint Closures</h2>
    </div>


    <!-- Search Form -->
    <form method="GET" action="{{ route('closures.index') }}" class="px-6 py-4 border-b border-gray-200">
        <div class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                class="border border-gray-300 rounded-lg px-4 py-2 w-full"
                placeholder="Cari Closure ID, name, location, atau region...">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('closures.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow transition">
                Clear
            </a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Closure ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($closures as $closure)
                <tr class="hover:bg-gray-50">
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
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ $closure->capacity > 0 ? ($closure->used_capacity / $closure->capacity) * 100 : 0 }}%"></div>
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
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $closure->status === 'ok' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $closure->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            {{-- <a href="{{ route('closures.show', $closure) }}" class="text-indigo-600 hover:text-indigo-900">View</a> --}}
                            <a href="{{ route('closures.connections', $closure) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                            <a href="{{ route('closures.edit', $closure) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        No joint closures found. <a href="{{ route('closures.create') }}" class="text-blue-600 hover:text-blue-900">Create your first closure</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Pagination Section -->
        @if($closures->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="flex-1 flex justify-between items-center">
                <!-- Showing Results Info -->
                <div>
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">{{ $closures->firstItem() }}</span>
                        to
                        <span class="font-medium">{{ $closures->lastItem() }}</span>
                        of
                        <span class="font-medium">{{ $closures->total() }}</span>
                        results
                    </p>
                </div>

                <!-- Pagination Links -->
                <div class="flex items-center space-x-2">
                    {{-- Previous Page Link --}}
                    @if ($closures->onFirstPage())
                    <span
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-l-md">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    @else
                    <a href="{{ $closures->previousPageUrl() }}"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($closures->getUrlRange(1, $closures->lastPage()) as $page => $url)
                    @if ($page == $closures->currentPage())
                    <span
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $url }}"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                        {{ $page }}
                    </a>
                    @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($closures->hasMorePages())
                    <a href="{{ $closures->nextPageUrl() }}"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    @else
                    <span
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-r-md">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>

    <!-- @if($closures->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $closures->links() }}
    </div>
    @endif -->
</div>
@endsection
