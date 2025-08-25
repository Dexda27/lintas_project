@extends('layouts.app')

@section('title', 'Closure Details - ' . $closure->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $closure->name }} ({{ $closure->closure_id }})</h1>
            <p class="text-gray-600 text-sm">Joint Closure Detail & Connection Management</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="showConnectModal()" 
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 {{ $closure->available_capacity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $closure->available_capacity <= 0 ? 'disabled' : '' }}>
                Connect Cores
            </button>
            <a href="{{ route('closures.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back
            </a>
        </div>
    </div>
</div>

<!-- Closure Info + Capacity -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <h2 class="text-lg font-semibold mb-4 text-gray-800">Closure Information</h2>
    
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
        <div>
            <dt class="text-gray-500">Closure ID</dt>
            <dd class="text-gray-900">{{ $closure->closure_id }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Name</dt>
            <dd class="text-gray-900">{{ $closure->name }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Location</dt>
            <dd class="text-gray-900">{{ $closure->location }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Region</dt>
            <dd class="text-gray-900">{{ $closure->region }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Capacity</dt>
            <dd class="text-gray-900">{{ $closure->used_capacity }} / {{ $closure->capacity }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Status</dt>
            <dd>
                <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                    {{ $closure->status === 'ok' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $closure->status)) }}
                </span>
            </dd>
        </div>
    </dl>

    <!-- Progress Bar -->
    <div class="mt-4">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
            <span>Usage</span>
            <span>{{ $closure->available_capacity }} available</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                 style="width: {{ $closure->capacity > 0 ? ($closure->used_capacity / $closure->capacity) * 100 : 0 }}%">
            </div>
        </div>
    </div>
</div>

<!-- Active Connections -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Active Connections ({{ $closure->coreConnections->count() }})</h2>
    </div>
    <div class="overflow-x-auto">
        @if($closure->coreConnections->count() > 0)
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Core A</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Core B</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Splice Loss</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($closure->coreConnections as $connection)
                <tr>
                    <td class="px-4 py-2">#{{ $connection->id }}</td>
                    <td class="px-4 py-2">
                        <p class="font-medium">{{ $connection->coreA->cable->name }}</p>
                        <p class="text-xs text-gray-500">
                            T{{ $connection->coreA->tube_number }}C{{ $connection->coreA->core_number }} ({{ $connection->coreA->cable->cable_id }})
                        </p>
                    </td>
                    <td class="px-4 py-2">
                        <p class="font-medium">{{ $connection->coreB->cable->name }}</p>
                        <p class="text-xs text-gray-500">
                            T{{ $connection->coreB->tube_number }}C{{ $connection->coreB->core_number }} ({{ $connection->coreB->cable->cable_id }})
                        </p>
                    </td>
                    <td class="px-4 py-2">{{ $connection->splice_loss ? $connection->splice_loss . ' dB' : '-' }}</td>
                    <td class="px-4 py-2">{{ $connection->description ?: '-' }}</td>
                    <td class="px-4 py-2">
                        <button onclick="disconnectConnection({{ $connection->id }})" class="text-red-600 hover:text-red-900">Disconnect</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-gray-500">
            <p class="mt-4">No connections found. Connect cores to create fiber optic connections.</p>
        </div>
        @endif
    </div>
</div>

<!-- Connect Cores Modal -->
{{-- @include('partials.connect-modal', ['closure' => $closure, 'availableCores' => $availableCores]) --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const coreASelect = document.getElementById('core_a_id');
    const coreBSelect = document.getElementById('core_b_id');
    
    function updateCoreOptions() {
        const selectedCableA = coreASelect.options[coreASelect.selectedIndex]?.dataset.cable;
        const selectedCableB = coreBSelect.options[coreBSelect.selectedIndex]?.dataset.cable;
        
        Array.from(coreBSelect.options).forEach(option => {
            option.disabled = option.dataset.cable === selectedCableA;
        });
        Array.from(coreASelect.options).forEach(option => {
            option.disabled = option.dataset.cable === selectedCableB;
        });
    }
    
    coreASelect.addEventListener('change', updateCoreOptions);
    coreBSelect.addEventListener('change', updateCoreOptions);
});

function showConnectModal() {
    document.getElementById('connect-modal').classList.remove('hidden');
}

function closeConnectModal() {
    document.getElementById('connect-modal').classList.add('hidden');
    document.getElementById('connect-form').reset();
}

function disconnectConnection(connectionId) {
    if (!confirm('Are you sure you want to disconnect this core connection?')) return;

    fetch(`/connections/${connectionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
        else alert('Error: ' + (data.message || 'Unknown error'));
    })
    .catch(() => alert('Error disconnecting cores'));
}
</script>
@endsection
