@extends('layouts.app')

@section('title', 'Edit CVLAN')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit CVLAN</h1>
            <a href="{{ route('cvlan.all') }}" class="inline-flex items-center gap-2 py-1.5 px-3 font-semibold text-white bg-gray-500 rounded-lg shadow-sm hover:bg-gray-600 transition-colors">
                &larr; Batal
            </a>
        </div>

        <div class="mb-6 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Ubah Status Koneksi</h3>
            <div id="connection-buttons">
                <button type="button" id="make-standalone-btn" class="{{ $cvlan->svlan_id ? '' : 'hidden' }} inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                    <i data-lucide="unlink" class="w-5 h-5"></i>
                    Jadikan Standalone
                </button>
                <button type="button" id="reconnect-svlan-btn" class="{{ $cvlan->svlan_id ? 'hidden' : '' }} inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                    <i data-lucide="link" class="w-5 h-5"></i>
                    Connect ke SVLAN
                </button>
            </div>
        </div>

        @if ($errors->any())
            {{-- Validation Errors --}}
        @endif

        <form action="{{ route('cvlan.updateall', ['id' => $cvlan->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_standalone" id="is_standalone" value="{{ $cvlan->svlan_id ? '0' : '1' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Wrapper untuk field yang terkait SVLAN --}}
                <div id="svlan-fields-wrapper" class="{{ $cvlan->svlan_id ? '' : 'hidden' }} md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="svlan_id" class="block text-sm font-medium text-gray-700 mb-1">Hubungkan ke SVLAN</label>
                        <select id="svlan_id" name="svlan_id" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach($svlans as $svlan)
                                <option 
                                    value="{{ $svlan->id }}" 
                                    {{ old('svlan_id', $cvlan->svlan_id) == $svlan->id ? 'selected' : '' }}
                                    data-node="{{ $svlan->node->nama_node ?? 'N/A' }}"
                                    data-nms="{{ $svlan->svlan_nms }}"
                                    data-metro="{{ $svlan->svlan_me }}"
                                    data-vpn="{{ $svlan->svlan_vpn }}"
                                    data-inet="{{ $svlan->svlan_inet }}"
                                    data-extra="{{ $svlan->extra }}"
                                >
                                    {{-- Teks ini akan diisi oleh JavaScript --}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Koneksi</label>
                            @php
                                $activeType = '';
                                if ($cvlan->nms) $activeType = 'nms';
                                elseif ($cvlan->metro) $activeType = 'metro';
                                elseif ($cvlan->vpn) $activeType = 'vpn';
                                elseif ($cvlan->inet) $activeType = 'inet';
                                elseif ($cvlan->extra) $activeType = 'extra';
                            @endphp
                            <select id="connection_type" name="connection_type" class="form-select">
                                <option value="nms" {{ old('connection_type', $activeType) == 'nms' ? 'selected' : '' }}>NMS</option>
                                <option value="metro" {{ old('connection_type', $activeType) == 'metro' ? 'selected' : '' }}>Metro</option>
                                <option value="vpn" {{ old('connection_type', $activeType) == 'vpn' ? 'selected' : '' }}>VPN</option>
                                <option value="inet" {{ old('connection_type', $activeType) == 'inet' ? 'selected' : '' }}>INET</option>
                                <option value="extra" {{ old('connection_type', $activeType) == 'extra' ? 'selected' : '' }}>EXTRA</option>
                            </select>
                        </div>
                        <div id="connection-value-wrapper" class="hidden">
                            <label id="connection-value-label" for="connection_value" class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                            <input type="number" id="connection_value" name="connection_value" value="{{ old('connection_value', $cvlan->metro ?? $cvlan->vpn ?? $cvlan->inet ?? $cvlan->extra) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" max="9999" required max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46" oninput="formatCvlan(this)">
                        </div>
                    </div>
                </div>

                {{-- Wrapper untuk field Node (saat mandiri) --}}
                <div id="node-field-wrapper" class="{{ $cvlan->svlan_id ? 'hidden' : '' }} md:col-span-2">
                    <label for="node_id" class="block text-sm font-medium text-gray-700 mb-1">Node (Wajib jika mandiri)</label>
                    <select id="node_id" name="node_id" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @foreach($nodes as $node)
                            <option value="{{ $node->id }}" {{ old('node_id', $cvlan->node_id) == $node->id ? 'selected' : '' }}>
                                {{ $node->nama_node }}
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-6">
                        <label for="cvlan_slot" class="block text-sm font-medium text-gray-700 mb-1">CVLAN</label>
                        <input type="text" name="cvlan_slot" id="cvlan_slot" value="{{ old('cvlan_slot', $cvlan->cvlan_slot) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"  oninput="formatCvlan(this)" max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46" oninput="formatCvlan(this)">
                    </div>
                </div>

                {{-- Field Umum --}}
                
                <div class="md:col-span-2">
                    <label for="no_jaringan" class="block text-sm font-medium text-gray-700 mb-1">No Jaringan</label>
                    {{-- PERBAIKAN: Mengganti 'form-input' dengan kelas CSS lengkap --}}
                    <input type="text" id="no_jaringan" name="no_jaringan" value="{{ old('no_jaringan', $cvlan->no_jaringan) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="md:col-span-2">
                    <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                    {{-- PERBAIKAN: Mengganti 'form-input' dengan kelas CSS lengkap --}}
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan', $cvlan->nama_pelanggan) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="mt-8 text-right">
                <button type="submit" class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-white bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg shadow-md hover:-translate-y-0.5 transition-transform duration-200">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
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
    const cvlanSlotInput = $('#cvlan_slot');

    // Inisialisasi Select2
    $('#node_id, #connection_type').select2({ theme: "bootstrap-5", width: '100%' });

    svlanIdSelect.select2({
        theme: "bootstrap-5",
        width: '100%',
        templateResult: formatSvlanOption,
        templateSelection: formatSvlanOption
    });

    // Fungsi untuk memformat tampilan opsi SVLAN secara dinamis
    function formatSvlanOption(option) {
        if (!option.id) return option.text;
        const optionElement = $(option.element);
        const nodeName = optionElement.data('node');
        const selectedType = connectionTypeSelect.val();

        if (!selectedType) {
            return $(`<span>Node: ${nodeName} | SVLAN-NMS: ${optionElement.data('nms')}</span>`);
        }
        const svlanValue = optionElement.data(selectedType);
        const newHtml = `<span>Node: ${nodeName} | SVLAN-${selectedType.toUpperCase()}: ${svlanValue}</span>`;
        return $(newHtml);
    }

    // Fungsi untuk mode Standalone
    function switchToStandaloneMode() {
        svlanFieldsWrapper.hide();
        nodeFieldWrapper.show();
        isStandaloneInput.val('1');
        makeStandaloneBtn.addClass('hidden');
        reconnectSvlanBtn.removeClass('hidden');
        cvlanSlotInput.prop('required', true);      // JADIKAN CVLAN SLOT REQUIRED
        connectionValueInput.prop('required', false); // HAPUS REQUIRED DARI NILAI KONEKSI
    }

    // Fungsi untuk mode terhubung ke SVLAN
    function switchToSvlanMode() {
        nodeFieldWrapper.hide();
        svlanFieldsWrapper.show();
        isStandaloneInput.val('0');
        reconnectSvlanBtn.addClass('hidden');
        makeStandaloneBtn.removeClass('hidden');
        cvlanSlotInput.prop('required', false);     // HAPUS REQUIRED DARI CVLAN SLOT
        connectionValueInput.prop('required', true); // JADIKAN NILAI KONEKSI REQUIRED
    }

    // Event listener untuk tombol mode
    makeStandaloneBtn.on('click', switchToStandaloneMode);
    reconnectSvlanBtn.on('click', switchToSvlanMode);

    // Fungsi untuk menampilkan/menyembunyikan input nilai koneksi
    function toggleConnectionValue() {
        const selectedType = connectionTypeSelect.val();
        if (selectedType) {
            const labelText = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
            connectionValueLabel.text('Nilai ' + labelText);
            connectionValueInput.prop('placeholder', 'Masukkan nilai ' + labelText);
            connectionValueWrapper.removeClass('hidden');
            connectionValueInput.prop('required', true); // Wajib diisi jika jenis koneksi dipilih
        } else {
            connectionValueWrapper.addClass('hidden');
            connectionValueInput.prop('required', false); // Tidak wajib jika "Tidak ada"
        }
    }

    // Atur kondisi awal saat halaman dimuat
    if (isStandaloneInput.val() === '1') {
        switchToStandaloneMode();
    } else {
        switchToSvlanMode();
    }
    toggleConnectionValue();

    // Event Listener untuk Jenis Koneksi
    connectionTypeSelect.on('change', function() {
        toggleConnectionValue();
        svlanIdSelect.trigger('change.select2');
    });
});
</script>
@endpush