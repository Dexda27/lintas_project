@extends('layouts.app')

@section('title', 'Edit SVLAN')

@section('content')

<div class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        {{-- Main grid for the two-column layout --}}
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">

            {{-- Left Column: Edit Form --}}
            <div class="lg:col-span-8">
                <div class="">
                    <div class="flex items-center justify-between mb-8">
                        <h1 class="text-3xl font-bold text-gray-800">Edit SVLAN</h1>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <strong class="font-bold">Oops! Something Wrong.</strong>
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
                            {{-- Node Selection --}}
                            <div>
                                <label for="node_id" class="block text-sm font-semibold text-gray-600 mb-2">Select Node</label>
                                <select id="node_id" name="node_id" required tabindex="1"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                    <option value="">Select or Type the Node name</option>
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
                                    <input type="number" id="svlan_nms" name="svlan_nms" value="{{ old('svlan_nms', $svlan->svlan_nms) }}" required max="9999" tabindex="2"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 9"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                                <div>
                                    <label for="svlan_me" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN ME</label>
                                    <input type="number" id="svlan_me" name="svlan_me" value="{{ old('svlan_me', $svlan->svlan_me) }}" required max="9999" tabindex="3"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 9"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="svlan_vpn" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN VPN</label>
                                    <input type="number" id="svlan_vpn" name="svlan_vpn" value="{{ old('svlan_vpn', $svlan->svlan_vpn) }}" required max="9999" tabindex="4"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 9"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                                <div>
                                    <label for="svlan_inet" class="block text-sm font-semibold text-gray-600 mb-2">SVLAN INET</label>
                                    <input type="number" id="svlan_inet" name="svlan_inet" value="{{ old('svlan_inet', $svlan->svlan_inet) }}" required max="9999" tabindex="5"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 9"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                </div>
                            </div>

                            <div>
                                <label for="extra" class="block text-sm font-semibold text-gray-600 mb-2">Extra</label>
                                <input type="number" id="extra" name="extra" value="{{ old('extra', $svlan->extra) }}" max="9999" tabindex="6"
                                    oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);"
                                    onkeydown="return event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 9"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            </div>

                            <div>
                                <label for="keterangan" class="block text-sm font-semibold text-gray-600 mb-2">Notes</label>
                                <textarea id="keterangan" name="keterangan" rows="3" tabindex="7"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">{{ old('keterangan', $svlan->keterangan) }}</textarea>
                            </div>
                        </div>

                        {{-- Form Footer with Action Buttons --}}
                        <div class="mt-auto pt-8 flex items-center gap-4">
                            <button type="submit" tabindex="8" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                                Save Changes
                            </button>
                            <a href="{{ route('svlan.index') }}" tabindex="9" class="w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition duration-300">
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
    $(document).ready(function() {
        // Inisialisasi Select2 pada dropdown node_id
        $('#node_id').select2({
            placeholder: "Select or Type the Node name",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
