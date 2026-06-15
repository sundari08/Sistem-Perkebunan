@extends('layouts.app')

@section('title', 'Detail Hasil Panen')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Detail Hasil Panen</h2>
            <div class="space-x-2">
                @php $canEditDelete = (str_contains(session('otorisasi'), 'edit, hapus') || session('jabatan') == 'ADMIN'); @endphp
                @if($canEditDelete)
                <a href="{{ route('panen.edit', $id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Tanggal:</span>
                <span class="ml-2">{{ $data['tgl'] ?? '-' }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Estate:</span>
                <span class="ml-2">{{ $data['estate'] ?? '-' }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Divisi:</span>
                <span class="ml-2">{{ $data['divisi'] ?? '-' }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Blok:</span>
                <span class="ml-2">{{ $data['blok'] ?? '-' }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Mandor:</span>
                <span class="ml-2">{{ $data['mandor'] ?? '-' }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Kerani:</span>
                <span class="ml-2">{{ $data['kerani'] ?? '-' }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">TPH:</span>
                <span class="ml-2">{{ $data['tph'] ?? '-' }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Pemanen:</span>
                <span class="ml-2">{{ $data['pemanen'] ?? '-' }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Janjang:</span>
                <span class="ml-2">{{ number_format($data['janjang'] ?? 0) }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Matang:</span>
                <span class="ml-2">{{ number_format($data['matang'] ?? 0) }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Mentah:</span>
                <span class="ml-2">{{ number_format($data['mentah'] ?? 0) }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Kurang Matang:</span>
                <span class="ml-2">{{ number_format($data['kurangmatang'] ?? 0) }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Lewat Matang:</span>
                <span class="ml-2">{{ number_format($data['lewatmatang'] ?? 0) }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Partenor Carpi:</span>
                <span class="ml-2">{{ number_format($data['partenorcarpi'] ?? 0) }}</span>
            </div>
            <div class="border-b py-2">
                <span class="font-bold text-gray-700">Buah Batu:</span>
                <span class="ml-2">{{ number_format($data['buahbatu'] ?? 0) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection