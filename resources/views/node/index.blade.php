@extends('layouts.app')

@section('title', 'Manajemen Node')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- Header Section --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Node</h1>
        <p class="text-gray-600">Overview of Node and related SVLAN infrastructure</p>
    </div>

    {{-- Add Button, Search and Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 flex items-center justify-between flex-wrap gap-4">

        {{-- Tombol Aksi --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('nodes.create') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg shadow-sm transition-all duration-200">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <span>Tambah Node</span>
            </a>
        </div>

        {{-- Form Pencarian --}}
        <form action="{{ route('nodes.index') }}" method="GET" class="w-full md:w-auto">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text"
                        name="search"
                        placeholder="Cari Nama Node..."
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
            <h2 class="text-xl font-semibold text-gray-900">Data Semua Node</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider w-1/4">Nama Node</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">SVLAN Terkait</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                                    <div class="flex flex-col gap-3">
                                        @foreach($node->svlans as $svlan)
                                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                                                <span class="font-semibold">NMS:</span><span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full">{{ $svlan->svlan_nms }}</span>
                                                <span class="font-semibold">ME:</span><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full">{{ $svlan->svlan_me }}</span>
                                                <span class="font-semibold">VPN:</span><span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full">{{ $svlan->svlan_vpn }}</span>
                                                <span class="font-semibold">INET:</span><span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full">{{ $svlan->svlan_inet }}</span>
                                                @if($svlan->extra)
                                                    <span class="font-semibold">Extra:</span><span class="px-2 py-0.5 bg-gray-100 text-gray-800 rounded-full">{{ $svlan->extra }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Belum ada SVLAN terkait</span>
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
                                        <p class="font-semibold">Tidak ada Node yang cocok dengan pencarian "{{ request('search') }}".</p>
                                    @else
                                        <p class="font-semibold">Belum ada data Node di sistem.</p>
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
    @if($nodes->hasPages())
        <div class="mt-6">
            {{ $nodes->appends(request()->query())->links() }}
            {{-- Jika Anda memiliki custom pagination view, gunakan: --}}
            {{-- {{ $nodes->appends(request()->query())->links('vendor.pagination.custom-pagination') }} --}}
        </div>
    @endif
</div>

<style>
/* Custom scrollbar untuk table (opsional, tapi bagus untuk konsistensi) */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}
.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection