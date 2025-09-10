@extends('layouts.app')

@section('title', 'List All CVLANs')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header Banner --}}
    <div class="bg-gradient-to-br from-blue-400 to-indigo-600 text-slate-50 rounded-2xl shadow-lg p-6 sm:p-8 my-8 relative">
        <div class="flex flex-wrap items-center justify-between gap-4">
            {{-- Left Side: Title and Actions --}}
            <div>
                <h1 class="text-3xl font-bold drop-shadow-lg">Daftar Semua CVLAN</h1>
                <p class="text-blue-200 mt-1">Menampilkan semua CVLAN yang Connected dan Standalone.</p>
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('cvlan.createall') }}" class="inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-green-500 hover:bg-green-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        <span>Tambah CVLAN</span>
                    </a>
                    <a href="{{ route('cvlan.exportAll', request()->all()) }}" class="inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-teal-500 hover:bg-teal-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                        <i data-lucide="download" class="w-5 h-5"></i>
                        <span>Export CSV</span>
                    </a>
                    <a href="{{ route('svlan.index') }}" class="inline-flex items-center gap-2 py-2 px-4 font-semibold text-black bg-white rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        <span>Kembali ke SVLAN</span>
                    </a>
                </div>
            </div>

            {{-- Right Side: Search and Filter --}}
            <div class="w-full sm:w-auto mt-4 sm:mt-0">
                <form action="{{ route('cvlan.all') }}" method="GET">
                    <div class="grid grid-cols-[1fr,auto] gap-2">

                        <div class="relative">
                            <input type="text" name="search" placeholder="Cari..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2.5 bg-white/10 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 placeholder-blue-100 transition-colors">
                            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-blue-200 pointer-events-none"></i>
                        </div>
                        <button type="submit" class="p-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition-colors flex-shrink-0">
                            <i data-lucide="arrow-right" class="w-6 h-6"></i>
                        </button>
                        <div class="relative">
                            <i data-lucide="filter" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-white pointer-events-none z-10"></i>
                            <select name="koneksi_filter" onchange="this.form.submit()"
                                    class="w-full appearance-none bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2.5 pl-12 pr-8 rounded-lg shadow-md transition-all duration-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-300">
                                <option value="">Filter VLAN</option>
                                <option value="mandiri" {{ request('koneksi_filter') == 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                                <option value="nms" {{ request('koneksi_filter') == 'nms' ? 'selected' : '' }}>NMS</option>
                                <option value="metro" {{ request('koneksi_filter') == 'metro' ? 'selected' : '' }}>Metro</option>
                                <option value="vpn" {{ request('koneksi_filter') == 'vpn' ? 'selected' : '' }}>VPN</option>
                                <option value="inet" {{ request('koneksi_filter') == 'inet' ? 'selected' : '' }}>INET</option>
                                <option value="extra" {{ request('koneksi_filter') == 'extra' ? 'selected' : '' }}>EXTRA</option>
                            </select>
                            <i data-lucide="chevron-down" class="w-6 h-6 absolute right-3 top-1/2 -translate-y-1/2 text-white pointer-events-none"></i>
                        </div>
                        @if(request('search') || request('koneksi_filter') || request('sort'))
                            <a href="{{ route('cvlan.all') }}" title="Reset Filter" class="p-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow-md transition-colors flex-shrink-0">
                                <i data-lucide="rotate-cw" class="w-6 h-6"></i>
                            </a>
                        @else
                            <div></div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Content Table --}}
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">Node</div>
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">Status</div>
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">CVLAN</div>
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider align-middle">
                            VLAN
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">No Jaringan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pelanggan</th>
                        <th class="relative px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($cvlans as $cvlan)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 text-center">
                                {{ $cvlan->svlan->node->nama_node ?? $cvlan->node->nama_node ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                @if($cvlan->svlan)
                                    <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2 py-0.5 rounded-full">Connected</span>
                                    <span class="text-xs text-gray-500 block mt-1">
                                        @if(!is_null($cvlan->nms))
                                            SVLAN-NMS: {{ $cvlan->svlan->svlan_nms }}
                                        @elseif(!is_null($cvlan->metro))
                                            SVLAN-Metro: {{ $cvlan->svlan->svlan_me }}
                                        @elseif(!is_null($cvlan->vpn))
                                            SVLAN-VPN: {{ $cvlan->svlan->svlan_vpn }}
                                        @elseif(!is_null($cvlan->inet))
                                            SVLAN-INET: {{ $cvlan->svlan->svlan_inet }}
                                        @elseif(!is_null($cvlan->extra))
                                            SVLAN-EXTRA: {{ $cvlan->svlan->extra }}
                                        @else
                                            SVLAN: {{ $cvlan->svlan->svlan_nms }}
                                        @endif

                                    </span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full">Standalone</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-center">
                                @if(!$cvlan->svlan)
                                    {{-- Jika Standalone, tampilkan CVLAN Slot --}}
                                    {{ $cvlan->cvlan_slot }}
                                @else
                                    {{-- Jika Connected, tampilkan None --}}
                                    <span class="text-gray-400 italic">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-center">
                                @if($cvlan->svlan)
                                    {{-- Jika Connected, tampilkan detail koneksi --}}
                                    @if(!is_null($cvlan->nms))
                                        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded-full">NMS: {{ $cvlan->nms }}</span>
                                    @elseif(!is_null($cvlan->metro))
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full">Metro: {{ $cvlan->metro }}</span>
                                    @elseif(!is_null($cvlan->vpn))
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5 rounded-full">VPN: {{ $cvlan->vpn }}</span>
                                    @elseif(!is_null($cvlan->inet))
                                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded-full">INET: {{ $cvlan->inet }}</span>
                                    @elseif(!is_null($cvlan->extra))
                                        <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-0.5 rounded-full">EXTRA: {{ $cvlan->extra }}</span>
                                    @else
                                        -
                                    @endif
                                @else
                                    {{-- Jika Standalone, tampilkan None --}}
                                    <span class="text-gray-400 italic">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-center">
                                {{ $cvlan->no_jaringan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-center">
                                {{ $cvlan->nama_pelanggan ?? '-' }}
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    @php
                                        // Menentukan rute dan parameter dasar
                                        $isStandalone = !$cvlan->svlan;
                                        $editRoute = $isStandalone ? 'cvlan.editall' : 'cvlan.edit';
                                        $editParams = $isStandalone
                                            ? ['id' => $cvlan->id]
                                            : ['svlan_id' => $cvlan->svlan_id, 'id' => $cvlan->id];

                                        // Menambahkan parameter 'origin' untuk memberi tahu halaman edit dari mana kita datang
                                        $editParams['origin'] = 'all';
                                    @endphp
                                    <a href="{{ route($editRoute, $editParams) }}"
                                       title="Edit"
                                       class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>

                                    <form action="{{ route('cvlan.destroyall', $cvlan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus CVLAN ini?');">
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

    <div class="my-8">
        {{ $cvlans->appends(request()->query())->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
@endsection
