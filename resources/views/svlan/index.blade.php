@extends('layouts.app')
@section('title', 'Data SVLAN')
@section('content' )


<div class="container mx-auto px-4">
    {{-- Banner Atas --}}
    <div class="bg-gradient-to-br from-blue-800 to-blue-400 text-slate-50 rounded-2xl shadow-lg p-6 sm:p-10 my-8 relative overflow-hidden">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold drop-shadow-lg">Data SVLAN</h1>
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('nodes.index') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-black bg-white rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <i data-lucide="network" class="w-5 h-5"></i>
                        <span>Lihat Semua Node</span>
                    </a>
                    <a href="{{ route('cvlan.all') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-black bg-white rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <i data-lucide="list-checks" class="w-5 h-5"></i>
                        <span>Lihat Semua CVLAN</span>
                    </a>
                    <a href="{{ route('svlan.exportAll', request()->query()) }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-white bg-teal-500 hover:bg-teal-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <i data-lucide="cloud-download" class="w-5 h-5"></i>
                        <span>Export Semua</span>
                    </a>
                    <a href="{{ route('svlan.create') }}" class="inline-flex items-center gap-2 py-2.5 px-5 font-semibold text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        <span>Tambah SVLAN</span>
                    </a>

                </div>
            </div>
        </div>
        {{-- MODIFIED: Lucide Icon as background element --}}
        <div class="absolute right-2 top-1 opacity-20 pointer-events-none">
            <i data-lucide="waypoints" class="w-64 h-64 text-white"></i>
        </div>
    </div>

    {{-- Konten Tabel --}}
    <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 my-8 overflow-x-auto">

        {{--Search Bar--}}
        <div class="mb-6">
            <form action="{{ route('svlan.index') }}" method="GET" class="flex items-center gap-3">
                <div class="relative w-full">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        placeholder="Cari data Node ID, VPN, NMS..."
                        value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2.5 bg-blue-500 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 placeholder-blue-100 transition-colors">
                </div>
                <button type="submit" class="flex-shrink-0 p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition-colors">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('svlan.index') }}" title="Reset Pencarian" class="flex-shrink-0 p-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-md transition-colors">
                        <i data-lucide="rotate-cw" class="w-5 h-5"></i>
                    </a>
                @endif
            </form>
        </div>

        <table class="w-full text-sm text-left text-gray-600 border-collapse">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    {{-- MODIFIED: Added sorting links and icons --}}
                    <th scope="col" class="px-6 py-3 border border-slate-300">
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
                    </th>
                    <th scope="col" class="px-6 py-3 border border-slate-300 text-center">NMS</th>
                    <th scope="col" class="px-6 py-3 border border-slate-300 text-center">ME</th>
                    <th scope="col" class="px-6 py-3 border border-slate-300 text-center">VPN</th>
                    <th scope="col" class="px-6 py-3 border border-slate-300 text-center">INET</th>
                    <th scope="col" class="px-6 py-3 border border-slate-300 text-center">Extra</th>
                    <th scope="col" class="px-6 py-3 border border-slate-300">Keterangan</th>
                    <th scope="col" class="px-6 py-3 text-center border border-slate-300">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($svlans as $svlan)
                <tr class="bg-white hover:bg-gray-50 cursor-pointer">
                    <td class="px-6 py-4 font-bold text-gray-900 border border-slate-300 align-middle">{{ $svlan->node->nama_node ?? $svlan->node_id }}</td>

                    {{-- KOLOM NMS (BELUM ADA FILTER DI CONTROLLER, TAPI LINK DISIAPKAN) --}}
                    <td class="px-6 py-4 border border-slate-300 text-center align-middle">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'nms']) }}" title="Lihat CVLAN dengan NMS ini" class="block hover:opacity-80 transition-opacity">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $svlan->svlan_nms }}</span>
                        </a>
                    </td>

                    {{-- KOLOM ME (FILTER KE 'metro') --}}
                    <td class="px-6 py-4 border border-slate-300 text-center align-middle">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'metro']) }}" title="Lihat CVLAN dengan Metro ini" class="block hover:opacity-80 transition-opacity">
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $svlan->svlan_me }}</span>
                        </a>
                    </td>

                    {{-- KOLOM VPN (FILTER KE 'vpn') --}}
                    <td class="px-6 py-4 border border-slate-300 text-center align-middle">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'vpn']) }}" title="Lihat CVLAN dengan VPN ini" class="block hover:opacity-80 transition-opacity">
                            <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $svlan->svlan_vpn }}</span>
                        </a>
                    </td>

                    {{-- KOLOM INET (FILTER KE 'inet') --}}
                    <td class="px-6 py-4 border border-slate-300 text-center align-middle">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'inet']) }}" title="Lihat CVLAN dengan INET ini" class="block hover:opacity-80 transition-opacity">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $svlan->svlan_inet }}</span>
                        </a>
                    </td>

                    {{-- KOLOM Extra (FILTER KE 'extra') --}}
                    <td class="px-6 py-4 border border-slate-300 text-center align-middle">
                        <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => 'extra']) }}" title="Lihat CVLAN dengan EXTRA ini" class="block hover:opacity-80 transition-opacity">
                            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $svlan->extra }}</span>
                        </a>
                    </td>
                    <td class="px-6 py-4 border border-slate-300 align-middle">{{ $svlan->keterangan }}</td>
                    <td class="px-2 py-4 text-center border border-slate-300 align-middle">
                        {{-- Mengubah layout menjadi horizontal (flex-row) --}}
                        <div class="flex flex-col items-center justify-center gap-2">

                            <a href="{{ route('svlan.edit', $svlan->id) }}" title="Edit" class="inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                                <i data-lucide="file-pen-line" class="w-4 h-4"></i>
                            </a>

                            <button type="button"
                                    title="Hapus"
                                    class="delete-svlan-btn inline-flex items-center justify-center p-2 font-semibold text-white bg-gradient-to-br from-red-600 to-red-700 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200"
                                    data-svlan-id="{{ $svlan->id }}"
                                    data-cvlan-count="{{ $svlan->cvlans->count() }}"
                                    data-action-url="{{ route('svlan.destroy', $svlan->id) }}">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-400 py-10 border border-slate-300">
                        @if(request('search'))
                            Data tidak ditemukan untuk pencarian '<strong>{{ request('search') }}</strong>'.
                        @else
                            <div class="flex flex-col items-center">
                            <i data-lucide="folder-search" class="w-12 h-12 text-gray-400 mb-4"></i><br>
                            <b>Belum ada Data SVLAN di sistem,</b><br>klik tombol tambah untuk menambahkan SVLAN.
                        </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="my-8">
        {{ $svlans->appends(request()->query())->links('vendor.pagination.custom-pagination') }}
    </div>
</div>

{{-- MODAL SECTION (No changes) --}}
<div id="deleteSvlanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Konfirmasi Hapus SVLAN</h3>
            <div class="mt-2 px-7 py-3">
                <p id="modal-text" class="text-sm text-gray-500">
                    SVLAN ini memiliki <strong id="cvlan-count"></strong> CVLAN terkait. Apa yang ingin Anda lakukan?
                </p>
            </div>
            <form id="deleteSvlanForm" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="cascade_delete" id="cascadeDeleteInput">
                <div class="items-center px-4 py-3">
                    <button id="deleteCascadeBtn" type="button" class="w-full mb-2 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Hapus SVLAN Beserta CVLAN Terkait</button>
                    <button id="deleteOrphanBtn" type="button" class="w-full mb-2 px-4 py-2 bg-amber-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500">Hanya Hapus SVLAN (Jadikan CVLAN mandiri)</button>
                    <button id="cancelBtn" type="button" class="w-full px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT SECTION --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    lucide.createIcons();

    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.addEventListener('click', function (event) {
            if (event.target.closest('a, button, form')) {
                return;
            }
            window.location.href = this.dataset.href;
        });
    });

    document.querySelectorAll('.toggle-cvlan-visibility').forEach(button => {
        button.addEventListener('click', function() {
            const targetSelector = this.dataset.target;
            const cvlanContainer = document.querySelector(targetSelector);

            if (cvlanContainer) {
                const allCvlanItems = cvlanContainer.querySelectorAll('.cvlan-item');

                allCvlanItems.forEach((item, index) => {
                    if (index >= 2) {
                        item.classList.toggle('hidden');
                    }
                });

                const isHidden = cvlanContainer.querySelector('.cvlan-item:nth-child(3)').classList.contains('hidden');
                if (isHidden) {
                    this.textContent = this.dataset.textShow;
                } else {
                    this.textContent = this.dataset.textHide;
                }
            }
        });
    });

    // Modal logic (No changes)
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
                if (confirm('Apakah Anda yakin ingin menghapus SVLAN ini? Tidak ada CVLAN yang terkait.')) {
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
});
</script>
@endsection
