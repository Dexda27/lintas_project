@extends('layouts.app')

@section('title', 'Daftar CVLAN untuk ' . $svlan->svlan_nms)

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">

        {{-- Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Data CVLAN</h1>
            <p class="text-gray-600">
                @switch($koneksiFilter)
                    @case('vpn')
                        For SVLAN VPN: <span class="font-bold">{{ $svlan->svlan_vpn }}</span>
                        @break
                    @case('inet')
                        For SVLAN INET: <span class="font-bold">{{ $svlan->svlan_inet }}</span>
                        @break
                    @case('extra')
                        For SVLAN EXTRA: <span class="font-bold">{{ $svlan->extra }}</span>
                        @break
                    @case('metro')
                        For SVLAN Metro: <span class="font-bold">{{ $svlan->svlan_me }}</span>
                        @break
                    @case('nms')
                        For SVLAN NMS: <span class="font-bold">{{ $svlan->svlan_nms }}</span>
                        @break
                    @default
                        For SVLAN: <span class="font-bold">{{ $svlan->svlan_nms }}</span>
                @endswitch
                <br>Node: <span class="font-bold">{{ $svlan->node->nama_node ?? 'N/A' }}</span>
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 flex flex-wrap items-center gap-4">
            <a href="{{ route('cvlan.create', ['svlan_id' => $svlan->id, 'koneksi_filter' => request('koneksi_filter')]) }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add New CVLAN</span>
            </a>
            <a href="{{ route('cvlan.exportForSvlan', $svlan->id) }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export CSV</span>
            </a>
        </div>

        {{-- Search Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('cvlan.index', $svlan->id) }}" method="GET">
                <div class="flex flex-col sm:flex-row gap-4">
                    {{-- Hidden input untuk menjaga state sorting & filter saat mencari --}}
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="order" value="{{ request('order') }}">
                    <input type="hidden" name="koneksi_filter" value="{{ request('koneksi_filter') }}">
                    <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                    
                    <div class="flex-1">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="text"
                                   name="search"
                                   placeholder="search..."
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => request('koneksi_filter')]) }}"
                           title="Reset Pencarian"
                           class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- CVLAN Data Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">CVLAN Data</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center gap-2">
                                    <span>VLAN</span>
                                    <div class="flex flex-col">
                                        <a href="{{ route('cvlan.index', array_merge(request()->query(), ['svlan_id' => $svlan->id, 'sort' => 'nms', 'order' => 'asc'])) }}">
                                            <i data-lucide="chevron-up" class="w-4 h-4 {{ $sortField == 'nms' && $sortOrder == 'asc' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                        </a>
                                        <a href="{{ route('cvlan.index', array_merge(request()->query(), ['svlan_id' => $svlan->id, 'sort' => 'nms', 'order' => 'desc'])) }}">
                                            <i data-lucide="chevron-down" class="w-4 h-4 {{ $sortField == 'nms' && $sortOrder == 'desc' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center gap-2">
                                    <span>No Network</span>
                                    <div class="flex flex-col">
                                        <a href="{{ route('cvlan.index', array_merge(request()->query(), ['svlan_id' => $svlan->id, 'sort' => 'no_jaringan', 'order' => 'asc'])) }}">
                                            <i data-lucide="chevron-up" class="w-4 h-4 {{ $sortField == 'no_jaringan' && $sortOrder == 'asc' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                        </a>
                                        <a href="{{ route('cvlan.index', array_merge(request()->query(), ['svlan_id' => $svlan->id, 'sort' => 'no_jaringan', 'order' => 'desc'])) }}">
                                            <i data-lucide="chevron-down" class="w-4 h-4 {{ $sortField == 'no_jaringan' && $sortOrder == 'desc' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center gap-2">
                                    <span>Customer</span>
                                    <div class="flex flex-col">
                                        <a href="{{ route('cvlan.index', array_merge(request()->query(), ['svlan_id' => $svlan->id, 'sort' => 'nama_pelanggan', 'order' => 'asc'])) }}">
                                            <i data-lucide="chevron-up" class="w-4 h-4 {{ $sortField == 'nama_pelanggan' && $sortOrder == 'asc' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                        </a>
                                        <a href="{{ route('cvlan.index', array_merge(request()->query(), ['svlan_id' => $svlan->id, 'sort' => 'nama_pelanggan', 'order' => 'desc'])) }}">
                                            <i data-lucide="chevron-down" class="w-4 h-4 {{ $sortField == 'nama_pelanggan' && $sortOrder == 'desc' ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th class="relative px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cvlans as $cvlan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    @if(!is_null($cvlan->nms))
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full">NMS: {{ $cvlan->nms }}</span>
                                    @elseif(!is_null($cvlan->metro))
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2 py-0.5 rounded-full">Metro: {{ $cvlan->metro }}</span>
                                    @elseif(!is_null($cvlan->vpn))
                                        <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-0.5 rounded-full">VPN: {{ $cvlan->vpn }}</span>
                                    @elseif(!is_null($cvlan->inet))
                                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded-full">INET: {{ $cvlan->inet }}</span>
                                    @elseif(!is_null($cvlan->extra))
                                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded-full">EXTRA: {{ $cvlan->extra }}</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $cvlan->no_jaringan ?? 'N/A' }}</td>

                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $cvlan->nama_pelanggan ?? 'N/A' }}</td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('cvlan.edit', ['svlan_id' => $svlan->id, 'id' => $cvlan->id, 'koneksi_filter' => request('koneksi_filter'), 'origin' => 'index']) }}" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200" title="Edit">
                                            <i data-lucide="pen-square" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('cvlan.destroy', ['svlan_id' => $svlan->id, 'id' => $cvlan->id]) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus CVLAN ini?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="koneksi_filter_origin" value="{{ request('koneksi_filter') }}">
                                            <button type="submit" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-red-400 to-red-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="folder-search" class="w-12 h-12 text-gray-400 mb-4"></i>
                                        <p class="font-semibold">There is no CVLAN data in this SVLAN.</p>
                                        <p class="text-sm">Please Add new data using the button above.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination with Per Page Selector and Back Button --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- Per Page Selector (Bottom Left) --}}
            @if($cvlans->hasPages() || $cvlans->total() > 10)
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
            @else
            <div></div>
            @endif

            {{-- Pagination Links (Center) --}}
            @if($cvlans->hasPages())
            <div class="flex-1 flex justify-center">
                {{ $cvlans->appends(request()->query())->links('vendor.pagination.custom-pagination') }}
            </div>
            @else
            <div class="flex-1"></div>
            @endif

            {{-- Back Button (Bottom Right) --}}
            <a href="{{ route('svlan.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back</span>
            </a>
        </div>
    </div>
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
@endsection