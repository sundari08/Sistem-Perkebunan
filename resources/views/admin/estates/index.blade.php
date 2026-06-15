@extends('layouts.app')

@section('title', 'Kelola Estate')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Kelola Estate & Divisi</h2>
            <a href="{{ route('admin.estates.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                + Tambah Estate
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 border">ID Estate</th>
                        <th class="px-4 py-2 border">Nama Estate</th>
                        <th class="px-4 py-2 border">Divisi</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estates as $id => $estate)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $id }}</td>
                        <td class="px-4 py-2 border">{{ $estate['nama'] ?? $id }}</td>
                        <td class="px-4 py-2 border">{{ implode(', ', $estate['divisi'] ?? []) }}</td>
                        <td class="px-4 py-2 border">
                            <a href="{{ route('admin.estates.edit', $id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">Edit</a>
                            <form action="{{ route('admin.estates.destroy', $id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus estate ini? Data panen yang terkait akan tetap ada!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 border text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection