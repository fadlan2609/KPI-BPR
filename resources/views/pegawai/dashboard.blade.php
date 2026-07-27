@extends('layouts.pegawai')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ $pegawai->nama }}!</h1>
                <p class="text-sm text-gray-500">
                    {{ $pegawai->jabatan->nama ?? '-' }} | {{ $pegawai->kantor->nama ?? '-' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">NIP: {{ $pegawai->nip }}</p>
            </div>
            <div class="text-right">
                @if($periodeAktif)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                        <i class="fas fa-circle text-green-500 mr-1 text-xs"></i>
                        Periode Aktif: {{ $periodeAktif->nama }}
                    </span>
                @else
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                        <i class="fas fa-circle text-yellow-500 mr-1 text-xs"></i>
                        Tidak Ada Periode Aktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Status Penilaian</p>
                    @if($progress && $progress['finalized'])
                        <p class="text-lg font-bold text-green-600">Selesai</p>
                    @elseif($progress)
                        <p class="text-lg font-bold text-yellow-600">Proses</p>
                    @else
                        <p class="text-lg font-bold text-gray-400">Belum Mulai</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-star text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Nilai Terakhir</p>
                    @if($hasilTerakhir)
                        <p class="text-lg font-bold">{{ $hasilTerakhir->nilai_akhir }}</p>
                        <p class="text-xs text-gray-500">{{ $hasilTerakhir->predikat->nama ?? '-' }}</p>
                    @else
                        <p class="text-lg font-bold text-gray-400">-</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-calendar-alt text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Sisa Cuti</p>
                    @if($saldoCuti)
                        <p class="text-lg font-bold">{{ $saldoCuti->sisa_hari }} hari</p>
                    @else
                        <p class="text-lg font-bold text-gray-400">0 hari</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-clock text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Cuti Pending</p>
                    <p class="text-lg font-bold">{{ $cutiPending }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Penilaian -->
    @if($periodeAktif && $progress)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-tasks text-blue-600 mr-2"></i>
                Progress Penilaian {{ $periodeAktif->nama }}
            </h3>
            <div class="space-y-3">
                @foreach($progress['levels'] as $key => $level)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            @if($level['completed'])
                                <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                            @else
                                <span class="text-gray-400"><i class="fas fa-circle"></i></span>
                            @endif
                            <span class="ml-3 text-sm font-medium">{{ $level['label'] }}</span>
                            @if(isset($level['required']) && $level['required'])
                                <span class="ml-2 text-xs text-red-500">*</span>
                            @endif
                        </div>
                        <div class="flex items-center">
                            @if($level['nilai'])
                                <span class="text-sm font-semibold text-blue-600 mr-3">{{ $level['nilai'] }}</span>
                            @endif
                            <span class="text-xs {{ $level['completed'] ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $level['completed'] ? 'Selesai' : 'Belum' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($progress['finalized'])
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                Penilaian sudah final!
                            </p>
                            <p class="text-lg font-bold text-green-700">
                                Nilai Akhir: {{ $progress['nilai_akhir'] }}
                            </p>
                            @if($progress['predikat'])
                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                      style="color: {{ $progress['predikat_warna'] ? '#fff' : '#000' }}; 
                                             background-color: {{ $progress['predikat_warna'] ?? '#ccc' }}">
                                    {{ $progress['predikat'] }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('pegawai.hasil-penilaian') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                            <i class="fas fa-eye mr-2"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            @else
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-clock mr-2"></i>
                        Penilaian masih dalam proses. Pastikan semua level penilaian sudah diisi.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection