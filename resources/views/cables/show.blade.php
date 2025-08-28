@extends('layouts.app')

@section('title', 'Cable Details - ' . $cable->name)

@push('scripts')
<script>
    window.currentCableId = {
        {
            $cable - > id
        }
    };
</script>
<script src="{{ asset('js/manage-cores.js') }}"></script>
@endpush

@section('content')

{{-- Header --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $cable->name }}</h1>
        <p class="text-gray-800 font-medium mt-2">Cable ID: {{ $cable->cable_id }}</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('cables.edit', $cable) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Edit Cable</a>
        <a href="{{ route('cables.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Back to List</a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition border-t-4 border-blue-500">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-500 text-white p-3 rounded-lg">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Total Cores</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_cores'] }}</p>
            </div>
        </div>
    </div>

    <!-- Active Cores -->
    <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition border-t-4 border-green-500">
        <div class="flex items-center space-x-4">
            <div class="bg-green-500 text-white p-3 rounded-lg">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Active Cores</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $statistics['active_cores'] }}</p>
            </div>
        </div>
    </div>

    <!-- Inactive Cores -->
    <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition border-t-4 border-gray-500">
        <div class="flex items-center space-x-4">
            <div class="bg-gray-500 text-white p-3 rounded-lg">
                <i data-lucide="circle-minus" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Inactive Cores</p>
                <p class="text-2xl font-bold text-gray-600">{{ $statistics['inactive_cores'] }}</p>
            </div>
        </div>
    </div>

    <!-- Problems Cores -->
    <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition border-t-4 border-red-500">
        <div class="flex items-center space-x-4">
            <div class="bg-red-500 text-white p-3 rounded-lg">
                <i data-lucide="triangle-alert" class="w-6 h-6"></i>
            </div>
            <div>
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
            <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition border-t-7 border-blue-900">
                <div class="space-y-6">
                    <div class="space-x-4 text-center">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Source Site</p>
                            <p class="text-lg font-semibold text-blue-900">{{ $cable->source_site }}</p>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="flex flex-col items-center space-y-2">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            <div class="h-8 w-0.5 bg-gray-500"></div>
                            <svg class="w-6 h-6 text-gray-500   " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class=" space-x-4">
                            <p class="text-sm font-medium text-gray-600 mb-1">Destination Site</p>
                            <p class="text-lg font-semibold text-green-900">{{ $cable->destination_site }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Core Management --}}
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Core Management</h2>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="flex flex-wrap gap-4 items-center">
            @foreach([
            ['tube-filter', 'Tube', 'All Tubes', range(1, $cable->total_tubes)],
            ['status-filter', 'Status', 'All Status', [['ok', 'OK'], ['not_ok', 'Not OK']]],
            ['usage-filter', 'Usage', 'All Usage', [['active', 'Active'], ['inactive', 'Inactive']]]
            ] as [$id, $label, $defaultOption, $options])
            <div>
                <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                <select id="{{ $id }}" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ $defaultOption }}</option>
                    @if($id === 'tube-filter')
                    @foreach($options as $tube)
                    <option value="{{ $tube }}">Tube {{ $tube }}</option>
                    @endforeach
                    @else
                    @foreach($options as $option)
                    <option value="{{ $option[0] }}">{{ $option[1] }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Core Colors --}}
    @php
    $coreColors = ['#0000ff', '#ff7f00', '#00ff00', '#964b00', '#808080', '#ffffff', '#ff0000', '#000000', '#ffff00', '#8f00ff', '#ff00ff', '#00ffff'];
    $getCoreColor = fn($coreNumber) => $coreColors[($coreNumber - 1) % 12];
    @endphp

    {{-- Cores by Tube --}}
    @foreach($coresByTube as $tubeNumber => $cores)
    <div class="bg-white rounded-lg shadow mb-6 tube-section" data-tube="{{ $tubeNumber }}">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-900">Tube {{ $tubeNumber }} ({{ $cores->count() }} cores)</h3>
            <div class="flex items-center space-x-4 text-sm">
                @foreach([
                ['Active', $cores->where('usage', 'active')->count(), 'green-500'],
                ['Inactive', $cores->where('usage', 'inactive')->count(), 'gray-400'],
                ['Problems', $cores->where('status', 'not_ok')->count(), 'red-500']
                ] as [$label, $count, $color])
                <span class="flex items-center">
                    <span class="w-3 h-3 bg-{{ $color }} rounded-full mr-2"></span>{{ $label }}: {{ $count }}
                </span>
                @endforeach
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($cores as $core)
                <div class="core-card border rounded-lg p-4 hover:shadow-md transition-shadow bg-gradient-to-b from-white to-gray-50"
                    data-tube="{{ $core->tube_number }}"
                    data-status="{{ $core->status }}"
                    data-usage="{{ $core->usage }}"
                    data-core="{{ $core->id }}"
                    data-description="{{ $core->description }}">

                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">Core {{ $core->core_number }}</h4>
                            <p class="text-xs text-gray-500 mt-1">Tube {{ $core->tube_number }}</p>
                        </div>
                        <div class="flex space-x-1">
                            <div class="w-3 h-3 rounded-full border" style="background-color: {{ $getCoreColor($core->core_number) }}"></div>
                            <span class="w-3 h-3 rounded-full {{ $core->status === 'ok' ? 'bg-green-500' : 'bg-red-500' }}" title="Status: {{ ucfirst(str_replace('_', ' ', $core->status)) }}"></span>
                            <span class="w-3 h-3 rounded-full {{ $core->usage === 'active' ? 'bg-blue-500' : 'bg-gray-400' }}" title="Usage: {{ ucfirst($core->usage) }}"></span>
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
                        @php $connectedCore = $core->connection->coreA->id === $core->id ? $core->connection->coreB : $core->connection->coreA; @endphp
                        <div class="mt-2 p-2 bg-blue-50 rounded text-xs border-l-4 border-blue-400">
                            <p class="font-medium text-blue-800 border-b border-blue-300 pb-1 mb-2">Connected to:</p>
                            <div class="space-y-1 text-blue-700">
                                <p class="font-medium">{{ $connectedCore->cable->cable_id }} | {{ $connectedCore->cable->name }}</p>
                                <p class="font-medium">Via: {{ $core->connection->closure->closure_id }} | {{ $core->connection->closure->name ?? $core->connection->closure->closure_id }}</p>
                                <p>Tube {{ $connectedCore->tube_number }} Core {{ $connectedCore->core_number }}</p>
                                @if($core->connection->connection_type)
                                <p>Type: {{ ucfirst($core->connection->connection_type) }}</p>
                                @endif
                                @if($core->connection->loss)
                                <p>Loss: {{ $core->connection->loss }} dB</p>
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
                        <button onclick="openCoreEditModal({{ $core->id }})" class="flex-1 px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Edit</button>
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

{{-- Join Core Modal --}}
<div id="join-core-modal" class="fixed inset-0 backdrop-blur-xs hidden">
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

{{-- Core Edit Modal --}}
<div id="core-edit-modal" class="fixed inset-0 backdrop-blur-xs hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
            <div class="px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Edit Core Information</h3>
                </div>
            </div>

            <div class="p-6">

                <form id="core-edit-form" class="space-y-4">
                    <input type="hidden" id="edit-core-id">

                    <div>
                        <label for="edit-core-status" class="block text-sm font-medium mb-1">Status</label>
                        <select id="edit-core-status" class="w-full px-3 py-2 border rounded-md">
                            <option value="ok">OK</option>
                            <option value="not_ok">Not OK</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit-core-usage" class="block text-sm font-medium mb-1">Usage</label>
                        <select id="edit-core-usage" class="w-full px-3 py-2 border rounded-md">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit-core-attenuation" class="block text-sm font-medium mb-1">Attenuation (dB)</label>
                        <input type="number" id="edit-core-attenuation" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2 border rounded-md">
                    </div>

                    <div>
                        <label for="edit-core-description" class="block text-sm font-medium mb-1">Description</label>
                        <textarea id="edit-core-description" rows="3" placeholder="Enter core description or notes..." class="w-full px-3 py-2 border rounded-md resize-none"></textarea>
                    </div>
                </form>

                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeCoreEditModal()" class="px-4 py-2 border rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" form="core-edit-form" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Core
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
