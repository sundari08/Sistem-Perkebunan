@extends('layouts.app')

@section('title', 'Edit Hasil Panen')

@section('content')
<div class="max-w-4xl mx-auto px-2 sm:px-4">
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Edit Hasil Panen</h2>
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

        <form method="POST" action="{{ route('panen.update', $id) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <!-- semua field sama seperti create, tapi pakai value dari $data -->
                <!-- contoh untuk tanggal -->
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl" value="{{ $data['tgl'] ?? '' }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <!-- ... dan seterusnya ... -->
            </div>
            <div class="mt-5 flex flex-col sm:flex-row gap-2">
                <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 w-full sm:w-auto text-sm">
                    <i class="fas fa-save"></i> Update Data
                </button>
                <a href="{{ route('panen.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 w-full sm:w-auto text-sm text-center">
                    Batal
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