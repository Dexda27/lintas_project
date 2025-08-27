@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Header Section -->
<div class="mb-10">
    <div class="space-y-1">
        <h1 class="text-4xl font-light text-gray-900 tracking-tight">Dashboard</h1>
        <p class="text-gray-500 text-lg">Overview of fiber core infrastructure</p>
    </div>
</div>

<!-- Statistics Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Total Cores -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border border-blue-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-600 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-light text-blue-900">{{ number_format($totalCores) }}</p>
                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">Total Cores</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-blue-900/5 group-hover:to-blue-900/10 transition-all duration-300"></div>
    </div>

    <!-- Active Cores -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl border border-emerald-200 hover:border-emerald-300 transition-all duration-300 hover:shadow-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-600 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-light text-emerald-900">{{ number_format($activeCores) }}</p>
                    <p class="text-sm font-medium text-emerald-600 uppercase tracking-wider">Active Cores</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-emerald-900/5 group-hover:to-emerald-900/10 transition-all duration-300"></div>
    </div>

    <!-- Inactive Cores -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl border border-slate-200 hover:border-slate-300 transition-all duration-300 hover:shadow-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-slate-600 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-light text-slate-900">{{ number_format($inactiveCores) }}</p>
                    <p class="text-sm font-medium text-slate-600 uppercase tracking-wider">Inactive Cores</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-slate-900/5 group-hover:to-slate-900/10 transition-all duration-300"></div>
    </div>

    <!-- Problem Cores -->
    <div class="group relative overflow-hidden bg-gradient-to-br from-red-50 to-red-100 rounded-2xl border border-red-200 hover:border-red-300 transition-all duration-300 hover:shadow-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-600 rounded-xl shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-light text-red-900">{{ number_format($problemCores) }}</p>
                    <p class="text-sm font-medium text-red-600 uppercase tracking-wider">Problem Cores</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-red-900/5 group-hover:to-red-900/10 transition-all duration-300"></div>
    </div>
</div>

@if(auth()->user()->isSuperAdmin() && $regionalData->count() > 0)
<!-- Regional Overview -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-2xl font-light text-gray-900 tracking-tight">Regional Overview</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($regionalData as $region)
            <div class="group bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200 hover:border-gray-300 transition-all duration-300 hover:shadow-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $region->region }}</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Cables</span>
                        <span class="font-medium text-gray-900">{{ $region->total_cables }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Cores</span>
                        <span class="font-medium text-gray-900">{{ number_format($region->total_cores) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Cables Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-light text-gray-900 tracking-tight">Fiber Cables</h2>
            <p class="text-gray-500 text-sm mt-1">Manage your cable infrastructure</p>
        </div>
        <a href="{{ route('cables.create') }}"
           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-gray-800 transition-all duration-200 shadow-sm hover:shadow-md group">
            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Cable
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cable ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Region</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Route</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cores</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Usage</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($cables as $cable)
                <tr class="hover:bg-gray-50 group transition-colors duration-150">
                    <td class="px-6 py-5">
                        <div class="font-mono text-sm font-medium text-gray-900">{{ $cable->cable_id }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm font-medium text-gray-900">{{ $cable->name }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                            {{ $cable->region }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm text-gray-600 flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                            {{ $cable->source_site }} → {{ $cable->destination_site }}
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="space-y-2">
                            <div class="flex space-x-3 text-sm">
                                <span class="text-emerald-600 font-medium">{{ $cable->active_cores_count }} Active</span>
                                <span class="text-gray-500">{{ $cable->inactive_cores_count }} Inactive</span>
                                @if($cable->problem_cores_count > 0)
                                <span class="text-red-600 font-medium">{{ $cable->problem_cores_count }} Problems</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">Total: {{ $cable->total_cores }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        @php
                        $statusConfig = [
                            'ok' => ['bg-emerald-100', 'text-emerald-800', 'border-emerald-200'],
                            'problem' => ['bg-red-100', 'text-red-800', 'border-red-200']
                        ];
                        $status = $cable->status ?? 'ok';
                        $config = $statusConfig[$status] ?? $statusConfig['ok'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border {{ implode(' ', $config) }}">
                            <div class="w-1.5 h-1.5 rounded-full mr-2 {{ str_replace('bg-', 'bg-', $config[0]) === 'bg-emerald-100' ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        @php
                        $usageConfig = [
                            'active' => ['bg-blue-100', 'text-blue-800'],
                            'inactive' => ['bg-gray-100', 'text-gray-800']
                        ];
                        $usage = $cable->usage ?? 'inactive';
                        $usageStyle = $usageConfig[$usage] ?? $usageConfig['inactive'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full {{ implode(' ', $usageStyle) }}">
                            {{ ucfirst($usage) }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('cables.show', $cable) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-150">
                                View
                            </a>
                            <a href="{{ route('cables.cores', $cable) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors duration-150">
                                Cores
                            </a>
                            <a href="{{ route('cables.edit', $cable) }}"
                               class="text-yellow-600 hover:text-yellow-800 text-sm font-medium transition-colors duration-150">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-600 font-medium">No cables found</p>
                                <p class="text-gray-400 text-sm">Get started by creating your first cable</p>
                            </div>
                            <a
                                href="{{ route('cables.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-all duration-200 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create First Cable
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    @if($cables->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
        <div class="flex-1 flex justify-between items-center">
            <!-- Showing Results Info -->
            <div>
                <p class="text-sm text-gray-600">
                    Showing <span class="font-medium text-gray-900">{{ $cables->firstItem() ?: 0 }}</span>
                    to <span class="font-medium text-gray-900">{{ $cables->lastItem() ?: 0 }}</span>
                    of <span class="font-medium text-gray-900">{{ $cables->total() }}</span> results
                </p>
            </div>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-1">
                {{-- Previous Page Link --}}
                @if ($cables->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    Previous
                </span>
                @else
                <a href="{{ $cables->previousPageUrl() }}"
                   class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    Previous
                </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($cables->getUrlRange(1, $cables->lastPage()) as $page => $url)
                    @if ($page == $cables->currentPage())
                    <span class="px-3 py-2 text-sm text-white bg-gray-900 border border-gray-900 rounded-lg">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $url }}"
                       class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        {{ $page }}
                    </a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($cables->hasMorePages())
                <a href="{{ $cables->nextPageUrl() }}"
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
@endsection
