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
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('panen.update', $id) }}" id="formHasilPanen">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tanggal -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl" required value="{{ old('tgl', $data['tgl'] ?? '') }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Estate -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Estate <span class="text-red-500">*</span></label>
                    @if($isAdmin)
                        <select name="estate_id" id="estate_id" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Pilih Estate --</option>
                            @foreach($estates as $eid => $estate)
                                <option value="{{ $eid }}" 
                                    data-nama="{{ $estate['nama'] }}" 
                                    data-divisi="{{ json_encode($estate['divisi']) }}"
                                    {{ (old('estate_id', $data['estate_id'] ?? '') == $eid) ? 'selected' : '' }}>
                                    {{ $estate['nama'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="estate" id="estate_name" value="{{ old('estate', $data['estate'] ?? '') }}">
                    @else
                        @if(count($estates) > 1)
                            <select name="estate_id" id="estate_id" required class="w-full border rounded-lg px-3 py-2">
                                @foreach($estates as $eid => $estate)
                                    <option value="{{ $eid }}" 
                                        data-nama="{{ $estate['nama'] }}" 
                                        data-divisi="{{ json_encode($estate['divisi']) }}"
                                        {{ (old('estate_id', $selectedEstateId) == $eid) ? 'selected' : '' }}>
                                        {{ $estate['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="estate" id="estate_name" value="{{ old('estate', $data['estate'] ?? '') }}">
                        @else
                            @php
                                $firstEstateId = array_key_first($estates);
                                $firstEstateName = $estates[$firstEstateId]['nama'] ?? $userEstate;
                            @endphp
                            <input type="hidden" name="estate_id" id="estate_id" value="{{ $firstEstateId }}">
                            <input type="hidden" name="estate" id="estate_name" value="{{ $firstEstateName }}">
                            <input type="text" value="{{ $firstEstateName }}" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                        @endif
                    @endif
                </div>

                <!-- Divisi -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Divisi <span class="text-red-500">*</span></label>
                    <select name="divisi" id="divisi" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">-- Pilih Divisi --</option>
                    </select>
                </div>

                <!-- Blok -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Blok <span class="text-red-500">*</span></label>
                    <input type="text" name="blok" required placeholder="Contoh: C023X, D018X" value="{{ old('blok', $data['blok'] ?? '') }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Mandor -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Mandor <span class="text-red-500">*</span></label>
                    <input type="text" name="mandor" required value="{{ old('mandor', $data['mandor'] ?? '') }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Kerani -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Kerani <span class="text-red-500">*</span></label>
                    <input type="text" name="kerani" required value="{{ old('kerani', $data['kerani'] ?? '') }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- TPH -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">TPH <span class="text-red-500">*</span></label>
                    <input type="number" name="tph" required value="{{ old('tph', $data['tph'] ?? 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Pemanen -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Pemanen <span class="text-red-500">*</span></label>
                    <input type="text" name="pemanen" required value="{{ old('pemanen', $data['pemanen'] ?? '') }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Janjang -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Janjang <span class="text-red-500">*</span></label>
                    <input type="number" name="janjang" required value="{{ old('janjang', $data['janjang'] ?? 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Matang -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Matang <span class="text-red-500">*</span></label>
                    <input type="number" name="matang" required value="{{ old('matang', $data['matang'] ?? 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Mentah -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Mentah</label>
                    <input type="number" name="mentah" value="{{ old('mentah', $data['mentah'] ?? 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Kurang Matang -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Kurang Matang</label>
                    <input type="number" name="kurangmatang" value="{{ old('kurangmatang', $data['kurangmatang'] ?? 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Lewat Matang -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Lewat Matang</label>
                    <input type="number" name="lewatmatang" value="{{ old('lewatmatang', $data['lewatmatang'] ?? 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Partenor Carpi -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Partenor Carpi</label>
                    <input type="number" name="partenorcarpi" value="{{ old('partenorcarpi', $data['partenorcarpi'] ?? 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <!-- Buah Batu -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Buah Batu</label>
                    <input type="number" name="buahbatu" value="{{ old('buahbatu', $data['buahbatu'] ?? 0) }}" class="w-full border rounded-lg px-3 py-2">
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
    const currentDivisi = "{{ old('divisi', $data['divisi'] ?? '') }}";

    // Debug (opsional, bisa dihapus)
    console.log('Estates:', estates);
    console.log('Current Divisi:', currentDivisi);

    // Fungsi load divisi
    function loadDivisi(estateId) {
        const divisiSelect = document.getElementById('divisi');
        const estateNameInput = document.getElementById('estate_name');

        if (!divisiSelect) return;

        divisiSelect.innerHTML = '<option value="">-- Pilih Divisi --</option>';

        if (!estateId) return;

        const estate = estates[estateId];
        if (estateNameInput && estate) {
            estateNameInput.value = estate.nama;
        }

        if (estate && estate.divisi && Array.isArray(estate.divisi) && estate.divisi.length > 0) {
            estate.divisi.forEach(divisiNama => {
                const option = document.createElement('option');
                option.value = divisiNama;
                option.textContent = divisiNama;
                if (currentDivisi && divisiNama === currentDivisi) {
                    option.selected = true;
                }
                divisiSelect.appendChild(option);
            });
        } else {
            divisiSelect.innerHTML = '<option value="">-- Tidak ada divisi --</option>';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const estateSelect = document.getElementById('estate_id');

        if (estateSelect) {
            // Jika estateSelect adalah dropdown
            if (estateSelect.tagName === 'SELECT') {
                estateSelect.addEventListener('change', function() {
                    loadDivisi(this.value);
                });

                // Load divisi untuk nilai awal
                if (estateSelect.value) {
                    loadDivisi(estateSelect.value);
                } else if (estateSelect.options.length > 0 && estateSelect.options[0].value) {
                    estateSelect.value = estateSelect.options[0].value;
                    loadDivisi(estateSelect.value);
                }
            } else if (estateSelect.tagName === 'INPUT' && estateSelect.type === 'hidden') {
                // Jika hidden (non-admin dengan 1 estate)
                const estateId = estateSelect.value;
                if (estateId) {
                    loadDivisi(estateId);
                }
            }
        }
    });
</script>
@endpush
@endsection