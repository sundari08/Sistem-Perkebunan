@extends('layouts.app')

@section('title', 'Edit Estate')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Estate: {{ $estate['id'] }}</h2>
        
        <form method="POST" action="{{ route('admin.estates.update', $estate['id']) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nama Estate <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ $estate['nama'] }}" required class="w-full border rounded-lg px-3 py-2">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Divisi <span class="text-red-500">*</span></label>
                <input type="text" name="divisi" value="{{ $estate['divisi_string'] }}" required placeholder="Contoh: DE01,DE02,DE03" class="w-full border rounded-lg px-3 py-2">
                <p class="text-sm text-gray-500 mt-1">Pisahkan dengan koma. Contoh: DE01,DE02,DE03</p>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">Update</button>
                <a href="{{ route('admin.estates.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection