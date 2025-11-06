@extends('layouts.app')

@section('title', 'Management Node')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- Header Section --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Node Management</h1>
        <p class="text-gray-600">Overview of Node and related SVLAN infrastructure</p>
    </div>

    {{-- Add Button, Search and Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 flex items-center justify-between flex-wrap gap-4">

        {{-- Tombol Aksi --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('nodes.create') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg shadow-sm transition-all duration-200">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <span>Add Node</span>
            </a>
        </div>

        {{-- Form Pencarian --}}
        <form action="{{ route('nodes.index') }}" method="GET" class="w-full md:w-auto">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text"
                        name="search"
                        placeholder="Search Node Name..."
                        value="{{ request('search') }}"
                        class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('nodes.index') }}"
                    class="px-5 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Node Data Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">List All Node</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider w-1/4">Node Name</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Connected SVLAN</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($nodes as $node)
                        <tr class="align-top">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ $node->nama_node }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if($node->svlans->isNotEmpty())
                                    {{-- [MODIFIED] Logika Alpine.js ditulis ulang agar lebih sederhana dan andal --}}
                                    <div x-data="{
                                        isMobile: window.innerWidth < 768,
                                        showAll: false,
                                        total: {{ $node->svlans->count() }},
                                        get limit() {
                                            if (this.showAll) return this.total;
                                            return this.isMobile ? 1 : 3;
                                        }
                                    }" @resize.window="isMobile = window.innerWidth < 768">

                                        <div class="flex flex-col">
                                            <template x-for="(svlan, index) in {{ $node->svlans->toJson() }}" :key="svlan.id">
                                                <div x-show="index < limit" x-transition.opacity.duration.300ms>
                                                    {{-- [MODIFIED] Logika garis pemisah yang lebih simpel --}}
                                                    <div :class="{ 'border-t border-gray-200 pt-3 mt-3': index > 0 }">
                                                        <div class="grid grid-cols-2 gap-x-4 gap-y-2 md:flex md:flex-wrap md:items-center md:gap-x-4 md:gap-y-1 text-xs">
                                                            <div>
                                                                <span class="font-semibold">NMS:</span>
                                                                <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full" x-text="svlan.svlan_nms"></span>
                                                            </div>
                                                            <div>
                                                                <span class="font-semibold">ME:</span>
                                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full" x-text="svlan.svlan_me"></span>
                                                            </div>
                                                            <div>
                                                                <span class="font-semibold">VPN:</span>
                                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full" x-text="svlan.svlan_vpn"></span>
                                                            </div>
                                                            <div>
                                                                <span class="font-semibold">INET:</span>
                                                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full" x-text="svlan.svlan_inet"></span>
                                                            </div>
                                                            <template x-if="svlan.extra">
                                                                <div>
                                                                    <span class="font-semibold">Extra:</span>
                                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full" x-text="svlan.extra"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Tombol "View All" / "Show Less" --}}
                                        <div x-show="total > (isMobile ? 1 : 3)">
                                            <button @click="showAll = !showAll" class="mt-2 text-blue-600 text-xs font-medium hover:underline w-fit">
                                                <span x-text="showAll ? 'Show Less' : 'View All'"></span>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">No SVLAN Related</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('nodes.edit', $node->id) }}"
                                       title="Edit"
                                       class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>

                                    <form action="{{ route('nodes.destroy', $node->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Node ini? Menghapus Node juga akan menghapus semua SVLAN dan CVLAN yang terkait.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus"
                                                class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-red-400 to-red-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="folder-search" class="w-12 h-12 text-gray-400 mb-4"></i>
                                    @if(request('search'))
                                        <p class="font-semibold">Search "{{ request('search') }}" Not Found.</p>
                                    @else
                                        <p class="font-semibold">Empty Node.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination with Per Page Selector --}}
    @if($nodes->hasPages() || $nodes->total() > 10)
    <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Per Page Selector (Bottom Left) --}}
        <div class="flex items-center gap-3">
            <label for="perPage" class="text-sm text-gray-700 font-medium">Show:</label>
            <select id="perPage" 
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="text-sm text-gray-700">entries per page</span>
        </div>

        {{-- Pagination Links (Center) --}}
        @if($nodes->hasPages())
        <div class="flex-1 flex justify-center">
            {{ $nodes->onEachSide(2)->appends(request()->query())->links('vendor.pagination.custom-pagination') }}
        </div>
        @else
        <div class="flex-1"></div>
        @endif

        {{-- Info - Tidak ada tombol Back seperti CVLAN --}}
        <div></div>
    </div>
    @endif
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();

        // Per Page Selector Logic
        const perPageSelect = document.getElementById('perPage');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('per_page', this.value);
                currentUrl.searchParams.set('page', '1'); // Reset to first page
                window.location.href = currentUrl.toString();
            });
        }
    });
</script>


</div>
@endsection