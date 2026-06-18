@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <!-- Selamat datang -->
    <div class="mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
            Selamat Datang, {{ session('username') }}!
        </h2>
        <p class="text-sm sm:text-base text-gray-600">
            Jabatan: {{ session('jabatan') }} 
            @if(session('unit')) | Unit: {{ session('unit') }} @endif
        </p>
        <p class="text-xs sm:text-sm text-gray-500">
            Otorisasi: {{ session('otorisasi') }}
        </p>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-blue-500">
            <p class="text-xs sm:text-sm text-gray-500">Janjang</p>
            <p class="text-lg sm:text-2xl font-bold text-gray-800" id="totalJanjang">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-green-500">
            <p class="text-xs sm:text-sm text-gray-500">Matang</p>
            <p class="text-lg sm:text-2xl font-bold text-gray-800" id="totalMatang">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-yellow-500">
            <p class="text-xs sm:text-sm text-gray-500">Mentah</p>
            <p class="text-lg sm:text-2xl font-bold text-gray-800" id="totalMentah">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 border-l-4 border-red-500 col-span-2 sm:col-span-1">
            <p class="text-xs sm:text-sm text-gray-500">Buah Batu</p>
            <p class="text-lg sm:text-2xl font-bold text-gray-800" id="totalBuahBatu">0</p>
        </div>
    </div>

    <!-- Grafik -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-4">Tren Harian (30 hari terakhir)</h3>
        <div class="w-full h-60 sm:h-72 md:h-80 relative">
            <canvas id="dashboardChart"></canvas>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('panen.index') }}" class="bg-blue-500 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-table mr-2"></i> Lihat Data Panen
        </a>
        @if(str_contains(session('otorisasi'), 'input data') || session('jabatan') == 'ADMIN')
        <a href="{{ route('panen.create') }}" class="bg-green-500 text-white text-center py-2 px-4 rounded-lg hover:bg-green-600 transition">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Data
        </a>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("panen.statistik") }}')
        .then(response => {
            if (!response.ok) throw new Error('Gagal fetch statistik');
            return response.json();
        })
        .then(data => {
            // Update total cards
            document.getElementById('totalJanjang').textContent = data.total.janjang ?? 0;
            document.getElementById('totalMatang').textContent = data.total.matang ?? 0;
            document.getElementById('totalMentah').textContent = data.total.mentah ?? 0;
            document.getElementById('totalBuahBatu').textContent = data.total.buahbatu ?? 0;

            // Render chart
            const ctx = document.getElementById('dashboardChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [
                        {
                            label: 'Janjang',
                            data: data.janjang || [],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 2,
                        },
                        {
                            label: 'Matang',
                            data: data.matang || [],
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 2,
                        },
                        {
                            label: 'Mentah',
                            data: data.mentah || [],
                            borderColor: 'rgb(234, 179, 8)',
                            backgroundColor: 'rgba(234, 179, 8, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: { size: window.innerWidth < 600 ? 10 : 12 }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxTicksLimit: 10,
                                font: { size: window.innerWidth < 600 ? 8 : 10 }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: window.innerWidth < 600 ? 8 : 10 }
                            }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error('Error statistik:', err);
            // Tampilkan pesan error jika diperlukan
            document.querySelectorAll('.stat-card .stat-value').forEach(el => el.textContent = 'Err');
        });
});
</script>
@endpush
@endsection