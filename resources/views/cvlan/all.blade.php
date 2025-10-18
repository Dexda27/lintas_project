@extends('layouts.app')

@section('title', 'List All CVLANs')

@section('content')
<div class="min-h-screen bg-gray-50">

        {{-- Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard CVLAN Management</h1>
            <p class="text-gray-600">Overview of CVLAN infrastructure</p>
        </div>

        {{-- Export, Add Button, Search and Filter Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 flex items-center justify-between flex-wrap gap-4">

        <div class="flex items-center gap-4">
            <a href="{{ route('cvlan.exportAll', request()->all()) }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg shadow-sm transition-all duration-200">
                <i data-lucide="download" class="w-5 h-5"></i>
                <span>Export CVLAN</span>
            </a>
            <a href="{{ route('cvlan.createall') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg shadow-sm transition-all duration-200">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <span>Add New CVLAN</span>
            </a>
        </div>

        <form action="{{ route('cvlan.all') }}" method="GET" class="w-full md:w-auto">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text"
                        name="search"
                        placeholder="Search Node ID, VPN, NMS..."
                        value="{{ request('search') }}"
                        class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="w-full sm:w-48">
                    <select name="koneksi_filter"
                            onchange="this.form.submit()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="mandiri" {{ request('koneksi_filter') == 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="nms" {{ request('koneksi_filter') == 'nms' ? 'selected' : '' }}>NMS</option>
                        <option value="metro" {{ request('koneksi_filter') == 'metro' ? 'selected' : '' }}>Metro</option>
                        <option value="vpn" {{ request('koneksi_filter') == 'vpn' ? 'selected' : '' }}>VPN</option>
                        <option value="inet" {{ request('koneksi_filter') == 'inet' ? 'selected' : '' }}>INET</option>
                        <option value="extra" {{ request('koneksi_filter') == 'extra' ? 'selected' : '' }}>EXTRA</option>
                    </select>
                </div>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    Search
                </button>
                @if(request('search') || request('koneksi_filter') || request('sort'))
                    <a href="{{ route('cvlan.all') }}"
                    class="px-5 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

        {{-- CVLAN Data Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 md:bg-transparent md:shadow-none md:border-none">
            <div class="px-6 py-4 border-b border-gray-200 hidden md:block">
                <h2 class="text-xl font-semibold text-gray-900">CVLAN Data</h2>
            </div>

            <div class="md:overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 hidden md:table-header-group">
                        <tr>
                            <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Node ID</th>
                            <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">CVLAN</th>
                            <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">VLAN</th>
                            <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Network No</th>
                            <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="md:bg-white md:divide-y md:divide-gray-200">
                        @forelse ($cvlans as $cvlan)
                            {{-- [MODIFIED] Menghilangkan 'border' dan 'md:border-none' untuk hanya menggunakan shadow --}}
                            <tr class="block md:table-row bg-white rounded-lg shadow-xl mb-4 md:shadow-none md:rounded-none md:mb-0 hover:bg-gray-50 md:border-b">
                                <td class="px-4 py-3 md:px-6 md:py-4 block md:table-cell text-right md:text-center">
                                    <span class="float-left font-semibold md:hidden">Node ID</span>
                                    {{ $cvlan->svlan->node->nama_node ?? $cvlan->node->nama_node ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 md:px-6 md:py-4 block md:table-cell text-right md:text-center">
                                    <span class="float-left font-semibold md:hidden">Status</span>
                                    <div>
                                        @if($cvlan->svlan)
                                            <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2 py-0.5 rounded-full">Connected</span>
                                            <span class="text-xs text-gray-500 block mt-1">
                                                @if(!is_null($cvlan->nms)) SVLAN-NMS: {{ $cvlan->svlan->svlan_nms }}
                                                @elseif(!is_null($cvlan->metro)) SVLAN-Metro: {{ $cvlan->svlan->svlan_me }}
                                                @elseif(!is_null($cvlan->vpn)) SVLAN-VPN: {{ $cvlan->svlan->svlan_vpn }}
                                                @elseif(!is_null($cvlan->inet)) SVLAN-INET: {{ $cvlan->svlan->svlan_inet }}
                                                @elseif(!is_null($cvlan->extra)) SVLAN-EXTRA: {{ $cvlan->svlan->extra }}
                                                @else SVLAN: {{ $cvlan->svlan->svlan_nms }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full">Standalone</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 md:px-6 md:py-4 block md:table-cell text-right md:text-center">
                                    <span class="float-left font-semibold md:hidden">CVLAN</span>
                                    @if(!$cvlan->svlan) {{ $cvlan->cvlan_slot }}
                                    @else <span class="text-gray-400 italic">None</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 md:px-6 md:py-4 block md:table-cell text-right md:text-center">
                                    <span class="float-left font-semibold md:hidden">VLAN</span>
                                    <div>
                                        @if($cvlan->svlan)
                                            @if(!is_null($cvlan->nms)) <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded-full">NMS: {{ $cvlan->nms }}</span>
                                            @elseif(!is_null($cvlan->metro)) <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full">Metro: {{ $cvlan->metro }}</span>
                                            @elseif(!is_null($cvlan->vpn)) <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-0.5 rounded-full">VPN: {{ $cvlan->vpn }}</span>
                                            @elseif(!is_null($cvlan->inet)) <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded-full">INET: {{ $cvlan->inet }}</span>
                                            @elseif(!is_null($cvlan->extra)) <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-0.5 rounded-full">EXTRA: {{ $cvlan->extra }}</span>
                                            @else -
                                            @endif
                                        @else <span class="text-gray-400 italic">None</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 md:px-6 md:py-4 block md:table-cell text-right md:text-center">
                                    <span class="float-left font-semibold md:hidden">Network No</span>
                                    {{ $cvlan->no_jaringan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 md:px-6 md:py-4 block md:table-cell text-right md:text-center">
                                    <span class="float-left font-semibold md:hidden">Customer</span>
                                    {{ $cvlan->nama_pelanggan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 md:px-6 md:py-4 block md:table-cell text-right md:text-center">
                                    <span class="float-left font-semibold md:hidden">Actions</span>
                                    <div class="flex items-center justify-end md:justify-center gap-2">
                                        @php
                                            $isStandalone = !$cvlan->svlan;
                                            $editRoute = $isStandalone ? 'cvlan.editall' : 'cvlan.edit';
                                            $editParams = $isStandalone
                                                ? ['id' => $cvlan->id]
                                                : ['svlan_id' => $cvlan->svlan_id, 'id' => $cvlan->id];
                                            $editParams['origin'] = 'all';
                                        @endphp
                                        <a href="{{ route($editRoute, $editParams) }}" title="Edit" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>

                                        <form action="{{ route('cvlan.destroyall', $cvlan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus CVLAN ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-red-400 to-red-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="folder-search" class="w-12 h-12 text-gray-400 mb-4"></i>
                                        @if(request('search') || request('koneksi_filter'))
                                            <p class="font-semibold">Tidak ada CVLAN yang cocok dengan filter.</p>
                                        @else
                                            <p class="font-semibold">Belum ada data CVLAN di sistem.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($cvlans->hasPages())
            <div class="mt-6">
                {{ $cvlans->appends(request()->query())->links('vendor.pagination.custom-pagination') }}
            </div>
        @endif
</div>
@endsection
