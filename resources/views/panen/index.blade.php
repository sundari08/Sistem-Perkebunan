@extends('layouts.app')

@section('title', 'Daftar Hasil Panen')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4 w-full">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-tachometer-alt text-green-500"></i> Data Hasil Panen
            </h2>
            <div class="flex items-center gap-4 w-full md:w-auto justify-end">
                @if(str_contains(session('otorisasi'), 'input data') || session('jabatan') == 'ADMIN')
                <a href="{{ route('panen.create') }}" class="bg-green-500 text-white px-5 py-2 rounded-lg hover:bg-green-600">
                    <i class="fas fa-plus-circle"></i> Tambah Data
                </a>
                @endif
                <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Form Pencarian dengan Filter Per Role -->
        <div class="bg-gradient-to-r from-blue-50 to-gray-50 p-5 rounded-lg mb-6 shadow-sm">
            <form method="GET" action="{{ route('panen.index') }}" class="flex flex-wrap gap-3 items-end">
                
                <!-- Filter TANGGAL (Semua Role) -->
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-calendar-alt text-blue-500"></i> Dari Tanggal
                    </label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-calendar-check text-blue-500"></i> Sampai Tanggal
                    </label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                
                <!-- Filter UNIT (KHUSUS DIREKTUR SAJA) -->
                @if(session('jabatan') == 'DIREKTUR')
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-building text-blue-500"></i> Filter Unit
                    </label>
                    <select name="filter_unit" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">-- Semua Unit --</option>
                        <option value="PG 1A" {{ request('filter_unit') == 'PG 1A' ? 'selected' : '' }}>PG 1A</option>
                        <option value="PG 1B" {{ request('filter_unit') == 'PG 1B' ? 'selected' : '' }}>PG 1B</option>
                        <option value="PG 2" {{ request('filter_unit') == 'PG 2' ? 'selected' : '' }}>PG 2</option>
                    </select>
                </div>
                @endif
                
                <!-- Filter ESTATE (Khusus ADMIN, DIREKTUR, & GM) -->
                @if(in_array(session('jabatan'), ['ADMIN', 'DIREKTUR', 'GENERAL MANAGER']))
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-globe text-blue-500"></i> Filter Estate
                    </label>
                    <select name="filter_estate" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">-- Semua Estate --</option>
                        @foreach($availableEstates as $estate)
                            <option value="{{ $estate }}" {{ request('filter_estate') == $estate ? 'selected' : '' }}>
                                {{ $estate }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <!-- Filter DIVISI (Semua Role kecuali ASISTEN) -->
                @if(session('jabatan') != 'ASISTEN')
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-list text-blue-500"></i> Filter Divisi
                    </label>
                    <select name="filter_divisi" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">-- Semua Divisi --</option>
                        @foreach($availableDivisis as $divisi)
                            <option value="{{ $divisi }}" {{ request('filter_divisi') == $divisi ? 'selected' : '' }}>
                                {{ $divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <!-- Tombol Aksi -->
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                    <a href="{{ route('panen.export', request()->all()) }}" class="bg-green-700 text-white px-5 py-2 rounded-lg hover:bg-green-800">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </form>
        </div>

        <!-- Informasi jumlah data -->
        <div class="mb-4 p-3 bg-gray-100 rounded-lg flex flex-wrap justify-between items-center">
            <div class="text-gray-700">
                <i class="fas fa-database text-blue-500"></i> 
                <span class="font-semibold">Total Data:</span> {{ count($data) }}
                @if(request('start_date') && request('end_date'))
                    <span class="ml-2 text-blue-600">
                        <i class="fas fa-calendar-week"></i> 
                        Periode: {{ date('d/m/Y', strtotime(request('start_date'))) }} - {{ date('d/m/Y', strtotime(request('end_date'))) }}
                    </span>
                @endif
                @if(request('filter_divisi'))
                    <span class="ml-2 text-green-600">
                        <i class="fas fa-filter"></i> Divisi: {{ request('filter_divisi') }}
                    </span>
                @endif
                @if(request('filter_estate'))
                    <span class="ml-2 text-purple-600">
                        <i class="fas fa-filter"></i> Estate: {{ request('filter_estate') }}
                    </span>
                @endif
            </div>
            <div class="text-gray-500 text-sm">
                <i class="fas fa-info-circle"></i> Klik <i class="fas fa-eye"></i> untuk detail
                @php
                    $canEditDelete = (str_contains(session('otorisasi'), 'edit, hapus') || session('jabatan') == 'ADMIN');
                @endphp
                @if($canEditDelete)
                    , <i class="fas fa-edit"></i> untuk edit, <i class="fas fa-trash"></i> untuk hapus
                @endif
            </div>
        </div>

        <!-- Tabel Data (Sama seperti sebelumnya) -->
        <div class="overflow-x-auto shadow-md rounded-lg">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-200 to-gray-300">
                        <th class="px-3 py-3 border text-center font-bold">No</th>
                        <th class="px-3 py-3 border text-center font-bold">Tanggal</th>
                        <th class="px-3 py-3 border text-center font-bold">Estate</th>
                        <th class="px-3 py-3 border text-center font-bold">Divisi</th>
                        <th class="px-3 py-3 border text-center font-bold">Blok</th>
                        <th class="px-3 py-3 border text-center font-bold">Mandor</th>
                        <th class="px-3 py-3 border text-center font-bold">Kerani</th>
                        <th class="px-3 py-3 border text-center font-bold">TPH</th>
                        <th class="px-3 py-3 border text-center font-bold">Pemanen</th>
                        <th class="px-3 py-3 border text-center font-bold">Janjang</th>
                        <th class="px-3 py-3 border text-center font-bold">Matang</th>
                        <th class="px-3 py-3 border text-center font-bold">Mentah</th>
                        <th class="px-3 py-3 border text-center font-bold">Kurang Matang</th>
                        <th class="px-3 py-3 border text-center font-bold">Lewat Matang</th>
                        <th class="px-3 py-3 border text-center font-bold">Partenor Carpi</th>
                        <th class="px-3 py-3 border text-center font-bold">Buah Batu</th>
                        <th class="px-3 py-3 border text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $id => $item)
                    <tr id="row-{{ $id }}" class="hover:bg-blue-50 transition duration-150 ease-in-out border-b">
                        <td class="px-3 py-2 border text-center">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2 border text-center">{{ date('d/m/Y', strtotime($item['tgl'] ?? 'now')) }}</td>
                        <td class="px-3 py-2 border">{{ $item['estate'] ?? '-' }}</td>
                        <td class="px-3 py-2 border text-center">{{ $item['divisi'] ?? '-' }}</td>
                        <td class="px-3 py-2 border text-center">{{ $item['blok'] ?? '-' }}</td>
                        <td class="px-3 py-2 border">{{ $item['mandor'] ?? '-' }}</td>
                        <td class="px-3 py-2 border">{{ $item['kerani'] ?? '-' }}</td>
                        <td class="px-3 py-2 border text-center">{{ $item['tph'] ?? '-' }}</td>
                        <td class="px-3 py-2 border">{{ $item['pemanen'] ?? '-' }}</td>
                        <td class="px-3 py-2 border text-right font-semibold text-blue-600">{{ number_format($item['janjang'] ?? 0) }}</td>
                        <td class="px-3 py-2 border text-right font-semibold text-green-600">{{ number_format($item['matang'] ?? 0) }}</td>
                        <td class="px-3 py-2 border text-right">{{ number_format($item['mentah'] ?? 0) }}</td>
                        <td class="px-3 py-2 border text-right">{{ number_format($item['kurangmatang'] ?? 0) }}</td>
                        <td class="px-3 py-2 border text-right">{{ number_format($item['lewatmatang'] ?? 0) }}</td>
                        <td class="px-3 py-2 border text-right">{{ number_format($item['partenorcarpi'] ?? 0) }}</td>
                        <td class="px-3 py-2 border text-right">{{ number_format($item['buahbatu'] ?? 0) }}</td>
                        <td class="px-3 py-2 border text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('panen.show', $id) }}" class="bg-blue-500 text-white px-3 py-1 rounded-md text-xs hover:bg-blue-600" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($canEditDelete)
                                <a href="{{ route('panen.edit', $id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded-md text-xs hover:bg-yellow-600" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('panen.destroy', $id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md text-xs hover:bg-red-600" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="17" class="px-4 py-8 border text-center text-gray-500">
                            <i class="fas fa-inbox fa-3x mb-2 block"></i>
                            Belum ada data. Silakan tambah data baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Footer info -->
        <div class="mt-4 text-center text-gray-500 text-sm">
            <i class="fas fa-print"></i> Untuk mencetak, gunakan fitur Export Excel atau tekan Ctrl+P
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterEstate = document.querySelector('select[name="filter_estate"]');
    const filterDivisi = document.querySelector('select[name="filter_divisi"]');
    const filterUnit = document.querySelector('select[name="filter_unit"]');
    const jabatan = "{{ session('jabatan') }}";
    
    // Fungsi untuk mengambil divisi berdasarkan estate (via AJAX)
    function loadDivisiByEstate(estate) {
        if (!estate) {
            loadAllDivisi();
            return;
        }
        
        fetch(`/panen/get-divisi-by-estate?estate=${estate}`)
            .then(response => response.json())
            .then(data => {
                if (filterDivisi) {
                    filterDivisi.innerHTML = '<option value="">-- Semua Divisi --</option>';
                    if (data.length > 0) {
                        data.forEach(divisi => {
                            const option = document.createElement('option');
                            option.value = divisi;
                            option.textContent = divisi;
                            filterDivisi.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => console.error('Error loading divisi:', error));
    }
    
    // Fungsi untuk load semua divisi
    function loadAllDivisi() {
        fetch(`/panen/get-all-divisi`)
            .then(response => response.json())
            .then(data => {
                if (filterDivisi) {
                    filterDivisi.innerHTML = '<option value="">-- Semua Divisi --</option>';
                    if (data.length > 0) {
                        data.forEach(divisi => {
                            const option = document.createElement('option');
                            option.value = divisi;
                            option.textContent = divisi;
                            filterDivisi.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => console.error('Error loading divisi:', error));
    }
    
    // Fungsi untuk load estate berdasarkan unit (untuk DIREKTUR)
    function loadEstatesByUnit(unit) {
        if (!unit) return;
        
        fetch(`/panen/get-estates-by-unit?unit=${unit}`)
            .then(response => response.json())
            .then(data => {
                if (filterEstate) {
                    filterEstate.innerHTML = '<option value="">-- Semua Estate --</option>';
                    if (data.estates && data.estates.length > 0) {
                        data.estates.forEach(estate => {
                            const option = document.createElement('option');
                            option.value = estate;
                            option.textContent = estate;
                            filterEstate.appendChild(option);
                        });
                    }
                    // Trigger change event untuk load divisi
                    if (filterEstate.value) {
                        loadDivisiByEstate(filterEstate.value);
                    } else {
                        loadAllDivisi();
                    }
                }
            })
            .catch(error => console.error('Error loading estates:', error));
    }
    
    // Event listener untuk perubahan estate
    if (filterEstate) {
        filterEstate.addEventListener('change', function() {
            loadDivisiByEstate(this.value);
        });
    }
    
    // Event listener untuk perubahan unit (khusus DIREKTUR)
    if (filterUnit && jabatan === 'DIREKTUR') {
        filterUnit.addEventListener('change', function() {
            loadEstatesByUnit(this.value);
        });
    }
    
    // Inisialisasi awal: jika sudah ada filter estate, load divisinya
    if (filterEstate && filterEstate.value) {
        loadDivisiByEstate(filterEstate.value);
    } else if (filterDivisi && filterEstate && !filterEstate.value) {
        loadAllDivisi();
    }
});
</script>
@endpush
@endsection