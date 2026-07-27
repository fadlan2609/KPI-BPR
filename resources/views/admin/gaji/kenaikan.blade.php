@extends('layouts.admin')

@section('title', 'Kenaikan Gaji')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kenaikan Gaji</h1>
            <p class="text-sm text-gray-500">Periode: {{ $periode->nama ?? '-' }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.gaji.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if($results && count($results) > 0)
        <!-- Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $summary['total_pegawai'] }}</p>
                <p class="text-sm text-gray-500">Total Pegawai</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['total_kenaikan'], 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500">Total Kenaikan</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($summary['rata_rata_persentase'], 2) }}%</p>
                <p class="text-sm text-gray-500">Rata-rata Kenaikan</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <a href="{{ route('admin.gaji.export-kenaikan', ['periode_id' => $periode->id]) }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm inline-block">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>

        <!-- Table Kenaikan Gaji -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gaji Sekarang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predikat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Kenaikan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gaji Baru</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($results as $index => $r)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $r['pegawai_nama'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $r['pegawai_nip'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">Rp {{ number_format($r['gaji_sekarang'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                  style="color: {{ $r['predikat'] == 'Sangat Baik' ? '#fff' : '#000' }}; 
                                         background-color: {{ $r['predikat'] == 'Sangat Baik' ? '#28A745' : ($r['predikat'] == 'Baik' ? '#FFC107' : ($r['predikat'] == 'Cukup' ? '#FD7E14' : '#DC3545')) }}">
                                {{ $r['predikat'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold">{{ $r['persentase_kenaikan'] }}%</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-green-600">
                            Rp {{ number_format($r['gaji_baru'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            + Rp {{ number_format($r['nominal_kenaikan'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tombol Approve -->
        <div class="flex justify-end">
            <form action="{{ route('admin.gaji.approve-kenaikan') }}" method="POST">
                @csrf
                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                @foreach($results as $r)
                    <input type="hidden" name="pegawai_ids[]" value="{{ $r['pegawai_id'] }}">
                @endforeach
                <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md"
                        onclick="return confirm('Approve semua kenaikan gaji?')">
                    <i class="fas fa-check-double mr-2"></i> Approve Semua Kenaikan
                </button>
            </form>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                Belum ada data kenaikan gaji untuk periode ini. Pastikan penilaian sudah final.
            </p>
        </div>
    @endif
</div>
@endsection