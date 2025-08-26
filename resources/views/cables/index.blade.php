@extends('layouts.app')

@section('title', 'Daftar Kabel')

@push('scripts')
<script>
// Simple delete confirmation function
function confirmDelete(event, cableName, cableId) {
    event.preventDefault(); // Prevent form submission

    // Show native browser confirmation
    const confirmMessage = `Apakah Anda yakin ingin menghapus kabel?\n\nNama: ${cableName}\nCable ID: ${cableId}\n\n⚠️ Tindakan ini tidak dapat dibatalkan!`;

    if (confirm(confirmMessage)) {
        // User confirmed, submit the form
        event.target.submit();
        return true;
    } else {
        // User cancelled
        return false;
    }
}

// Alternative with custom modal (if needed)
let currentForm = null;

function showDeleteModal(event, cableName, cableId) {
    event.preventDefault();
    currentForm = event.target;

    // Set modal content
    document.getElementById("cableName").textContent = cableName;
    document.getElementById("cableId").textContent = `Cable ID: ${cableId}`;

    // Show modal
    const modal = document.getElementById("deleteModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.classList.add("modal-open");
}

function closeModal() {
    const modal = document.getElementById("deleteModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    document.body.classList.remove("modal-open");
    currentForm = null;
}

function executeDelete() {
    if (currentForm) {
        const submitBtn = document.querySelector('#deleteModal button[onclick="executeDelete()"]');
        submitBtn.textContent = 'Menghapus...';
        submitBtn.disabled = true;

        currentForm.submit();
    }
}

// Modal event listeners
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("deleteModal");

    if (modal) {
        // Close on backdrop click
        modal.addEventListener("click", function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close on Escape key
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape" && !modal.classList.contains("hidden")) {
                closeModal();
            }
        });
    }
});
</script>
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
                    <td class="px-6 py-4">{{ $cable->cable_id }}</td>
                    <td class="px-6 py-4">{{ $cable->name }}</td>
                    <td class="px-6 py-4">{{ $cable->source_site ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $cable->destination_site ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $cable->region ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $cable->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('cables.show', $cable->id) }}"
                                class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                Detail
                            </a>
                            <a href="{{ route('cables.edit', $cable->id) }}"
                                class="text-yellow-600 hover:text-yellow-800 font-medium transition-colors">
                                Edit
                            </a>
                            <!-- Simple Delete Form -->
                            <form method="POST" action="{{ route('cables.destroy', $cable->id) }}"
                                  onsubmit="return confirmDelete(event, '{{ addslashes($cable->name) }}', '{{ addslashes($cable->cable_id) }}')"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-800 font-medium cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 rounded px-1">
                                    Hapus
                                </button>
                            </form>
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

    <!-- Pagination Section -->
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
                    <!-- Chevron Left Icon -->
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                @else
                <a href="{{ $cables->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                    <!-- Chevron Left Icon -->
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
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
                    <!-- Chevron Right Icon -->
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                @else
                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-r-md">
                    <!-- Chevron Right Icon -->
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Optional: Custom Delete Confirmation Modal (uncomment to use instead of native confirm) -->
<!--
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
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
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1">
                    Batal
                </button>
                <button type="button" onclick="executeDelete()"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
-->

<style>
    /* Modal styles */
    .modal-open {
        overflow: hidden;
    }

    /* Quick hover transitions */
    tr:hover {
        transition: background-color 0.1s ease;
    }

    /* Ensure modal appears above everything */
    #deleteModal {
        transition: opacity 0.2s ease;
    }

    #deleteModal.flex {
        opacity: 1;
    }

    #deleteModal.hidden {
        opacity: 0;
    }

    /* Modal animation */
    #deleteModal .transform {
        transform: scale(0.95);
        transition: transform 0.2s ease;
    }

    #deleteModal.flex .transform {
        transform: scale(1);
    }
</style>
@endsection
