@extends('layouts.admin')

@section('title', 'Cetak Hasil Penilaian')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cetak Hasil Penilaian</h1>
            <p class="text-sm text-gray-500">Cetak laporan hasil penilaian kinerja pegawai</p>
        </div>
        <a href="{{ route('admin.penilaian.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Filter Periode -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.penilaian.cetak') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="periode" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Periode</option>
                @foreach($periodeList as $p)
                    <option value="{{ $p->id }}" {{ $periode && $periode->id == $p->id ? 'selected' : '' }}>
                        {{ $p->nama }} ({{ $p->tanggal_mulai->format('d/m/Y') }} - {{ $p->tanggal_selesai->format('d/m/Y') }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-filter mr-2"></i> Tampilkan
            </button>
            @if($periode)
                <a href="{{ route('admin.penilaian.cetak') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                    <i class="fas fa-undo mr-2"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($periode)
        <!-- Tombol Download -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.penilaian.preview-all', $periode->id) }}" 
               class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-sm" target="_blank">
                <i class="fas fa-eye mr-2"></i> Preview All PDF
            </a>
            <a href="{{ route('admin.penilaian.cetak-pdf', $periode->id) }}" 
               class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
            <a href="{{ route('admin.penilaian.cetak-excel', $periode->id) }}" 
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                <i class="fas fa-file-excel mr-2"></i> Download Excel
            </a>
        </div>

        <!-- Statistik -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $statistik['total'] }}</p>
                <p class="text-sm text-gray-500">Total Pegawai</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $statistik['sangat_baik'] }}</p>
                <p class="text-sm text-gray-500">Sangat Baik</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ $statistik['baik'] }}</p>
                <p class="text-sm text-gray-500">Baik</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-orange-600">{{ $statistik['cukup'] }}</p>
                <p class="text-sm text-gray-500">Cukup</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $statistik['kurang'] }}</p>
                <p class="text-sm text-gray-500">Kurang</p>
            </div>
        </div>

        <!-- Table Hasil Penilaian -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Self</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atasan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penilai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Akhir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predikat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $index => $d)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $d->pegawai->nama }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $d->pegawai->nip }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $d->pegawai->jabatan->nama ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $d->nilai_self ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $d->nilai_atasan_langsung ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $d->nilai_atasan_penilai ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold">{{ $d->nilai_akhir }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                  style="color: {{ $d->predikat->warna_text ?? '#000' }}; background-color: {{ $d->predikat->warna_latar ?? '#ccc' }}">
                                {{ $d->predikat->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.penilaian.preview', [$d->pegawai_id, $periode->id]) }}" 
                               class="text-purple-600 hover:text-purple-900 mr-2" target="_blank">
                                <i class="fas fa-eye mr-1"></i> Preview
                            </a>
                            <a href="{{ route('admin.penilaian.cetak-single', [$d->pegawai_id, $periode->id]) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-print mr-1"></i> Download
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-3 text-center text-gray-500">
                            Belum ada data penilaian final untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                Silakan pilih periode penilaian untuk melihat hasil
            </p>
        </div>
    @endif
</div>
@endsection