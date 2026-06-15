@extends('layouts.app')

@section('title', 'Edit Hasil Panen')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Hasil Panen</h2>
            <div class="space-x-2">
                <a href="{{ route('dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('panen.update', $id) }}" id="formHasilPanen">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tanggal -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tgl" value="{{ $data['tgl'] ?? '' }}" required 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Estate -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Estate <span class="text-red-500">*</span>
                    </label>
                    @if($isAdmin)
                        <select name="estate_id" id="estate_id" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Pilih Estate --</option>
                            @foreach($estates as $eid => $estate)
                                <option value="{{ $eid }}" 
                                        data-nama="{{ $estate['nama'] }}"
                                        data-divisi="{{ json_encode($estate['divisi']) }}"
                                        {{ ($data['estate_id'] ?? '') == $eid ? 'selected' : '' }}>
                                    {{ $estate['nama'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="estate" id="estate_name">
                    @else
                        @if(count($estates) > 1)
                            <select name="estate_id" id="estate_id" required class="w-full border rounded-lg px-3 py-2">
                                @foreach($estates as $eid => $estate)
                                    <option value="{{ $eid }}" 
                                            data-nama="{{ $estate['nama'] }}"
                                            data-divisi="{{ json_encode($estate['divisi']) }}"
                                            {{ $selectedEstateId == $eid ? 'selected' : '' }}>
                                        {{ $estate['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="estate" id="estate_name">
                        @else
                            @php
                                $firstEstateId = array_key_first($estates);
                                $firstEstateName = $estates[$firstEstateId]['nama'] ?? $userEstate;
                            @endphp
                            <input type="hidden" name="estate_id" id="estate_id" value="{{ $firstEstateId }}">
                            <input type="hidden" name="estate" id="estate_name" value="{{ $firstEstateName }}">
                            <input type="text" value="{{ $firstEstateName }}" readonly 
                                   class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                        @endif
                    @endif
                </div>

                <!-- Divisi -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Divisi <span class="text-red-500">*</span>
                    </label>
                    <select name="divisi" id="divisi" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">-- Pilih Divisi --</option>
                    </select>
                </div>

                <!-- Blok -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Blok <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="blok" value="{{ $data['blok'] ?? '' }}" required 
                           placeholder="Contoh: C023X, D018X"
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Mandor -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Mandor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mandor" value="{{ $data['mandor'] ?? '' }}" required 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Kerani -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Kerani <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kerani" value="{{ $data['kerani'] ?? '' }}" required 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- TPH -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        TPH <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tph" value="{{ $data['tph'] ?? 0 }}" required 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Pemanen -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Pemanen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pemanen" value="{{ $data['pemanen'] ?? '' }}" required 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Janjang -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Janjang <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="janjang" value="{{ $data['janjang'] ?? 0 }}" required 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Matang -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        Matang <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="matang" value="{{ $data['matang'] ?? 0 }}" required 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Mentah -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Mentah</label>
                    <input type="number" name="mentah" value="{{ $data['mentah'] ?? 0 }}" 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Kurang Matang -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Kurang Matang</label>
                    <input type="number" name="kurangmatang" value="{{ $data['kurangmatang'] ?? 0 }}" 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Lewat Matang -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Lewat Matang</label>
                    <input type="number" name="lewatmatang" value="{{ $data['lewatmatang'] ?? 0 }}" 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Partenor Carpi -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Partenor Carpi</label>
                    <input type="number" name="partenorcarpi" value="{{ $data['partenorcarpi'] ?? 0 }}" 
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Buah Batu -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Buah Batu</label>
                    <input type="number" name="buahbatu" value="{{ $data['buahbatu'] ?? 0 }}" 
                           class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-save"></i> Update Data
                </button>
                <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
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
    const currentDivisi = "{{ $data['divisi'] ?? '' }}";
    
    // Debug
    console.log('========== DEBUG EDIT ==========');
    console.log('Estates:', estates);
    console.log('isAdmin:', isAdmin);
    console.log('Current Divisi:', currentDivisi);
    console.log('================================');
    
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
                // Set selected jika sesuai dengan current divisi
                if (currentDivisi && divisiNama === currentDivisi) {
                    option.selected = true;
                    console.log('Selected option:', divisiNama);
                }
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
        console.log('DOM Content Loaded - Edit Page');
        
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