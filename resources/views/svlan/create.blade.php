@extends('layouts.app')

@section('content')

<div class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        {{-- Main grid for the two-column layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Left Column: Input Form --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 md:p-10 h-full flex flex-col">
                    <h1 class="text-3xl font-bold text-gray-800 mb-8">Tambah SVLAN Baru</h1>

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
                                <label for="node_id" class="block text-sm font-semibold text-gray-600 mb-2">Pilih Node</label>
                                {{-- Tidak ada perubahan di sini, Select2 akan menargetkan ID ini --}}
                                <select id="node_id" name="node_id" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                    <option value="" disabled selected>-- Pilih atau ketik untuk mencari Node --</option>
                                    
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
                                <label for="keterangan" class="block text-sm font-semibold text-gray-600 mb-2">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>

                        {{-- Form Footer with Action Buttons --}}
                        <div class="mt-auto pt-8 flex items-center gap-4">
                            <button type="submit" class="w-full md:w-auto bg-green-700 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                                Simpan Data
                            </button>
                            <a href="{{ route('svlan.index') }}" class="w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition duration-300 transform hover:scale-105">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right Column: Information Panel --}}
            <div class="lg:col-span-4">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-8 md:p-10 h-full flex flex-col justify-center items-center text-center">
                    {{-- SVG Icon for Network/Data --}}
                    <svg class="w-20 h-20 mb-6 opacity-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 0 1-9-9 9 9 0 0 1 9-9 9 9 0 0 1 9 9 9 9 0 0 1-9 9Zm0 0a8.949 8.949 0 0 0 4.95-1.755M12 21a8.949 8.949 0 0 1-4.95-1.755m0 0A8.949 8.949 0 0 1 3 12m0 0a8.949 8.949 0 0 1 4.05-7.245M12 3a8.949 8.949 0 0 1 7.95 4.755m-15.9 0A8.949 8.949 0 0 1 12 3m0 18a9 9 0 0 0 0-18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                    </svg>
                    <h3 class="text-2xl font-bold mb-4">Manajemen SVLAN</h3>
                    <p class="opacity-90 leading-relaxed">
                        Gunakan formulir ini untuk menambahkan data SVLAN baru ke dalam sistem. Pastikan semua informasi yang diperlukan telah diisi dengan benar sebelum menyimpan.
                    </p>
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
            placeholder: "-- Pilih atau ketik untuk mencari Node --",
            allowClear: true,
            width: '100%'         // Memastikan lebar dropdown sesuai form
        });
    });
</script>
@endpush