@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4">
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Selamat Datang, {{ session('username') }}!</h2>
            <p class="text-sm sm:text-base text-gray-600 mt-1">
                Jabatan: <span class="font-semibold">{{ session('jabatan') }}</span> |
                Unit: <span class="font-semibold">{{ session('unit') }}</span>
            </p>
            <p class="text-sm text-gray-600">
                Otorisasi: <span class="text-blue-600 font-semibold">{{ session('otorisasi') }}</span>
            </p>
        </div>

        <!-- Statistik Ringkasan Total Bulan Ini -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-2 md:gap-3 lg:gap-4 mb-6">
            <div class="bg-blue-100 p-2 rounded text-center">
                <p class="text-[10px] sm:text-xs text-gray-600">Janjang</p>
                <p class="text-base sm:text-lg md:text-xl font-bold text-blue-700" id="totalJanjang">0</p>
            </div>
            <div class="bg-green-100 p-2 rounded text-center">
                <p class="text-[10px] sm:text-xs text-gray-600">Matang</p>
                <p class="text-base sm:text-lg md:text-xl font-bold text-green-700" id="totalMatang">0</p>
            </div>
            <div class="bg-yellow-100 p-2 rounded text-center">
                <p class="text-[10px] sm:text-xs text-gray-600">Mentah</p>
                <p class="text-base sm:text-lg md:text-xl font-bold text-yellow-700" id="totalMentah">0</p>
            </div>
            <div class="bg-orange-100 p-2 rounded text-center">
                <p class="text-[10px] sm:text-xs text-gray-600">Kurang Matang</p>
                <p class="text-base sm:text-lg md:text-xl font-bold text-orange-700" id="totalKurangMatang">0</p>
            </div>
            <div class="bg-red-100 p-2 rounded text-center">
                <p class="text-[10px] sm:text-xs text-gray-600">Lewat Matang</p>
                <p class="text-base sm:text-lg md:text-xl font-bold text-red-700" id="totalLewatMatang">0</p>
            </div>
            <div class="bg-purple-100 p-2 rounded text-center">
                <p class="text-[10px] sm:text-xs text-gray-600">Partenor Carpi</p>
                <p class="text-base sm:text-lg md:text-xl font-bold text-purple-700" id="totalPartenor">0</p>
            </div>
            <div class="bg-pink-100 p-2 rounded text-center col-span-2 sm:col-span-1">
                <p class="text-[10px] sm:text-xs text-gray-600">Buah Batu</p>
                <p class="text-base sm:text-lg md:text-xl font-bold text-pink-700" id="totalBuahBatu">0</p>
            </div>
        </div>

        <!-- Grafik Tren 30 Hari -->
        <div class="mb-6">
            <h3 class="text-base sm:text-lg font-semibold mb-2">Tren Harian (30 hari terakhir)</h3>
            <div class="relative w-full h-52 sm:h-64 md:h-80">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Menu Aksi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <div class="text-3xl sm:text-4xl mb-2">📊</div>
                <h3 class="font-bold text-base sm:text-lg mb-2">Data Panen</h3>
                <p class="text-sm text-gray-600 mb-4">Lihat semua data hasil panen</p>
                <a href="{{ route('panen.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 inline-block w-full sm:w-auto">
                    Lihat Data
                </a>
            </div>

            @if(str_contains(session('otorisasi'), 'input data') || session('jabatan') == 'ADMIN')
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <div class="text-3xl sm:text-4xl mb-2">➕</div>
                <h3 class="font-bold text-base sm:text-lg mb-2">Tambah Data</h3>
                <p class="text-sm text-gray-600 mb-4">Tambah data hasil panen baru</p>
                <a href="{{ route('panen.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 inline-block w-full sm:w-auto">
                    Tambah Data
                </a>
            </div>
            @endif
        </div>

        <!-- Logout -->
        <div class="mt-6 text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 w-full sm:w-auto">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/statistik-bulan')
        .then(response => response.json())
        .then(data => {
            // Update total ringkasan
            document.getElementById('totalJanjang').innerText = data.total.janjang ?? 0;
            document.getElementById('totalMatang').innerText = data.total.matang ?? 0;
            document.getElementById('totalMentah').innerText = data.total.mentah ?? 0;
            document.getElementById('totalKurangMatang').innerText = data.total.kurangmatang ?? 0;
            document.getElementById('totalLewatMatang').innerText = data.total.lewatmatang ?? 0;
            document.getElementById('totalPartenor').innerText = data.total.partenorcarpi ?? 0;
            document.getElementById('totalBuahBatu').innerText = data.total.buahbatu ?? 0;

            // Grafik
            const ctx = document.getElementById('trendChart').getContext('2d');
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
                            pointRadius: window.innerWidth < 600 ? 1 : 3,
                        },
                        {
                            label: 'Matang',
                            data: data.matang || [],
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: window.innerWidth < 600 ? 1 : 3,
                        },
                        {
                            label: 'Mentah',
                            data: data.mentah || [],
                            borderColor: 'rgb(234, 179, 8)',
                            backgroundColor: 'rgba(234, 179, 8, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: window.innerWidth < 600 ? 1 : 3,
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
                                padding: window.innerWidth < 600 ? 6 : 10,
                                font: { size: window.innerWidth < 600 ? 10 : 12 }
                            }
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
                                text: 'Jumlah',
                                font: { size: window.innerWidth < 600 ? 10 : 12 }
                            },
                            ticks: {
                                font: { size: window.innerWidth < 600 ? 9 : 11 }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal',
                                font: { size: window.innerWidth < 600 ? 10 : 12 }
                            },
                            ticks: {
                                maxRotation: 45,
                                autoSkip: true,
                                maxTicksLimit: 10,
                                font: { size: window.innerWidth < 600 ? 8 : 10 }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Gagal memuat statistik:', error));
});
</script>
@endpush
@endsection