@extends('layouts.app')

@section('title', 'Manage Connections - ' . $closure->name)

@push('scripts')
<script src="{{ asset('js/jc-connection.js') }}"></script>
@endpush

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Connections</h1>
            <p class="text-gray-600 mt-2">{{ $closure->name }} ({{ $closure->closure_id }})</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="showConnectModal()" 
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 {{ $closure->available_capacity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $closure->available_capacity <= 0 ? 'disabled' : '' }}>
                Connect Cores
            </button>
            <a href="{{ route('closures.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Capacity Info -->
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
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Active Connections ({{ $closure->coreConnections->count() }})</h2>
    </div>
    <div class="overflow-x-auto">
        @if($closure->coreConnections->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Core A</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Core B</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Splice Loss</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($closure->coreConnections as $connection)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>
                            <p class="font-medium">{{ $connection->coreA->cable->name }}</p>
                            <p class="text-xs text-gray-500">
                                T{{ $connection->coreA->tube_number }}C{{ $connection->coreA->core_number }} 
                                ({{ $connection->coreA->cable->cable_id }})
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $connection->coreA->cable->source_site }} → {{ $connection->coreA->cable->destination_site }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>
                            <p class="font-medium">{{ $connection->coreB->cable->name }}</p>
                            <p class="text-xs text-gray-500">
                                T{{ $connection->coreB->tube_number }}C{{ $connection->coreB->core_number }} 
                                ({{ $connection->coreB->cable->cable_id }})
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $connection->coreB->cable->source_site }} → {{ $connection->coreB->cable->destination_site }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $connection->splice_loss ? $connection->splice_loss . ' dB' : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $connection->description ?: '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="disconnectConnection({{ $connection->id }})" 
                                class="text-red-600 hover:text-red-900">
                            Disconnect
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-8 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <p class="mt-4">No connections found</p>
            <p class="text-sm">Connect cores to create fiber optic connections</p>
        </div>
        @endif
    </div>
</div>

<!-- Connect Cores Modal -->
<div id="connect-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Connect Cores</h3>
                <p class="text-sm text-gray-600 mt-1">Select two cores from different cables to create a connection</p>
            </div>
            
            <form id="connect-form" method="POST" action="{{ route('closures.connect', $closure) }}" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Core A Selection -->
                    <div>
                        <label for="core_a_id" class="block text-sm font-medium text-gray-700 mb-2">Select Core A</label>
                        <select id="core_a_id" 
                                name="core_a_id" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Choose first core...</option>
                            @foreach($availableCores as $cableId => $cores)
                                @php $cable = $cores->first()->cable; @endphp
                                <optgroup label="{{ $cable->name }} ({{ $cable->cable_id }})">
                                    @foreach($cores as $core)
                                        <option value="{{ $core->id }}" data-cable="{{ $cableId }}">
                                            Tube {{ $core->tube_number }}, Core {{ $core->core_number }}
                                            @if($core->attenuation) - {{ $core->attenuation }}dB @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Core B Selection -->
                    <div>
                        <label for="core_b_id" class="block text-sm font-medium text-gray-700 mb-2">Select Core B</label>
                        <select id="core_b_id" 
                                name="core_b_id" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Choose second core...</option>
                            @foreach($availableCores as $cableId => $cores)
                                @php $cable = $cores->first()->cable; @endphp
                                <optgroup label="{{ $cable->name }} ({{ $cable->cable_id }})">
                                    @foreach($cores as $core)
                                        <option value="{{ $core->id }}" data-cable="{{ $cableId }}">
                                            Tube {{ $core->tube_number }}, Core {{ $core->core_number }}
                                            @if($core->attenuation) - {{ $core->attenuation }}dB @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <!-- Splice Loss -->
                    <div>
                        <label for="splice_loss" class="block text-sm font-medium text-gray-700 mb-2">Splice Loss (dB)</label>
                        <input type="number" 
                               id="splice_loss" 
                               name="splice_loss" 
                               step="0.001" 
                               min="0" 
                               max="10"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., 0.15">
                    </div>
                    
                    <!-- Connection Description -->
                    <div>
                        <label for="connection_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" 
                               id="connection_description" 
                               name="description" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Connection notes...">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" 
                            onclick="closeConnectModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Create Connection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection