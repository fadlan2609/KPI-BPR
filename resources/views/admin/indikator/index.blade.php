@extends('layouts.admin')

@section('title', 'Indikator Penilaian Jabatan')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Indikator Penilaian Jabatan Pegawai</h1>
        <div class="text-sm text-gray-500">
            @if($periodeAktif)
                Periode Aktif: {{ $periodeAktif->nama }}
            @else
                <span class="text-yellow-600">Belum ada periode aktif</span>
            @endif
        </div>
    </div>

    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            Pilih jabatan di bawah ini untuk mengelola indikator penilaian
        </p>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jabatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atasan Langsung / Penilai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atasan Penilai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Indikator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($jabatan as $index => $j)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $j->nama }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $j->atasan->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $j->atasan->atasan->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ $j->pegawai_count }} pegawai
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($j->status_indikator == 'lengkap')
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i> Ada Indikator
                                </span>
                                <span class="text-xs text-gray-400">({{ $j->total_indikator }})</span>
                            </div>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                <i class="fas fa-exclamation-circle mr-1"></i> Belum Ada
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.indikator.edit', $j->id) }}" 
                           class="px-3 py-1 {{ $j->status_indikator == 'lengkap' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-md text-xs transition">
                            <i class="fas {{ $j->status_indikator == 'lengkap' ? 'fa-edit' : 'fa-plus' }} mr-1"></i> 
                            {{ $j->status_indikator == 'lengkap' ? 'Edit Indikator' : 'Buat Indikator' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data jabatan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3">
            {{ $jabatan->links() }}
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $jabatan->total() }}</p>
            <p class="text-sm text-gray-500">Total Jabatan</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-green-600">
                {{ $jabatan->filter(function($j) { return $j->status_indikator == 'lengkap'; })->count() }}
            </p>
            <p class="text-sm text-gray-500">Sudah Ada Indikator</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-red-600">
                {{ $jabatan->filter(function($j) { return $j->status_indikator == 'kosong'; })->count() }}
            </p>
            <p class="text-sm text-gray-500">Belum Ada Indikator</p>
        </div>
    </div>
</div>
@endsection