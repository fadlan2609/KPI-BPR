@extends('layouts.pegawai')

@section('title', 'Laporan Penilaian')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Penilaian Kinerja</h1>
            <p class="text-sm text-gray-500">
                {{ $pegawai->nama }} ({{ $pegawai->nip }}) | Periode: {{ $hasil->periode->nama }}
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('pegawai.cetak-laporan-pdf', $hasil->id) }}" 
               class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
            <a href="{{ route('pegawai.hasil-penilaian') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-sm text-green-800">
            <i class="fas fa-check-circle mr-2"></i>
            Penilaian final pada {{ $hasil->created_at->format('d M Y H:i') }}
        </p>
    </div>

    <!-- Ringkasan Nilai -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Ringkasan Nilai</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Self Assessment</p>
                <p class="text-2xl font-bold text-blue-600">{{ $hasil->nilai_self ?? 0 }}</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_self ?? 20 }}%</p>
            </div>
            <div class="p-4 bg-green-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Atasan Langsung</p>
                <p class="text-2xl font-bold text-green-600">{{ $hasil->nilai_atasan_langsung ?? 0 }}</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_atasan_langsung ?? 50 }}%</p>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg text-center">
                <p class="text-sm text-gray-600">Atasan Penilai</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $hasil->nilai_atasan_penilai ?? 0 }}</p>
                <p class="text-xs text-gray-500">× {{ $bobot->bobot_atasan_penilai ?? 30 }}%</p>
            </div>
            <div class="p-4 bg-purple-50 rounded-lg text-center border-2 border-purple-300">
                <p class="text-sm text-gray-600">NILAI AKHIR</p>
                <p class="text-2xl font-bold text-purple-700">{{ $hasil->nilai_akhir }}</p>
                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                      style="color: {{ $hasil->predikat->warna_text ?? '#000' }}; 
                             background-color: {{ $hasil->predikat->warna_latar ?? '#ccc' }}">
                    {{ $hasil->predikat->nama ?? '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Detail Penilaian -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Detail Penilaian</h3>

        <!-- ============================================================ -->
        <!-- LEVEL 1: SELF ASSESSMENT (SELALU ADA) -->
        <!-- ============================================================ -->
        <div class="mb-6 border rounded-lg p-4">
            <h4 class="font-bold text-blue-600 mb-2">
                <i class="fas fa-user-check mr-2"></i>
                Self Assessment (Nilai: {{ $penilaian['self']->nilai_total ?? 0 }})
                <span class="text-xs text-gray-500 ml-2">- {{ $pegawai->nama }}</span>
            </h4>
            @if($detailSelf && isset($detailSelf['kpi']))
                <!-- KPI -->
                <div class="mt-2">
                    <p class="text-sm font-semibold text-blue-600">KPI (Bobot: {{ $bobot->bobot_kpi ?? 70 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Target</th>
                                    <th class="px-2 py-1 text-xs">Bobot</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailSelf['kpi'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorKPI[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">{{ $indikatorKPI[$index]->target ?? '-' }} {{ $indikatorKPI[$index]->satuan ?? '' }}</td>
                                    <td class="px-2 py-1 text-center">{{ $indikatorKPI[$index]->bobot ?? 0 }}%</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailSelf['komentar_kpi'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Kompetensi -->
                @if(isset($detailSelf['kompetensi']))
                <div class="mt-3">
                    <p class="text-sm font-semibold text-green-600">Kompetensi (Bobot: {{ $bobot->bobot_kompetensi ?? 15 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Skala</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailSelf['kompetensi'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorKompetensi[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">1-{{ $indikatorKompetensi[$index]->skala_maksimal ?? 5 }}</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailSelf['komentar_kompetensi'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                <!-- Core Values -->
                @if(isset($detailSelf['core_values']))
                <div class="mt-3">
                    <p class="text-sm font-semibold text-yellow-600">Core Values (Bobot: {{ $bobot->bobot_core_values ?? 15 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Skala</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailSelf['core_values'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorCore[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">1-{{ $indikatorCore[$index]->skala_maksimal ?? 5 }}</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailSelf['komentar_core_values'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @else
                <p class="text-sm text-gray-500">Belum ada data Self Assessment</p>
            @endif
        </div>

        <!-- ============================================================ -->
        <!-- LEVEL 2: ATASAN LANGSUNG (SELALU ADA JIKA PUNYA ATASAN) -->
        <!-- ============================================================ -->
        @if(isset($penilaian['atasan_langsung']))
        <div class="mb-6 border rounded-lg p-4">
            <h4 class="font-bold text-green-600 mb-2">
                <i class="fas fa-user-tie mr-2"></i>
                Atasan Langsung (Nilai: {{ $penilaian['atasan_langsung']->nilai_total ?? 0 }})
                <span class="text-xs text-gray-500 ml-2">- {{ $penilaian['atasan_langsung']->penilai->nama ?? '' }}</span>
            </h4>
            @if($detailAtasan && isset($detailAtasan['kpi']))
                <!-- KPI -->
                <div class="mt-2">
                    <p class="text-sm font-semibold text-blue-600">KPI (Bobot: {{ $bobot->bobot_kpi ?? 70 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Target</th>
                                    <th class="px-2 py-1 text-xs">Bobot</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailAtasan['kpi'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorKPI[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">{{ $indikatorKPI[$index]->target ?? '-' }} {{ $indikatorKPI[$index]->satuan ?? '' }}</td>
                                    <td class="px-2 py-1 text-center">{{ $indikatorKPI[$index]->bobot ?? 0 }}%</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailAtasan['komentar_kpi'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Kompetensi -->
                @if(isset($detailAtasan['kompetensi']))
                <div class="mt-3">
                    <p class="text-sm font-semibold text-green-600">Kompetensi (Bobot: {{ $bobot->bobot_kompetensi ?? 15 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Skala</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailAtasan['kompetensi'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorKompetensi[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">1-{{ $indikatorKompetensi[$index]->skala_maksimal ?? 5 }}</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailAtasan['komentar_kompetensi'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                <!-- Core Values -->
                @if(isset($detailAtasan['core_values']))
                <div class="mt-3">
                    <p class="text-sm font-semibold text-yellow-600">Core Values (Bobot: {{ $bobot->bobot_core_values ?? 15 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Skala</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailAtasan['core_values'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorCore[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">1-{{ $indikatorCore[$index]->skala_maksimal ?? 5 }}</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailAtasan['komentar_core_values'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @else
                <p class="text-sm text-gray-500">Belum ada penilaian dari Atasan Langsung</p>
            @endif
        </div>
        @endif

        <!-- ============================================================ -->
        <!-- LEVEL 3: ATASAN PENILAI (HANYA JIKA ADA) -->
        <!-- ============================================================ -->
        @php
            $hasAtasanPenilai = isset($penilaian['atasan_penilai']);
        @endphp

        @if($hasAtasanPenilai)
        <div class="border rounded-lg p-4">
            <h4 class="font-bold text-yellow-600 mb-2">
                <i class="fas fa-user-tie mr-2"></i>
                Atasan Penilai (Nilai: {{ $penilaian['atasan_penilai']->nilai_total ?? 0 }})
                <span class="text-xs text-gray-500 ml-2">- {{ $penilaian['atasan_penilai']->penilai->nama ?? '' }}</span>
            </h4>
            @if($detailPenilai && isset($detailPenilai['kpi']))
                <!-- KPI -->
                <div class="mt-2">
                    <p class="text-sm font-semibold text-blue-600">KPI (Bobot: {{ $bobot->bobot_kpi ?? 70 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Target</th>
                                    <th class="px-2 py-1 text-xs">Bobot</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailPenilai['kpi'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorKPI[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">{{ $indikatorKPI[$index]->target ?? '-' }} {{ $indikatorKPI[$index]->satuan ?? '' }}</td>
                                    <td class="px-2 py-1 text-center">{{ $indikatorKPI[$index]->bobot ?? 0 }}%</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailPenilai['komentar_kpi'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Kompetensi -->
                @if(isset($detailPenilai['kompetensi']))
                <div class="mt-3">
                    <p class="text-sm font-semibold text-green-600">Kompetensi (Bobot: {{ $bobot->bobot_kompetensi ?? 15 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Skala</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailPenilai['kompetensi'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorKompetensi[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">1-{{ $indikatorKompetensi[$index]->skala_maksimal ?? 5 }}</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailPenilai['komentar_kompetensi'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                <!-- Core Values -->
                @if(isset($detailPenilai['core_values']))
                <div class="mt-3">
                    <p class="text-sm font-semibold text-yellow-600">Core Values (Bobot: {{ $bobot->bobot_core_values ?? 15 }}%)</p>
                    <div class="overflow-x-auto mt-1">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-xs">No</th>
                                    <th class="px-2 py-1 text-xs text-left">Indikator</th>
                                    <th class="px-2 py-1 text-xs">Skala</th>
                                    <th class="px-2 py-1 text-xs">Nilai</th>
                                    <th class="px-2 py-1 text-xs text-left">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailPenilai['core_values'] as $index => $nilai)
                                <tr>
                                    <td class="px-2 py-1 text-center">{{ $index + 1 }}</td>
                                    <td class="px-2 py-1">{{ $indikatorCore[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                                    <td class="px-2 py-1 text-center">1-{{ $indikatorCore[$index]->skala_maksimal ?? 5 }}</td>
                                    <td class="px-2 py-1 text-center font-bold">{{ $nilai }}</td>
                                    <td class="px-2 py-1">{{ $detailPenilai['komentar_core_values'][$index] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @else
                <p class="text-sm text-gray-500">Belum ada penilaian dari Atasan Penilai</p>
            @endif
        </div>
        @else
        <!-- Jika tidak ada atasan penilai, tampilkan pesan -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
            <p class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-2"></i>
                Tidak ada Atasan Penilai karena atasan langsung tidak memiliki atasan lagi.
            </p>
        </div>
        @endif
    </div>
</div>
@endsection