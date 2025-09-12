@extends('layouts.app')
@section('title', 'Data SVLAN')
@section('content')

<div>
    {{-- Header Section --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard SVLAN Management</h1>
        <p class="text-gray-600">Overview of SVLAN infrastructure</p>
    </div>

    {{-- Action Buttons, Search and Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8 flex items-center justify-between flex-wrap gap-4">
        
        {{-- Grup Tombol Aksi --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('svlan.exportAll', request()->query()) }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg shadow-md transition-all duration-300">
                <i data-lucide="download" class="w-5 h-5"></i>
                <span>Export SVLAN</span>
            </a>
            <a href="{{ route('svlan.create') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg shadow-md transition-all duration-300">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <span>Add New SVLAN</span>
            </a>
        </div>

        {{-- Form Pencarian --}}
        <form action="{{ route('svlan.index') }}" method="GET" class="w-full md:w-auto">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input
                        type="text"
                        name="search"
                        placeholder="Search Node ID, VPN, NMS..."
                        value="{{ request('search') }}"
                        class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('svlan.index') }}" class="px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>


    {{-- SVLAN Data Cards --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">SVLAN Data</h2>
        </div>

        {{-- Table Header --}}
        <div class="hidden lg:grid lg:grid-cols-8 gap-4 p-6 bg-gray-50 border-b border-gray-200 text-sm font-medium text-gray-700 uppercase tracking-wider">
            <div class="flex items-center gap-2">
                <span>Node ID</span>
                <div class="flex flex-col">
                    <a href="{{ route('svlan.index', ['sort' => 'node_id', 'order' => 'asc', 'search' => request('search')]) }}">
                        <i data-lucide="chevron-up" class="w-4 h-4 {{ $sortField == 'node_id' && $sortOrder == 'asc' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    </a>
                    <a href="{{ route('svlan.index', ['sort' => 'node_id', 'order' => 'desc', 'search' => request('search')]) }}">
                        <i data-lucide="chevron-down" class="w-4 h-4 {{ $sortField == 'node_id' && $sortOrder == 'desc' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    </a>
                </div>
            </div>
            <div class="text-center">NMS</div>
            <div class="text-center">ME</div>
            <div class="text-center">VPN</div>
            <div class="text-center">INET</div>
            <div class="text-center">Extra</div>
            <div class="text-center">Description</div>
            <div class="text-center">Actions</div>
        </div>

        {{-- SVLAN Cards/Rows --}}
        <div class="divide-y divide-gray-200">
            @forelse($svlans as $svlan)
            <div class="p-6 hover:bg-gray-50 transition-colors">
                {{-- Mobile Card Layout --}}
                <div class="lg:hidden space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">{{ $svlan->node->nama_node ?? $svlan->node_id }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $svlan->keterangan }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">NMS</p>
                            <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'nms']) }}" class="inline-block mt-1">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->svlan_nms }}</span>
                            </a>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">ME</p>
                            <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'metro']) }}" class="inline-block mt-1">
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->svlan_me }}</span>
                            </a>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">VPN</p>
                            <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'vpn']) }}" class="inline-block mt-1">
                                <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->svlan_vpn }}</span>
                            </a>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">INET</p>
                            <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'inet']) }}" class="inline-block mt-1">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->svlan_inet }}</span>
                            </a>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <a href="{{ route('svlan.edit', $svlan->id) }}" class="inline-flex items-center justify-center gap-1 p-2 font-medium text-white bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                            <span>Edit</span>
                        </a>
                        <button type="button"
                                class="delete-svlan-btn flex items-center gap-1 px-3 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors"
                                data-svlan-id="{{ $svlan->id }}"
                                data-cvlan-count="{{ $svlan->cvlans->count() }}"
                                data-action-url="{{ route('svlan.destroy', $svlan->id) }}">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>

                {{-- Desktop Grid Layout --}}
                <div class="hidden lg:grid lg:grid-cols-8 gap-4 items-center">
                    <div class="font-bold text-gray-900">{{ $svlan->node->nama_node ?? $svlan->node_id }}</div>

                    <div class="text-center">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'nms']) }}" class="inline-block hover:opacity-80 transition-opacity">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->svlan_nms }}</span>
                        </a>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'metro']) }}" class="inline-block hover:opacity-80 transition-opacity">
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->svlan_me }}</span>
                        </a>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'vpn']) }}" class="inline-block hover:opacity-80 transition-opacity">
                            <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->svlan_vpn }}</span>
                        </a>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'inet']) }}" class="inline-block hover:opacity-80 transition-opacity">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->svlan_inet }}</span>
                        </a>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'extra']) }}" class="inline-block hover:opacity-80 transition-opacity">
                            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-1 rounded">{{ $svlan->extra }}</span>
                        </a>
                    </div>

                    <div class="text-sm text-center text-gray-600">{{ $svlan->keterangan }}</div>

                    <div class="flex justify-center items-center gap-2">
                        <a href="{{ route('svlan.edit', $svlan->id) }}" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        <button type="button"
                                class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-red-400 to-red-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200"
                                title="Delete"
                                data-svlan-id="{{ $svlan->id }}"
                                data-cvlan-count="{{ $svlan->cvlans->count() }}"
                                data-action-url="{{ route('svlan.destroy', $svlan->id) }}">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                @if(request('search'))
                    <div class="flex flex-col items-center">
                        <i data-lucide="search-x" class="w-16 h-16 text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No results found</h3>
                        <p class="text-gray-600">No SVLAN data found for '<strong>{{ request('search') }}</strong>'.</p>
                    </div>
                @else
                    <div class="flex flex-col items-center">
                        <i data-lucide="database" class="w-16 h-16 text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No SVLAN data</h3>
                        <p class="text-gray-600 mb-4">No SVLAN data in the system yet.</p>
                        <a href="{{ route('svlan.create') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                            <i data-lucide="plus-circle" class="w-5 h-5"></i>
                            <span>Add First SVLAN</span>
                        </a>
                    </div>
                @endif
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($svlans->hasPages())
    <div class="mt-8">
        {{ $svlans->appends(request()->query())->links('vendor.pagination.custom-pagination') }}
    </div>
    @endif
</div>

{{-- Delete Modal (unchanged) --}}
<div id="deleteSvlanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-sm rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm Delete SVLAN</h3>
            <div class="mt-2 px-7 py-3">
                <p id="modal-text" class="text-sm text-gray-500">
                    This SVLAN has <strong id="cvlan-count"></strong> related CVLAN(s). What would you like to do?
                </p>
            </div>
            <form id="deleteSvlanForm" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="cascade_delete" id="cascadeDeleteInput">
                <div class="items-center px-4 py-3">
                    <button id="deleteCascadeBtn" type="button" class="w-full mb-2 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Delete SVLAN with Related CVLANs</button>
                    <button id="deleteOrphanBtn" type="button" class="w-full mb-2 px-4 py-2 bg-amber-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500">Delete SVLAN Only (Make CVLANs Independent)</button>
                    <button id="cancelBtn" type="button" class="w-full px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    lucide.createIcons();

    // Modal logic
    const modal = document.getElementById('deleteSvlanModal');
    const deleteSvlanForm = document.getElementById('deleteSvlanForm');
    const cascadeDeleteInput = document.getElementById('cascadeDeleteInput');
    const cvlanCountSpan = document.getElementById('cvlan-count');
    const deleteCascadeBtn = document.getElementById('deleteCascadeBtn');
    const deleteOrphanBtn = document.getElementById('deleteOrphanBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    document.querySelectorAll('.delete-svlan-btn').forEach(button => {
        button.addEventListener('click', function () {
            const cvlanCount = parseInt(this.dataset.cvlanCount, 10);
            const actionUrl = this.dataset.actionUrl;
            deleteSvlanForm.action = actionUrl;

            if (cvlanCount > 0) {
                cvlanCountSpan.textContent = cvlanCount;
                modal.classList.remove('hidden');
            } else {
                if (confirm('Are you sure you want to delete this SVLAN? No CVLANs are related.')) {
                    cascadeDeleteInput.value = 'false';
                    deleteSvlanForm.submit();
                }
            }
        });
    });

    deleteCascadeBtn.addEventListener('click', function() {
        cascadeDeleteInput.value = 'true';
        deleteSvlanForm.submit();
    });

    deleteOrphanBtn.addEventListener('click', function() {
        cascadeDeleteInput.value = 'false';
        deleteSvlanForm.submit();
    });

    cancelBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection