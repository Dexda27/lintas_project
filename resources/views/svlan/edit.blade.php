@extends('layouts.app')

@section('title', 'Edit SVLAN')

@section('content')

<div class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        {{-- Main grid for the two-column layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Left Column: Edit Form --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 md:p-10 h-full flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <h1 class="text-3xl font-bold text-gray-800">Edit SVLAN</h1>
                        <a href="{{ route('svlan.index') }}" class="inline-flex items-center gap-2 py-1.5 px-3 font-semibold text-white bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg shadow-sm hover:-translate-y-0.5 transition-transform duration-200">
                            &larr; Kembali
                        </a>
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

                    <form action="{{ route('svlan.update', $svlan->id) }}" method="POST" class="flex flex-col flex-grow">
                        @csrf
                        @method('PUT')
                        
                        {{-- Form fields --}}
                        <div class="space-y-6">
                            <div>
                                <label for="node_id" class="block text-sm font-semibold text-gray-600 mb-2">Pilih Node</label>
                                <select id="node_id" name="node_id" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                    <option value="">-- Pilih atau ketik untuk mencari Node --</option>
                                    @foreach($nodes as $node)
                                        <option value="{{ $node->id }}" {{ old('node_id', $svlan->node_id) == $node->id ? 'selected' : '' }}>
                                            {{ $node->nama_node }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('node_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="svlan_nms" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN NMS</label>
                                    <input type="number" id="svlan_nms" name="svlan_nms" value="{{ old('svlan_nms', $svlan->svlan_nms) }}" required max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                                <div>
                                    <label for="svlan_me" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN ME</label>
                                    <input type="number" id="svlan_me" name="svlan_me" value="{{ old('svlan_me', $svlan->svlan_me) }}" required max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="svlan_vpn" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN VPN</label>
                                    <input type="number" id="svlan_vpn" name="svlan_vpn" value="{{ old('svlan_vpn', $svlan->svlan_vpn) }}" required max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                                <div>
                                    <label for="svlan_inet" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN INET</label>
                                    <input type="number" id="svlan_inet" name="svlan_inet" value="{{ old('svlan_inet', $svlan->svlan_inet) }}" required max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                            </div>

                            <div>
                                <label for="extra" class="block text-sm font-semibold text-gray-600 mb-2">Extra</label>
                                <input type="text" id="extra" name="extra" value="{{ old('extra', $svlan->extra) }}" required max="9999"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            </div>

                            <div>
                                <label for="keterangan" class="block text-sm font-semibold text-gray-600 mb-2">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">{{ old('keterangan', $svlan->keterangan) }}</textarea>
                            </div>
                        </div>

                        {{-- Form Footer with Action Buttons --}}
                        <div class="mt-auto pt-8 flex items-center gap-4">
                            <button type="submit" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('svlan.index') }}" class="w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition duration-300">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right Column: Information Panel --}}
            <div class="lg:col-span-4">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-2xl shadow-lg p-8 md:p-10 h-full flex flex-col justify-center items-center text-center">
                    <svg class="w-20 h-20 mb-6 opacity-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    <h3 class="text-2xl font-bold mb-4">Memperbarui Data</h3>
                    <p class="opacity-90 leading-relaxed">
                        Anda sedang mengubah data SVLAN yang sudah ada. Pastikan untuk memeriksa kembali semua perubahan sebelum menyimpannya untuk memastikan akurasi data.
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 pada dropdown node_id
        $('#node_id').select2({
            placeholder: "-- Pilih atau ketik untuk mencari Node --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
