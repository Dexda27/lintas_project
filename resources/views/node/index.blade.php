@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4">
    {{-- Banner Atas --}}
    <div class="bg-gradient-to-br from-purple-800 to-purple-400 text-slate-50 rounded-2xl shadow-lg p-6 sm:p-10 my-8 relative overflow-hidden">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold drop-shadow-lg">Data Semua Node</h1>
                <div class="mt-4 flex items-center gap-3">
                    {{-- TOMBOL TAMBAH NODE BARU --}}
                    <a href="{{ route('nodes.create') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-white bg-green-500 hover:bg-green-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        <span>Tambah Node</span>
                    </a>
                    {{-- Generate Sample
                    <a href="{{ route('nodes.generateSample') }}" 
                    class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-white bg-blue-500 hover:bg-blue-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out"
                    onclick="return confirm('Apakah Anda yakin ingin membuat 200 data sample? Data sample lama akan dihapus.');">
                        <i data-lucide="test-tube-2" class="w-5 h-5"></i>
                        <span>Generate Sample</span>
                    </a>
                    --}}
                    
                    <a href="{{ route('svlan.index') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-black bg-white rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        <span>Kembali ke SVLAN</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <div class="text-black">
                            <form action="{{ route('nodes.index') }}" method="GET" class="relative w-72">
                                <input type="text" name="search" id="search-input" 
                                       placeholder="Cari data Node ID, VPN, NMS..." 
                                       value="{{ request('search') }}"
                                       class="bg-white w-full h-10 pl-4 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <button type="submit" id="search-submit-btn" class="absolute top-0 right-0 h-10 w-10 flex items-center justify-center text-gray-600 hover:text-blue-600">
                                    <i data-lucide="search" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </div>
                        {{-- NEW: Reset Search Button --}}
                        @if(request('search'))
                            <a href="{{ route('nodes.index') }}" title="Reset Pencarian" class="inline-flex items-center justify-center w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full transition-colors">
                                <i data-lucide="rotate-cw" class="w-5 h-5 text-white"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute right-1 top-1 opacity-20 pointer-events-none">
            <i data-lucide="network" class="w-64 h-64 text-white"></i>
        </div>
    </div>

    {{-- Konten Tabel --}}
    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 my-8 overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600 border-collapse">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 border border-slate-300 w-1/3">Nama Node</th>
                    <th scope="col" class="px-6 py-3 border border-slate-300">SVLAN Terkait</th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 text-center">Aksi</th> 
                
                </tr>
            </thead>
            <tbody>
                @forelse($nodes as $node)
                <tr class="bg-white hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold text-gray-900 border border-slate-300 align-top">{{ $node->nama_node }}</td>
                    <td class="px-6 py-4 border border-slate-300 align-top">
                        @if($node->svlans->isNotEmpty())
                            <div class="flex flex-col gap-2">
                                @foreach($node->svlans as $svlan)
                                    <div class="p-2 bg-slate-100 rounded-lg text-xs">
                                        <span class="text-gray-700 ml-2">
                                            NMS: {{ $svlan->svlan_nms }}| ME: {{ $svlan->svlan_me }}| VPN: {{ $svlan->svlan_vpn }} | INET: {{ $svlan->svlan_inet }}| Extra: {{ $svlan->extra }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center">
                                <i data-lucide="folder-search" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <div class="text-center text-gray-400 italic text-xs py-2">Belum ada SVLAN terkait</div>
                            </div>
                        @endif
                    </td>
                    {{-- TOMBOL-TOMBOL AKSI --}}
                    <td class="px-2 py-2 text-center border border-slate-300 align-middle">
                        <div class="flex flex-col items-center justify-center gap-2">

                            <a href="{{ route('nodes.edit', $node->id) }}" title="Edit" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                <i data-lucide="file-pen-line" class="w-4 h-4"></i>
                            </a>
                            
                            <form action="{{ route('nodes.destroy', $node->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Node ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-red-600 to-red-700 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-gray-400 py-10 border border-slate-300">
                        <div class="flex flex-col items-center">
                            <i data-lucide="folder-search" class="w-12 h-12 text-gray-400 mb-4"></i><br>
                        </div>
                        <b>Belum ada Data Node di sistem,</b><br>klik tombol tambah untuk menambahkan Node ID.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{--pagination--}}
    <div class="mt-4">
        {{ $nodes->appends(request()->query())->links() }}
    </div>
</div>
    

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>
@endsection