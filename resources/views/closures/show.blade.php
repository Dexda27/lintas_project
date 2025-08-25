@extends('layouts.app')

@section('title', 'Closure Details')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Closure Details</h1>
            <p class="text-gray-600 mt-2">Detail informasi Joint Closure</p>
        </div>
        <a href="{{ route('closures.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            Back to List
        </a>
    </div>
</div>

<!-- Detail Card -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Closure Information</h2>
    
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
        <div>
            <dt class="text-sm font-medium text-gray-500">Closure ID</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $closure->closure_id }}</dd>
        </div>
        
        <div>
            <dt class="text-sm font-medium text-gray-500">Name</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $closure->name }}</dd>
        </div>

        <div>
            <dt class="text-sm font-medium text-gray-500">Location</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $closure->location }}</dd>
        </div>
        
        <div>
            <dt class="text-sm font-medium text-gray-500">Region</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $closure->region }}</dd>
        </div>

        <div>
            <dt class="text-sm font-medium text-gray-500">Capacity</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $closure->used_capacity }} / {{ $closure->capacity }} cores</dd>
        </div>

        <div>
            <dt class="text-sm font-medium text-gray-500">Status</dt>
            <dd class="mt-1">
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $closure->status === 'ok' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $closure->status)) }}
                </span>
            </dd>
        </div>
    </dl>

    <!-- Progress Bar -->
    <div class="mt-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Usage Progress</h3>
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="bg-blue-600 h-3 rounded-full" 
                 style="width: {{ $closure->capacity > 0 ? ($closure->used_capacity / $closure->capacity) * 100 : 0 }}%">
            </div>
        </div>
    </div>
</div>

<!-- Connections Section -->
<div class="bg-white rounded-lg shadow mt-8 p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Core Connections</h2>
        <a href="{{ route('closures.connections', $closure) }}" 
           class="text-blue-600 hover:text-blue-900 font-medium">
            Manage Connections
        </a>
    </div>

    @if($closure->core_connections_count > 0)
        <ul class="divide-y divide-gray-200">
            @foreach($closure->coreConnections as $connection)
            <li class="py-3 flex justify-between items-center">
                <div>
                    <p class="text-gray-900 text-sm">
                        {{ $connection->core_a->cable->name }} (Core {{ $connection->core_a->core_number }})
                        ↔ 
                        {{ $connection->core_b->cable->name }} (Core {{ $connection->core_b->core_number }})
                    </p>
                    <p class="text-xs text-gray-500">
                        Connected at: {{ $connection->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <form action="{{ route('connections.disconnect', $connection) }}" method="POST" 
                      onsubmit="return confirm('Are you sure to disconnect this core?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 hover:text-red-800 text-sm font-medium">
                        Disconnect
                    </button>
                </form>
            </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500 text-sm">No connections available for this closure.</p>
    @endif
</div>
@endsection
