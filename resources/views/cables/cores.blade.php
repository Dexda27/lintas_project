@extends('layouts.app')

@section('title', 'Manage Cores - ' . $cable->name)

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Cores</h1>
            <p class="text-gray-600 mt-2">{{ $cable->name }} ({{ $cable->cable_id }})</p>
            <p class="text-sm text-gray-500 mt-1">{{ $cable->source_site }} → {{ $cable->destination_site }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('cables.show', $cable) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Cable Details</a>
            <a href="{{ route('cables.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Back to List</a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <div class="flex flex-wrap gap-4 items-center">
        <div>
            <label for="tube-filter" class="block text-sm font-medium text-gray-700 mb-1">Tube</label>
            <select id="tube-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Tubes</option>
                @for($i = 1; $i <= $cable->total_tubes; $i++)
                    <option value="{{ $i }}">Tube {{ $i }}</option>
                    @endfor
            </select>
        </div>
        <div>
            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="ok">OK</option>
                <option value="not_ok">Not OK</option>
            </select>
        </div>
        <div>
            <label for="usage-filter" class="block text-sm font-medium text-gray-700 mb-1">Usage</label>
            <select id="usage-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Usage</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <!-- <div class="flex-1">
            <label for="search-core" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" id="search-core" placeholder="Search by core number or description..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div> -->
        <div class="flex items-end">
            <button id="clear-filters" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Clear</button>
        </div>
    </div>
</div>

@php
$coreColors = ['#0000ff', '#ff7f00', '#00ff00', '#964b00', '#808080', '#ffffff', '#ff0000', '#000000', '#ffff00', '#8f00ff', '#ff00ff', '#00ffff'];
function getCoreColor($coreNumber, $colors) {
return $colors[($coreNumber - 1) % 12];
}
@endphp

<!-- Cores by Tube -->
@foreach($coresByTube as $tubeNumber => $cores)
<div class="bg-white rounded-lg shadow mb-6 tube-section" data-tube="{{ $tubeNumber }}">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-900">Tube {{ $tubeNumber }} ({{ $cores->count() }} cores)</h2>
        <div class="flex items-center space-x-4 text-sm">
            <span class="flex items-center"><span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>Active: {{ $cores->where('usage', 'active')->count() }}</span>
            <span class="flex items-center"><span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>Inactive: {{ $cores->where('usage', 'inactive')->count() }}</span>
            <span class="flex items-center"><span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>Problems: {{ $cores->where('status', 'not_ok')->count() }}</span>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($cores as $core)
            @php $coreColor = getCoreColor($core->core_number, $coreColors); @endphp

            <div class="core-card border-2 border-gray-400 rounded-lg p-4 hover:shadow-md transition-shadow bg-gradient-to-b from-white to-gray-50"

                data-tube="{{ $core->tube_number }}" data-status="{{ $core->status }}"
                data-usage="{{ $core->usage }}" data-core="{{ $core->core_number }}"
                data-description="{{ $core->description }}">

                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center space-x-2">

                        <div>
                            <h3 class="font-semibold text-gray-900">Core {{ $core->core_number }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Tube {{ $core->tube_number }}</p>
                        </div>

                    </div>
                    <div class="flex space-x-1">
                        <div class="w-3 h-3 rounded-full border-2 border-gray-400" style="background-color: {{ $coreColor }}"></div>
                        <span class="w-3 h-3 rounded-full {{ $core->status === 'ok' ? 'bg-green-500' : 'bg-red-500' }}"
                            title="Status: {{ ucfirst(str_replace('_', ' ', $core->status)) }}"></span>
                        <span class="w-3 h-3 rounded-full {{ $core->usage === 'active' ? 'bg-blue-500' : 'bg-gray-400' }}"
                            title="Usage: {{ ucfirst($core->usage) }}"></span>
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
                        <span class="font-medium {{ $core->usage === 'active' ? 'text-blue-600' : 'text-gray-600' }}">{{ ucfirst($core->usage) }}</span>
                    </div>
                    @if($core->attenuation)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Attenuation:</span>
                        <span class="font-medium">{{ $core->attenuation }} dB</span>
                    </div>
                    @endif

                    @if($core->connection)
                    <div class="mt-2 p-2 bg-blue-50 rounded text-xs border-l-4 border-blue-400">
                        <p class="font-medium text-blue-800">Connected to:</p>
                        @php $connectedCore = $core->connection->coreA->id === $core->id ? $core->connection->coreB : $core->connection->coreA; @endphp
                        <p class="text-blue-600">{{ $connectedCore->cable->name }} - T{{ $connectedCore->tube_number }}C{{ $connectedCore->core_number }}</p>
                    </div>
                    @endif

                    @if($core->description)
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 italic">{{ $core->description }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-4 flex space-x-2">
                    <button onclick="editCore({{ $core->id }})" class="flex-1 px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Edit</button>
                    @if($core->connection)
                    <button onclick="disconnectCore({{ $core->connection->id }})" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Disconnect</button>
                    @else
                    <button onclick="joinCore({{ $core->id }})" class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Join</button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

<!-- Join Core Modal -->
<div id="join-core-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Join Core to Connection</h3>
            </div>
            <form id="join-core-form" class="p-6">
                <input type="hidden" id="join-core-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Joint Closure (JC)</label>
                        <select id="jc-selection" class="w-full px-3 py-2 border rounded-md" required>
                            <option value="">Select JC...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Target Cable</label>
                        <select id="target-cable" class="w-full px-3 py-2 border rounded-md" required disabled>
                            <option value="">Select Cable...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Target Tube</label>
                        <select id="target-tube" class="w-full px-3 py-2 border rounded-md" required disabled>
                            <option value="">Select Tube...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Target Core</label>
                        <select id="target-core" class="w-full px-3 py-2 border rounded-md" required disabled>
                            <option value="">Select Core...</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Connection Type</label>
                            <select id="connection-type" class="w-full px-3 py-2 border rounded-md">
                                <option value="splice">Splice</option>
                                <option value="patch">Patch</option>
                                <option value="direct">Direct</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Loss (dB)</label>
                            <input type="number" id="connection-loss" step="0.01" min="0" class="w-full px-3 py-2 border rounded-md">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Notes</label>
                        <textarea id="connection-notes" rows="2" class="w-full px-3 py-2 border rounded-md"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeJoinModal()" class="px-4 py-2 border rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Create Connection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Core Modal -->
<div id="edit-core-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Edit Core</h3>
            </div>
            <form id="edit-core-form" class="p-6">
                <input type="hidden" id="core-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select id="core-status" class="w-full px-3 py-2 border rounded-md">
                            <option value="ok">OK</option>
                            <option value="not_ok">Not OK</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Usage</label>
                        <select id="core-usage" class="w-full px-3 py-2 border rounded-md">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Attenuation (dB)</label>
                        <input type="number" id="core-attenuation" step="0.01" min="0" class="w-full px-3 py-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <textarea id="core-description" rows="3" class="w-full px-3 py-2 border rounded-md"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Core</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Core management functions (harus di luar DOMContentLoaded agar bisa diakses dari onclick)
    function editCore(coreId) {
        document.getElementById('core-id').value = coreId;
        document.getElementById('edit-core-modal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('edit-core-modal').classList.add('hidden');
    }

    function joinCore(coreId) {
        document.getElementById('join-core-id').value = coreId;
        loadJCs();
        document.getElementById('join-core-modal').classList.remove('hidden');
    }

    function closeJoinModal() {
        document.getElementById('join-core-modal').classList.add('hidden');
        document.getElementById('join-core-form').reset();
        ['target-cable', 'target-tube', 'target-core'].forEach(id => {
            document.getElementById(id).disabled = true;
        });
    }

    function loadJCs() {
        fetch('/connections/joint-closures')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('jc-selection');
                select.innerHTML = '<option value="">Select JC...</option>';
                data.forEach(jc => {
                    const available = (jc.capacity - jc.used_capacity) || jc.available_capacity || 0;
                    select.innerHTML += `<option value="${jc.id}">${jc.name} (${jc.location}) - ${available}/${jc.capacity} available</option>`;
                });
            })
            .catch(error => console.error('Error loading JCs:', error));
    }

    function disconnectCore(connectionId) {
        if (!confirm('Are you sure you want to disconnect this core connection?')) return;

        fetch(`/connections/${connectionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error disconnecting core');
            });
    }

    // Filter functionality dan event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const filters = {
            tube: document.getElementById('tube-filter'),
            status: document.getElementById('status-filter'),
            usage: document.getElementById('usage-filter')
            // Hapus search karena elemennya tidak ada
        };

        function applyFilters() {
            const values = {
                tube: filters.tube.value,
                status: filters.status.value,
                usage: filters.usage.value
                // Hapus search
            };

            const coreCards = document.querySelectorAll('.core-card');
            const tubeSections = document.querySelectorAll('.tube-section');
            const visibleTubes = new Set();

            tubeSections.forEach(section => section.style.display = 'none');

            coreCards.forEach(card => {
                const cardData = {
                    tube: card.dataset.tube,
                    status: card.dataset.status,
                    usage: card.dataset.usage,
                    core: card.dataset.core,
                    description: (card.dataset.description || '').toLowerCase()
                };

                const shouldShow = (!values.tube || cardData.tube === values.tube) &&
                    (!values.status || cardData.status === values.status) &&
                    (!values.usage || cardData.usage === values.usage);
                    // Hapus search filter

                card.style.display = shouldShow ? 'block' : 'none';
                if (shouldShow) visibleTubes.add(cardData.tube);
            });

            tubeSections.forEach(section => {
                if (visibleTubes.has(section.dataset.tube)) section.style.display = 'block';
            });
        }

        Object.values(filters).forEach(filter => {
            if (filter) { // Pastikan elemen ada
                filter.addEventListener('change', applyFilters);
            }
        });

        const clearButton = document.getElementById('clear-filters');
        if (clearButton) {
            clearButton.addEventListener('click', () => {
                Object.values(filters).forEach(filter => {
                    if (filter) filter.value = '';
                });
                applyFilters();
            });
        }

        // Event handlers for cascading dropdowns
        const jcSelection = document.getElementById('jc-selection');
        if (jcSelection) {
            jcSelection.addEventListener('change', function() {
                const jcId = this.value;
                const cableSelect = document.getElementById('target-cable');

                if (jcId) {
                    fetch(`/connections/joint-closures/${jcId}/cables`)
                        .then(response => response.json())
                        .then(data => {
                            cableSelect.innerHTML = '<option value="">Select Cable...</option>';
                            data.forEach(cable => {
                                // Perbaiki syntax error di sini
                                if (cable.id !== {{ $cable->id }}) {
                                    cableSelect.innerHTML += `<option value="${cable.id}">${cable.name} (${cable.cable_id})</option>`;
                                }
                            });
                            cableSelect.disabled = false;
                        });
                } else {
                    cableSelect.disabled = true;
                }

                ['target-tube', 'target-core'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.disabled = true;
                });
            });
        }

        const targetCable = document.getElementById('target-cable');
        if (targetCable) {
            targetCable.addEventListener('change', function() {
                const cableId = this.value;
                const tubeSelect = document.getElementById('target-tube');

                if (cableId) {
                    fetch(`/connections/cables/${cableId}/tubes`)
                        .then(response => response.json())
                        .then(data => {
                            tubeSelect.innerHTML = '<option value="">Select Tube...</option>';
                            for (let i = 1; i <= data.total_tubes; i++) {
                                tubeSelect.innerHTML += `<option value="${i}">Tube ${i}</option>`;
                            }
                            tubeSelect.disabled = false;
                        });
                } else {
                    tubeSelect.disabled = true;
                }

                const targetCore = document.getElementById('target-core');
                if (targetCore) targetCore.disabled = true;
            });
        }

        const targetTube = document.getElementById('target-tube');
        if (targetTube) {
            targetTube.addEventListener('change', function() {
                const cableId = document.getElementById('target-cable').value;
                const tubeNumber = this.value;
                const coreSelect = document.getElementById('target-core');

                if (cableId && tubeNumber) {
                    fetch(`/connections/cables/${cableId}/tubes/${tubeNumber}/cores`)
                        .then(response => response.json())
                        .then(data => {
                            coreSelect.innerHTML = '<option value="">Select Core...</option>';
                            data.forEach(core => {
                                coreSelect.innerHTML += `<option value="${core.id}" ${core.status !== 'ok' ? 'style="color: #ef4444"' : ''}>Core ${core.core_number}${core.status !== 'ok' ? ' (!)' : ''}</option>`;
                            });
                            coreSelect.disabled = false;
                        });
                } else {
                    coreSelect.disabled = true;
                }
            });
        }

        // Form submissions
        const joinForm = document.getElementById('join-core-form');
        if (joinForm) {
            joinForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData();
                ['join-core-id', 'target-core', 'jc-selection', 'connection-type', 'connection-loss', 'connection-notes'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        const name = id.replace('join-core-id', 'source_core_id')
                            .replace('target-core', 'target_core_id')
                            .replace('jc-selection', 'joint_closure_id')
                            .replace(/-/g, '_');
                        formData.append(name, element.value);
                    }
                });

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }

                fetch('/connections', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Connection created successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error creating connection');
                    });
            });
        }

        const editForm = document.getElementById('edit-core-form');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const coreId = document.getElementById('core-id').value;
                const formData = new FormData();
                ['core-status', 'core-usage', 'core-attenuation', 'core-description'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        const name = id.replace('core-', '');
                        formData.append(name, element.value);
                    }
                });

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                }
                formData.append('_method', 'PUT');

                fetch(`/cores/${coreId}`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating core');
                    });
            });
        }
    });
</script>
@endsection
