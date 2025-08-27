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
           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md group">
            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kabel
        </a>
    </div>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Search Section -->
    <div class="p-6 border-b border-gray-100">
        <form method="GET" action="{{ route('cables.index') }}">
            <div class="flex gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all duration-200 text-sm"
                        placeholder="Cari Cable ID, nama kabel, site, atau region..."
                        aria-label="Search cables">
                </div>
                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium text-sm">
                    Cari
                </button>
                @if(request('search'))
                <a
                    href="{{ route('cables.index') }}"
                    class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium text-sm">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

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
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-150">
                                Detail
                            </a>
                            <a href="{{ route('cables.edit', $cable->id) }}"
                               class="text-yellow-600 hover:text-yellow-800 text-sm font-medium transition-colors duration-150">
                                Edit
                            </a>
                            <button
                                class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-150"
                                onclick="showDeleteModal({{ $cable->id }},'{{ addslashes($cable->name) }}', '{{ $cable->cable_id }}')">
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

    <!-- Pagination Section -->
    @if($cables->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
        <div class="flex-1 flex justify-between items-center">
            <!-- Showing Results Info -->
            <div>
                <p class="text-sm text-gray-600">
                    Showing <span class="font-medium text-gray-900">{{ $cables->firstItem() ?: 0 }}</span>
                    to <span class="font-medium text-gray-900">{{ $cables->lastItem() ?: 0 }}</span>
                    of <span class="font-medium text-gray-900">{{ $cables->total() }}</span> results
                </p>
            </div>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-1">
                {{-- Previous Page Link --}}
                @if ($cables->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    Previous
                </span>
                @else
                <a href="{{ $cables->appends(request()->query())->previousPageUrl() }}"
                   class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    Previous
                </a>
                @endif

                {{-- Pagination Elements --}}
                @php
                $start = max(1, $cables->currentPage() - 2);
                $end = min($cables->lastPage(), $cables->currentPage() + 2);
                @endphp

                @if($start > 1)
                <a href="{{ $cables->appends(request()->query())->url(1) }}"
                   class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    1
                </a>
                @if($start > 2)
                <span class="px-3 py-2 text-sm text-gray-400">...</span>
                @endif
                @endif

                @for($page = $start; $page <= $end; $page++)
                    @if ($page == $cables->currentPage())
                    <span class="px-3 py-2 text-sm text-white bg-gray-900 border border-gray-900 rounded-lg">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $cables->appends(request()->query())->url($page) }}"
                       class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        {{ $page }}
                    </a>
                    @endif
                @endfor

                @if($end < $cables->lastPage())
                    @if($end < $cables->lastPage() - 1)
                    <span class="px-3 py-2 text-sm text-gray-400">...</span>
                    @endif
                    <a href="{{ $cables->appends(request()->query())->url($cables->lastPage()) }}"
                       class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        {{ $cables->lastPage() }}
                    </a>
                @endif

                {{-- Next Page Link --}}
                @if ($cables->hasMorePages())
                <a href="{{ $cables->appends(request()->query())->nextPageUrl() }}"
                   class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    Next
                </a>
                @else
                <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    Next
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 hidden items-center justify-center z-50 bg-black/50" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all" id="modalContent">
        <div class="p-6">
            <div class="flex items-start mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 id="modal-title" class="text-xl font-semibold text-gray-900 mb-2">Delete Kabel</h3>
                    <p class="text-gray-600 mb-4">Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin ingin menghapus kabel ini?</p>

                    <div class="bg-gray-50 rounded-xl p-4 border-l-4 border-red-400">
                        <p class="font-semibold text-gray-900" id="cableName"></p>
                        <p class="text-sm text-gray-600 font-mono" id="cableId"></p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="px-6 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button
                    type="button"
                    onclick="executeDelete()"
                    class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all duration-200 font-medium">
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
@endsection
