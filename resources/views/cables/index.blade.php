@extends('layouts.app')

@section('title', 'Daftar Kabel')

@push('scripts')
<script src="{{ asset('js/cables-index.js') }}"></script>
@endpush

@section('content')
<!-- Header Section -->
<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">List Cable</h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-base">Manage cable data easily
</p>
        </div>
        <a href="{{ route('cables.create') }}"
            class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm sm:text-base text-center">
            <span class="hidden sm:inline">+ Add New Cable</span>
            <span class="sm:hidden">+ Add</span>
        </a>
    </div>
</div>

<!-- Cables Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Cable Data</h2>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('cables.index') }}" class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 w-full text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Search Cable ID, cable name, site, atau region..."
                aria-label="Search cables">
            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg shadow transition-colors text-sm sm:text-base flex-1 sm:flex-initial">
                    Search
                </button>
                @if(request('search'))
                <a href="{{ route('cables.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 sm:px-4 py-2 rounded-lg shadow transition-colors text-sm sm:text-base flex-1 sm:flex-initial text-center">
                    Clear
                </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Mobile Card View (visible on screens smaller than lg) -->
    <div class="lg:hidden">
        @forelse ($cables as $cable)
        <div class="border-b border-gray-200 p-4 last:border-b-0">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $cable->cable_id }}</h3>
                    <p class="text-xs text-gray-600 mt-1 truncate">{{ $cable->name }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 ml-2 flex-shrink-0">
                    {{ $cable->region ?? '-' }}
                </span>
            </div>

            <div class="mb-2">
                @if($cable->source_site || $cable->destination_site)
                <div class="flex items-center text-sm text-gray-600 mb-1">
                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ $cable->source_site ?? '-' }} → {{ $cable->destination_site ?? '-' }}</span>
                </div>
                @endif
                <p class="text-xs text-gray-600">
                    <span class="font-medium">Dibuat:</span> {{ $cable->created_at->format('d M Y') }}
                </p>
            </div>

            <!-- Actions with Icons -->
            <div class="flex space-x-3 pt-3 border-gray-200 justify-center">
                <a href="{{ route('cables.show', $cable->id) }}"
                    class="flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 border border-blue-300 rounded-full transition-colors"
                    title="Detail">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>
                <a href="{{ route('cables.edit', $cable->id) }}"
                    class="flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 border border-yellow-300 rounded-full transition-colors"
                    title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                <button
                    type="button"
                    onclick="showDeleteModal('{{ $cable->id }}', '{{ addslashes($cable->name) }}', '{{ $cable->cable_id }}')"
                    class="flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 border border-red-300 rounded-full transition-colors"
                    title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <div class="p-6 sm:p-12 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-300 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 mb-2 text-sm">No cable data.</p>
                <a href="{{ route('cables.create') }}"
                    class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                    Add first cable...
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table View (visible on lg screens and up) -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cable ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source Site</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination Site</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($cables as $cable)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cable->cable_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cable->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cable->source_site ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cable->destination_site ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($cable->region)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $cable->region }}
                        </span>
                        @else
                        -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cable->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route('cables.show', $cable->id) }}"
                                class="flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-100 rounded-full transition-colors"
                                title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('cables.edit', $cable->id) }}"
                                class="flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-100 rounded-full transition-colors"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button
                                type="button"
                                onclick="showDeleteModal('{{ $cable->id }}', '{{ addslashes($cable->name) }}', '{{ $cable->cable_id }}')"
                                class="flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-100 rounded-full transition-colors"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 mb-2">No cable data.</p>
                            <a href="{{ route('cables.create') }}"
                                class="text-blue-600 hover:text-blue-900 font-medium">
                                Add first cable...
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Responsive Pagination Section -->
    @if($cables->hasPages())
    <div class="bg-white px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <!-- Showing Results Info -->
            <div class="text-center sm:text-left">
                <p class="text-xs sm:text-sm text-gray-700">
                    <span class="hidden sm:inline">Showing</span>
                    <span class="font-medium">{{ $cables->firstItem() ?: 0 }}</span>
                    <span class="hidden sm:inline">to</span>
                    <span class="sm:hidden">-</span>
                    <span class="font-medium">{{ $cables->lastItem() ?: 0 }}</span>
                    <span class="hidden sm:inline">of</span>
                    <span class="sm:hidden">/</span>
                    <span class="font-medium">{{ $cables->total() }}</span>
                    <span class="hidden sm:inline">results</span>
                </p>
            </div>

            <!-- Pagination Links -->
            <div class="flex justify-center sm:justify-end">
                <div class="flex items-center space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($cables->onFirstPage())
                    <span class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-l-md">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Previous</span>
                    </span>
                    @else
                    <a href="{{ $cables->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Previous</span>
                    </a>
                    @endif

                    {{-- Mobile: Show only current page and total pages --}}
                    <div class="sm:hidden flex items-center">
                        <span class="relative inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-blue-600">
                            {{ $cables->currentPage() }} / {{ $cables->lastPage() }}
                        </span>
                    </div>

                    {{-- Desktop: Show all page numbers --}}
                    <div class="hidden sm:flex items-center space-x-1">
                        @php
                        $start = max(1, $cables->currentPage() - 2);
                        $end = min($cables->lastPage(), $cables->currentPage() + 2);
                        @endphp

                        @if($start > 1)
                        <a href="{{ $cables->appends(request()->query())->url(1) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                            1
                        </a>
                        @if($start > 2)
                        <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300">
                            ...
                        </span>
                        @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if ($page==$cables->currentPage())
                            <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 border border-blue-600">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $cables->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                                {{ $page }}
                            </a>
                            @endif
                            @endfor

                            @if($end < $cables->lastPage())
                                @if($end < $cables->lastPage() - 1)
                                    <span class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300">
                                        ...
                                    </span>
                                    @endif
                                    <a href="{{ $cables->appends(request()->query())->url($cables->lastPage()) }}" class="relative inline-flex items-center px-3 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                                        {{ $cables->lastPage() }}
                                    </a>
                                    @endif
                    </div>

                    {{-- Next Page Link --}}
                    @if ($cables->hasMorePages())
                    <a href="{{ $cables->appends(request()->query())->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Next</span>
                    </a>
                    @else
                    <span class="relative inline-flex items-center px-2 py-1 sm:py-2 text-xs sm:text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-r-md">
                        <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Next</span>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto transform transition-all">
        <div class="p-4 sm:p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-title" class="text-base sm:text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                </div>
            </div>

            <div class="mb-4 sm:mb-6">
                <p class="text-sm sm:text-base text-gray-600 mb-3">Apakah Anda yakin ingin menghapus kabel ini?</p>
                <div class="bg-gray-50 p-3 rounded border-l-4 border-red-400">
                    <p class="font-semibold text-gray-900 text-sm sm:text-base break-words" id="cableName"></p>
                    <p class="text-xs sm:text-sm text-gray-600 font-mono" id="cableId"></p>
                </div>
                <p class="text-xs sm:text-sm text-red-600 mt-3 flex items-center">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-sm sm:text-base order-2 sm:order-1">
                    Batal
                </button>
                <button
                    type="button"
                    onclick="executeDelete()"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-sm sm:text-base order-1 sm:order-2">
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
