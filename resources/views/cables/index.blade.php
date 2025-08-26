@extends('layouts.app')

@section('title', 'Daftar Kabel')

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
                placeholder="Cari Cable ID, namakabel, site, atau region...">
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
                @forelse ($cables as $index => $cable)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 ">{{ $cable->cable_id }}</td>
                    <td class="px-6 py-4 ">{{ $cable->name }}</td>
                    <td class="px-6 py-4">{{ $cable->source_site ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $cable->destination_site ?? '-' }}</td>
                    <td class="px-6 py-4">
                        {{ $cable->region ?? '-' }}
                    </td>

                    <td class="px-6 py-4">{{ $cable->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('cables.show', $cable->id) }}"
                                class="text-blue-600 hover:text-blue-800 font-medium">Detail</a>
                            <a href="{{ route('cables.edit', $cable->id) }}"
                                class="text-yellow-600 hover:text-yellow-800 font-medium">Edit</a>
                            <button type="button"
                                onclick="confirmDelete('{{ $cable->id }}', '{{ $cable->name }}', '{{ $cable->cable_id }}')"
                                class="text-red-600 hover:text-red-800 font-medium cursor-pointer">
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0  hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-gray-600 mb-2">Anda akan menghapus kabel:</p>
                <div class="bg-gray-50 p-3 rounded border-l-4 border-red-400">
                    <p class="font-semibold text-gray-900" id="cableName"></p>
                    <p class="text-sm text-gray-600" id="cableId"></p>
                </div>
                <p class="text-sm text-red-600 mt-2">⚠️ Tindakan ini tidak dapat dibatalkan!</p>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                    Batal
                </button>
                <button type="button" onclick="executeDelete()"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    let currentCableId = null;

    function confirmDelete(cableId, cableName, cableIdText) {
        currentCableId = cableId;
        document.getElementById('cableName').textContent = cableName;
        document.getElementById('cableId').textContent = `Cable ID: ${cableIdText}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
        currentCableId = null;
    }

    function executeDelete() {
        if (currentCableId) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('cables.index') }}/${currentCableId}`;
            form.submit();
        }
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>

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
@endsection
