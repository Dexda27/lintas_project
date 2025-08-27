@extends('layouts.app')

@section('title', 'Closure Details - ' . $closure->name)

@section('content')
<div class="mb-6 md:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 break-words">
                {{ $closure->name }} ({{ $closure->closure_id }})
            </h1>
            <p class="text-gray-600 text-sm">Joint Closure Detail & Connection Management</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <button onclick="showConnectModal()"
                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm {{ $closure->available_capacity <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ $closure->available_capacity <= 0 ? 'disabled' : '' }}>
                Connect Cores
            </button>
            <a href="{{ route('closures.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-center text-sm">
                Back
            </a>
        </div>
    </div>
</div>

<!-- Closure Info + Capacity -->
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
        <div>
            <dt class="text-gray-500 font-medium">Capacity</dt>
            <dd class="text-gray-900">{{ $closure->used_capacity }} / {{ $closure->capacity }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Status</dt>
            <dd>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                    {{ $closure->status === 'ok' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $closure->status)) }}
                </span>
            </dd>
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
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <h2 class="text-lg font-semibold text-gray-900">
                Active Connections ({{ $closure->coreConnections->count() }})
            </h2>
        </div>
    </div>

    @if($closure->coreConnections->count() > 0)
    <!-- Mobile View - Cards -->
    <div class="block md:hidden">
        <div class="divide-y divide-gray-200">
            @foreach($closure->coreConnections as $connection)
            <div class="p-4 space-y-3">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-900">#{{ $connection->id }}</span>
                    <button onclick="disconnectConnection({{ $connection->id }})"
                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                        Disconnect
                    </button>
                </div>

                <div class="space-y-2">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Core A</label>
                        <p class="text-sm font-medium text-gray-900">{{ $connection->coreA->cable->name }}</p>
                        <p class="text-xs text-gray-500">
                            T{{ $connection->coreA->tube_number }}C{{ $connection->coreA->core_number }}
                            ({{ $connection->coreA->cable->cable_id }})
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Core B</label>
                        <p class="text-sm font-medium text-gray-900">{{ $connection->coreB->cable->name }}</p>
                        <p class="text-xs text-gray-500">
                            T{{ $connection->coreB->tube_number }}C{{ $connection->coreB->core_number }}
                            ({{ $connection->coreB->cable->cable_id }})
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Splice Loss</label>
                            <p class="text-sm text-gray-900">
                                {{ $connection->splice_loss ? $connection->splice_loss . ' dB' : '-' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</label>
                            <p class="text-sm text-gray-900">{{ $connection->description ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Desktop View - Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Core A</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Core B</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Splice Loss</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($closure->coreConnections as $connection)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">#{{ $connection->id }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">{{ $connection->coreA->cable->name }}</p>
                        <p class="text-xs text-gray-500">
                            T{{ $connection->coreA->tube_number }}C{{ $connection->coreA->core_number }}
                            ({{ $connection->coreA->cable->cable_id }})
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">{{ $connection->coreB->cable->name }}</p>
                        <p class="text-xs text-gray-500">
                            T{{ $connection->coreB->tube_number }}C{{ $connection->coreB->core_number }}
                            ({{ $connection->coreB->cable->cable_id }})
                        </p>
                    </td>
                    <td class="px-4 py-3">{{ $connection->splice_loss ? $connection->splice_loss . ' dB' : '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="truncate block max-w-xs" title="{{ $connection->description }}">
                            {{ $connection->description ?: '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <button onclick="disconnectConnection({{ $connection->id }})"
                            class="text-red-600 hover:text-red-900 font-medium">
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
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <p class="text-base font-medium">No connections found</p>
            <p class="text-sm mt-1">Connect cores to create fiber optic connections.</p>
        </div>
    </div>
    @endif
</div>

<!-- Connect Cores Modal -->
{{-- @include('partials.connect-modal', ['closure' => $closure, 'availableCores' => $availableCores]) --}}

@push('scripts')
<script src="{{ asset('js/jc-show.js') }}"></script>
@endpush
@endsection
