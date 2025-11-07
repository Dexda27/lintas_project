@extends('layouts.app')

@section('title', 'Detail Tiang - ' . $pole->pole_id)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div >
                        <i data-lucide="radio-tower" class="w-12 h-1 mr-2"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $pole->pole_id }}</h1>
                        <p class="text-sm text-gray-500">Detail Informasi Tiang</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('poles.edit', $pole) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('poles.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Dasar -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informasi Dasar</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-gray-500">ID Tiang</div>
                            <div class="col-span-2 text-sm font-medium text-gray-900">{{ $pole->pole_id }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                            <div class="text-sm text-gray-500">Nama Tiang</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $pole->name }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                            <div class="text-sm text-gray-500">Lokasi</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $pole->location }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                            <div class="text-sm text-gray-500">Region</div>
                            <div class="col-span-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $pole->region }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                            <div class="text-sm text-gray-500">Status</div>
                            <div class="col-span-2">
                                @if($pole->status == 'ok')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        OK
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800">
                                        <i class="fas fa-times-circle mr-1.5"></i>
                                        NOT OK
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spesifikasi Teknis -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Spesifikasi Teknis</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-gray-500">Tipe Tiang</div>
                            <div class="col-span-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($pole->type) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                            <div class="text-sm text-gray-500">Tinggi</div>
                            <div class="col-span-2 text-sm text-gray-900">{{ $pole->height }} meter</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Koordinat Lokasi -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Koordinat Lokasi</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-gray-500">Latitude</div>
                            <div class="col-span-2 text-sm text-gray-900 font-mono">{{ $pole->latitude ?? '-' }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                            <div class="text-sm text-gray-500">Longitude</div>
                            <div class="col-span-2 text-sm text-gray-900 font-mono">{{ $pole->longitude ?? '-' }}</div>
                        </div>

                        @if($pole->latitude && $pole->longitude)
                        <div class="pt-4 border-t border-gray-100">
                            <a href="https://www.google.com/maps?q={{ $pole->latitude }},{{ $pole->longitude }}"
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition duration-200">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Buka di Google Maps
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            @if($pole->description)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Catatan</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $pole->description }}</p>
                </div>
            </div>
            @endif

            <!-- Informasi Sistem -->
            <!-- Informasi Sistem -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informasi Sistem</h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="text-sm text-gray-500">Dibuat</div>
                <div class="col-span-2 text-sm text-gray-900">{{ $pole->created_at->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</div>
            </div>

            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-500">Terakhir Diupdate</div>
                <div class="col-span-2 text-sm text-gray-900">{{ $pole->updated_at->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</div>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Summary Stats -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Ringkasan</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Joint Closures</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $pole->jointClosures->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <span class="text-sm text-gray-600">Splitters</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $pole->splitters->count() }}</span>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-900">Total Equipment</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $pole->total_equipment }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Joint Closures List -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Joint Closures</h2>
                </div>
                <div class="p-6">
                    @if($pole->jointClosures->count() > 0)
                        <div class="space-y-3">
                            @foreach($pole->jointClosures as $jc)
                                <a href="{{ route('closures.connections', $jc) }}"
                                   class="block p-4 border border-gray-200 hover:border-gray-900 rounded-lg transition duration-200">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="font-medium text-sm text-gray-900">{{ $jc->closure_id }}</div>
                                        @if($jc->status == 'ok')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                                OK
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                NOT OK
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600 mb-1">{{ $jc->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($jc->location, 50) }}</div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">Tidak ada data</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Splitters List -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Splitters</h2>
                </div>
                <div class="p-6">
                    @if($pole->splitters->count() > 0)
                        <div class="space-y-3">
                            @foreach($pole->splitters as $splitter)
                                <a href="{{ route('splitters.show', $splitter) }}"
                                   class="block p-4 border border-gray-200 hover:border-gray-900 rounded-lg transition duration-200">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="font-medium text-sm text-gray-900">{{ $splitter->splitter_id }}</div>
                                        @if($splitter->status == 'ok')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                OK
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                NOT OK
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600 mb-1">{{ $splitter->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($splitter->location, 50) }}</div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">Tidak ada data</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
