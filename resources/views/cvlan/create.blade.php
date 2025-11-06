@extends('layouts.app')

@section('title', 'Add New CVLAN')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden md:flex">

        <div class="w-full md:w-2/3 p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Cvlan</h1>

            <form action="{{ route('cvlan.store', $svlan->id) }}" method="POST">
                @csrf
                <input type="hidden" name="koneksi_filter_origin" value="{{ request('koneksi_filter') }}">
                <input type="hidden" name="connection_type" value="{{ request('koneksi_filter') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Connection Type (Static Display) --}}
                    @if(request('koneksi_filter'))
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Connection type</label>
                            <p class="block w-full px-3 py-2 bg-gray-200 text-gray-700 border border-gray-300 rounded-md shadow-sm sm:text-sm">
                                {{ strtoupper(request('koneksi_filter')) }}
                            </p>
                        </div>

                        {{-- Connection Value Input --}}
                        <div class="md:col-span-1">
                            <label for="connection_value" class="block text-sm font-medium text-gray-700 mb-1">
                                Value {{ strtoupper(request('koneksi_filter')) }}
                            </label>
                            <input type="number" 
                                   id="connection_value" 
                                   name="connection_value" 
                                   value="{{ old('connection_value') }}"
                                   tabindex="1"
                                   class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Input Value..." 
                                   required
                                   max="9999" 
                                   oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                   onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 9">
                        </div>
                    @endif

                    {{-- No Jaringan --}}
                    <div class="md:col-span-2">
                        <label for="no_jaringan" class="block text-sm font-medium text-gray-700 mb-1">No Jaringan</label>
                        <input type="text" 
                               name="no_jaringan" 
                               id="no_jaringan" 
                               value="{{ old('no_jaringan') }}" 
                               tabindex="2"
                               class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    {{-- Nama Pelanggan --}}
                    <div class="md:col-span-2">
                        <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                        <input type="text" 
                               name="nama_pelanggan" 
                               id="nama_pelanggan" 
                               value="{{ old('nama_pelanggan') }}" 
                               tabindex="3"
                               class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex items-center gap-4">
                    <button type="submit" 
                            tabindex="4"
                            class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-white bg-green-500 hover:bg-green-600 rounded-lg shadow-md transition-colors">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Data
                    </button>
                    <a href="{{ route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => request('koneksi_filter')]) }}" 
                       tabindex="5"
                       class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg shadow-md transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Right Side: Info Panel --}}
        <div class="w-full md:w-1/3 bg-gradient-to-br from-blue-400 to-indigo-600 p-8 text-white flex flex-col justify-center items-center text-center">
            <i data-lucide="network" class="w-16 h-16 mb-4 opacity-80"></i>
            <h2 class="text-2xl font-bold">Manajemen CVLAN</h2>
            <div class="mt-6 w-full border-t border-blue-400/50 pt-6">
                @switch(request('koneksi_filter'))
                    @case('vpn')
                        <p class="text-sm text-blue-200">For SVLAN VPN:</p>
                        <p class="text-xl font-bold">{{ $svlan->svlan_vpn }}</p>
                        @break
                    @case('inet')
                        <p class="text-sm text-blue-200">For SVLAN INET:</p>
                        <p class="text-xl font-bold">{{ $svlan->svlan_inet }}</p>
                        @break
                    @case('metro')
                        <p class="text-sm text-blue-200">For SVLAN Metro:</p>
                        <p class="text-xl font-bold">{{ $svlan->svlan_me }}</p>
                        @break
                    @case('extra')
                        <p class="text-sm text-blue-200">For SVLAN Extra:</p>
                        <p class="text-xl font-bold">{{ $svlan->extra }}</p>
                        @break
                    @case('nms')
                    @default
                        <p class="text-sm text-blue-200">For SVLAN NMS:</p>
                        <p class="text-xl font-bold">{{ $svlan->svlan_nms }}</p>
                @endswitch
            </div>
            <div class="mt-4 w-full pt-4">
                <p class="text-sm text-blue-200">Connected to Node:</p>
                <p class="text-xl font-bold">{{ $svlan->node->nama_node ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>
@endsection
