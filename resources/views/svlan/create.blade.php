@extends('layouts.app')
@section('title', 'Add New SVLAN')
@section('content')

<div class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        {{-- Main grid for the two-column layout --}}
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">

            {{-- Left Column: Input Form --}}
            <div class="lg:col-span-8">
                <div class="">
                    <h1 class="text-3xl font-bold text-gray-800 mb-8">Add New SVLAN</h1>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <strong class="font-bold">Oops!</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('svlan.store') }}" method="POST" class="flex flex-col flex-grow">
                        @csrf

                        {{-- Form fields --}}
                        <div class="space-y-6">

                            {{-- ======================= PERUBAHAN DI SINI ======================= --}}
                            <div>
                                <label for="node_id" class="block text-sm font-semibold text-gray-600 mb-2">Select Node</label>
                                {{-- Tidak ada perubahan di sini, Select2 akan menargetkan ID ini --}}
                                <select id="node_id" name="node_id" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                    <option value="" disabled selected>Select or Search for Node</option>

                                    @foreach($nodes as $node)
                                        <option value="{{ $node->id }}" {{ old('node_id') == $node->id ? 'selected' : '' }}>
                                            {{ $node->nama_node }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('node_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- ===================== AKHIR DARI PERUBAHAN ==================== --}}

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="svlan_nms" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN NMS</label>
                                    <input type="number" id="svlan_nms" name="svlan_nms" value="{{ old('svlan_nms') }}" required max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                                <div>
                                    <label for="svlan_me" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN ME</label>
                                    <input type="number" id="svlan_me" name="svlan_me" value="{{ old('svlan_me') }}" max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="svlan_vpn" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN VPN</label>
                                    <input type="number" id="svlan_vpn" name="svlan_vpn" value="{{ old('svlan_vpn') }}" required max="9999"
                                        oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                        onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                                <div>
                                    <label for="svlan_inet" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN INET</label>
                                    <input type="number" id="svlan_inet" name="svlan_inet" value="{{ old('svlan_inet') }}" required max="9999"
                                        oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                        onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                            </div>

                            <div>
                                <label for="extra" class="block text-sm font-semibold text-gray-600 mb-2">Extra</label>
                                <input type="number" id="extra" name="extra" value="{{ old('extra') }}" max="9999"
                                oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            </div>

                            <div>
                                <label for="keterangan" class="block text-sm font-semibold text-gray-600 mb-2">Notes</label>
                                <textarea id="keterangan" name="keterangan" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>

                        {{-- Form Footer with Action Buttons --}}
                        <div class="mt-auto pt-8 flex items-center gap-4">
                            <button type="submit" class="w-full md:w-auto bg-green-700 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                                Save Data
                            </button>
                            <a href="{{ route('svlan.index') }}" class="w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition duration-300 transform hover:scale-105">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pastikan DOM sudah siap
    $(document).ready(function() {
        // Inisialisasi Select2 pada elemen dengan ID 'node_id'
        $('#node_id').select2({
            placeholder: "Select or search for node",
            allowClear: true,
            width: '100%'         // Memastikan lebar dropdown sesuai form
        });
    });
</script>
@endpush
