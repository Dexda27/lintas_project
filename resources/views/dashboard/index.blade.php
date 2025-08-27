<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600 mt-2">Overview of fiber core infrastructure</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <i data-lucide="database" class="w-6 h-6 text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Cores</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCores) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <i data-lucide="activity" class="w-6 h-6 text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Cores</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($activeCores) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-gray-100 rounded-lg">
                <i data-lucide="pause-circle" class="w-6 h-6 text-gray-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Inactive Cores</p>
                <p class="text-2xl font-bold text-gray-600">{{ number_format($inactiveCores) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-lg">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Problem Cores</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($problemCores) }}</p>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isSuperAdmin() && $regionalData->count() > 0)
<!-- Regional Overview -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Regional Overview</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($regionalData as $region)
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-900">{{ $region->region }}</h3>
                <div class="mt-2 space-y-1">
                    <p class="text-sm text-gray-600">Cables: {{ $region->total_cables }}</p>
                    <p class="text-sm text-gray-600">Total Cores: {{ number_format($region->total_cores) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Cables Table -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-900">Fiber Cables</h2>
        <a href="{{ route('cables.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 flex items-center gap-2">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Add New Cable
        </a>
    </div>
    <div class="overflow-x-auto">
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
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cable->status === 'ok' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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

    <!-- Pagination Section -->
    @if($cables->hasPages())
    <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        {{ $cables->links() }}
    </div>
    @endif
</div>
@endsection
