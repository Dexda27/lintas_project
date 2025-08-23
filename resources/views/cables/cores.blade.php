<!-- resources/views/cables/cores.blade.php -->
@extends('layouts.app')

@section('title', 'Manage Cores - ' . $cable->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Cores</h1>
            <p class="text-gray-600 mt-2">{{ $cable->name }} ({{ $cable->cable_id }})</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('cables.show', $cable) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Cable Details
            </a>
            <a href="{{ route('cables.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Filter and Search -->
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <div class="flex flex-wrap gap-4 items-center">
        <div>
            <label for="tube-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Tube</label>
            <select id="tube-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Tubes</option>
                @for($i = 1; $i <= $cable->total_tubes; $i++)
                    <option value="{{ $i }}">Tube {{ $i }}</option>
                @endfor
            </select>
        </div>
        
        <div>
            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
            <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="ok">OK</option>
                <option value="not_ok">Not OK</option>
            </select>
        </div>
        
        <div>
            <label for="usage-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Usage</label>
            <select id="usage-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Usage</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        
        <div class="flex-1">
            <label for="search-core" class="block text-sm font-medium text-gray-700 mb-1">Search Core</label>
            <input type="text" 
                   id="search-core" 
                   placeholder="Search by core number or description..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="flex items-end">
            <button id="clear-filters" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                Clear Filters
            </button>
        </div>
    </div>
</div>

<!-- Cores by Tube -->
@foreach($coresByTube as $tubeNumber => $cores)
<div class="bg-white rounded-lg shadow mb-6 tube-section" data-tube="{{ $tubeNumber }}">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-900">Tube {{ $tubeNumber }} ({{ $cores->count() }} cores)</h2>
        <div class="flex items-center space-x-4 text-sm">
            <span class="flex items-center">
                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                Active: {{ $cores->where('usage', 'active')->count() }}
            </span>
            <span class="flex items-center">
                <span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                Inactive: {{ $cores->where('usage', 'inactive')->count() }}
            </span>
            <span class="flex items-center">
                <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                Problems: {{ $cores->where('status', 'not_ok')->count() }}
            </span>
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($cores as $core)
            <div class="core-card border rounded-lg p-4 hover:shadow-md transition-shadow"
                 data-tube="{{ $core->tube_number }}"
                 data-status="{{ $core->status }}"
                 data-usage="{{ $core->usage }}"
                 data-core="{{ $core->core_number }}"
                 data-description="{{ $core->description }}">
                
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">Core {{ $core->core_number }}</h3>
                        <p class="text-xs text-gray-500">Tube {{ $core->tube_number }}</p>
                    </div>
                    <div class="flex space-x-1">
                        <span class="w-3 h-3 rounded-full {{ $core->status === 'ok' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        <span class="w-3 h-3 rounded-full {{ $core->usage === 'active' ? 'bg-blue-500' : 'bg-gray-400' }}"></span>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium {{ $core->status === 'ok' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst(str_replace('_', ' ', $core->status)) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Usage:</span>
                        <span class="font-medium {{ $core->usage === 'active' ? 'text-blue-600' : 'text-gray-600' }}">
                            {{ ucfirst($core->usage) }}
                        </span>
                    </div>
                    
                    @if($core->attenuation)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Attenuation:</span>
                        <span class="font-medium">{{ $core->attenuation }} dB</span>
                    </div>
                    @endif
                    
                    @if($core->connection)
                    <div class="mt-2 p-2 bg-blue-50 rounded text-xs">
                        <p class="font-medium text-blue-800">Connected to:</p>
                        @php
                            $connectedCore = $core->connection->coreA->id === $core->id ? $core->connection->coreB : $core->connection->coreA;
                        @endphp
                        <p class="text-blue-600">
                            {{ $connectedCore->cable->name }} - T{{ $connectedCore->tube_number }}C{{ $connectedCore->core_number }}
                        </p>
                    </div>
                    @endif
                    
                    @if($core->description)
                    <div class="mt-2">
                        <p class="text-xs text-gray-600">{{ $core->description }}</p>
                    </div>
                    @endif
                </div>
                
                <div class="mt-4 flex space-x-2">
                    <button onclick="editCore({{ $core->id }})" 
                            class="flex-1 px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                        Edit
                    </button>
                    @if($core->connection)
                    <button onclick="disconnectCore({{ $core->connection->id }})" 
                            class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                        Disconnect
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

<!-- Edit Core Modal -->
<div id="edit-core-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Core</h3>
            </div>
            
            <form id="edit-core-form" class="p-6">
                <input type="hidden" id="core-id">
                
                <div class="space-y-4">
                    <div>
                        <label for="core-status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="core-status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="ok">OK</option>
                            <option value="not_ok">Not OK</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="core-usage" class="block text-sm font-medium text-gray-700 mb-1">Usage</label>
                        <select id="core-usage" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="core-attenuation" class="block text-sm font-medium text-gray-700 mb-1">Attenuation (dB)</label>
                        <input type="number" 
                               id="core-attenuation" 
                               step="0.01" 
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., 0.25">
                    </div>
                    
                    <div>
                        <label for="core-description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="core-description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Additional notes about this core..."></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" 
                            onclick="closeEditModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update Core
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const tubeFilter = document.getElementById('tube-filter');
    const statusFilter = document.getElementById('status-filter');
    const usageFilter = document.getElementById('usage-filter');
    const searchCore = document.getElementById('search-core');
    const clearFilters = document.getElementById('clear-filters');

    function applyFilters() {
        const tubeValue = tubeFilter.value;
        const statusValue = statusFilter.value;
        const usageValue = usageFilter.value;
        const searchValue = searchCore.value.toLowerCase();

        const coreCards = document.querySelectorAll('.core-card');
        const tubeSections = document.querySelectorAll('.tube-section');

        // Hide all tube sections first
        tubeSections.forEach(section => {
            section.style.display = 'none';
        });

        let visibleCards = 0;
        const visibleTubes = new Set();

        coreCards.forEach(card => {
            const cardTube = card.dataset.tube;
            const cardStatus = card.dataset.status;
            const cardUsage = card.dataset.usage;
            const cardCore = card.dataset.core;
            const cardDescription = card.dataset.description?.toLowerCase() || '';

            let shouldShow = true;

            // Apply filters
            if (tubeValue && cardTube !== tubeValue) shouldShow = false;
            if (statusValue && cardStatus !== statusValue) shouldShow = false;
            if (usageValue && cardUsage !== usageValue) shouldShow = false;
            if (searchValue && !cardCore.includes(searchValue) && !cardDescription.includes(searchValue)) {
                shouldShow = false;
            }

            if (shouldShow) {
                card.style.display = 'block';
                visibleCards++;
                visibleTubes.add(cardTube);
            } else {
                card.style.display = 'none';
            }
        });

        // Show tube sections that have visible cards
        tubeSections.forEach(section => {
            const sectionTube = section.dataset.tube;
            if (visibleTubes.has(sectionTube)) {
                section.style.display = 'block';
            }
        });

        // Show message if no results
        if (visibleCards === 0) {
            // You can add a "no results" message here
            console.log('No cores match the current filters');
        }
    }

    // Add event listeners
    tubeFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    usageFilter.addEventListener('change', applyFilters);
    searchCore.addEventListener('input', debounce(applyFilters, 300));

    clearFilters.addEventListener('click', function() {
        tubeFilter.value = '';
        statusFilter.value = '';
        usageFilter.value = '';
        searchCore.value = '';
        applyFilters();
    });

    // Debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});

// Core management functions
function editCore(coreId) {
    // Find the core card
    const coreCard = document.querySelector(`[data-core-id="${coreId}"]`);
    if (!coreCard) {
        console.error('Core card not found');
        return;
    }

    // Get current values (you would typically fetch from server)
    document.getElementById('core-id').value = coreId;
    
    // Show modal
    document.getElementById('edit-core-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-core-modal').classList.add('hidden');
}

// Handle form submission
document.getElementById('edit-core-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const coreId = document.getElementById('core-id').value;
    const formData = new FormData();
    formData.append('status', document.getElementById('core-status').value);
    formData.append('usage', document.getElementById('core-usage').value);
    formData.append('attenuation', document.getElementById('core-attenuation').value);
    formData.append('description', document.getElementById('core-description').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('_method', 'PUT');

    fetch(`/cores/${coreId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated data
            location.reload();
        } else {
            alert('Error updating core: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating core');
    });
});

function disconnectCore(connectionId) {
    if (!confirm('Are you sure you want to disconnect this core connection?')) {
        return;
    }

    fetch(`/connections/${connectionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error disconnecting core: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error disconnecting core');
    });
}
</script>
@endsection