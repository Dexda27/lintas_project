@extends('layouts.app')

@section('title', 'Add New CVLAN')

@section('content')
<div class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">

            {{-- Left Column: Form --}}
            <div class="lg:col-span-8">
                <div class="">

                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">Add New CVLAN</h1>
                            <p class="text-gray-500 mt-1">Fill in the following CVLAN data</p>
                        </div>
                        <a href="{{ route('cvlan.all') }}"
                           class="inline-flex items-center gap-2 py-1.5 px-3 font-semibold text-white bg-gray-500 rounded-lg shadow-sm hover:bg-gray-600 transition-colors">
                            &larr; Cancel
                        </a>
                    </div>

                    {{-- Connection Status --}}
                    <div class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Connection Status</h3>
                        <div id="connection-buttons">
                            {{-- Default --}}
                            <button type="button" id="make-standalone-btn" class="hidden inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                                <i data-lucide="unlink" class="w-5 h-5"></i>
                                Make Standalone
                            </button>
                            <button type="button" id="reconnect-svlan-btn" class="inline-flex items-center gap-2 py-2 px-4 font-semibold text-white bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out">
                                <i data-lucide="link" class="w-5 h-5"></i>
                                Connect to SVLAN
                            </button>
                        </div>
                    </div>

                    {{-- Error Handling --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                            <p class="font-bold">An error occurred:</p>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('cvlan.storeAll') }}" method="POST">
                        @csrf
                        <input type="hidden" name="is_standalone" id="is_standalone" value="1">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- SVLAN Wrapper --}}
                            <div id="svlan-fields-wrapper"
                                 class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="svlan_id" class="block text-sm font-medium text-gray-700 mb-1">Connect to SVLAN</label>
                                    <select id="svlan_id" name="svlan_id" class="form-select">
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

                                {{-- Dynamic Connection --}}
                                <div class="md:col-span-2 mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1">Connection Type</label>
                                        <select id="connection_type" name="connection_type" class="form-select">
                                            <option value="nms" {{ old('connection_type') == 'nms' ? 'selected' : '' }}>NMS</option>
                                            <option value="metro" {{ old('connection_type') == 'metro' ? 'selected' : '' }}>Metro</option>
                                            <option value="vpn" {{ old('connection_type') == 'vpn' ? 'selected' : '' }}>VPN</option>
                                            <option value="inet" {{ old('connection_type') == 'inet' ? 'selected' : '' }}>INET</option>
                                            <option value="extra" {{ old('connection_type') == 'extra' ? 'selected' : '' }}>EXTRA</option>
                                        </select>
                                    </div>

                                    <div id="connection-value-wrapper" class="hidden">
                                        <label id="connection-value-label" for="connection_value"
                                               class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                        <input type="number" id="connection_value" name="connection_value"
                                               value="{{ old('connection_value') }}" max="9999"
                                               oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                               onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                               class="form-input">
                                    </div>
                                </div>

                                {{-- Kolom Kedua: Nilai Koneksi --}}
                                <div id="connection-value-wrapper">
                                    <label id="connection-value-label" for="connection_value" class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                                    <input type="number" id="connection_value" name="connection_value" value="{{ old('connection_value') }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm" max="9999" oninput="if (this.value.length > 4) this.value = this.value.slice(0, 4);">
                                </div>

                            </div>
                        </div>

                            {{-- Node Wrapper --}}
                            <div id="node-field-wrapper" class="md:col-span-2">
                                <label for="node_id" class="block text-sm font-medium text-gray-700 mb-1">Node (Required if standalone)</label>
                                <select id="node_id" name="node_id" class="form-select">
                                    <option value="">No Node Available</option>
                                    @foreach($nodes as $node)
                                        <option value="{{ $node->id }}" {{ old('node_id') == $node->id ? 'selected' : '' }}>
                                            {{ $node->nama_node }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- General Fields --}}
                            <div class="md:col-span-2">
                                <label for="cvlan_slot" class="block text-sm font-medium text-gray-700 mb-1">CVLAN</label>
                                <input type="number" id="cvlan_slot" name="cvlan_slot"
                                       value="{{ old('cvlan_slot') }}" class="form-input"
                                       placeholder="Enter value..." required max="9999"
                                       oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                       onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46">
                            </div>
                            <div class="md:col-span-2">
                                <label for="no_jaringan" class="block text-sm font-medium text-gray-700 mb-1">Network No</label>
                                <input type="text" id="no_jaringan" name="no_jaringan"
                                       value="{{ old('no_jaringan') }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan"
                                       value="{{ old('nama_pelanggan') }}" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <div class="mt-8 text-right">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 py-2 px-6 font-semibold text-white bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg shadow-md hover:-translate-y-0.5 transition-transform duration-200">
                                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                                Save CVLAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
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
    $('.form-input, .form-select').addClass('block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm');
    $('#svlan_id, #node_id').select2({ width: '100%' });
    $('#connection_type').select2({ width: '100%'});
    $(document).ready(function() {
        switchToStandaloneMode();
    });

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

    // PERUBAHAN DI SINI: Deklarasikan wrapper & input CVLAN Slot
    const cvlanSlotWrapper = $('#cvlan-slot-wrapper');
    const cvlanSlotInput = $('#cvlan_slot');

    // --- 2. INISIALISASI PLUGIN (SELECT2) ---
    $('#node_id').select2({ theme: "bootstrap-5", width: '100%' });
    connectionTypeSelect.select2({ theme: "bootstrap-5", width: '100%', minimumResultsForSearch: Infinity });
    svlanIdSelect.select2({
        theme: "bootstrap-5",
        width: '100%',
        templateResult: formatSvlanOption,
        templateSelection: formatSvlanOption
    });

    // --- 3. DEFINISI FUNGSI ---
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

    function switchToStandaloneMode() {
        svlanFieldsWrapper.hide();
        nodeFieldWrapper.show();
        isStandaloneInput.val('1');
        hideBtn(makeStandaloneBtn);
        showBtn(reconnectSvlanBtn);

        // PERUBAHAN DI SINI: Tampilkan field CVLAN dan jadikan 'required'
        cvlanSlotWrapper.show();
        cvlanSlotInput.prop('required', true);
    }

    function switchToSvlanMode() {
        nodeFieldWrapper.hide();
        svlanFieldsWrapper.show();
        isStandaloneInput.val('0');
        hideBtn(reconnectSvlanBtn);
        showBtn(makeStandaloneBtn);

        // PERUBAHAN DI SINI: Sembunyikan field CVLAN dan hapus 'required'
        cvlanSlotWrapper.hide();
        cvlanSlotInput.prop('required', false);
    }

    function toggleConnectionValue() {
        const selectedType = connectionTypeSelect.val();
        if (selectedType) {
            const labelText = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
            connectionValueLabel.text('Value ' + labelText);
            connectionValueInput.prop('placeholder', 'Enter value ' + labelText);
            connectionValueWrapper.removeClass('hidden');
        } else {
            connectionValueWrapper.addClass('hidden');
        }
    }

    // --- 4. PASANG EVENT LISTENERS ---
    makeStandaloneBtn.on('click', switchToStandaloneMode);
    reconnectSvlanBtn.on('click', switchToSvlanMode);
    connectionTypeSelect.on('change', function() {
        toggleConnectionValue();
        svlanIdSelect.trigger('change.select2');
    });

    // --- 5. ATUR KONDISI AWAL HALAMAN ---
    switchToStandaloneMode();
    toggleConnectionValue();
});
</script>
@endpush
