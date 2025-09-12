@extends('layouts.app')

@section('title', 'Edit CVLAN')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit CVLAN</h1>
        </div>

        <div class="mb-6 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Change Connection Type</h3>
            <div id="connection-buttons">
                <button type="button" id="make-standalone-btn" class="{{ $cvlan->svlan_id ? 'inline-flex' : 'hidden' }} items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                    <i data-lucide="unlink" class="w-5 h-5"></i>
                    Jadikan Standalone
                </button>
                <button type="button" id="reconnect-svlan-btn" class="{{ $cvlan->svlan_id ? 'hidden' : 'inline-flex' }} items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                    <i data-lucide="link" class="w-5 h-5"></i>
                    Connect ke SVLAN
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-2">Gunakan tombol ini untuk mengubah status koneksi CVLAN.</p>
        </div>

        <form action="{{ route('cvlan.updateall', ['id' => $cvlan->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_standalone" id="is_standalone" value="{{ $cvlan->svlan_id ? '0' : '1' }}">

            <div id="form-content-wrapper" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Konten untuk Mode SVLAN --}}
                <div id="svlan-fields-wrapper" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 {{ $cvlan->svlan_id ? '' : 'hidden' }}">
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
                                ></option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 mt-1 grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            <input type="number" id="connection_value" name="connection_value" value="{{ old('connection_value', $cvlan->metro ?? $cvlan->vpn ?? $cvlan->inet ?? $cvlan->extra) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" max="9999"
                                oninput="if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46">
                        </div>
                    </div>
                </div>

                {{-- Konten untuk Mode Standalone --}}
                <div id="node-field-wrapper" class="md:col-span-2 space-y-6 {{ $cvlan->svlan_id ? 'hidden' : '' }}">
                    <div>
                        <label for="node_id" class="block text-sm font-medium text-gray-700 mb-1">Node (Wajib jika mandiri)</label>
                        <select id="node_id" name="node_id" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach($nodes as $node)
                                <option value="{{ $node->id }}" {{ old('node_id', $cvlan->node_id) == $node->id ? 'selected' : '' }}>
                                    {{ $node->nama_node }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="cvlan_slot" class="block text-sm font-medium text-gray-700 mb-1">CVLAN (Wajib untuk Mandiri)</label>
                        <input type="text" name="cvlan_slot" id="cvlan_slot" value="{{ old('cvlan_slot', $cvlan->cvlan_slot) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"  
                            oninput="if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                            onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46">
                    </div>
                </div>

                {{-- Field Umum --}}
                <div class="md:col-span-2">
                    <label for="no_jaringan" class="block text-sm font-medium text-gray-700 mb-1">No Jaringan</label>
                    <input type="text" id="no_jaringan" name="no_jaringan" value="{{ old('no_jaringan', $cvlan->no_jaringan) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="md:col-span-2">
                    <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan', $cvlan->nama_pelanggan) }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="mt-8 flex items-center gap-4">
                <button type="submit" class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-white bg-green-500 hover:bg-green-600 rounded-lg shadow-md transition-colors">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Perubahan
                </button>
                
                <a href="{{ route('cvlan.all') }}" class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg shadow-md transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

---

### **Perubahan JavaScript (jQuery)**

Logika JavaScript juga perlu disesuaikan. Daripada menyembunyikan dan menampilkan `svlan-fields-wrapper` dan `node-field-wrapper` secara terpisah, kita hanya perlu menukar kelas `.hidden` di antara keduanya.

```javascript
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

    $('#node_id, #connection_type').select2({width: '100%'});

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
        if (!selectedType) {
            return $(`<span>Node: ${nodeName} | SVLAN-NMS: ${optionElement.data('nms')}</span>`);
        }
        const svlanValue = optionElement.data(selectedType);
        return $(`<span>Node: ${nodeName} | SVLAN-${selectedType.toUpperCase()}: ${svlanValue}</span>`);
    }

    // Helper functions to manage button visibility (using Tailwind classes)
    function showBtn($el) { $el.removeClass('hidden').addClass('inline-flex'); }
    function hideBtn($el) { $el.addClass('hidden').removeClass('inline-flex'); }

    function switchToStandaloneMode() {
        svlanFieldsWrapper.addClass('hidden');
        nodeFieldWrapper.removeClass('hidden');
        isStandaloneInput.val('1');
        hideBtn(makeStandaloneBtn);
        showBtn(reconnectSvlanBtn);
        cvlanSlotInput.prop('required', true);
        connectionValueInput.prop('required', false);
    }

    function switchToSvlanMode() {
        nodeFieldWrapper.addClass('hidden');
        svlanFieldsWrapper.removeClass('hidden');
        isStandaloneInput.val('0');
        hideBtn(reconnectSvlanBtn);
        showBtn(makeStandaloneBtn);
        cvlanSlotInput.prop('required', false);
        connectionValueInput.prop('required', true);
        toggleConnectionValue();
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
            connectionValueInput.prop('required', true);
        } else {
            connectionValueWrapper.addClass('hidden');
            connectionValueInput.prop('required', false);
        }
    }

    const isCurrentlyStandalone = {{ $cvlan->svlan_id ? 'false' : 'true' }};
    if (isCurrentlyStandalone) {
        switchToStandaloneMode();
    } else {
        switchToSvlanMode();
    }
    
    connectionTypeSelect.on('change', function() {
        toggleConnectionValue();
        svlanIdSelect.trigger('change.select2');
    });
});
</script>
@endpush