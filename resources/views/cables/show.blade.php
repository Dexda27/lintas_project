<!-- resources/views/cables/show.blade.php -->
@extends('layouts.app')

@section('title', 'Cable Details - ' . $cable->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $cable->name }}</h1>
            <p class="text-gray-600 mt-2">Cable ID: {{ $cable->cable_id }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('cables.cores', $cable) }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                View Cores
            </a>
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

<!-- Cable Information -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Cable Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
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
                        class="inline-block px-2 py-1 text-sm font-semibold rounded-full {{ $cable->status === 'ok' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
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
            <div>
                <p class="text-sm font-medium text-gray-600">OTDR Length</p>
                <p class="text-lg text-gray-900">{{ number_format($cable->otdr_length, 2) }} meters</p>
            </div>
            @endif
            @if($cable->description)
            <div>
                <p class="text-sm font-medium text-gray-600">Description</p>
                <p class="text-gray-900">{{ $cable->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Route Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <p class="text-sm font-medium text-gray-600">Source Site</p>
                <p class="text-lg text-gray-900">{{ $cable->source_site }}</p>
            </div>
            <div class="flex justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Destination Site</p>
                <p class="text-lg text-gray-900">{{ $cable->destination_site }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Cores by Tube Overview -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Cores by Tube</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            @foreach($coresByTube as $tubeNumber => $cores)
            <div class="border rounded-lg p-3 text-center">
                <p class="font-semibold text-gray-900">Tube {{ $tubeNumber }}</p>
                <p class="text-sm text-gray-600">{{ $cores->count() }} cores</p>
                <div class="mt-2 space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-green-600">{{ $cores->where('usage', 'active')->count() }}</span>
                        <span class="text-gray-500">{{ $cores->where('usage', 'inactive')->count() }}</span>
                        <span class="text-red-600">{{ $cores->where('status', 'not_ok')->count() }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('cables.cores', $cable) }}"
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
                View Detailed Core Management
            </a>
        </div>
    </div>
</div>
@endsection
