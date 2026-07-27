@extends('layouts.pegawai')

@section('title', 'Hasil Self Assessment')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hasil Self Assessment</h1>
            <p class="text-sm text-gray-500">
                Periode: {{ $periodeAktif->nama ?? '-' }} | Pegawai: {{ $pegawai->nama ?? '-' }}
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('pegawai.self-assessment.edit') }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('pegawai.self-assessment.print') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                <i class="fas fa-print mr-2"></i> Cetak
            </a>
            <a href="{{ route('pegawai.dashboard') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if($penilaian)
        <!-- Info -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800">
                <i class="fas fa-check-circle mr-2"></i>
                Self assessment sudah disimpan pada 
                @if($penilaian->submitted_at)
                    {{ \Carbon\Carbon::parse($penilaian->submitted_at)->format('d M Y H:i') }}
                @else
                    {{ $penilaian->created_at ? \Carbon\Carbon::parse($penilaian->created_at)->format('d M Y H:i') : '-' }}
                @endif
            </p>
        </div>

        <!-- Ringkasan Nilai -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Ringkasan Nilai</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- KPI - Nilai langsung 0-100 -->
                <div class="p-4 bg-blue-50 rounded-lg text-center">
                    <p class="text-sm text-gray-600">Nilai KPI</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $penilaian->nilai_kpi }}</p>
                    <p class="text-xs text-gray-500">Skala 0-100 × {{ $bobot->bobot_kpi ?? 70 }}%</p>
                </div>
                
                <!-- Kompetensi - Tampilkan nilai asli 1-5 dan hasil konversi -->
                <div class="p-4 bg-green-50 rounded-lg text-center">
                    <p class="text-sm text-gray-600">Nilai Kompetensi</p>
                    <p class="text-2xl font-bold text-green-600">
                        @php
                            // Ambil nilai asli dari detail (1-5)
                            $nilaiKompetensiAsli = isset($detail['kompetensi']) ? array_sum($detail['kompetensi']) / count($detail['kompetensi']) : 0;
                        @endphp
                        {{ number_format($nilaiKompetensiAsli, 2) }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Skala 1-5 × {{ $bobot->bobot_kompetensi ?? 15 }}%
                        <br>
                        <span class="text-green-700">(Konversi: {{ $penilaian->nilai_kompetensi }})</span>
                    </p>
                </div>
                
                <!-- Core Values - Tampilkan nilai asli 1-5 dan hasil konversi -->
                <div class="p-4 bg-yellow-50 rounded-lg text-center">
                    <p class="text-sm text-gray-600">Nilai Core Values</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        @php
                            $nilaiCoreAsli = isset($detail['core_values']) ? array_sum($detail['core_values']) / count($detail['core_values']) : 0;
                        @endphp
                        {{ number_format($nilaiCoreAsli, 2) }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Skala 1-5 × {{ $bobot->bobot_core_values ?? 15 }}%
                        <br>
                        <span class="text-yellow-700">(Konversi: {{ $penilaian->nilai_core_values }})</span>
                    </p>
                </div>
                
                <!-- Nilai Total -->
                <div class="p-4 bg-purple-50 rounded-lg text-center border-2 border-purple-300">
                    <p class="text-sm text-gray-600">NILAI TOTAL</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $penilaian->nilai_total }}</p>
                    <p class="text-xs text-gray-500">Sudah termasuk konversi skala</p>
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
                <p class="text-sm text-gray-500 mb-2">Skala 0-100</p>
                @if(isset($detail['kpi']) && count($detail['kpi']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (0-100)</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detail['kpi'] as $index => $nilai)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $indikator['kpi'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-4 py-2 text-sm font-bold">{{ $nilai }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $detail['komentar_kpi'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="2" class="px-4 py-2 text-right font-medium">Rata-rata:</td>
                                    <td class="px-4 py-2 font-bold text-blue-600">
                                        @php
                                            $avgKPI = array_sum($detail['kpi']) / count($detail['kpi']);
                                        @endphp
                                        {{ number_format($avgKPI, 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
                <p class="text-sm text-gray-500 mb-2">Skala 1-5 (Konversi ke 0-100 untuk perhitungan)</p>
                @if(isset($detail['kompetensi']) && count($detail['kompetensi']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (1-5)</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detail['kompetensi'] as $index => $nilai)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $indikator['kompetensi'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-4 py-2 text-sm font-bold">{{ $nilai }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $detail['komentar_kompetensi'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="2" class="px-4 py-2 text-right font-medium">Rata-rata (1-5):</td>
                                    <td class="px-4 py-2 font-bold text-green-600">
                                        @php
                                            $avgKompetensiAsli = array_sum($detail['kompetensi']) / count($detail['kompetensi']);
                                        @endphp
                                        {{ number_format($avgKompetensiAsli, 2) }}
                                        <span class="text-xs text-gray-500">(Konversi: {{ number_format(($avgKompetensiAsli / 5) * 100, 2) }})</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
                <p class="text-sm text-gray-500 mb-2">Skala 1-5 (Konversi ke 0-100 untuk perhitungan)</p>
                @if(isset($detail['core_values']) && count($detail['core_values']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Indikator</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nilai (1-5)</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detail['core_values'] as $index => $nilai)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $indikator['core_values'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-4 py-2 text-sm font-bold">{{ $nilai }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $detail['komentar_core_values'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="2" class="px-4 py-2 text-right font-medium">Rata-rata (1-5):</td>
                                    <td class="px-4 py-2 font-bold text-yellow-600">
                                        @php
                                            $avgCoreAsli = array_sum($detail['core_values']) / count($detail['core_values']);
                                        @endphp
                                        {{ number_format($avgCoreAsli, 2) }}
                                        <span class="text-xs text-gray-500">(Konversi: {{ number_format(($avgCoreAsli / 5) * 100, 2) }})</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Tidak ada data Core Values</p>
                @endif
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                {{ $message ?? 'Belum ada data self assessment' }}
            </p>
        </div>
    @endif
</div>
@endsection