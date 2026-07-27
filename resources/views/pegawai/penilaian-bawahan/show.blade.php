@extends('layouts.pegawai')

@section('title', 'Detail Penilaian Bawahan')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Penilaian Bawahan</h1>
            <p class="text-sm text-gray-500">
                Pegawai: {{ $pegawai->nama }} ({{ $pegawai->nip }}) | 
                Jabatan: {{ $pegawai->jabatan->nama ?? '-' }}
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('pegawai.penilaian-bawahan.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-sm text-green-800">
            <i class="fas fa-check-circle mr-2"></i>
            Penilaian sudah disimpan pada {{ \Carbon\Carbon::parse($penilaian->submitted_at)->format('d M Y H:i') }}
        </p>
    </div>

    <!-- Ringkasan Nilai -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Ringkasan Nilai</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Nilai KPI</p>
                <p class="text-2xl font-bold text-blue-600">{{ $penilaian->nilai_kpi }}</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_kpi ?? 70 }}%</p>
            </div>
            <div class="p-4 bg-green-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Nilai Kompetensi</p>
                <p class="text-2xl font-bold text-green-600">{{ $penilaian->nilai_kompetensi }}</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_kompetensi ?? 15 }}%</p>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Nilai Core Values</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $penilaian->nilai_core_values }}</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_core_values ?? 15 }}%</p>
            </div>
            <div class="p-4 bg-purple-50 rounded-lg text-center border-2 border-purple-300">
                <p class="text-sm text-gray-600">NILAI TOTAL</p>
                <p class="text-2xl font-bold text-purple-700">{{ $penilaian->nilai_total }}</p>
            </div>
        </div>
    </div>

    <!-- Detail Penilaian -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Detail Penilaian</h3>

        <!-- KPI -->
        <div class="mb-6">
            <h4 class="font-medium text-blue-600 mb-2">
                <i class="fas fa-chart-line mr-2"></i>
                KPI (Bobot: {{ $bobot->bobot_kpi ?? 70 }}%)
            </h4>
            @if(isset($detail['kpi']) && count($detail['kpi']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Bobot</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail['kpi'] as $index => $nilai)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm">{{ $indikator['kpi'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                <td class="px-4 py-2 text-sm">{{ $indikator['kpi'][$index]->bobot ?? 0 }}%</td>
                                <td class="px-4 py-2 text-sm font-bold">{{ $nilai }}</td>
                                <td class="px-4 py-2 text-sm">{{ $detail['komentar_kpi'][$index] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">Tidak ada data KPI</p>
            @endif
        </div>

        <!-- Kompetensi -->
        <div class="mb-6">
            <h4 class="font-medium text-green-600 mb-2">
                <i class="fas fa-brain mr-2"></i>
                Kompetensi (Bobot: {{ $bobot->bobot_kompetensi ?? 15 }}%)
            </h4>
            @if(isset($detail['kompetensi']) && count($detail['kompetensi']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Skala</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail['kompetensi'] as $index => $nilai)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm">{{ $indikator['kompetensi'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                <td class="px-4 py-2 text-sm">1-{{ $indikator['kompetensi'][$index]->skala_maksimal ?? 5 }}</td>
                                <td class="px-4 py-2 text-sm font-bold">{{ $nilai }}</td>
                                <td class="px-4 py-2 text-sm">{{ $detail['komentar_kompetensi'][$index] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">Tidak ada data Kompetensi</p>
            @endif
        </div>

        <!-- Core Values -->
        <div>
            <h4 class="font-medium text-yellow-600 mb-2">
                <i class="fas fa-heart mr-2"></i>
                Core Values (Bobot: {{ $bobot->bobot_core_values ?? 15 }}%)
            </h4>
            @if(isset($detail['core_values']) && count($detail['core_values']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Skala</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail['core_values'] as $index => $nilai)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 text-sm">{{ $indikator['core_values'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                <td class="px-4 py-2 text-sm">1-{{ $indikator['core_values'][$index]->skala_maksimal ?? 5 }}</td>
                                <td class="px-4 py-2 text-sm font-bold">{{ $nilai }}</td>
                                <td class="px-4 py-2 text-sm">{{ $detail['komentar_core_values'][$index] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">Tidak ada data Core Values</p>
            @endif
        </div>
    </div>

    <div class="mt-4 flex justify-end">
        <a href="{{ route('pegawai.penilaian-bawahan.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>
</div>
@endsection