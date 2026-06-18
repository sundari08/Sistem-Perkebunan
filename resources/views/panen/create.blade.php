@extends('layouts.app')

@section('title', 'Tambah Hasil Panen')

@section('content')
<div class="max-w-4xl mx-auto px-2 sm:px-4">
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Tambah Data Hasil Panen</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-600">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-gray-600">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-3 text-sm">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('panen.store') }}" id="formHasilPanen">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <!-- Tanggal -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl" id="tgl" required value="{{ old('tgl', date('Y-m-d')) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Estate -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Estate <span class="text-red-500">*</span></label>
                    @if($isAdmin)
                        <select name="estate_id" id="estate_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="">-- Pilih Estate --</option>
                            @foreach($estates as $id => $estate)
                                <option value="{{ $id }}" data-nama="{{ $estate['nama'] }}" data-divisi="{{ json_encode($estate['divisi']) }}">{{ $estate['nama'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="estate" id="estate_name">
                    @else
                        @if($showEstateDropdown && count($estates) > 1)
                            <select name="estate_id" id="estate_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                                @foreach($estates as $id => $estate)
                                    <option value="{{ $id }}" data-nama="{{ $estate['nama'] }}" data-divisi="{{ json_encode($estate['divisi']) }}" {{ $selectedEstateId == $id ? 'selected' : '' }}>{{ $estate['nama'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="estate" id="estate_name">
                        @else
                            @php $firstEstateId = array_key_first($estates); $firstEstateName = $estates[$firstEstateId]['nama'] ?? $userEstate; @endphp
                            <input type="hidden" name="estate_id" id="estate_id" value="{{ $firstEstateId }}">
                            <input type="hidden" name="estate" id="estate_name" value="{{ $firstEstateName }}">
                            <input type="text" value="{{ $firstEstateName }}" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-sm">
                        @endif
                    @endif
                </div>

                <!-- Divisi -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Divisi <span class="text-red-500">*</span></label>
                    <select name="divisi" id="divisi" required class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Pilih Divisi --</option>
                    </select>
                </div>

                <!-- Blok -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Blok <span class="text-red-500">*</span></label>
                    <input type="text" name="blok" id="blok" required placeholder="Contoh: C023X, D018X"
                           value="{{ old('blok') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Mandor -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Mandor <span class="text-red-500">*</span></label>
                    <input type="text" name="mandor" id="mandor" required value="{{ old('mandor') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Kerani -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Kerani <span class="text-red-500">*</span></label>
                    <input type="text" name="kerani" id="kerani" required value="{{ old('kerani') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- TPH -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">TPH <span class="text-red-500">*</span></label>
                    <input type="number" name="tph" id="tph" required value="{{ old('tph', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Pemanen -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Pemanen <span class="text-red-500">*</span></label>
                    <input type="text" name="pemanen" id="pemanen" required value="{{ old('pemanen') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Janjang -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Janjang <span class="text-red-500">*</span></label>
                    <input type="number" name="janjang" id="janjang" required value="{{ old('janjang', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Matang -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Matang <span class="text-red-500">*</span></label>
                    <input type="number" name="matang" id="matang" required value="{{ old('matang', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Mentah -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Mentah</label>
                    <input type="number" name="mentah" id="mentah" value="{{ old('mentah', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Kurang Matang -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Kurang Matang</label>
                    <input type="number" name="kurangmatang" id="kurangmatang" value="{{ old('kurangmatang', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Lewat Matang -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Lewat Matang</label>
                    <input type="number" name="lewatmatang" id="lewatmatang" value="{{ old('lewatmatang', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Partenor Carpi -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Partenor Carpi</label>
                    <input type="number" name="partenorcarpi" id="partenorcarpi" value="{{ old('partenorcarpi', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <!-- Buah Batu -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Buah Batu</label>
                    <input type="number" name="buahbatu" id="buahbatu" value="{{ old('buahbatu', 0) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="mt-5 flex flex-col sm:flex-row gap-2">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 w-full sm:w-auto text-sm">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
                <button type="reset" class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 w-full sm:w-auto text-sm">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 w-full sm:w-auto text-sm text-center">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Data estate dari controller
    const estates = @json($estates);
    const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
    
    // Debug
    console.log('========== DEBUG ==========');
    console.log('Estates:', estates);
    console.log('isAdmin:', isAdmin);
    console.log('============================');
    
    // Fungsi untuk load divisi
    function loadDivisi(estateId) {
        console.log('loadDivisi called with estateId:', estateId);
        
        const divisiSelect = document.getElementById('divisi');
        const estateNameInput = document.getElementById('estate_name');
        
        if (!divisiSelect) {
            console.error('Element #divisi not found!');
            return;
        }
        
        // Reset dropdown
        divisiSelect.innerHTML = '<option value="">-- Pilih Divisi --</option>';
        
        if (!estateId) {
            console.log('No estateId, divisi select cleared');
            return;
        }
        
        // Cari estate
        const estate = estates[estateId];
        console.log('Found estate:', estate);
        
        if (estateNameInput && estate) {
            estateNameInput.value = estate.nama;
            console.log('Set estate_name to:', estate.nama);
        }
        
        // Load divisi
        if (estate && estate.divisi && Array.isArray(estate.divisi) && estate.divisi.length > 0) {
            console.log('Divisi to load:', estate.divisi);
            estate.divisi.forEach(divisiNama => {
                const option = document.createElement('option');
                option.value = divisiNama;
                option.textContent = divisiNama;
                divisiSelect.appendChild(option);
            });
            console.log('Divisi select now has', estate.divisi.length, 'options');
        } else {
            console.warn('No divisi found for estate:', estateId);
            divisiSelect.innerHTML = '<option value="">-- Tidak ada divisi untuk estate ini --</option>';
        }
    }
    
    // Jalankan saat DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');
        
        const estateSelect = document.getElementById('estate_id');
        console.log('Estate select element:', estateSelect);
        console.log('Estate select value:', estateSelect ? estateSelect.value : 'not found');
        
        if (estateSelect) {
            // Pasang event listener
            estateSelect.addEventListener('change', function() {
                console.log('Estate changed to:', this.value);
                loadDivisi(this.value);
            });
            
            // Load divisi untuk nilai awal
            if (estateSelect.value) {
                loadDivisi(estateSelect.value);
            } else if (estateSelect.options && estateSelect.options.length > 0) {
                // Jika tidak ada value tapi ada options, pilih yang pertama
                if (estateSelect.options[0].value) {
                    console.log('Auto selecting first option:', estateSelect.options[0].value);
                    estateSelect.value = estateSelect.options[0].value;
                    loadDivisi(estateSelect.value);
                }
            }
        } else {
            console.error('Element #estate_id not found!');
        }
    });
</script>
@endpush
@endsection