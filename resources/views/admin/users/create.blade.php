@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah User Baru</h2>
        
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" required class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Jabatan <span class="text-red-500">*</span></label>
                <select name="jabatan" id="jabatan" required class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Jabatan --</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="DIREKTUR">DIREKTUR</option>
                    <option value="GENERAL MANAGER">GENERAL MANAGER</option>
                    <option value="MANAGER">MANAGER</option>
                    <option value="ASKEP">ASKEP</option>
                    <option value="KERANI">KERANI</option>
                    <option value="ASISTEN">ASISTEN</option>
                </select>
            </div>
            
            <div class="mb-4" id="estate-field" style="display:none;">
                <label class="block text-gray-700 font-bold mb-2">Estate</label>
                <select name="estate" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Estate --</option>
                    @foreach($estates as $id => $estate)
                        <option value="{{ $estate['nama'] ?? $id }}">{{ $estate['nama'] ?? $id }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4" id="divisi-field" style="display:none;">
                <label class="block text-gray-700 font-bold mb-2">Divisi</label>
                <input type="text" name="divisi" class="w-full border rounded-lg px-3 py-2" placeholder="Contoh: DE01">
            </div>
            
            <div class="mb-4" id="unit-field" style="display:none;">
                <label class="block text-gray-700 font-bold mb-2">Unit</label>
                <select name="unit" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Unit --</option>
                    <option value="PG 1A">PG 1A</option>
                    <option value="PG 1B">PG 1B</option>
                    <option value="PG 2">PG 2</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Otorisasi</label>
                <textarea name="otorisasi" rows="2" class="w-full border rounded-lg px-3 py-2" placeholder="Contoh: input data dan lihat laporan"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('jabatan').addEventListener('change', function() {
    const estateField = document.getElementById('estate-field');
    const divisiField = document.getElementById('divisi-field');
    const unitField = document.getElementById('unit-field');
    const jabatan = this.value;
    
    if (jabatan === 'KERANI' || jabatan === 'ASKEP' || jabatan === 'MANAGER') {
        estateField.style.display = 'block';
        divisiField.style.display = 'none';
        unitField.style.display = 'block';
    } else if (jabatan === 'ASISTEN') {
        estateField.style.display = 'block';
        divisiField.style.display = 'block';
        unitField.style.display = 'block';
    } else if (jabatan === 'GENERAL MANAGER') {
        estateField.style.display = 'none';
        divisiField.style.display = 'none';
        unitField.style.display = 'block';
    } else {
        estateField.style.display = 'none';
        divisiField.style.display = 'none';
        unitField.style.display = 'none';
    }
});
</script>
@endsection