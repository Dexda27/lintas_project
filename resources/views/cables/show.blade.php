@extends('layouts.app')

@section('title', 'Cable Details - ' . $cable->name)

@push('scripts')
<script>
    // Pass cable ID to JavaScript
    window.currentCableId = {
        {
            $cable - > id
        }
    };
</script>
<script src="{{ asset('js/manage-cores.js') }}"></script>
@endpush

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $cable->name }}</h1>
            <p class="text-gray-800 font-medium mt-2">Cable ID: {{ $cable->cable_id }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('cables.edit', $cable) }}"
                class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                Edit Cable
            </a>
            <a href="{{ route('cables.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Cores</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_cores'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-emerald-100 rounded-lg">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Cores</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $statistics['active_cores'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-gray-100 rounded-lg">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Inactive Cores</p>
                <p class="text-2xl font-bold text-gray-600">{{ $statistics['inactive_cores'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-black-100 rounded-lg">
                <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Problem Cores</p>
                <p class="text-2xl font-bold text-black-600">{{ $statistics['problem_cores'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Cable and Route Information in Single Frame -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Cable & Route Information</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cable Information Column -->
            <div class="lg:col-span-2 space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Cable ID</p>
                        <p class="text-lg text-gray-900">{{ $cable->cable_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Region</p>
                        <span class="inline-block px-2 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $cable->region }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Tubes</p>
                        <p class="text-lg text-gray-900">{{ $cable->total_tubes }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Cores per Tube</p>
                        <p class="text-lg text-gray-900">{{ $cable->cores_per_tube }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Status</p>
                        <span
                            class="inline-block px-2 py-1 text-sm font-semibold rounded-full {{ $cable->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst($cable->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Usage</p>
                        <span
                            class="inline-block px-2 py-1 text-sm font-semibold rounded-full {{ $cable->usage === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($cable->usage) }}
                        </span>
                    </div>
                </div>

                @if($cable->otdr_length)
                <div class="pt-2">
                    <p class="text-sm font-medium text-gray-600">OTDR Length</p>
                    <p class="text-lg text-gray-900">{{ number_format($cable->otdr_length, 2) }} meters</p>
                </div>
                @endif

                @if($cable->description)
                <div class="pt-2">
                    <p class="text-sm font-medium text-gray-600">Description</p>
                    <p class="text-gray-900">{{ $cable->description }}</p>
                </div>
                @endif
            </div>

            <!-- Route Information Column -->
            <div class="lg:col-span-1 space-y-4">
                <div class="space-y-6">
                    <div class="text-center">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <p class="text-sm font-medium text-gray-600 mb-1">Source Site</p>
                            <p class="text-lg font-semibold text-blue-900">{{ $cable->source_site }}</p>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="flex flex-col items-center space-y-2">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            <div class="h-8 w-0.5 bg-gray-300"></div>
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <p class="text-sm font-medium text-gray-600 mb-1">Destination Site</p>
                            <p class="text-lg font-semibold text-green-900">{{ $cable->destination_site }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core Management Section -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Core Management</h2>

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
            <h3 class="text-xl font-semibold text-gray-900">Tube {{ $tubeNumber }} ({{ $cores->count() }} cores)</h3>
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

                <div class="core-card border border-gray-400 rounded-lg p-4 hover:shadow-md transition-shadow bg-gradient-to-b from-white to-gray-50"
                    data-tube="{{ $core->tube_number }}"
                    data-status="{{ $core->status }}"
                    data-usage="{{ $core->usage }}"
                    data-core="{{ $core->core_number }}"
                    data-description="{{ $core->description }}">

                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center space-x-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">Core {{ $core->core_number }}</h4>
                                <p class="text-xs text-gray-500 mt-1">Tube {{ $core->tube_number }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-1">
                            <div class="w-3 h-3 rounded-full border border-gray-400" style="background-color: {{ $coreColor }}"></div>
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
                            <p class="font-medium text-blue-800 border-b border-blue-300 pb-1 mb-2">Connected to:</p>

                            @php $connectedCore = $core->connection->coreA->id === $core->id ? $core->connection->coreB : $core->connection->coreA; @endphp
                            <div class="space-y-1">
                                <p class="text-blue-700 font-medium">{{ $connectedCore->cable->cable_id }} | {{ $connectedCore->cable->name }}</p>
                                <p class="text-blue-700 font-medium">Via : {{ $core->connection->closure->closure_id }} | {{ $core->connection->closure->name ?? $core->connection->closure->closure_id }} </p>

                                <p class="text-blue-600">Tube {{ $connectedCore->tube_number }} Core {{ $connectedCore->core_number }}</p>

                                @if($core->connection->connection_type)
                                <p class="text-blue-600">Type: {{ ucfirst($core->connection->connection_type) }}</p>
                                @endif
                                @if($core->connection->loss)
                                <p class="text-blue-600">Loss: {{ $core->connection->loss }} dB</p>
                                @endif
                                @if($core->connection->joint_closure_id)
                                <p class="text-gray-600">via JC: {{ $core->connection->jointClosure->name ?? 'JC-' . $core->connection->joint_closure_id }} (ID: {{ $core->connection->joint_closure_id }})</p>
                                @endif
                                @if($core->connection->notes)
                                <p class="text-gray-500 italic">{{ $core->connection->notes }}</p>
                                @endif
                            </div>
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
</div>

<!-- Join Core Modal -->
<div id="join-core-modal" class="fixed inset-0 backdrop-blur-md hidden">
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
<div id="edit-core-modal" class="fixed inset-0 backdrop-blur-md hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Edit Core</h3>
            </div>
            <form id="edit-core-form" class="p-6">
                <input type="hidden" id="core-id">
                <div class="space-y-4">
                    <!-- Connection Info -->
                    <div id="connection-info" class="hidden p-3 bg-blue-50 rounded-md border-l-4 border-blue-400">
                        <h4 class="font-medium text-blue-800 mb-2">Connection Information</h4>
                        <div class="space-y-1 text-sm">
                            <p id="connected-cable" class="text-blue-700"></p>
                            <p id="connected-core" class="text-blue-600"></p>
                            <p id="connected-jc" class="text-purple-600 font-medium"></p>
                            <p id="connected-jc-id" class="text-purple-600"></p>
                        </div>
                    </div>

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

@endsection
