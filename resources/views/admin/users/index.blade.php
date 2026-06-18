@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Kelola User</h2>
            <a href="{{ route('admin.users.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                + Tambah User
            </a>
        </div>
        
        <!-- Filter -->
        <form method="GET" class="mb-6 flex gap-3">
            <select name="filter_jabatan" class="border rounded px-3 py-2">
                <option value="">-- Semua Jabatan --</option>
                <option value="ADMIN" {{ request('filter_jabatan') == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                <option value="DIREKTUR" {{ request('filter_jabatan') == 'DIREKTUR' ? 'selected' : '' }}>DIREKTUR</option>
                <option value="GENERAL MANAGER" {{ request('filter_jabatan') == 'GENERAL MANAGER' ? 'selected' : '' }}>GENERAL MANAGER</option>
                <option value="MANAGER" {{ request('filter_jabatan') == 'MANAGER' ? 'selected' : '' }}>MANAGER</option>
                <option value="ASKEP" {{ request('filter_jabatan') == 'ASKEP' ? 'selected' : '' }}>ASKEP</option>
                <option value="KERANI" {{ request('filter_jabatan') == 'KERANI' ? 'selected' : '' }}>KERANI</option>
                <option value="ASISTEN" {{ request('filter_jabatan') == 'ASISTEN' ? 'selected' : '' }}>ASISTEN</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Reset</a>
        </form>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 border">Username</th>
                        <th class="px-4 py-2 border">Jabatan</th>
                        <th class="px-4 py-2 border">Estate</th>
                        <th class="px-4 py-2 border">Divisi</th>
                        <th class="px-4 py-2 border">Unit</th>
                        <th class="px-4 py-2 border">Otorisasi</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $id => $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $user['username'] ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $user['jabatan'] ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $user['estate'] ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $user['divisi'] ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $user['unit'] ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $user['otorisasi'] ?? '-' }}</td>
                        <td class="px-4 py-2 border">
                            <a href="{{ secure_url('admin.users.edit', $id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">Edit</a>
                            <form action="{{ secure_url('admin.users.destroy', $id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 border text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection