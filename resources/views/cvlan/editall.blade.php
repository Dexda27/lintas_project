@extends('layouts.app')

@section('title', 'Edit CVLAN')

@section('content')
@php
    // Untuk editall, URL kembali selalu ke halaman daftar semua CVLAN.
    $backUrl = route('cvlan.all');

    // Menentukan jenis koneksi aktif jika ada (untuk old value)
    $activeFilter = '';
    if (old('connection_type')) {
        $activeFilter = old('connection_type');
    }
@endphp

<div class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Kolom Form Utama --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 md:p-10 h-full flex flex-col">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-4 flex-wrap gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">Edit CVLAN</h1>
                            <p class="text-gray-500 mt-1">Mengubah detail untuk CVLAN Standalone</p>
                        </div>
                    </div>
                    
                    {{-- Tombol Aksi Koneksi --}}
                    <div class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Ubah Status Koneksi</h3>
                        <div id="connection-buttons">
                            <button type="button" id="make-standalone-btn" class="hidden items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                                <i data-lucide="unlink" class="w-5 h-5"></i>
                                Jadikan Standalone
                            </button>
                            <button type="button" id="reconnect-svlan-btn" class="inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                                <i data-lucide="link" class="w-5 h-5"></i>
                                Sambungkan ke SVLAN
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Gunakan tombol ini untuk mengubah status koneksi CVLAN.</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <strong class="font-bold">Oops! Ada kesalahan.</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cvlan.updateall', ['id' => $cvlan->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_standalone" id="is_standalone" value="1">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Wrapper untuk field yang terkait SVLAN --}}
                        <div id="svlan-fields-wrapper" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Dropdown untuk memilih SVLAN (memakan 2 kolom) --}}
                            <div class="md:col-span-2">
                                <label for="svlan_id" class="block text-sm font-medium text-gray-700 mb-1">Terhubung ke SVLAN</label>
                                <select id="svlan_id" name="svlan_id" class="block w-full">
                                    @foreach($svlans as $svlan)
                                        <option 
                                            value="{{ $svlan->id }}" 
                                            {{ old('svlan_id') == $svlan->id ? 'selected' : '' }}
                                            data-nms="{{ $svlan->svlan_nms }}"
                                            data-metro="{{ $svlan->svlan_me }}"
                                            data-vpn="{{ $svlan->svlan_vpn }}"
                                            data-inet="{{ $svlan->svlan_inet }}"
                                            data-extra="{{ $svlan->extra }}"
                                            data-node="{{ $svlan->node->nama_node ?? 'N/A' }}"
                                        >
                                            {{-- Teks ini akan diisi oleh JavaScript --}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                                {{-- Kolom Pertama: Jenis Koneksi --}}
                                <div>
                                    <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Koneksi</label>
                                    <select id="connection_type" name="connection_type" class="block w-full">
                                        <option value="nms" {{ old('connection_type') == 'nms' ? 'selected' : '' }}>NMS</option>
                                        <option value="metro" {{ old('connection_type') == 'metro' ? 'selected' : '' }}>Metro</option>
                                        <option value="vpn" {{ old('connection_type') == 'vpn' ? 'selected' : '' }}>VPN</option>
                                        <option value="inet" {{ old('connection_type') == 'inet' ? 'selected' : '' }}>INET</option>
                                        <option value="extra" {{ old('connection_type') == 'extra' ? 'selected' : '' }}>EXTRA</option>
                                    </select>
                                </div>

                                {{-- Kolom Kedua: Nilai Koneksi --}}
                                <div id="connection-value-wrapper">
                                    <label id="connection-value-label" for="connection_value" class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                                    <input type="number" id="connection_value" name="connection_value" value="{{ old('connection_value') }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm" max="9999" oninput="if (this.value.length > 4) this.value = this.value.slice(0, 4);">
                                </div>

                            </div>

                        </div>
                            
                            {{-- Wrapper untuk field Node (mode standalone) --}}
                            <div id="node-field-wrapper" class="md:col-span-2 space-y-6">
                                <div>
                                    <label for="node_id" class="block text-sm font-medium text-gray-700 mb-1">Node (Wajib jika mandiri)</label>
                                    <select id="node_id" name="node_id" class="block w-full">
                                        @foreach($nodes as $node)
                                            <option value="{{ $node->id }}" {{ old('node_id', $cvlan->node_id) == $node->id ? 'selected' : '' }}>
                                                {{ $node->nama_node }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="cvlan_slot" class="block text-sm font-medium text-gray-700 mb-1">CVLAN (Wajib jika mandiri)</label>
                                    <input type="text" name="cvlan_slot" id="cvlan_slot" value="{{ old('cvlan_slot', $cvlan->cvlan_slot) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm" max="9999" oninput="if (this.value.length > 4) this.value = this.value.slice(0, 4);" onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46">
                                </div>
                            </div>

                            {{-- Field Umum yang selalu tampil --}}
                            <div class="md:col-span-2">
                                <label for="no_jaringan" class="block text-sm font-medium text-gray-700 mb-1">No Jaringan</label>
                                <input type="text" name="no_jaringan" id="no_jaringan" value="{{ old('no_jaringan', $cvlan->no_jaringan) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" id="nama_pelanggan" value="{{ old('nama_pelanggan', $cvlan->nama_pelanggan) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <div class="mt-8 flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-white bg-green-500 hover:bg-green-600 rounded-lg shadow-md transition-colors">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                Simpan Perubahan
                            </button>
                            
                            <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg shadow-md transition-colors">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Kolom Info Kanan --}}
            <div class="lg:col-span-4">
                <div class="bg-gradient-to-br from-blue-400 to-indigo-600 p-8 text-white rounded-2xl shadow-lg h-full flex flex-col justify-center items-center text-center">
                    <div id="info-panel-icon">
                        <i data-lucide="server-cog" class="w-16 h-16 mb-4 opacity-80"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Edit CVLAN</h3>
                    <p id="info-panel-text" class="opacity-90 leading-relaxed">
                        Anda sedang mengedit CVLAN yang berstatus **Standalone**.
                        <br><br>Gunakan form di samping untuk menghubungkannya ke SVLAN yang tersedia atau mengubah detail lainnya.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Style untuk Select2 agar konsisten dengan Tailwind */
    .select2-container--bootstrap-5 .select2-selection {
        background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 0.375rem;
        min-height: 42px; padding: 0.375rem 0.75rem; display: flex; align-items: center; box-shadow: 0 0 #0000;
    }
    .select2-container--bootstrap-5 .select2-selection__rendered { color: #374151; line-height: 1.25rem; }
    .select2-container--bootstrap-5 .select2-selection__arrow { height: 100%; }
    .select2-container--bootstrap-5.select2-container .select2-search--dropdown .select2-search__field { border-radius: 0.375rem; }
    .select2-container { width: 100% !important; }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #3b82f6; box-shadow: 0 0 0 1px rgba(59,130,246,0.2);
    }
</style>
<script>
$(document).ready(function() {
    // Deklarasi elemen
    const makeStandaloneBtn = $('#make-standalone-btn');
    const reconnectSvlanBtn = $('#reconnect-svlan-btn');
    const svlanFieldsWrapper = $('#svlan-fields-wrapper');
    const nodeFieldWrapper = $('#node-field-wrapper');
    const isStandaloneInput = $('#is_standalone');
    const connectionTypeSelect = $('#connection_type');
    const connectionValueWrapper = $('#connection-value-wrapper');
    const connectionValueLabel = $('#connection-value-label');
    const connectionValueInput = $('#connection_value');
    const svlanIdSelect = $('#svlan_id');
    const infoPanelText = $('#info-panel-text');
    const cvlanSlotInput = $('#cvlan_slot');
    const nodeIdSelect = $('#node_id');


    // Inisialisasi Select2
    nodeIdSelect.select2({ theme: "bootstrap-5", width: '100%' });
    connectionTypeSelect.select2({ theme: "bootstrap-5", width: '100%', minimumResultsForSearch: Infinity });
    
    svlanIdSelect.select2({
        theme: "bootstrap-5",
        width: '100%',
        templateResult: formatSvlanOption,
        templateSelection: formatSvlanOption
    });

    function formatSvlanOption(option) {
        if (!option.id) return option.text;
        const optionElement = $(option.element);
        const nodeName = optionElement.data('node');
        const selectedType = connectionTypeSelect.val();
        if (!selectedType) return $(`<span>Node: ${nodeName} | SVLAN-NMS: ${optionElement.data('nms')}</span>`);
        const svlanValue = optionElement.data(selectedType);
        const newHtml = `<span>Node: ${nodeName} | SVLAN-${selectedType.toUpperCase()}: ${svlanValue}</span>`;
        return $(newHtml);
    }
    
    function showBtn($el) { $el.removeClass('hidden').addClass('inline-flex'); }
    function hideBtn($el) { $el.addClass('hidden').removeClass('inline-flex'); }

    // Fungsi untuk mode Standalone
    function switchToStandaloneMode() {
        svlanFieldsWrapper.hide();
        nodeFieldWrapper.show();
        isStandaloneInput.val('1');
        hideBtn(makeStandaloneBtn);
        showBtn(reconnectSvlanBtn);
        // Atur required fields untuk mode standalone
        cvlanSlotInput.prop('required', true);
        nodeIdSelect.prop('required', true);
        connectionValueInput.prop('required', false);
    }

    // Fungsi untuk mode terhubung ke SVLAN
    function switchToSvlanMode() {
        nodeFieldWrapper.hide();
        svlanFieldsWrapper.show();
        isStandaloneInput.val('0');
        hideBtn(reconnectSvlanBtn);
        showBtn(makeStandaloneBtn);
        // Atur required fields untuk mode terhubung
        cvlanSlotInput.prop('required', false);
        nodeIdSelect.prop('required', false);
        connectionValueInput.prop('required', true);
    }

    makeStandaloneBtn.on('click', switchToStandaloneMode);
    reconnectSvlanBtn.on('click', switchToSvlanMode);

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
    
    function updateInfoPanel() {
        const isStandalone = isStandaloneInput.val() === '1';
        let newText = '';

        if(isStandalone) {
            newText = `Anda sedang mengedit CVLAN yang berstatus **Standalone**.
                       <br><br>Gunakan form di samping untuk menghubungkannya ke SVLAN yang tersedia atau mengubah detail lainnya.`;
        } else {
            const selectedType = connectionTypeSelect.val();
            const selectedSvlanOption = svlanIdSelect.find('option:selected');
            if (selectedType && selectedSvlanOption.length > 0) {
                const svlanValue = selectedSvlanOption.data(selectedType);
                const typeText = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
                newText = `CVLAN akan dihubungkan ke SVLAN-${typeText}: <br><span class="font-bold text-white">${svlanValue}</span>.
                           <br><br>Anda bisa kembali menjadikannya Standalone dengan tombol di samping.`;
            }
        }
        infoPanelText.html(newText);
    }

    // Inisialisasi Halaman
    const isInitiallyStandalone = isStandaloneInput.val() === '1';
    if (isInitiallyStandalone) {
        switchToStandaloneMode();
    } else {
        // Jika karena validation error halaman reload ke mode connect
        switchToSvlanMode();
    }
    toggleConnectionValue();
    updateInfoPanel();
    
    // Event Listeners
    connectionTypeSelect.on('change', function() {
        toggleConnectionValue();
        svlanIdSelect.trigger('change.select2');
        updateInfoPanel();
    });

    svlanIdSelect.on('change', function() {
        updateInfoPanel();
    });

    makeStandaloneBtn.on('click', updateInfoPanel);
    reconnectSvlanBtn.on('click', updateInfoPanel);
});
</script>
@endpush