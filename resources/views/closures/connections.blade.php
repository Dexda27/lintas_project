@extends('layouts.app')

@section('title', 'Manage Connections - ' . $closure->name)

@push('scripts')
<script src="{{ asset('js/jc-connection.js') }}"></script>
@endpush

@section('content')
<div class="mb-6 md:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Manage Connections</h1>
            <p class="text-gray-600 mt-1 md:mt-2 break-words">{{ $closure->name }} ({{ $closure->closure_id }})</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <button onclick="showConnectModal()"
                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm {{ $closure->available_capacity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ $closure->available_capacity <= 0 ? 'disabled' : '' }}>
                Connect Cores
            </button>
            <a href="{{ route('closures.edit', $closure) }}"
                class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 text-center text-sm">
                Edit Closure
            </a>
            <a href="{{ route('closures.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-center text-sm">
                Back to List
            </a>

        </div>
    </div>
</div>

<!-- Capacity Info -->
<div class="bg-white rounded-lg shadow p-4 md:p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4 text-gray-800">Closure Information</h2>

    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-3 text-sm">
        <div>
            <dt class="text-gray-500 font-medium">Closure ID</dt>
            <dd class="text-gray-900 break-words">{{ $closure->closure_id }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Name</dt>
            <dd class="text-gray-900 break-words">{{ $closure->name }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Location</dt>
            <dd class="text-gray-900 break-words">{{ $closure->location }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Region</dt>
            <dd class="text-gray-900 break-words">{{ $closure->region }}</dd>
        </div>


        <!-- Coordinates with Map Link -->
        @if(isset($closure->latitude) && isset($closure->longitude))
        <div>
            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Coordinates</dt>
            <dd class="mt-1">
                <button onclick="openMaps({{ $closure->latitude }}, {{ $closure->longitude }})"
                    class="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $closure->latitude }}, {{ $closure->longitude }}
                </button>
            </dd>
        </div>
        @endif

        <div>
            <dt class="text-gray-500 font-medium">Capacity</dt>
            <dd class="text-gray-900">{{ $closure->used_capacity }} / {{ $closure->capacity }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Status</dt>
            <dd>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                    {{ $closure->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $closure->status)) }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Description</dt>
            <dd class="text-gray-900">{{ $closure->description }}</dd>
        </div>
    </dl>

    <!-- Progress Bar -->
    <div class="mt-6">
        <div class="flex justify-between text-xs text-gray-500 mb-2">
            <span>Usage</span>
            <span>{{ $closure->available_capacity }} available</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                style="width: {{ $closure->capacity > 0 ? ($closure->used_capacity / $closure->capacity) * 100 : 0 }}%">
            </div>
        </div>
    </div>
</div>

<!-- Active Connections -->
<div class="bg-white rounded-lg shadow">
    <div class="px-4 md:px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Active Connections ({{ $closure->coreConnections->count() }})</h2>
    </div>

    @if($closure->coreConnections->count() > 0)
    <!-- Mobile View - Cards -->
    <div class="block lg:hidden">
        <div class="divide-y divide-gray-200">
            @foreach($closure->coreConnections as $connection)
            <div class="p-4 space-y-4">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-900">Connection #{{ $connection->id }}</span>
                    <button onclick="disconnectConnection({{ $connection->id }})"
                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                        Disconnect
                    </button>
                </div>

                <div class="space-y-3">
                    <!-- Core A -->
                    <div class="bg-blue-50 rounded-lg p-3">
                        <label class="text-xs font-medium text-blue-700 uppercase tracking-wider">Core A</label>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $connection->coreA->cable->name }}</p>
                        <p class="text-xs text-gray-600">
                            T{{ $connection->coreA->tube_number }}C{{ $connection->coreA->core_number }}
                            ({{ $connection->coreA->cable->cable_id }})
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $connection->coreA->cable->source_site }} → {{ $connection->coreA->cable->destination_site }}
                        </p>
                    </div>

                    <!-- Core B -->
                    <div class="bg-green-50 rounded-lg p-3">
                        <label class="text-xs font-medium text-green-700 uppercase tracking-wider">Core B</label>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $connection->coreB->cable->name }}</p>
                        <p class="text-xs text-gray-600">
                            T{{ $connection->coreB->tube_number }}C{{ $connection->coreB->core_number }}
                            ({{ $connection->coreB->cable->cable_id }})
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $connection->coreB->cable->source_site }} → {{ $connection->coreB->cable->destination_site }}
                        </p>
                    </div>

                    <!-- Connection Details -->
                    <div class="flex justify-between pt-2 border-t border-gray-200">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Splice Loss</label>
                            <p class="text-sm text-gray-900 font-medium">
                                {{ $connection->splice_loss ? $connection->splice_loss . ' dB' : '-' }}
                            </p>
                        </div>
                        <div class="text-right max-w-xs">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</label>
                            <p class="text-sm text-gray-900 break-words">
                                {{ $connection->description ?: '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Desktop View - Table -->
    <div class="hidden lg:block overflow-x-auto">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                        {{ $connection->splice_loss ? $connection->splice_loss . ' dB' : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <span class="line-clamp-2 max-w-xs">
                            {{ $connection->description ?: '-' }}
                        </span>
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
    </div>
    @else
    <div class="p-6 md:p-8 text-center text-gray-500">
        <div class="max-w-sm mx-auto">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <p class="text-base font-medium">No connections found</p>
            <p class="text-sm mt-1">Connect cores to create fiber optic connections</p>
        </div>
    </div>
    @endif
</div>

<!-- Enhanced Connect Cores Modal - Multi-Connection Support -->
<div id="connect-modal" class="fixed inset-0 backdrop-blur-xs hidden z-50" onclick="if(event.target === this) closeConnectModal()">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] flex flex-col">

            <!-- Modal Header - Sticky -->
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 sticky top-0 bg-white rounded-t-lg z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Connect Cores</h3>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Create multiple connections at once. Click "Add Connection" for more.</p>
                    </div>
                    <button onclick="closeConnectModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Hidden field for closure ID -->
            <input type="hidden" id="current-closure-id" value="{{ $closure->id }}">

            <!-- Modal Content - Scrollable -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6">

                <!-- Connection Rows Container -->
                <div id="connection-rows-container" class="space-y-4">
                    <!-- Connection rows will be added dynamically by JavaScript -->
                </div>

                <!-- Add Connection Button -->
                <div class="mt-4">
                    <button type="button" id="add-connection-btn"
                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="font-medium text-sm sm:text-base">Add Another Connection</span>
                    </button>
                </div>

            </div>

            <!-- Modal Actions - Sticky Footer -->
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200 sticky bottom-0 bg-white rounded-b-lg z-10">
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3">
                    <button type="button"
                        onclick="closeConnectModal()"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm font-medium">
                        Cancel
                    </button>
                    <button type="button" id="submit-connection"
                        onclick="jcConnectionManager.submitConnections()"
                        class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        Create Connection
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Google Maps Integration -->
<script>
    // Available cables data
    window.availableCablesData = @json($availableCables);

    /**
     * Opens Google Maps with the given coordinates
     * Works on both mobile and desktop devices
     */
    function openMaps(latitude, longitude) {
        // Google Maps URL that works universally
        const googleMapsUrl = `https://maps.google.com/?q=${latitude},${longitude}&z=16`;

        // Check if user is on mobile device
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        if (isMobile) {
            // For mobile devices, try to open Google Maps app first
            if (/Android/i.test(navigator.userAgent)) {
                // Android - try Google Maps app first, fallback to web
                const googleMapsApp = `geo:${latitude},${longitude}?q=${latitude},${longitude}&z=16`;

                // Create a temporary link to try opening the app
                const tempLink = document.createElement('a');
                tempLink.href = googleMapsApp;
                tempLink.style.display = 'none';
                document.body.appendChild(tempLink);

                // Try to trigger the app
                tempLink.click();
                document.body.removeChild(tempLink);

                // Fallback to web version after a short delay
                setTimeout(() => {
                    window.open(googleMapsUrl, '_blank');
                }, 1500);

            } else if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                // iOS - open Google Maps web version (most reliable)
                window.open(googleMapsUrl, '_blank');
            } else {
                // Other mobile devices
                window.open(googleMapsUrl, '_blank');
            }
        } else {
            // Desktop - open Google Maps in new tab
            window.open(googleMapsUrl, '_blank');
        }
    }

    // Initialize connection manager when modal opens
    document.addEventListener('DOMContentLoaded', function() {
        // Override showConnectModal to initialize first row
        const originalShowConnectModal = window.showConnectModal;
        window.showConnectModal = function() {
            if (originalShowConnectModal) {
                originalShowConnectModal();
            }
            // Add first connection row if container is empty
            setTimeout(() => {
                const container = document.getElementById('connection-rows-container');
                if (container && container.children.length === 0) {
                    jcConnectionManager.addConnectionRow();
                }
            }, 100);
        };
    });
</script>

@endsection
