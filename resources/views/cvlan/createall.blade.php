@extends('layouts.app')

@section('title', 'Tambah CVLAN Baru')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Tambah CVLAN Baru</h1>
            <a href="{{ route('cvlan.all') }}" class="inline-flex items-center gap-2 py-1.5 px-3 font-semibold text-white bg-gray-500 rounded-lg shadow-sm hover:bg-gray-600 transition-colors">
                &larr; Batal
            </a>
        </div>
        
        <div class="mb-6 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Status Koneksi</h3>
            <div id="connection-buttons">
                <button type="button" id="make-standalone-btn" class="hidden inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                    <i data-lucide="unlink" class="w-5 h-5"></i>
                    Jadikan Mandiri
                </button>
                <button type="button" id="reconnect-svlan-btn" class="inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                    <i data-lucide="link" class="w-5 h-5"></i>
                    Sambungkan ke SVLAN
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                <p class="font-bold">Terjadi Kesalahan:</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cvlan.storeAll') }}" method="POST">
            @csrf
            <input type="hidden" name="is_standalone" id="is_standalone" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Wrapper untuk field SVLAN, awalnya hidden --}}
                <div id="svlan-fields-wrapper" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="svlan_id" class="block text-sm font-medium text-gray-700 mb-1">Hubungkan ke SVLAN</label>
                        <select id="svlan_id" name="svlan_id" class="form-select">
                            @foreach($svlans as $svlan)
                                <option value="{{ $svlan->id }}" {{ old('svlan_id') == $svlan->id ? 'selected' : '' }}>
                                    Node: {{ $svlan->node->nama_node ?? 'N/A' }} | NMS: {{ $svlan->svlan_nms }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PERUBAHAN: Wrapper baru untuk dropdown dan input dinamis --}}
                    <div class="md:col-span-2 mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kolom 1: Dropdown Jenis Koneksi --}}
                        <div>
                            <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Koneksi</label>
                            <select id="connection_type" name="connection_type" class="form-select">
                                <option value="nms" {{ old('connection_type') == 'nms' ? 'selected' : '' }}>NMS</option>
                                <option value="metro" {{ old('connection_type') == 'metro' ? 'selected' : '' }}>Metro</option>
                                <option value="vpn" {{ old('connection_type') == 'vpn' ? 'selected' : '' }}>VPN</option>
                                <option value="inet" {{ old('connection_type') == 'inet' ? 'selected' : '' }}>INET</option>
                                <option value="extra" {{ old('connection_type') == 'extra' ? 'selected' : '' }}>EXTRA</option>
                            </select>
                        </div>

                        {{-- Kolom 2: Input Nilai Koneksi (Dinamis) --}}
                        <div id="connection-value-wrapper" class="hidden">
                            <label id="connection-value-label" for="connection_value" class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                            <input type="number" id="connection_value" name="connection_value" value="{{ old('connection_value') }}" max="9999"
                                oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46" class="form-input">
                        </div>
                    </div>
                </div>

                {{-- Wrapper untuk field Node (mandiri), awalnya terlihat --}}
                <div id="node-field-wrapper" class="md:col-span-2">
                    <label for="node_id" class="block text-sm font-medium text-gray-700 mb-1">Node (Wajib jika mandiri)</label>
                    <select id="node_id" name="node_id" class="form-select">
                        <option value="">Belum Ada Node</option>
                        @foreach($nodes as $node)
                            <option value="{{ $node->id }}" {{ old('node_id') == $node->id ? 'selected' : '' }}>
                                {{ $node->nama_node }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Field Umum --}}
                <div class="md:col-span-2">
                    <label for="cvlan_slot" class="block text-sm font-medium text-gray-700 mb-1">CVLAN</label>
                        <input type="number" id="cvlan_slot" name="cvlan_slot" value="{{ old('cvlan_slot') }}" class="form-input" placeholder="Masukkan nilai..." required max="9999"
                                oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                        max="9999" oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);">
                </div>
                <div class="md:col-span-2">
                    <label for="no_jaringan" class="block text-sm font-medium text-gray-700 mb-1">No Jaringan</label>
                    <input type="text" id="no_jaringan" name="no_jaringan" value="{{ old('no_jaringan') }}" class="form-input">
                </div>
                <div class="md:col-span-2">
                    <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" class="form-input">
                </div>
            </div>

            <div class="mt-8 text-right">
                <button type="submit" class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-white bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg shadow-md hover:-translate-y-0.5 transition-transform duration-200">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    Simpan CVLAN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.form-input, .form-select').addClass('block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm');
    $('#svlan_id, #node_id').select2({ theme: "bootstrap-5", width: '100%' });
    $('#connection_type').select2({ theme: "bootstrap-5", width: '100%', minimumResultsForSearch: Infinity });

    // Elemen utama
    const makeStandaloneBtn = $('#make-standalone-btn');
    const reconnectSvlanBtn = $('#reconnect-svlan-btn');
    const svlanFieldsWrapper = $('#svlan-fields-wrapper');
    const nodeFieldWrapper = $('#node-field-wrapper');
    const isStandaloneInput = $('#is_standalone');

    // Elemen koneksi dinamis
    const connectionTypeSelect = $('#connection_type');
    const connectionValueWrapper = $('#connection-value-wrapper');
    const connectionValueLabel = $('#connection-value-label');
    const connectionValueInput = $('#connection_value');

    function switchToStandaloneMode() {
        svlanFieldsWrapper.hide();
        nodeFieldWrapper.show();
        isStandaloneInput.val('1');
        makeStandaloneBtn.addClass('hidden');
        reconnectSvlanBtn.removeClass('hidden');
    }

    function switchToSvlanMode() {
        nodeFieldWrapper.hide();
        svlanFieldsWrapper.show();
        isStandaloneInput.val('0');
        reconnectSvlanBtn.addClass('hidden');
        makeStandaloneBtn.removeClass('hidden');
    }

    

    makeStandaloneBtn.on('click', switchToStandaloneMode);
    reconnectSvlanBtn.on('click', switchToSvlanMode);

    // PERUBAHAN: Fungsi ini dikembalikan untuk menampilkan input field
    function toggleConnectionValue() {
        const selectedType = connectionTypeSelect.val();
        if (selectedType) {
            const labelText = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
            connectionValueLabel.text('Nilai ' + labelText);
            connectionValueInput.prop('placeholder', 'Masukkan nilai ' + labelText);
            connectionValueWrapper.removeClass('hidden');
        } else {
            connectionValueWrapper.addClass('hidden');
        }
    }
    
    toggleConnectionValue();
    connectionTypeSelect.on('change', toggleConnectionValue);
});
</script>
@endpush