@extends('layouts.app')

@section('title', 'Daftar Hasil Panen')

@section('content')
<div class="max-w-full mx-auto px-2 sm:px-4">
    <div class="bg-white rounded-lg shadow-lg p-3 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-center justify-between mb-4 gap-3">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-tachometer-alt text-green-500"></i> Data Hasil Panen
            </h2>
            <div class="flex flex-wrap items-center gap-2">
                @if(str_contains(session('otorisasi'), 'input data') || session('jabatan') == 'ADMIN')
                <a href="{{ route('panen.create') }}" class="bg-green-500 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-sm hover:bg-green-600">
                    <i class="fas fa-plus-circle"></i> Tambah Data
                </a>
                @endif
                <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-sm hover:bg-gray-600">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Form Filter (Responsif) -->
        <div class="bg-gradient-to-r from-blue-50 to-gray-50 p-3 sm:p-5 rounded-lg mb-4 shadow-sm">
            <form method="GET" action="{{ route('panen.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <!-- Tanggal -->
                <div>
                    <label class="block text-gray-700 text-xs sm:text-sm font-semibold mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full border rounded-lg px-2 py-1.5 text-sm">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs sm:text-sm font-semibold mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full border rounded-lg px-2 py-1.5 text-sm">
                </div>
                
                <!-- Filter Unit (Direktur) -->
                @if(session('jabatan') == 'DIREKTUR')
                <div>
                    <label class="block text-gray-700 text-xs sm:text-sm font-semibold mb-1">Filter Unit</label>
                    <select name="filter_unit" class="w-full border rounded-lg px-2 py-1.5 text-sm">
                        <option value="">-- Semua Unit --</option>
                        <option value="PG 1A" {{ request('filter_unit') == 'PG 1A' ? 'selected' : '' }}>PG 1A</option>
                        <option value="PG 1B" {{ request('filter_unit') == 'PG 1B' ? 'selected' : '' }}>PG 1B</option>
                        <option value="PG 2" {{ request('filter_unit') == 'PG 2' ? 'selected' : '' }}>PG 2</option>
                    </select>
                </div>
                @endif
                
                <!-- Filter Estate (Admin, Direktur, GM) -->
                @if(in_array(session('jabatan'), ['ADMIN', 'DIREKTUR', 'GENERAL MANAGER']))
                <div>
                    <label class="block text-gray-700 text-xs sm:text-sm font-semibold mb-1">Filter Estate</label>
                    <select name="filter_estate" class="w-full border rounded-lg px-2 py-1.5 text-sm">
                        <option value="">-- Semua Estate --</option>
                        @foreach($availableEstates as $estate)
                            <option value="{{ $estate }}" {{ request('filter_estate') == $estate ? 'selected' : '' }}>{{ $estate }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <!-- Filter Divisi (semua kecuali Asisten) -->
                @if(session('jabatan') != 'ASISTEN')
                <div>
                    <label class="block text-gray-700 text-xs sm:text-sm font-semibold mb-1">Filter Divisi</label>
                    <select name="filter_divisi" class="w-full border rounded-lg px-2 py-1.5 text-sm">
                        <option value="">-- Semua Divisi --</option>
                        @foreach($availableDivisis as $divisi)
                            <option value="{{ $divisi }}" {{ request('filter_divisi') == $divisi ? 'selected' : '' }}>{{ $divisi }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="flex flex-wrap gap-2 col-span-1 sm:col-span-2 lg:col-span-4 mt-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700">Cari</button>
                    <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-gray-600">Reset</a>
                    <a href="{{ route('panen.export', request()->all()) }}" class="bg-green-700 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-green-800">Export Excel</a>
                </div>
            </form>
        </div>

        <!-- Info Total -->
        <div class="mb-3 p-2 bg-gray-100 rounded-lg flex flex-wrap justify-between items-center text-sm">
            <span><i class="fas fa-database text-blue-500"></i> Total Data: <strong>{{ count($data) }}</strong></span>
            <span class="text-xs text-gray-500">Klik <i class="fas fa-eye"></i> detail</span>
        </div>

        <!-- Tabel Responsif -->
        <div class="overflow-x-auto shadow-md rounded-lg">
            <table class="min-w-full bg-white border border-gray-200 text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-200 to-gray-300">
                        <th class="px-1 sm:px-3 py-2 border text-center">No</th>
                        <th class="px-1 sm:px-3 py-2 border text-center">Tanggal</th>
                        <th class="px-1 sm:px-3 py-2 border text-center">Estate</th>
                        <th class="px-1 sm:px-3 py-2 border text-center">Divisi</th>
                        <th class="px-1 sm:px-3 py-2 border text-center">Blok</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden sm:table-cell">Mandor</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden sm:table-cell">Kerani</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden md:table-cell">TPH</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden md:table-cell">Pemanen</th>
                        <th class="px-1 sm:px-3 py-2 border text-center">Janjang</th>
                        <th class="px-1 sm:px-3 py-2 border text-center">Matang</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden sm:table-cell">Mentah</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden md:table-cell">Kurang Matang</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden md:table-cell">Lewat Matang</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden lg:table-cell">Partenor Carpi</th>
                        <th class="px-1 sm:px-3 py-2 border text-center hidden lg:table-cell">Buah Batu</th>
                        <th class="px-1 sm:px-3 py-2 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $id => $item)
                    <tr class="hover:bg-blue-50 border-b">
                        <td class="px-1 sm:px-3 py-2 border text-center">{{ $loop->iteration }}</td>
                        <td class="px-1 sm:px-3 py-2 border text-center">{{ date('d/m/Y', strtotime($item['tgl'] ?? 'now')) }}</td>
                        <td class="px-1 sm:px-3 py-2 border">{{ $item['estate'] ?? '-' }}</td>
                        <td class="px-1 sm:px-3 py-2 border text-center">{{ $item['divisi'] ?? '-' }}</td>
                        <td class="px-1 sm:px-3 py-2 border text-center">{{ $item['blok'] ?? '-' }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden sm:table-cell">{{ $item['mandor'] ?? '-' }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden sm:table-cell">{{ $item['kerani'] ?? '-' }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden md:table-cell text-center">{{ $item['tph'] ?? '-' }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden md:table-cell">{{ $item['pemanen'] ?? '-' }}</td>
                        <td class="px-1 sm:px-3 py-2 border text-right font-semibold text-blue-600">{{ number_format($item['janjang'] ?? 0) }}</td>
                        <td class="px-1 sm:px-3 py-2 border text-right font-semibold text-green-600">{{ number_format($item['matang'] ?? 0) }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden sm:table-cell text-right">{{ number_format($item['mentah'] ?? 0) }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden md:table-cell text-right">{{ number_format($item['kurangmatang'] ?? 0) }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden md:table-cell text-right">{{ number_format($item['lewatmatang'] ?? 0) }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden lg:table-cell text-right">{{ number_format($item['partenorcarpi'] ?? 0) }}</td>
                        <td class="px-1 sm:px-3 py-2 border hidden lg:table-cell text-right">{{ number_format($item['buahbatu'] ?? 0) }}</td>
                        <td class="px-1 sm:px-3 py-2 border text-center">
                            <div class="flex flex-wrap justify-center gap-1">
                                <a href="{{ route('panen.show', $id) }}" class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600"><i class="fas fa-eye"></i></a>
                                @if($canEditDelete)
                                <a href="{{ route('panen.edit', $id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs hover:bg-yellow-600"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('panen.destroy', $id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="17" class="px-4 py-8 text-center text-gray-500">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2 text-xs text-center text-gray-500"><i class="fas fa-print"></i> Export Excel atau Ctrl+P</div>
    </div>
</div>
@endsection