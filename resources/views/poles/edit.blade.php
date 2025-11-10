@extends('layouts.app')

@section('title', 'Edit Tiang')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Tiang</h1>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi tiang fiber optik</p>
            </div>
            <a href="{{ route('poles.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-600 mr-3 mt-0.5"></i>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-red-800 mb-2">Terdapat kesalahan pada form:</h3>
                <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ route('poles.update', $pole->id) }}" method="POST">
        @csrf
        @method('PUT')

       <!-- Informasi Dasar -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informasi Dasar</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- ID Tiang (Read-only) -->
            <div>
                <label for="pole_id" class="block text-sm font-medium text-gray-700 mb-2">
                    ID Tiang <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('pole_id') border-red-500 @enderror"
                       id="pole_id"
                       name="pole_id"
                       value="{{ old('pole_id', $pole->pole_id) }}"
                       readonly
                       required>
                <p class="mt-1 text-xs text-gray-500">ID Tiang tidak dapat diubah</p>
                @error('pole_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Tiang -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Tiang <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('name') border-red-500 @enderror"
                       id="name"
                       name="name"
                       value="{{ old('name', $pole->name) }}"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Region -->
            <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-2">
                    Region <span class="text-red-500">*</span>
                </label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('region') border-red-500 @enderror"
                        id="region"
                        name="region"
                        required>
                    <option value="">Pilih Region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region }}" {{ old('region', $pole->region) == $region ? 'selected' : '' }}>
                            {{ $region }}
                        </option>
                    @endforeach
                </select>
                @error('region')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Lokasi <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('location') border-red-500 @enderror"
                       id="location"
                       name="location"
                       value="{{ old('location', $pole->location) }}"
                       required>
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

        <!-- Spesifikasi Teknis -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Spesifikasi Teknis</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Tipe Tiang -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Tiang <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('type') border-red-500 @enderror"
                                id="type"
                                name="type"
                                required>
                            <option value="">Pilih Tipe</option>
                            <option value="besi" {{ old('type', $pole->type) == 'besi' ? 'selected' : '' }}>Besi</option>
                            <option value="beton" {{ old('type', $pole->type) == 'beton' ? 'selected' : '' }}>Beton</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tinggi -->
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                            Tinggi (meter) <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('height') border-red-500 @enderror"
                                id="height"
                                name="height"
                                required>
                            <option value="">Pilih Tinggi</option>
                            <option value="7" {{ old('height', $pole->height) == '7' ? 'selected' : '' }}>7 meter</option>
                            <option value="9" {{ old('height', $pole->height) == '9' ? 'selected' : '' }}>9 meter</option>
                        </select>
                        @error('height')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('status') border-red-500 @enderror"
                                id="status"
                                name="status"
                                required>
                            <option value="ok" {{ old('status', $pole->status) == 'ok' ? 'selected' : '' }}>OK</option>
                            <option value="not_ok" {{ old('status', $pole->status) == 'not_ok' ? 'selected' : '' }}>Not OK</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Koordinat Lokasi -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Koordinat Lokasi</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Latitude -->
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                            Latitude
                        </label>
                        <input type="number"
                               step="0.000001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('latitude') border-red-500 @enderror"
                               id="latitude"
                               name="latitude"
                               value="{{ old('latitude', $pole->latitude) }}"
                               placeholder="-8.409518">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Longitude -->
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                            Longitude
                        </label>
                        <input type="number"
                               step="0.000001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('longitude') border-red-500 @enderror"
                               id="longitude"
                               name="longitude"
                               value="{{ old('longitude', $pole->longitude) }}"
                               placeholder="115.188916">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Joint Closures Terhubung -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Joint Closures Terhubung</h2>
            </div>
            <div class="p-6">
                <div id="jcContainer"
                     class="border border-gray-200 rounded-lg p-4 bg-gray-50 overflow-y-auto"
                     style="max-height: 300px;">
                    <div class="text-gray-500 text-sm text-center py-8">
                        <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Splitters Terhubung -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Splitters Terhubung</h2>
            </div>
            <div class="p-6">
                <div id="splitterContainer"
                     class="border border-gray-200 rounded-lg p-4 bg-gray-50 overflow-y-auto"
                     style="max-height: 300px;">
                    <div class="text-gray-500 text-sm text-center py-8">
                        <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Catatan</h2>
            </div>
            <div class="p-6">
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-gray-900 @error('description') border-red-500 @enderror"
                          id="description"
                          name="description"
                          rows="4"
                          placeholder="Tambahkan catatan tambahan...">{{ old('description', $pole->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-end mb-8">
            <a href="{{ route('poles.index') }}"
               class="inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition duration-200">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition duration-200">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region');
    const jcContainer = document.getElementById('jcContainer');
    const splitterContainer = document.getElementById('splitterContainer');

    // Data yang sudah terhubung
    const existingJCs = @json($pole->jointClosures->pluck('id')->toArray());
    const existingSplitters = @json($pole->splitters->pluck('id')->toArray());

    // Load data saat halaman pertama kali dibuka
    const initialRegion = regionSelect.value;
    if (initialRegion) {
        loadJointClosures(initialRegion);
        loadSplitters(initialRegion);
    }

    regionSelect.addEventListener('change', function() {
        const region = this.value;

        if (!region) {
            jcContainer.innerHTML = `
                <div class="text-gray-500 text-sm text-center py-8">
                    <i class="fas fa-info-circle text-xl mb-2"></i>
                    <p>Pilih region terlebih dahulu</p>
                </div>
            `;
            splitterContainer.innerHTML = `
                <div class="text-gray-500 text-sm text-center py-8">
                    <i class="fas fa-info-circle text-xl mb-2"></i>
                    <p>Pilih region terlebih dahulu</p>
                </div>
            `;
            return;
        }

        loadJointClosures(region);
        loadSplitters(region);
    });

    function loadJointClosures(region) {
        jcContainer.innerHTML = `
            <div class="text-gray-500 text-sm text-center py-8">
                <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                <p>Memuat data...</p>
            </div>
        `;

        fetch(`/poles/joint-closures?region=${region}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    jcContainer.innerHTML = `
                        <div class="text-gray-500 text-sm text-center py-8">
                            <i class="fas fa-inbox text-xl mb-2"></i>
                            <p>Tidak ada Joint Closure di region ini</p>
                        </div>
                    `;
                } else {
                    let html = '<div class="space-y-2">';
                    data.forEach(jc => {
                        const isChecked = existingJCs.includes(jc.id) ? 'checked' : '';
                        html += `
                            <label class="flex items-start p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-900 cursor-pointer transition duration-150">
                                <input class="mt-1 mr-3 h-4 w-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900"
                                       type="checkbox"
                                       name="joint_closures[]"
                                       value="${jc.id}"
                                       ${isChecked}>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 text-sm">${jc.closure_id}</div>
                                    <div class="text-sm text-gray-600">${jc.name}</div>
                                    <div class="text-xs text-gray-500 mt-1">${jc.location}</div>
                                </div>
                            </label>
                        `;
                    });
                    html += '</div>';
                    jcContainer.innerHTML = html;
                }
            })
            .catch(error => {
                jcContainer.innerHTML = `
                    <div class="text-red-600 text-sm text-center py-8">
                        <i class="fas fa-exclamation-circle text-xl mb-2"></i>
                        <p>Gagal memuat data</p>
                    </div>
                `;
            });
    }

    function loadSplitters(region) {
        splitterContainer.innerHTML = `
            <div class="text-gray-500 text-sm text-center py-8">
                <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                <p>Memuat data...</p>
            </div>
        `;

        fetch(`/poles/splitters?region=${region}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    splitterContainer.innerHTML = `
                        <div class="text-gray-500 text-sm text-center py-8">
                            <i class="fas fa-inbox text-xl mb-2"></i>
                            <p>Tidak ada Splitter di region ini</p>
                        </div>
                    `;
                } else {
                    let html = '<div class="space-y-2">';
                    data.forEach(splitter => {
                        const isChecked = existingSplitters.includes(splitter.id) ? 'checked' : '';
                        html += `
                            <label class="flex items-start p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-900 cursor-pointer transition duration-150">
                                <input class="mt-1 mr-3 h-4 w-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900"
                                       type="checkbox"
                                       name="splitters[]"
                                       value="${splitter.id}"
                                       ${isChecked}>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 text-sm">${splitter.splitter_id}</div>
                                    <div class="text-sm text-gray-600">${splitter.name}</div>
                                    <div class="text-xs text-gray-500 mt-1">${splitter.location}</div>
                                </div>
                            </label>
                        `;
                    });
                    html += '</div>';
                    splitterContainer.innerHTML = html;
                }
            })
            .catch(error => {
                splitterContainer.innerHTML = `
                    <div class="text-red-600 text-sm text-center py-8">
                        <i class="fas fa-exclamation-circle text-xl mb-2"></i>
                        <p>Gagal memuat data</p>
                    </div>
                `;
            });
    }
});
</script>
@endpush
@endsection
