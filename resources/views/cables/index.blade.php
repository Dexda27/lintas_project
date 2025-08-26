@extends('layouts.app')

@section('title', 'Daftar Kabel')

@push('scripts')
<script src="{{ asset('js/cables-index.js') }}"></script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Daftar Kabel</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola data kabel dengan mudah</p>
        </div>
        <a href="{{ route('cables.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition">
            + Tambah Kabel
        </a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('cables.index') }}" class="mb-4">
        <div class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 w-full"
                   placeholder="Cari Cable ID, nama kabel, site, atau region...">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('cables.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow transition">
                    Clear
                </a>
            @endif
        </div>
    </form>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="text-left px-6 py-3">Cable ID</th>
                    <th class="text-left px-6 py-3">Nama Kabel</th>
                    <th class="text-left px-6 py-3">Source Site</th>
                    <th class="text-left px-6 py-3">Destination Site</th>
                    <th class="text-left px-6 py-3">Region</th>
                    <th class="text-left px-6 py-3">Tanggal Dibuat</th>
                    <th class="text-center px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cables as $cable)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $cable->cable_id }}</td>
                    <td class="px-6 py-4">{{ $cable->name }}</td>
                    <td class="px-6 py-4">{{ $cable->source_site ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $cable->destination_site ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $cable->region ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $cable->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('cables.show', $cable->id) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium">Detail</a>
                            <a href="{{ route('cables.edit', $cable->id) }}"
                               class="text-yellow-600 hover:text-yellow-800 font-medium">Edit</a>
                             <button 
                                class="text-red-600 hover:underline"
                                onclick="showDeleteModal({{ $cable->id }}, '{{ $cable->name }}', '{{ $cable->cable_id }}')">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">Tidak ada data kabel.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($cables->hasPages())
    <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        {{ $cables->links() }}
    </div>
    @endif
</div>

<!-- Modal Delete -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white bg-opacity-95 backdrop-blur-sm rounded-lg shadow-xl border border-white border-opacity-20 p-6 w-96 mx-4">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 bg-opacity-80 flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h2>
        </div>
        
        <div class="mb-6">
            <p class="text-sm text-gray-600 mb-2">Apakah Anda yakin ingin menghapus kabel:</p>
            <p class="font-medium text-gray-900" id="cableName">-</p>
            <p class="text-sm text-gray-500" id="cableId">-</p>
        </div>
        
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeDeleteModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white bg-opacity-80 backdrop-blur-sm border border-gray-300 border-opacity-50 rounded-md hover:bg-opacity-90 hover:bg-gray-50 transition-all duration-200">
                Batal
            </button>
            <button type="button" onclick="executeDelete()" 
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 bg-opacity-90 backdrop-blur-sm border border-transparent rounded-md hover:bg-opacity-100 hover:bg-red-700 transition-all duration-200">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
    /* Quick hover transitions */
    tr:hover {
        transition: background-color 0.1s ease;
    }

    /* Ensure modal appears above everything */
    #deleteModal {
        transition: opacity 0.15s ease;
    }
</style>
@endpush
@endsection