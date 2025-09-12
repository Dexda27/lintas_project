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
                                            <option value="{{ $svlan->id }}" {{ old('svlan_id') == $svlan->id ? 'selected' : '' }}>
                                                Node: {{ $svlan->node->nama_node ?? 'N/A' }} | NMS: {{ $svlan->svlan_nms }}
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
                                <label for="no_jaringan" class="block text-sm font-medium text-gray-700 mb-1">No Jaringan</label>
                                <input type="text" id="no_jaringan" name="no_jaringan"
                                       value="{{ old('no_jaringan') }}" class="form-input">
                            </div>
                            <div class="md:col-span-2">
                                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan"
                                       value="{{ old('nama_pelanggan') }}" class="form-input">
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

            {{-- Right Column: Info Panel --}}
            <div class="lg:col-span-4">
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl shadow-lg p-6 sticky top-8">
                    <h2 class="text-xl font-semibold mb-4">Guidelines</h2>
                    <ul class="space-y-2 text-sm">
                        <li>📌 Select <strong>Node</strong> if CVLAN is standalone.</li>
                        <li>🔗 Select <strong>SVLAN</strong> if you want to connect.</li>
                        <li>✍️ Fill in general data such as <strong>CVLAN, No Jaringan, Nama Pelanggan</strong>.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
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

    function showBtn($el) { $el.removeClass('hidden').addClass('inline-flex'); }
    function hideBtn($el) { $el.addClass('hidden').removeClass('inline-flex'); }

    function switchToStandaloneMode() {
        svlanFieldsWrapper.hide();
        nodeFieldWrapper.show();
        isStandaloneInput.val('1');
        hideBtn(makeStandaloneBtn);
        showBtn(reconnectSvlanBtn);
    }

    function switchToSvlanMode() {
        nodeFieldWrapper.hide();
        svlanFieldsWrapper.show();
        isStandaloneInput.val('0');
        hideBtn(reconnectSvlanBtn);
        showBtn(makeStandaloneBtn);
    }

    makeStandaloneBtn.on('click', switchToStandaloneMode);
    reconnectSvlanBtn.on('click', switchToSvlanMode);

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

    toggleConnectionValue();
    connectionTypeSelect.on('change', toggleConnectionValue);
});
</script>
@endpush
