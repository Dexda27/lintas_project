@extends('layouts.app')

@section('title', 'Splitter Details - ' . $splitter->name)

@section('content')
<div class="mb-6 md:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Splitter Details</h1>
            <p class="text-gray-600 mt-1 md:mt-2 break-words">{{ $splitter->name }} ({{ $splitter->splitter_id }})</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('splitters.edit', $splitter) }}"
                class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 text-center text-sm">
                Edit Splitter
            </a>
            <a href="{{ route('splitters.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-center text-sm">
                Back to List
            </a>
        </div>
    </div>
</div>

<!-- Splitter Information -->
<div class="bg-white rounded-lg shadow p-4 md:p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4 text-gray-800">Splitter Information</h2>

    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-3 text-sm">
        <div>
            <dt class="text-gray-500 font-medium">Splitter ID</dt>
            <dd class="text-gray-900 break-words">{{ $splitter->splitter_id }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Name</dt>
            <dd class="text-gray-900 break-words">{{ $splitter->name }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Location</dt>
            <dd class="text-gray-900 break-words">{{ $splitter->location }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Region</dt>
            <dd class="text-gray-900 break-words">{{ $splitter->region }}</dd>
        </div>

        <!-- Coordinates with Map Link -->
        @if(isset($splitter->latitude) && isset($splitter->longitude))
        <div>
            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Coordinates</dt>
            <dd class="mt-1">
                <button onclick="openMaps({{ $splitter->latitude }}, {{ $splitter->longitude }})"
                    class="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $splitter->latitude }}, {{ $splitter->longitude }}
                </button>
            </dd>
        </div>
        @endif

        <div>
            <dt class="text-gray-500 font-medium">Capacity</dt>
            <dd class="text-gray-900">{{ $splitter->used_capacity }} / {{ $splitter->capacity }}</dd>
        </div>
        <div>
            <dt class="text-gray-500 font-medium">Status</dt>
            <dd>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                    {{ $splitter->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $splitter->status)) }}
                </span>
            </dd>
        </div>
        @if($splitter->description)
        <div class="lg:col-span-3">
            <dt class="text-gray-500 font-medium">Description</dt>
            <dd class="text-gray-900 mt-1">{{ $splitter->description }}</dd>
        </div>
        @endif
    </dl>

    <!-- Progress Bar -->
    <div class="mt-6">
        <div class="flex justify-between text-xs text-gray-500 mb-2">
            <span>Usage</span>
            <span>{{ $statistics['available_capacity'] }} available</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                style="width: {{ $splitter->capacity > 0 ? ($splitter->used_capacity / $splitter->capacity) * 100 : 0 }}%">
            </div>
        </div>
    </div>
</div>

<!-- Capacity Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
    <!-- Total Capacity -->
    <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <div class="flex items-center">
            <div class="bg-blue-500 text-white p-3 rounded-lg mr-4">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Capacity</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $statistics['total_capacity'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Used Capacity -->
    <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <div class="flex items-center">
            <div class="bg-orange-500 text-white p-3 rounded-lg mr-4">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-medium">Used Capacity</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $statistics['used_capacity'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Available Capacity -->
    <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <div class="flex items-center">
            <div class="bg-green-500 text-white p-3 rounded-lg mr-4">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-medium">Available Capacity</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $statistics['available_capacity'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Google Maps Integration -->
<script>
    function openMaps(latitude, longitude) {
        const googleMapsUrl = `https://maps.google.com/?q=${latitude},${longitude}&z=16`;
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        if (isMobile) {
            if (/Android/i.test(navigator.userAgent)) {
                const googleMapsApp = `geo:${latitude},${longitude}?q=${latitude},${longitude}&z=16`;
                const tempLink = document.createElement('a');
                tempLink.href = googleMapsApp;
                tempLink.style.display = 'none';
                document.body.appendChild(tempLink);
                tempLink.click();
                document.body.removeChild(tempLink);
                
                setTimeout(() => {
                    window.open(googleMapsUrl, '_blank');
                }, 1500);
            } else {
                window.open(googleMapsUrl, '_blank');
            }
        } else {
            window.open(googleMapsUrl, '_blank');
        }
    }
</script>

@endsection