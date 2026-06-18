@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit User: {{ $user['username'] }}</h2>
        
        <form method="POST" action="{{ secure_url('admin.users.update', $user['id']) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" value="{{ $user['username'] }}" required class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Password (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Jabatan <span class="text-red-500">*</span></label>
                <select name="jabatan" id="jabatan" required class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Jabatan --</option>
                    <option value="ADMIN" {{ ($user['jabatan'] ?? '') == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="DIREKTUR" {{ ($user['jabatan'] ?? '') == 'DIREKTUR' ? 'selected' : '' }}>DIREKTUR</option>
                    <option value="GENERAL MANAGER" {{ ($user['jabatan'] ?? '') == 'GENERAL MANAGER' ? 'selected' : '' }}>GENERAL MANAGER</option>
                    <option value="MANAGER" {{ ($user['jabatan'] ?? '') == 'MANAGER' ? 'selected' : '' }}>MANAGER</option>
                    <option value="ASKEP" {{ ($user['jabatan'] ?? '') == 'ASKEP' ? 'selected' : '' }}>ASKEP</option>
                    <option value="KERANI" {{ ($user['jabatan'] ?? '') == 'KERANI' ? 'selected' : '' }}>KERANI</option>
                    <option value="ASISTEN" {{ ($user['jabatan'] ?? '') == 'ASISTEN' ? 'selected' : '' }}>ASISTEN</option>
                </select>
            </div>
            
            <div class="mb-4" id="estate-field" style="{{ in_array($user['jabatan'] ?? '', ['KERANI', 'ASKEP', 'MANAGER', 'ASISTEN']) ? 'display:block' : 'display:none' }}">
                <label class="block text-gray-700 font-bold mb-2">Estate</label>
                <select name="estate" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Estate --</option>
                    @foreach($estates as $id => $estate)
                        <option value="{{ $estate['nama'] ?? $id }}" {{ ($user['estate'] ?? '') == ($estate['nama'] ?? $id) ? 'selected' : '' }}>
                            {{ $estate['nama'] ?? $id }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4" id="divisi-field" style="{{ ($user['jabatan'] ?? '') == 'ASISTEN' ? 'display:block' : 'display:none' }}">
                <label class="block text-gray-700 font-bold mb-2">Divisi</label>
                <input type="text" name="divisi" value="{{ $user['divisi'] ?? '' }}" class="w-full border rounded-lg px-3 py-2" placeholder="Contoh: DE01">
            </div>
            
            <div class="mb-4" id="unit-field" style="{{ in_array($user['jabatan'] ?? '', ['KERANI', 'ASKEP', 'MANAGER', 'ASISTEN', 'GENERAL MANAGER']) ? 'display:block' : 'display:none' }}">
                <label class="block text-gray-700 font-bold mb-2">Unit</label>
                <select name="unit" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Unit --</option>
                    <option value="PG 1A" {{ ($user['unit'] ?? '') == 'PG 1A' ? 'selected' : '' }}>PG 1A</option>
                    <option value="PG 1B" {{ ($user['unit'] ?? '') == 'PG 1B' ? 'selected' : '' }}>PG 1B</option>
                    <option value="PG 2" {{ ($user['unit'] ?? '') == 'PG 2' ? 'selected' : '' }}>PG 2</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Otorisasi</label>
                <textarea name="otorisasi" rows="2" class="w-full border rounded-lg px-3 py-2">{{ $user['otorisasi'] ?? '' }}</textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">Update</button>
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