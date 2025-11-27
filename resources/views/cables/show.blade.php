@extends('layouts.app')

@section('title', 'Cable Details - ' . $cable->name)

@push('scripts')
<script>
    window.currentCableId = {{ $cable->id }};
</script>
<script src="{{ asset('js/manage-cores.js') }}"></script>
@endpush

@section('content')

{{-- Header --}}
<div class="mb-8">
    <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 break-words">{{ $cable->name }}</h1>
            <p class="text-gray-800 font-medium mt-2 text-sm sm:text-base break-all">Cable ID: {{ $cable->cable_id }}</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 flex-shrink-0">
            <a href="{{ route('cables.edit', $cable) }}"
                class="inline-flex items-center justify-center bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors duration-200 text-sm sm:text-base w-full sm:w-auto">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Cable
            </a>
            <a href="{{ route('cables.index') }}"
                class="inline-flex items-center justify-center bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors duration-200 text-sm sm:text-base w-full sm:w-auto">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
        </div>
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
                <p class="text-md font-semibold text-gray-600">Total Cores</p>
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
                <p class="text-md font-semibold text-gray-600">Active Cores</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['active_cores'] }}</p>
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
                <p class="text-md font-semibold text-gray-600">Inactive Cores</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['inactive_cores'] }}</p>
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
                <p class="text-md font-semibold text-gray-600">Problem Cores</p>
                <p class="text-2xl font-bold text-gray-900">{{ $statistics['problem_cores'] }}</p>
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
                    <p class="text-lg text-gray-900">{{ number_format($cable->otdr_length) }} meters</p>
                </div>
                @endif
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @if($cable->description)
                    <div class="pt-2">
                        <p class="text-sm font-medium text-gray-600">Description</p>
                        <p class="text-gray-900">{{ $cable->description }}</p>
                    </div>
                    @endif
                </div>
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
                            <i data-lucide="arrow-up-down" class="w-15 h-15 text-gray-600"></i>

                        </div>
                    </div>
                    <div class="text-center">
                        <div class=" space-x-4">
                            <p class="text-sm font-medium text-gray-600 mb-1">Destination Site</p>
                            <p class="text-lg font-semibold text-green-700">{{ $cable->destination_site }}</p>
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

                        @if($core->description)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Description:</span>
                            <span class="font-medium">{{ $core->description }}</span>
                        </div>
                        @endif


                        @php
                            $connections = \App\Models\CoreConnection::where('core_a_id', $core->id)
                                ->orWhere('core_b_id', $core->id)
                                ->with(['coreA.cable', 'coreB.cable', 'closure'])
                                ->get();
                        @endphp

                        @if($connections->count() > 0)
                        <div class="mt-2 space-y-2">
                            <p class="font-medium text-blue-800 text-xs mb-1">
                                Connected to {{ $connections->count() }} {{ $connections->count() === 1 ? 'core' : 'cores' }}:
                            </p>

                            @foreach($connections as $index => $connection)
                                @php
                                    $connectedCore = $connection->coreA->id === $core->id ? $connection->coreB : $connection->coreA;
                                @endphp
                                <div class="p-2 bg-blue-50 rounded text-xs border-l-4 border-blue-400">
                                    <div class="space-y-1 text-blue-700">
                                        <p class="font-medium">{{ $connectedCore->cable->cable_id }} | {{ $connectedCore->cable->name }}</p>
                                        <p class="font-semibold text-green-700">Via: {{ $connection->closure->closure_id }} | {{ $connection->closure->name ?? $connection->closure->closure_id }}</p>
                                        <p>Tube {{ $connectedCore->tube_number }} Core {{ $connectedCore->core_number }}</p>
                                        @if($connection->connection_type)
                                        <p>Type: {{ ucfirst($connection->connection_type) }}</p>
                                        @endif
                                        @if($connection->loss)
                                        <p>Loss: {{ $connection->loss }} dB</p>
                                        @endif
                                        @if($connection->notes)
                                        <p class="text-md text-gray-600">{{ $connection->notes }}</p>
                                        @endif
                                    </div>
                                    <button onclick="showDisconnectModal({{ $connection->id }}, '{{ $core->core_number }}', '{{ $core->tube_number }}', '{{ $connectedCore->cable->name }}', '{{ $connectedCore->core_number }}', '{{ $connectedCore->tube_number }}', '{{ $connection->closure->closure_id }}')"
                                        class="mt-2 w-full px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center justify-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Disconnect
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <button onclick="openCoreEditModal({{ $core->id }})" class="flex-1 px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Edit</button>
                        <button onclick="joinCore({{ $core->id }})" class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Join</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Disconnect Confirmation Modal --}}
<div id="disconnect-confirmation-modal" class="fixed inset-0 backdrop-blur-xs hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-red-900">Disconnect Core Connection</h3>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">You are about to disconnect the following connection:</p>

                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">Source:</span>
                            <span id="disconnect-source-info" class="text-gray-900"></span>
                        </div>
                        <div class="flex items-center justify-center py-2">
                            <div class="flex flex-col items-center">
                                <svg class="w-4 h-4 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                                <span id="disconnect-closure-info" class="text-xs font-semibold text-green-700"></span>
                                <svg class="w-4 h-4 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">Target:</span>
                            <span id="disconnect-target-info" class="text-gray-900"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Warning</p>
                            <p class="text-sm text-yellow-700 mt-1">This action will permanently remove the connection between these cores. This operation cannot be undone.</p>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="connection-to-disconnect">

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDisconnectModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmDisconnect()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Disconnect
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Join Core Modal --}}
<div id="join-core-modal" class="fixed inset-0 hidden items-center justify-center backdrop-blur-xs z-50 p-4">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Create Connection</h3>
            <button onclick="closeJoinModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="join-core-form" class="mt-4">
            @csrf
            <input type="hidden" id="join-core-id" name="core_id">

            {{-- Source Core Info --}}
            <div id="source-core-info" class="mb-4">
                <!-- Will be populated by JavaScript -->
            </div>

            {{-- Connection Rows Container - IMPORTANT! --}}
            <div id="connection-rows-container" class="space-y-4">
                <!-- Connection rows will be added here dynamically -->
            </div>

            {{-- Add Connection Button --}}
            <div class="mt-4">
                <button type="button" id="add-connection-btn" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Another Connection
                    </span>
                </button>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeJoinModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span>Create Connection</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Core Edit Modal --}}
<div id="core-edit-modal" class="fixed inset-0 hidden items-center justify-center bg-gray-600 bg-opacity-50 backdrop-blur-xs z-50 p-4" onclick="if(event.target === this) closeCoreEditModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto mx-auto">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b sticky top-0 bg-white z-10">
            <div class="flex justify-between items-center">
                <h3 class="text-base sm:text-lg font-semibold">Edit Core Information</h3>
                <button type="button" onclick="closeCoreEditModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
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

                <div>
                    <label for="edit-core-usage" class="block text-sm font-medium mb-1">Usage</label>
                    <select id="edit-core-usage" class="w-full px-3 py-2 text-sm sm:text-base border rounded-md focus:ring-2 focus:ring-blue-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div>
                    <label for="edit-core-attenuation" class="block text-sm font-medium mb-1">Attenuation (dB)</label>
                    <input type="number" id="edit-core-attenuation" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2 text-sm sm:text-base border rounded-md focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="edit-core-description" class="block text-sm font-medium mb-1">Description</label>
                    <textarea id="edit-core-description" rows="3" placeholder="Enter core description or notes..." class="w-full px-3 py-2 text-sm sm:text-base border rounded-md resize-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </form>

            <div class="mt-4 sm:mt-6 flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                <button type="button" onclick="closeCoreEditModal()" class="w-full sm:w-auto px-4 py-2 text-sm sm:text-base border rounded-md hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" form="core-edit-form" class="w-full sm:w-auto px-4 py-2 text-sm sm:text-base bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Core
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
