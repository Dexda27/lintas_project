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
                        Untuk SVLAN VPN: <span class="font-bold">{{ $svlan->svlan_vpn }}</span>
                        @break
                    @case('inet')
                        Untuk SVLAN INET: <span class="font-bold">{{ $svlan->svlan_inet }}</span>
                        @break
                    @case('extra')
                    Untuk SVLAN EXTRA: <span class="font-bold">{{ $svlan->extra }}</span>
                        @break
                    @case('metro')
                        Untuk SVLAN Metro: <span class="font-bold">{{ $svlan->svlan_me }}</span>
                        @break
                    @case('nms')
                        Untuk SVLAN NMS: <span class="font-bold">{{ $svlan->svlan_nms }}</span>
                        @break
                    @default
                        Untuk SVLAN: <span class="font-bold">{{ $svlan->svlan_nms }}</span>
                @endswitch
                <br>Node: <span class="font-bold">{{ $svlan->node->nama_node ?? 'N/A' }}</span>
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 flex flex-wrap items-center gap-4">
            <a href="{{ route('cvlan.create', ['svlan_id' => $svlan->id, 'koneksi_filter' => request('koneksi_filter')]) }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah CVLAN</span>
            </a>
            <a href="{{ route('cvlan.exportForSvlan', $svlan->id) }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export CSV</span>
            </a>
        </div>

        {{-- Search and Filter Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form action="{{ route('cvlan.index', $svlan->id) }}" method="GET">
                <div class="flex flex-col sm:flex-row gap-4">
                    {{-- Hidden input untuk menjaga state sorting & filter saat mencari --}}
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="order" value="{{ request('order') }}">
                    <div class="flex-1">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="text"
                                   name="search"
                                   placeholder="Cari..."
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    {{-- kata juven ga perlu
                    <div class="sm:w-48">
                        <select name="koneksi_filter" onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="nms" {{ request('koneksi_filter') == 'nms' ? 'selected' : '' }}>NMS</option>
                            <option value="metro" {{ request('koneksi_filter') == 'metro' ? 'selected' : '' }}>Metro</option>
                            <option value="vpn" {{ request('koneksi_filter') == 'vpn' ? 'selected' : '' }}>VPN</option>
                            <option value="inet" {{ request('koneksi_filter') == 'inet' ? 'selected' : '' }}>INET</option>
                            <option value="extra" {{ request('koneksi_filter') == 'extra' ? 'selected' : '' }}>EXTRA</option>
                        </select>
                    </div>
                    --}}
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
                               VLAN
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No Jaringan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pelanggan</th>
                            <th class="relative px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cvlans as $cvlan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{-- LOGIKA NMS DIMASUKKAN KE DALAM BLOK @if INI --}}
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

                                            {{-- TAMBAHKAN INPUT TERSEMBUNYI INI --}}
                                            <input type="hidden" name="koneksi_filter_origin" value="{{ request('koneksi_filter') }}">

                                            <button type="submit" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-red-400 to-red-500 rounded-lg shadow-sm" title="Hapus">
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
                                        <p class="font-semibold">Belum ada data CVLAN untuk SVLAN ini.</p>
                                        <p class="text-sm">Silakan tambah data melalui tombol di atas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <a href="{{ route('svlan.index') }}"
            class="inline-flex items-center gap-2 mt-5 px-3 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>


<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>
@endsection