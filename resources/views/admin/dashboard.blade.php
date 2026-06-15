@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
</style>

<div class="max-w-7xl mx-auto px-4">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-6 mb-6 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Selamat Datang, {{ session('username') }}!</h1>
                <p class="text-blue-100 mt-1">Admin Panel - Kelola seluruh data aplikasi</p>
                <div class="mt-3 flex gap-2">
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">👑 Super Admin</span>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">📊 Full Access</span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold">{{ date('d/m/Y') }}</p>
                <p class="text-blue-100">{{ date('l') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="stat-card bg-white rounded-xl shadow-md p-5 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
                    <p class="text-green-500 text-sm mt-1">
                        <i class="fas fa-users"></i> Seluruh pengguna
                    </p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="mt-3 inline-block text-blue-500 text-sm hover:underline">
                Kelola User <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-md p-5 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Estates</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalEstates }}</p>
                    <p class="text-green-500 text-sm mt-1">
                        <i class="fas fa-building"></i> Kebun yang terdaftar
                    </p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-building text-green-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.estates.index') }}" class="mt-3 inline-block text-green-500 text-sm hover:underline">
                Kelola Estate <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-md p-5 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Data Panen</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalPanen }}</p>
                    <p class="text-purple-500 text-sm mt-1">
                        <i class="fas fa-database"></i> Seluruh record
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-database text-purple-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('panen.index') }}" class="mt-3 inline-block text-purple-500 text-sm hover:underline">
                Lihat Data <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-md p-5 border-l-4 border-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Janjang</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($totalJanjang ?? 0) }}</p>
                    <p class="text-orange-500 text-sm mt-1">
                        <i class="fas fa-chart-line"></i> Akumulasi
                    </p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-orange-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('panen.index') }}" class="mt-3 inline-block text-orange-500 text-sm hover:underline">
                Detail Laporan <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Quick Actions & Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-md p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-bolt text-yellow-500"></i> Quick Actions
            </h3>
            <div class="space-y-3">
                <a href="{{ route('admin.users.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                    <div class="bg-green-100 rounded-full p-2 mr-3">
                        <i class="fas fa-user-plus text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold">Tambah User Baru</p>
                        <p class="text-gray-500 text-sm">Buat akun untuk karyawan baru</p>
                    </div>
                </a>
                <a href="{{ route('admin.estates.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                    <div class="bg-blue-100 rounded-full p-2 mr-3">
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold">Tambah Estate Baru</p>
                        <p class="text-gray-500 text-sm">Tambahkan kebun baru</p>
                    </div>
                </a>
                <a href="{{ route('panen.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                    <div class="bg-purple-100 rounded-full p-2 mr-3">
                        <i class="fas fa-plus-circle text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold">Input Data Panen</p>
                        <p class="text-gray-500 text-sm">Tambah data hasil panen</p>
                    </div>
                </a>
                <a href="{{ route('panen.export') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                    <div class="bg-green-100 rounded-full p-2 mr-3">
                        <i class="fas fa-file-excel text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold">Export Excel</p>
                        <p class="text-gray-500 text-sm">Download laporan lengkap</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Chart Area - 2 columns -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-bar text-blue-500"></i> Statistik 30 Hari Terakhir
            </h3>
            <canvas id="adminChart" height="200"></canvas>
        </div>
    </div>

    <!-- Recent Data Table -->
    <div class="bg-white rounded-xl shadow-md p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-clock text-blue-500"></i> Data Panen Terbaru
            </h3>
            <a href="{{ route('panen.index') }}" class="text-blue-500 text-sm hover:underline">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Estate</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Divisi</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Blok</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-600">Janjang</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-600">Matang</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPanen ?? [] as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm">{{ \Carbon\Carbon::parse($item['tgl'])->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-sm">{{ $item['estate'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm">{{ $item['divisi'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm">{{ $item['blok'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ number_format($item['janjang'] ?? 0) }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ number_format($item['matang'] ?? 0) }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('panen.show', $item['id'] ?? '') }}" class="text-blue-500 hover:underline text-sm">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada data panen</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/statistik-bulan')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('adminChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Janjang',
                            data: data.janjang,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Matang',
                            data: data.matang,
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Mentah',
                            data: data.mentah,
                            borderColor: 'rgb(249, 115, 22)',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
                        }
                    }
                }
            });
        });
});
</script>
@endpush
@endsection