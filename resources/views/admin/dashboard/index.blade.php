@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <span class="text-sm text-gray-500">Periode Aktif: {{ $periodeAktif->nama ?? 'Belum ada periode aktif' }}</span>
    </div>
    
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Pegawai</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalPegawai }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-briefcase text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Jabatan</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalJabatan }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-store text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Kantor</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalKantor }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Status Penilaian</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $periodeAktif ? 'Aktif' : 'Tidak Aktif' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Chart 1: Pegawai per Jabatan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">📊 Pegawai per Jabatan</h3>
            <canvas id="chartJabatan" height="250"></canvas>
        </div>
        
        <!-- Chart 2: Pegawai per Kantor -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">📊 Pegawai per Kantor</h3>
            <canvas id="chartKantor" height="250"></canvas>
        </div>
    </div>
    
    <!-- Progress KPI Checklist -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">📋 Progress Penilaian KPI</h3>
        <div class="space-y-3">
            @php
                $items = [
                    'bpr' => ['label' => 'Informasi BPR', 'icon' => 'fa-building'],
                    'jabatan' => ['label' => 'Jabatan Pegawai', 'icon' => 'fa-briefcase'],
                    'pegawai' => ['label' => 'Daftar Pegawai', 'icon' => 'fa-users'],
                    'predikat' => ['label' => 'Predikat Kinerja', 'icon' => 'fa-trophy'],
                    'indikator' => ['label' => 'Indikator Penilaian', 'icon' => 'fa-list-check'],
                    'self_assessment' => ['label' => 'Self Assessment', 'icon' => 'fa-user-check'],
                    'laporan' => ['label' => 'Laporan Penilaian', 'icon' => 'fa-file-pdf']
                ];
            @endphp
            
            @foreach($items as $key => $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas {{ $item['icon'] }} text-gray-500 mr-3"></i>
                        <span class="text-sm font-medium">{{ $item['label'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm mr-3">
                            @if($progress[$key])
                                <span class="text-green-600">✅ Selesai</span>
                            @else
                                <span class="text-yellow-600">⏳ Belum</span>
                            @endif
                        </span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $progress[$key] ? 'bg-green-500 w-full' : 'bg-yellow-500 w-0' }}"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart 1: Pegawai per Jabatan
    const ctx1 = document.getElementById('chartJabatan').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['perJabatan']['labels']) !!},
            datasets: [{
                label: 'Jumlah Pegawai',
                data: {!! json_encode($chartData['perJabatan']['data']) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Chart 2: Pegawai per Kantor
    const ctx2 = document.getElementById('chartKantor').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['perKantor']['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['perKantor']['data']) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush