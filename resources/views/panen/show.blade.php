@extends('layouts.app')

@section('title', 'Detail Hasil Panen')

@section('content')
<div class="max-w-4xl mx-auto px-2 sm:px-4">
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Detail Hasil Panen</h2>
            <div class="flex flex-wrap gap-2">
                @php $canEditDelete = (str_contains(session('otorisasi'), 'edit, hapus') || session('jabatan') == 'ADMIN'); @endphp
                @if($canEditDelete)
                <a href="{{ route('panen.edit', $id) }}" class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-yellow-600">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-gray-600">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
            <div class="border-b py-2"><span class="font-bold">Tanggal:</span> <span class="ml-2">{{ $data['tgl'] ?? '-' }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Estate:</span> <span class="ml-2">{{ $data['estate'] ?? '-' }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Divisi:</span> <span class="ml-2">{{ $data['divisi'] ?? '-' }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Blok:</span> <span class="ml-2">{{ $data['blok'] ?? '-' }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Mandor:</span> <span class="ml-2">{{ $data['mandor'] ?? '-' }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Kerani:</span> <span class="ml-2">{{ $data['kerani'] ?? '-' }}</span></div>
            <div class="border-b py-2"><span class="font-bold">TPH:</span> <span class="ml-2">{{ $data['tph'] ?? '-' }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Pemanen:</span> <span class="ml-2">{{ $data['pemanen'] ?? '-' }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Janjang:</span> <span class="ml-2">{{ number_format($data['janjang'] ?? 0) }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Matang:</span> <span class="ml-2">{{ number_format($data['matang'] ?? 0) }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Mentah:</span> <span class="ml-2">{{ number_format($data['mentah'] ?? 0) }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Kurang Matang:</span> <span class="ml-2">{{ number_format($data['kurangmatang'] ?? 0) }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Lewat Matang:</span> <span class="ml-2">{{ number_format($data['lewatmatang'] ?? 0) }}</span></div>
            <div class="border-b py-2"><span class="font-bold">Partenor Carpi:</span> <span class="ml-2">{{ number_format($data['partenorcarpi'] ?? 0) }}</span></div>
            <div class="border-b py-2 sm:col-span-2"><span class="font-bold">Buah Batu:</span> <span class="ml-2">{{ number_format($data['buahbatu'] ?? 0) }}</span></div>
        </div>
    </div>
</div>
@endsection