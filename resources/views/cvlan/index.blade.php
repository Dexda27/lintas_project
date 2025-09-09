@extends('layouts.app')

@section('title', 'Daftar CVLAN untuk ' . $svlan->svlan_nms)

@section('content')
<div class="container mx-auto px-4">
    <div class="bg-gradient-to-br from-blue-500 to-indigo-700 text-slate-50 rounded-2xl shadow-lg p-6 sm:p-8 my-8 relative">
        <div class="flex flex-wrap items-center justify-between gap-4">
            
            {{-- Sisi Kiri: Judul dan Tombol Aksi --}}
            <div>
                <h1 class="text-3xl font-bold drop-shadow-lg">Data CVLAN</h1>
                <p class="text-blue-200 mt-1">
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
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('cvlan.create', ['svlan_id' => $svlan->id, 'koneksi_filter' => request('koneksi_filter')]) }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg shadow-md">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        <span>Tambah CVLAN</span>
                    </a>
                    <a href="{{ route('cvlan.exportForSvlan', $svlan->id) }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-white bg-teal-500 hover:bg-teal-600 rounded-lg shadow-md">
                        <i data-lucide="download" class="w-5 h-5"></i>
                        <span>Export CSV</span>
                    </a>
                    <a href="{{ route('svlan.index') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-black bg-white rounded-lg shadow-md">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        <span>Kembali ke SVLAN</span>
                    </a>
                </div>
            </div>
            
            {{-- Sisi Kanan: Form Pencarian dan Filter dengan Flexbox --}}
            <div class="w-full sm:w-auto mt-4 sm:mt-0">
                <form action="{{ route('cvlan.index', $svlan->id) }}" method="GET">
                    {{-- Hidden input untuk menjaga state --}}
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="order" value="{{ request('order') }}">
                    
                    <div class="flex flex-col gap-2">

                        <div class="flex items-center gap-2">
                            <div class="relative flex-grow">
                                <input type="text" name="search" placeholder="Cari..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2.5 bg-white/10 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 placeholder-blue-100 transition-colors">
                                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-blue-200 pointer-events-none"></i>
                            </div>
                            <button type="submit" class="p-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition-colors flex-shrink-0">
                                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="relative flex-grow">
                                <i data-lucide="filter" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-white pointer-events-none z-10"></i>
                                <select name="koneksi_filter" onchange="this.form.submit()" 
                                        class="w-full appearance-none bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2.5 pl-12 pr-8 rounded-lg shadow-md transition-all duration-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-300">
                                    <option value="nms" {{ request('koneksi_filter') == 'nms' ? 'selected' : '' }}>NMS</option>
                                    <option value="metro" {{ request('koneksi_filter') == 'metro' ? 'selected' : '' }}>Metro</option>
                                    <option value="vpn" {{ request('koneksi_filter') == 'vpn' ? 'selected' : '' }}>VPN</option>
                                    <option value="inet" {{ request('koneksi_filter') == 'inet' ? 'selected' : '' }}>INET</option>
                                    <option value="extra" {{ request('koneksi_filter') == 'extra' ? 'selected' : '' }}>EXTRA</option>
                                </select>
                                <i data-lucide="chevron-down" class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-white pointer-events-none"></i>
                            </div>
                            
                            @if(request('search'))
                                <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => request('koneksi_filter')]) }}" 
                                title="Reset Pencarian" 
                                class="p-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow-md transition-colors flex-shrink-0">
                                    <i data-lucide="rotate-cw" class="w-5 h-5"></i>
                                </a>
                            @endif
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>

    

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
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
                            {{-- Colspan diubah dari 7 menjadi 6 karena satu kolom dihapus --}}
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
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. Inisialisasi ikon Lucide
        lucide.createIcons();

        // 2. Logika untuk dropdown filter
        const dropdownContainer = document.getElementById('filter-dropdown-container');
        const dropdownButton = document.getElementById('filter-dropdown-button');
        const dropdownMenu = document.getElementById('filter-dropdown-menu');

        if (dropdownButton && dropdownMenu && dropdownContainer) {
            dropdownButton.addEventListener('click', function (event) {
                // Mencegah event 'click' menyebar ke document
                event.stopPropagation(); 
                dropdownMenu.classList.toggle('hidden');
            });

            // Klik di luar dropdown akan menutupnya
            document.addEventListener('click', function (event) {
                if (!dropdownContainer.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection