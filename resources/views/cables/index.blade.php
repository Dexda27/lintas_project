@extends('layouts.app')

@section('title', 'Daftar Kabel')

@push('scripts')
<script src="{{ asset('js/cables-index.js') }}"></script>
@endpush

@section('content')
<!-- Header Section -->
<div class="mb-10">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <h1 class="text-4xl font-light text-gray-900 tracking-tight">Daftar Kabel</h1>
            <p class="text-gray-500 text-lg">Kelola data kabel dengan mudah</p>
        </div>
        <a href="{{ route('cables.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition">
            + Tambah Kabel
        </a>
    </div>
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

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cable ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kabel</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Source Site</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Destination Site</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Region</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Dibuat</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($cables as $cable)
                <tr class="hover:bg-gray-50 group transition-colors duration-150">
                    <td class="px-6 py-5">
                        <div class="font-mono text-sm font-medium text-gray-900">{{ $cable->cable_id }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm font-medium text-gray-900">{{ $cable->name }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm text-gray-600">{{ $cable->source_site ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm text-gray-600">{{ $cable->destination_site ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-5">
                        @if($cable->region)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                                {{ $cable->region }}
                            </span>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm text-gray-600">{{ $cable->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('cables.show', $cable->id) }}"
                                class="text-blue-600 hover:text-blue-800 font-medium">Detail</a>
                            <a href="{{ route('cables.edit', $cable->id) }}"
                                class="text-yellow-600 hover:text-yellow-800 font-medium">Edit</a>
                            <button
                                class="text-red-600 hover:underline"
                                onclick="showDeleteModal('{{ $cable->id }}', '{{ $cable->name }}', '{{ $cable->cable_id }}')">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-600 font-medium">Tidak ada data kabel</p>
                                <p class="text-gray-400 text-sm">Mulai dengan membuat kabel pertama Anda</p>
                            </div>
                            <a
                                href="{{ route('cables.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-all duration-200 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Buat Kabel Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Section - Dipindah ke dalam card dan diberi styling yang lebih rapi -->
    @if($cables->hasPages())
    <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="flex-1 flex justify-between items-center">
            <!-- Showing Results Info -->
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ $cables->firstItem() }}</span>
                    to
                    <span class="font-medium">{{ $cables->lastItem() }}</span>
                    of
                    <span class="font-medium">{{ $cables->total() }}</span>
                    results
                </p>
            </div>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-2">
                {{-- Previous Page Link --}}
                @if ($cables->onFirstPage())
                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-l-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                @else
                <a href="{{ $cables->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($cables->getUrlRange(1, $cables->lastPage()) as $page => $url)
                @if ($page == $cables->currentPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600">
                    {{ $page }}
                </span>
                @else
                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                    {{ $page }}
                </a>
                @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($cables->hasMorePages())
                <a href="{{ $cables->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                @else
                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-r-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                @endif
            </div>
        </div>
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

<script>
    function executeDelete() {
        const cableId = window.selectedCableId;
        const form = document.getElementById('deleteForm');
        form.action = `/cables/${cableId}`;
        form.submit();
    }
</script>
