@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Node Baru</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('nodes.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="nama_node" class="block text-sm font-semibold text-gray-600 mb-2">Nama Node</label>
                    <input type="text" id="nama_node" name="nama_node" value="{{ old('nama_node') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"
                           placeholder="Contoh: DPSTKUGA01">
                </div>
            </div>

            <div class="mt-8 flex items-center gap-4">
                <button type="submit" class="w-full md:w-auto bg-green-700 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                    Simpan Node
                </button>
                <a href="{{ route('nodes.index') }}" class="w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition duration-300 transform hover:scale-105">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection