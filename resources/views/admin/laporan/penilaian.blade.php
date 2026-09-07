@extends('layouts.admin')

@section('title', 'Laporan Penilaian Kinerja')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Penilaian Kinerja</h1>
            <p class="text-sm text-gray-500">Lihat dan download laporan penilaian kinerja pegawai</p>
        </div>
        <a href="{{ route('admin.laporan.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Filter Periode -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.laporan.penilaian') }}" method="GET" class="flex flex-wrap gap-4">
            <select name="periode_id" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <a href="{{ route('admin.laporan.penilaian') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                    <i class="fas fa-undo mr-2"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($periode)
        <!-- Tombol Download -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.laporan.download-penilaian', ['periode_id' => $periode->id, 'format' => 'pdf']) }}" 
               class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm" target="_blank">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
            <a href="{{ route('admin.laporan.download-penilaian', ['periode_id' => $periode->id, 'format' => 'excel']) }}" 
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                <i class="fas fa-file-excel mr-2"></i> Download Excel
            </a>
        </div>

        <!-- Statistik -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $summary['total'] }}</p>
                <p class="text-sm text-gray-500">Total Pegawai</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $summary['sangat_baik'] }}</p>
                <p class="text-sm text-gray-500">Sangat Baik</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $summary['baik'] }}</p>
                <p class="text-sm text-gray-500">Baik</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ $summary['cukup'] }}</p>
                <p class="text-sm text-gray-500">Cukup</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $summary['kurang'] }}</p>
                <p class="text-sm text-gray-500">Kurang</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Self</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atasan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penilai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predikat</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $index => $d)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $d->pegawai->nama }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $d->pegawai->nip }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $d->pegawai->jabatan->nama ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $d->nilai_self ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $d->nilai_atasan_langsung ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $d->nilai_atasan_penilai ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-blue-600">{{ $d->nilai_akhir }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                  style="color: {{ $d->predikat->warna_text ?? '#000' }}; background-color: {{ $d->predikat->warna_latar ?? '#ccc' }}">
                                {{ $d->predikat->nama ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-3 text-center text-gray-500">
                            Belum ada data penilaian untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3"></i>
                <div>
                    <p class="text-yellow-800 font-medium">Belum Ada Data</p>
                    <p class="text-yellow-700 text-sm">Silakan pilih periode penilaian untuk melihat laporan</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection