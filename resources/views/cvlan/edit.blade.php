@extends('layouts.app')

@section('title', 'Edit CVLAN')

@section('content')
@php

    $activeFilter = request('koneksi_filter');

    if (!$activeFilter) {
        if ($cvlan->nms) { $activeFilter = 'nms'; }
        elseif ($cvlan->metro) { $activeFilter = 'metro'; }
        elseif ($cvlan->vpn) { $activeFilter = 'vpn'; }
        elseif ($cvlan->inet) { $activeFilter = 'inet'; }
        elseif ($cvlan->extra) { $activeFilter = 'extra'; }
    }
    // Logika untuk menentukan URL kembali yang benar
    $backUrl = route('cvlan.index', ['svlan_id' => $svlan->id, 'koneksi_filter' => request('koneksi_filter')]); // Default
    
    // Jika parameter 'origin' ada dan nilainya 'all', ubah URL kembali ke cvlan.all
    if (request('origin') === 'all') {
        $backUrl = route('cvlan.all');
    }
@endphp
<div>
    
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            
                <div class="">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-4 flex-wrap gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">Edit CVLAN</h1>
                            <p class="text-gray-500 mt-1">Change CVLAN Detail</p>
                        </div>
                    </div>
                    
                    {{-- Tombol Aksi Koneksi --}}
                    <div class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Change Connection Status</h3>
                        <div id="connection-buttons">
                            <button type="button" id="make-standalone-btn" class="{{ $cvlan->svlan_id ? 'inline-flex' : 'hidden' }} items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                                <i data-lucide="unlink" class="w-5 h-5"></i>
                                Make Standalone
                            </button>
                            <button type="button" id="reconnect-svlan-btn" class="{{ $cvlan->svlan_id ? 'hidden' : 'inline-flex' }} items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                                <i data-lucide="link" class="w-5 h-5"></i>
                                Connect to SVLan
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Use this button for change Cvlan conenction status.</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <strong class="font-bold">Oops! Something Went Wrong.</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cvlan.update', ['svlan_id' => $svlan->id, 'id' => $cvlan->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="origin" value="{{ request('origin') }}">
                        <input type="hidden" name="is_standalone" id="is_standalone" value="0">
                        <input type="hidden" name="koneksi_filter_origin" value="{{ request('koneksi_filter') }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Wrapper untuk field yang terkait SVLAN (mode default) --}}
                            {{-- PERBAIKAN: class="contents" dihapus dan diganti dengan sub-grid --}}
                            <div id="svlan-fields-wrapper" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="svlan_id" class="block text-sm font-medium text-gray-700 mb-1">Connect to SVLAN</label>
                                    <select id="svlan_id" name="svlan_id" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @foreach($allSvlan as $singleSvlan)
                                            <option 
                                                value="{{ $singleSvlan->id }}" 
                                                {{ old('svlan_id', $cvlan->svlan_id) == $singleSvlan->id ? 'selected' : '' }}
                                                data-nms="{{ $singleSvlan->svlan_nms }}"
                                                data-metro="{{ $singleSvlan->svlan_me }}"
                                                data-vpn="{{ $singleSvlan->svlan_vpn }}"
                                                data-inet="{{ $singleSvlan->svlan_inet }}"
                                                data-extra="{{ $singleSvlan->extra }}"
                                                data-node="{{ $singleSvlan->node->nama_node ?? 'N/A' }}"
                                            >
                                                {{-- Teks ini akan diisi oleh JavaScript --}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1">Connection Type</label>
                                    <select id="connection_type" name="connection_type" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="nms" {{ $activeFilter == 'nms' ? 'selected' : '' }}>NMS</option>
                                        <option value="metro" {{ $activeFilter == 'metro' ? 'selected' : '' }}>Metro</option>
                                        <option value="vpn" {{ $activeFilter == 'vpn' ? 'selected' : '' }}>VPN</option>
                                        <option value="inet" {{ $activeFilter == 'inet' ? 'selected' : '' }}>INET</option>
                                        <option value="extra" {{ $activeFilter == 'extra' ? 'selected' : '' }}>EXTRA</option>
                                    </select>
                                </div>

                                <div id="connection-value-wrapper" class="{{ $activeFilter ? '' : 'hidden' }}">
                                    <label id="connection-value-label" for="connection_value" class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                    <input type="number" id="connection_value" name="connection_value" value="{{ old('connection_value', $cvlan->nms ?? $cvlan->metro ?? $cvlan->vpn ?? $cvlan->inet ?? $cvlan->extra) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" max="9999" oninput="if (this.value.length > 4) this.value = this.value.slice(0, 4);">
                                </div>
                                
                            </div>
                            
                            {{-- Wrapper untuk field Node (hanya tampil saat mode mandiri) --}}
                            <div id="node-field-wrapper" class="hidden md:col-span-2 space-y-6">
                                <div>
                                    <label for="node_id" class="block text-sm font-medium text-gray-700 mb-1">Node (Mandatory if Standalone)</label>
                                    <select id="node_id" name="node_id" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
                                        @foreach($nodes as $node)
                                            <option value="{{ $node->id }}" {{ old('node_id', $cvlan->node_id) == $node->id ? 'selected' : '' }}>
                                                {{ $node->nama_node }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                {{-- Input CVLAN Slot DIPINDAHKAN KE SINI --}}
                                <div>
                                    <label for="cvlan_slot" class="block text-sm font-medium text-gray-700 mb-1">CVLAN (Mandatory if Standalone)</label>
                                    <input type="text" name="cvlan_slot" id="cvlan_slot" value="{{ old('cvlan_slot', $cvlan->cvlan_slot) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm" placeholder="There is no Value" max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46">
                                </div>
                            </div>

                            {{-- Field Umum yang selalu tampil --}}
                            
                            <div class="md:col-span-2">
                                <label for="no_jaringan" class="block text-sm font-medium text-gray-700 mb-1">Network No</label>
                                <input type="text" name="no_jaringan" id="no_jaringan" value="{{ old('no_jaringan', $cvlan->no_jaringan) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                                <input type="text" name="nama_pelanggan" id="nama_pelanggan" value="{{ old('nama_pelanggan', $cvlan->nama_pelanggan) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="mt-8 flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-white bg-green-500 hover:bg-green-600 rounded-lg shadow-md transition-colors">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                Save Changes
                            </button>
                            
                            <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg shadow-md transition-colors">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            
        </div>
    
</div>
@endsection

@push('scripts')
<style>
    /* Selaraskan Select2 agar menyerupai input Tailwind */
    .select2-container--bootstrap-5 .select2-selection {
        background-color: #f9fafb; /* bg-gray-50 */
        border: 1px solid #d1d5db; /* border-gray-300 */
        border-radius: 0.375rem; /* rounded-md */
        min-height: 42px;
        padding: 0.375rem 0.75rem; /* px-3 py-2 */
        display: flex; align-items: center;
        box-shadow: 0 0 #0000; /* no BS shadow */
    }
    .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #374151; /* text-gray-700 */
        line-height: 1.25rem;
    }
    .select2-container--bootstrap-5 .select2-selection__arrow {
        height: 100%;
    }
    .select2-container--bootstrap-5.select2-container .select2-search--dropdown .select2-search__field {
        border-radius: 0.375rem;
    }
    .select2-container { width: 100% !important; }
    /* Hilangkan outline biru tebal bawaan */
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #3b82f6; /* ring-blue-500 */
        box-shadow: 0 0 0 1px rgba(59,130,246,0.2);
    }
</style>
<script>
$(document).ready(function() {
    // Deklarasi elemen-elemen penting
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

    // Inisialisasi Select2 untuk Node dan Jenis Koneksi (non-search for connection type)
    $('#node_id, #connection_type').select2({width: '100%'});
    
    // ==================================================================
    // == PERUBAHAN UTAMA DI SINI ==
    // ==================================================================
    // Inisialisasi Select2 untuk SVLAN dengan template kustom
    svlanIdSelect.select2({
        theme: "bootstrap-5",
        width: '100%',
        templateResult: formatSvlanOption, // Fungsi untuk format opsi di daftar
        templateSelection: formatSvlanOption // Fungsi untuk format opsi terpilih
    });

    // Fungsi untuk memformat tampilan opsi SVLAN
    function formatSvlanOption(option) {
        if (!option.id) {
            return option.text;
        }

        // Ambil elemen <option> asli untuk membaca data-* atribut
        const optionElement = $(option.element);
        const nodeName = optionElement.data('node');
        
        // Ambil jenis koneksi yang sedang aktif dari dropdown lain
        const selectedType = connectionTypeSelect.val();

        // Jika tidak ada jenis koneksi yang dipilih, tampilkan info default
        if (!selectedType) {
            return $(`<span>Node: ${nodeName} | SVLAN-NMS: ${optionElement.data('nms')}</span>`);
        }

        // Ambil nilai svlan yang sesuai dari data-*
        const svlanValue = optionElement.data(selectedType);

        // Buat HTML baru untuk ditampilkan
        const newHtml = `<span>Node: ${nodeName} | SVLAN-${selectedType.toUpperCase()}: ${svlanValue}</span>`;
        return $(newHtml);
    }
    
    // --- AKHIR PERUBAHAN UTAMA ---

    // Helpers to show/hide toggle buttons correctly (avoid Tailwind display clash)
    function showBtn($el) { $el.removeClass('hidden').addClass('inline-flex'); }
    function hideBtn($el) { $el.addClass('hidden').removeClass('inline-flex'); }

    // Fungsi untuk mode Standalone
    function switchToStandaloneMode() {
        svlanFieldsWrapper.hide();
        nodeFieldWrapper.show();
        isStandaloneInput.val('1');
        hideBtn(makeStandaloneBtn);
        showBtn(reconnectSvlanBtn);
    }

    // Fungsi untuk mode terhubung ke SVLAN
    function switchToSvlanMode() {
        nodeFieldWrapper.hide();
        svlanFieldsWrapper.show();
        isStandaloneInput.val('0');
        hideBtn(reconnectSvlanBtn);
        showBtn(makeStandaloneBtn);
    }

    // Event listener untuk tombol mode
    makeStandaloneBtn.on('click', switchToStandaloneMode);
    reconnectSvlanBtn.on('click', switchToSvlanMode);

    // Fungsi untuk menampilkan/menyembunyikan input nilai koneksi
    function toggleConnectionValue() {
        const selectedType = connectionTypeSelect.val();
        if (selectedType) {
            const labelText = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
            connectionValueLabel.text('Value ' + labelText);
            connectionValueInput.prop('placeholder', 'Input Value' + labelText);
            connectionValueWrapper.removeClass('hidden');
        } else {
            connectionValueWrapper.addClass('hidden');
        }
    }
    
    // Panggil saat halaman dimuat
    
    
    // Event Listener untuk Jenis Koneksi
    connectionTypeSelect.on('change', function() {
        // Panggil fungsi toggle
        toggleConnectionValue();
        
        // Picu Select2 SVLAN untuk me-render ulang dirinya sendiri
        svlanIdSelect.trigger('change.select2');
    });

    function updateInfoPanel() {
        const selectedType = connectionTypeSelect.val();
        const selectedSvlanOption = svlanIdSelect.find('option:selected');
        
        let newText = '';

        if (selectedType && selectedSvlanOption.length > 0) {
            const svlanValue = selectedSvlanOption.data(selectedType);
            const typeText = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
            
            // Susun ulang teks HTML
            newText = `Mengubah data CVLAN pada SVLAN-${typeText}: <br><span class="font-bold text-white">${svlanValue}</span>.
                       <br><br>Anda bisa memindahkannya ke SVLAN lain atau menjadikannya Standalone dengan tombol di samping.`;
        } else {
            // Teks default jika tidak ada koneksi yang dipilih
            const defaultSvlanValue = selectedSvlanOption.data('nms');
            newText = `Mengubah data CVLAN pada SVLAN-NMS: <br><span class="font-bold text-white">${defaultSvlanValue}</span>.
                       <br><br>Anda bisa memindahkannya ke SVLAN lain atau menjadikannya Standalone dengan tombol di samping.`;
        }
        
        infoPanelText.html(newText);
    }
    // Set initial mode based on current CVLAN state
    const isCurrentlyStandalone = {{ $cvlan->svlan_id ? 'false' : 'true' }};
    if (isCurrentlyStandalone) {
        switchToStandaloneMode();
    } else {
        switchToSvlanMode();
    }

    // Panggil fungsi toggle saat halaman dimuat
    toggleConnectionValue();
    // Panggil fungsi panel info saat halaman dimuat
    updateInfoPanel();
    
    // Event Listener untuk Jenis Koneksi
    connectionTypeSelect.on('change', function() {
        toggleConnectionValue();
        svlanIdSelect.trigger('change.select2');
        updateInfoPanel(); // Panggil fungsi panel info di sini
    });

    // Event listener jika pengguna mengubah SVLAN itu sendiri
    svlanIdSelect.on('change', function() {
        updateInfoPanel(); // Panggil fungsi panel info juga di sini
    });
    
});
</script>
@endpush
