@extends('layouts.app')

@section('title', 'Daftar Kabel')

@push('scripts')
<script src="{{ asset('js/cables-index.js') }}"></script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl p-4 md:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Daftar Kabel</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola data kabel dengan mudah</p>
        </div>
        <a href="{{ route('cables.create') }}"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition text-center">
            + Tambah Kabel
        </a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('cables.index') }}" class="mb-6">
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                class="border border-gray-300 rounded-lg px-4 py-2 w-full text-sm"
                placeholder="Cari Cable ID, nama kabel, site, atau region...">
            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition text-sm whitespace-nowrap">
                    Cari
                </button>
                @if(request('search'))
                <a href="{{ route('cables.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow transition text-sm whitespace-nowrap">
                    Clear
                </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto rounded-xl border border-gray-200">
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
                    <td class="px-6 py-4 font-medium">{{ $cable->cable_id }}</td>
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
                                class="text-red-600 hover:text-red-800 font-medium"
                                onclick="showDeleteModal('{{ $cable->id }}', '{{ $cable->name }}', '{{ $cable->cable_id }}')">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Tidak ada data kabel.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-4">
        @forelse ($cables as $cable)
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <!-- Cable Header -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $cable->name }}</h3>
                    <p class="text-sm text-gray-600 font-mono">{{ $cable->cable_id }}</p>
                </div>
                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded">
                    {{ $cable->created_at->format('d M Y') }}
                </span>
            </div>

            <!-- Cable Details -->
            <div class="space-y-2 mb-4">
                @if($cable->source_site || $cable->destination_site)
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ $cable->source_site ?? '-' }} → {{ $cable->destination_site ?? '-' }}</span>
                </div>
                @endif

                @if($cable->region)
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    <span>{{ $cable->region }}</span>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-200">
                <a href="{{ route('cables.show', $cable->id) }}"
                    class="flex-1 text-center bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-blue-700 transition">
                    Detail
                </a>
                <a href="{{ route('cables.edit', $cable->id) }}"
                    class="flex-1 text-center bg-yellow-500 text-white px-3 py-2 rounded text-sm font-medium hover:bg-yellow-600 transition">
                    Edit
                </a>
                <button
                    class="flex-1 bg-red-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-red-700 transition"
                    onclick="showDeleteModal('{{ $cable->id }}', '{{ $cable->name }}', '{{ $cable->cable_id }}')">
                    Hapus
                </button>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-lg font-medium">Tidak ada data kabel.</p>
                <p class="text-sm mt-1">Mulai tambahkan kabel untuk melihat daftar di sini.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination Section -->
    @if($cables->hasPages())
    <div class="bg-white px-4 md:px-6 py-4 border-t border-gray-200 mt-6">
        <!-- Desktop Pagination -->
        <div class="hidden md:flex items-center justify-between">
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
            <div class="flex items-center space-x-1">
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
                @foreach ($cables->getUrlRange(max(1, $cables->currentPage() - 2), min($cables->lastPage(), $cables->currentPage() + 2)) as $page => $url)
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

        <!-- Mobile Pagination -->
        <div class="md:hidden">
            <div class="text-center mb-3">
                <p class="text-sm text-gray-700">
                    <span class="font-medium">{{ $cables->firstItem() }}</span>
                    -
                    <span class="font-medium">{{ $cables->lastItem() }}</span>
                    of
                    <span class="font-medium">{{ $cables->total() }}</span>
                </p>
            </div>
            <div class="flex justify-between">
                @if ($cables->onFirstPage())
                <span class="flex-1 bg-gray-100 text-gray-400 px-4 py-2 rounded-l-md text-center text-sm">
                    Previous
                </span>
                @else
                <a href="{{ $cables->previousPageUrl() }}" class="flex-1 bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-l-md hover:bg-gray-50 text-center text-sm">
                    Previous
                </a>
                @endif

                <span class="bg-blue-600 text-white px-4 py-2 text-sm flex items-center justify-center min-w-[80px]">
                    {{ $cables->currentPage() }} / {{ $cables->lastPage() }}
                </span>

                @if ($cables->hasMorePages())
                <a href="{{ $cables->nextPageUrl() }}" class="flex-1 bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-r-md hover:bg-gray-50 text-center text-sm">
                    Next
                </a>
                @else
                <span class="flex-1 bg-gray-100 text-gray-400 px-4 py-2 rounded-r-md text-center text-sm">
                    Next
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Responsive Delete Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white bg-opacity-95 backdrop-blur-sm rounded-lg shadow-xl border border-white border-opacity-20 p-6 w-full max-w-md mx-4">
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
            <p class="font-medium text-gray-900 break-words" id="cableName">-</p>
            <p class="text-sm text-gray-500 font-mono" id="cableId">-</p>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <button type="button" onclick="closeDeleteModal()"
                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-white bg-opacity-80 backdrop-blur-sm border border-gray-300 border-opacity-50 rounded-md hover:bg-opacity-90 hover:bg-gray-50 transition-all duration-200">
                Batal
            </button>
            <button type="button" onclick="executeDelete()"
                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-red-600 bg-opacity-90 backdrop-blur-sm border border-transparent rounded-md hover:bg-opacity-100 hover:bg-red-700 transition-all duration-200">
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

    /* Line clamp for long text on mobile */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
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
