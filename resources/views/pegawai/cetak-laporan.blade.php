@extends('layouts.pegawai')

@section('title', 'Laporan Penilaian')

@push('styles')
<style>
    .laporan-wrap { max-width: 1200px; margin: 0 auto; padding: 20px 16px; }
    .laporan-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 2px solid #e5e7eb; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .laporan-header h1 { font-size: 22px; font-weight: 700; color: #111827; margin: 0; }
    .laporan-header p { font-size: 13px; color: #6b7280; margin: 4px 0 0 0; }
    .laporan-btn { display: inline-flex; align-items: center; padding: 9px 18px; font-size: 13px; font-weight: 600; border-radius: 6px; text-decoration: none; transition: all 0.15s; }
    .laporan-btn-back { background: #fff; color: #374151; border: 1px solid #d1d5db; }
    .laporan-btn-back:hover { background: #f9fafb; }
    .laporan-btn-pdf { background: #b91c1c; color: #fff; border: none; cursor: pointer; }
    .laporan-btn-pdf:hover { background: #991b1b; }
    .laporan-btn-print { background: #1d4ed8; color: #fff; border: none; cursor: pointer; }
    .laporan-btn-print:hover { background: #1e40af; }

    .laporan-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 16px; }

    .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
    .summary-item { border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; text-align: center; background: #f9f9f9; }
    .summary-item.self { background: #eff6ff; border-color: #bfdbfe; }
    .summary-item.atasan { background: #f0fdf4; border-color: #bbf7d0; }
    .summary-item.penilai { background: #fef3c7; border-color: #fde68a; }
    .summary-item.akhir { background: #f3e8ff; border-color: #ddd6fe; }
    .summary-label { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
    .summary-value { font-size: 24px; font-weight: 700; color: #111827; margin: 4px 0; }
    .summary-badge { display: inline-block; padding: 3px 12px; border-radius: 12px; font-size: 11px; font-weight: 700; }

    .section-title { font-size: 14px; font-weight: 700; color: #1F4E79; padding-bottom: 6px; border-bottom: 2px solid #1F4E79; margin: 20px 0 10px 0; }

    .gabung-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .gabung-table th, .gabung-table td { border: 1px solid #d1d5db; padding: 8px 10px; vertical-align: middle; }
    .gabung-table thead th { background: #1F4E79; color: #fff; font-weight: 700; text-align: center; font-size: 11px; }
    .gabung-table thead tr.subheader th { background: #374151; font-size: 11px; }
    .gabung-table td.center { text-align: center; }
    .gabung-table td.self { background: #eff6ff; }
    .gabung-table td.atasan { background: #f0fdf4; }
    .gabung-table td.penilai { background: #fef3c7; }
    .gabung-table td.rata { background: #f3e8ff; font-weight: 700; }
    .gabung-table tfoot td { background: #e5e7eb; font-weight: 700; text-align: center; }

    .komentar-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px 16px; margin-bottom: 12px; background: #fafafa; }
    .komentar-box .komentar-nama { font-weight: 700; color: #1F4E79; font-size: 13px; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dotted #d1d5db; }
    .komentar-box .komentar-level { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; margin-left: 6px; }
    .level-self { background: #dbeafe; color: #1d4ed8; }
    .level-atasan { background: #dcfce7; color: #15803d; }
    .level-penilai { background: #fef3c7; color: #b45309; }
    .komentar-subsection { margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dotted #e5e7eb; }
    .komentar-subsection:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .komentar-subsection strong { display: block; font-size: 12px; color: #1F4E79; margin-bottom: 4px; }
    .komentar-item { padding-left: 10px; font-size: 12px; line-height: 1.5; margin-bottom: 3px; }
    .komentar-indikator { font-weight: 600; color: #374151; }
    .komentar-isi { font-style: italic; color: #6b7280; }

    @media print {
        .no-print { display: none !important; }
        .laporan-wrap { max-width: 100%; padding: 0; }
    }
</style>
@endpush

@section('content')
<div class="laporan-wrap">

    {{-- ==================== HEADER ==================== --}}
    <div class="laporan-header">
        <div>
            <h1>Laporan Penilaian Kinerja</h1>
            <p>
                {{ $pegawai->nama }} ({{ $pegawai->nip }}) |
                Periode: {{ $periode->nama }} |
                Jabatan: {{ $pegawai->jabatan->nama ?? '-' }}
            </p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap: wrap;" class="no-print">
            <a href="{{ route('pegawai.cetak-laporan-pdf', $hasil->id) }}" class="laporan-btn laporan-btn-pdf">
                <i class="fas fa-file-pdf" style="margin-right:6px;"></i> Download PDF
            </a>
            <button onclick="window.print()" class="laporan-btn laporan-btn-print">
                <i class="fas fa-print" style="margin-right:6px;"></i> Print
            </button>
            <a href="{{ route('pegawai.hasil-penilaian') }}" class="laporan-btn laporan-btn-back">
                <i class="fas fa-arrow-left" style="margin-right:6px;"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ==================== INFO STATUS ==================== --}}
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; color: #166534; font-size: 13px;">
        <i class="fas fa-check-circle" style="margin-right:6px;"></i>
        Penilaian final pada {{ $hasil->created_at->format('d M Y H:i') }}
    </div>

    {{-- ==================== RINGKASAN ==================== --}}
    <div class="summary-grid">
        <div class="summary-item self">
            <div class="summary-label">Self Assessment</div>
            <div class="summary-value" style="color: #1d4ed8;">{{ $hasil->nilai_self ?? 0 }}</div>
        </div>
        <div class="summary-item atasan">
            <div class="summary-label">Atasan Langsung</div>
            <div class="summary-value" style="color: #15803d;">{{ $hasil->nilai_atasan_langsung ?? 0 }}</div>
        </div>
        <div class="summary-item penilai">
            <div class="summary-label">Atasan Penilai</div>
            <div class="summary-value" style="color: #b45309;">{{ $hasil->nilai_atasan_penilai ?? 0 }}</div>
        </div>
        <div class="summary-item akhir">
            <div class="summary-label">NILAI AKHIR</div>
            <div class="summary-value" style="color: #6d28d9;">{{ $hasil->nilai_akhir }}</div>
            <span class="summary-badge"
                  style="color: {{ $hasil->predikat->warna_text ?? '#000' }};
                         background-color: {{ $hasil->predikat->warna_latar ?? '#ccc' }};">
                {{ $hasil->predikat->nama ?? '-' }}
            </span>
        </div>
    </div>

    @php
        // Parse detail per level
        $detailSelf    = isset($penilaian['self'])            ? json_decode($penilaian['self']->detail_indikator, true)            : null;
        $detailAtasan  = isset($penilaian['atasan_langsung']) ? json_decode($penilaian['atasan_langsung']->detail_indikator, true)  : null;
        $detailPenilai = isset($penilaian['atasan_penilai'])  ? json_decode($penilaian['atasan_penilai']->detail_indikator, true)   : null;

        $hasAtasanPenilai = isset($penilaian['atasan_penilai']);

        // Helper
        if (!function_exists('getNilaiFromDetail')) {
            function getNilaiFromDetail($detail, $kategori, $index) {
                if (!is_array($detail) || !isset($detail[$kategori]) || !is_array($detail[$kategori])) return '-';
                if (!isset($detail[$kategori][$index])) return '-';
                $val = $detail[$kategori][$index];
                return ($val === null || $val === '') ? '-' : $val;
            }
        }
        if (!function_exists('hitungRataLocal')) {
            function hitungRataLocal($self, $atasan, $penilai) {
                $arr = array_filter([$self, $atasan, $penilai], 'is_numeric');
                return count($arr) > 0 ? round(array_sum($arr) / count($arr), 2) : '-';
            }
        }
        if (!function_exists('getKomentarFromDetail')) {
            function getKomentarFromDetail($detail, $kategori, $index) {
                if (!is_array($detail)) return null;
                $key = 'komentar_' . $kategori;
                if (!isset($detail[$key])) return null;
                if (is_array($detail[$key])) {
                    $val = $detail[$key][$index] ?? null;
                    return !empty(trim($val ?? '')) ? trim($val) : null;
                }
                if (is_string($detail[$key]) && $index === 0) {
                    return !empty(trim($detail[$key])) ? trim($detail[$key]) : null;
                }
                return null;
            }
        }
    @endphp

    {{-- ==================== A. KPI ==================== --}}
    <div class="section-title">A. INDIKATOR KINERJA PEGAWAI (KPI)</div>
    <div class="laporan-card">
        <div style="overflow-x:auto;">
            <table class="gabung-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:4%;">No</th>
                        <th rowspan="2" style="width:28%;">Indikator</th>
                        <th rowspan="2" style="width:8%;">Target</th>
                        <th rowspan="2" style="width:6%;">Bobot</th>
                        <th colspan="{{ $hasAtasanPenilai ? 3 : 2 }}">Nilai</th>
                        <th rowspan="2" style="width:10%;">Rata-rata</th>
                    </tr>
                    <tr class="subheader">
                        <th>Self</th>
                        <th>Atasan Lgsg</th>
                        @if($hasAtasanPenilai)
                        <th>Atasan Nilai</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($indikatorKPI as $i => $ind)
                        @php
                            $nSelf    = getNilaiFromDetail($detailSelf,    'kpi', $i);
                            $nAtasan  = getNilaiFromDetail($detailAtasan,  'kpi', $i);
                            $nPenilai = $hasAtasanPenilai ? getNilaiFromDetail($detailPenilai, 'kpi', $i) : null;
                            $rata     = hitungRataLocal($nSelf, $nAtasan, $hasAtasanPenilai ? $nPenilai : null);
                        @endphp
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $ind->indikator ?? $ind->nama ?? '-' }}</td>
                            <td class="center">{{ $ind->target ?? '-' }} {{ $ind->satuan ?? '' }}</td>
                            <td class="center">{{ $ind->bobot ?? 0 }}%</td>
                            <td class="center self">{{ $nSelf }}</td>
                            <td class="center atasan">{{ $nAtasan }}</td>
                            @if($hasAtasanPenilai)
                            <td class="center penilai">{{ $nPenilai }}</td>
                            @endif
                            <td class="center rata">{{ $rata }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $hasAtasanPenilai ? 8 : 7 }}" class="center" style="color:#9ca3af; font-style:italic;">Belum ada indikator KPI</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;">RATA-RATA KPI</td>
                        <td>{{ $penilaian['self']->nilai_kpi ?? 0 }}</td>
                        <td>{{ $penilaian['atasan_langsung']->nilai_kpi ?? 0 }}</td>
                        @if($hasAtasanPenilai)
                        <td>{{ $penilaian['atasan_penilai']->nilai_kpi ?? 0 }}</td>
                        @endif
                        <td class="rata">
                            @php
                                $vals = array_filter([
                                    $penilaian['self']->nilai_kpi ?? null,
                                    $penilaian['atasan_langsung']->nilai_kpi ?? null,
                                    $hasAtasanPenilai ? ($penilaian['atasan_penilai']->nilai_kpi ?? null) : null,
                                ], 'is_numeric');
                                echo count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : '-';
                            @endphp
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ==================== B. KOMPETENSI ==================== --}}
    <div class="section-title">B. KOMPETENSI</div>
    <div class="laporan-card">
        <div style="overflow-x:auto;">
            <table class="gabung-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:4%;">No</th>
                        <th rowspan="2" style="width:28%;">Kompetensi</th>
                        <th rowspan="2" style="width:8%;">Skala</th>
                        <th rowspan="2" style="width:6%;">Bobot</th>
                        <th colspan="{{ $hasAtasanPenilai ? 3 : 2 }}">Nilai</th>
                        <th rowspan="2" style="width:10%;">Rata-rata</th>
                    </tr>
                    <tr class="subheader">
                        <th>Self</th>
                        <th>Atasan Lgsg</th>
                        @if($hasAtasanPenilai)
                        <th>Atasan Nilai</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($indikatorKompetensi as $i => $ind)
                        @php
                            $nSelf    = getNilaiFromDetail($detailSelf,    'kompetensi', $i);
                            $nAtasan  = getNilaiFromDetail($detailAtasan,  'kompetensi', $i);
                            $nPenilai = $hasAtasanPenilai ? getNilaiFromDetail($detailPenilai, 'kompetensi', $i) : null;
                            $rata     = hitungRataLocal($nSelf, $nAtasan, $hasAtasanPenilai ? $nPenilai : null);
                        @endphp
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $ind->indikator ?? $ind->nama ?? '-' }}</td>
                            <td class="center">1-{{ $ind->skala_maksimal ?? 5 }}</td>
                            <td class="center">{{ $ind->bobot ?? 0 }}%</td>
                            <td class="center self">{{ $nSelf }}</td>
                            <td class="center atasan">{{ $nAtasan }}</td>
                            @if($hasAtasanPenilai)
                            <td class="center penilai">{{ $nPenilai }}</td>
                            @endif
                            <td class="center rata">{{ $rata }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $hasAtasanPenilai ? 8 : 7 }}" class="center" style="color:#9ca3af; font-style:italic;">Belum ada indikator Kompetensi</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;">RATA-RATA KOMPETENSI</td>
                        <td>{{ $penilaian['self']->nilai_kompetensi ?? 0 }}</td>
                        <td>{{ $penilaian['atasan_langsung']->nilai_kompetensi ?? 0 }}</td>
                        @if($hasAtasanPenilai)
                        <td>{{ $penilaian['atasan_penilai']->nilai_kompetensi ?? 0 }}</td>
                        @endif
                        <td class="rata">
                            @php
                                $vals = array_filter([
                                    $penilaian['self']->nilai_kompetensi ?? null,
                                    $penilaian['atasan_langsung']->nilai_kompetensi ?? null,
                                    $hasAtasanPenilai ? ($penilaian['atasan_penilai']->nilai_kompetensi ?? null) : null,
                                ], 'is_numeric');
                                echo count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : '-';
                            @endphp
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ==================== C. CORE VALUES ==================== --}}
    <div class="section-title">C. CORE VALUES</div>
    <div class="laporan-card">
        <div style="overflow-x:auto;">
            <table class="gabung-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:4%;">No</th>
                        <th rowspan="2" style="width:28%;">Core Value</th>
                        <th rowspan="2" style="width:8%;">Skala</th>
                        <th rowspan="2" style="width:6%;">Bobot</th>
                        <th colspan="{{ $hasAtasanPenilai ? 3 : 2 }}">Nilai</th>
                        <th rowspan="2" style="width:10%;">Rata-rata</th>
                    </tr>
                    <tr class="subheader">
                        <th>Self</th>
                        <th>Atasan Lgsg</th>
                        @if($hasAtasanPenilai)
                        <th>Atasan Nilai</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($indikatorCore as $i => $ind)
                        @php
                            $nSelf    = getNilaiFromDetail($detailSelf,    'core_values', $i);
                            $nAtasan  = getNilaiFromDetail($detailAtasan,  'core_values', $i);
                            $nPenilai = $hasAtasanPenilai ? getNilaiFromDetail($detailPenilai, 'core_values', $i) : null;
                            $rata     = hitungRataLocal($nSelf, $nAtasan, $hasAtasanPenilai ? $nPenilai : null);
                        @endphp
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $ind->indikator ?? $ind->nama ?? '-' }}</td>
                            <td class="center">1-{{ $ind->skala_maksimal ?? 5 }}</td>
                            <td class="center">{{ $ind->bobot ?? 0 }}%</td>
                            <td class="center self">{{ $nSelf }}</td>
                            <td class="center atasan">{{ $nAtasan }}</td>
                            @if($hasAtasanPenilai)
                            <td class="center penilai">{{ $nPenilai }}</td>
                            @endif
                            <td class="center rata">{{ $rata }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $hasAtasanPenilai ? 8 : 7 }}" class="center" style="color:#9ca3af; font-style:italic;">Belum ada indikator Core Values</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;">RATA-RATA CORE VALUES</td>
                        <td>{{ $penilaian['self']->nilai_core_values ?? 0 }}</td>
                        <td>{{ $penilaian['atasan_langsung']->nilai_core_values ?? 0 }}</td>
                        @if($hasAtasanPenilai)
                        <td>{{ $penilaian['atasan_penilai']->nilai_core_values ?? 0 }}</td>
                        @endif
                        <td class="rata">
                            @php
                                $vals = array_filter([
                                    $penilaian['self']->nilai_core_values ?? null,
                                    $penilaian['atasan_langsung']->nilai_core_values ?? null,
                                    $hasAtasanPenilai ? ($penilaian['atasan_penilai']->nilai_core_values ?? null) : null,
                                ], 'is_numeric');
                                echo count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : '-';
                            @endphp
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ==================== D. REKAPITULASI ==================== --}}
    <div class="section-title">D. REKAPITULASI NILAI</div>
    <div class="laporan-card">
        <div style="overflow-x:auto;">
            <table class="gabung-table">
                <thead>
                    <tr>
                        <th style="width:5%;">No</th>
                        <th style="width:35%;">Komponen</th>
                        <th style="width:15%;">Self</th>
                        <th style="width:15%;">Atasan Langsung</th>
                        @if($hasAtasanPenilai)
                        <th style="width:15%;">Atasan Penilai</th>
                        @endif
                        <th style="width:15%;">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td>KPI</td>
                        <td class="center self">{{ $penilaian['self']->nilai_kpi ?? 0 }}</td>
                        <td class="center atasan">{{ $penilaian['atasan_langsung']->nilai_kpi ?? 0 }}</td>
                        @if($hasAtasanPenilai)
                        <td class="center penilai">{{ $penilaian['atasan_penilai']->nilai_kpi ?? 0 }}</td>
                        @endif
                        <td class="center rata">
                            @php
                                $v = array_filter([
                                    $penilaian['self']->nilai_kpi ?? null,
                                    $penilaian['atasan_langsung']->nilai_kpi ?? null,
                                    $hasAtasanPenilai ? ($penilaian['atasan_penilai']->nilai_kpi ?? null) : null,
                                ], 'is_numeric');
                                echo count($v) > 0 ? round(array_sum($v) / count($v), 2) : '-';
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <td class="center">2</td>
                        <td>Kompetensi</td>
                        <td class="center self">{{ $penilaian['self']->nilai_kompetensi ?? 0 }}</td>
                        <td class="center atasan">{{ $penilaian['atasan_langsung']->nilai_kompetensi ?? 0 }}</td>
                        @if($hasAtasanPenilai)
                        <td class="center penilai">{{ $penilaian['atasan_penilai']->nilai_kompetensi ?? 0 }}</td>
                        @endif
                        <td class="center rata">
                            @php
                                $v = array_filter([
                                    $penilaian['self']->nilai_kompetensi ?? null,
                                    $penilaian['atasan_langsung']->nilai_kompetensi ?? null,
                                    $hasAtasanPenilai ? ($penilaian['atasan_penilai']->nilai_kompetensi ?? null) : null,
                                ], 'is_numeric');
                                echo count($v) > 0 ? round(array_sum($v) / count($v), 2) : '-';
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <td class="center">3</td>
                        <td>Core Values</td>
                        <td class="center self">{{ $penilaian['self']->nilai_core_values ?? 0 }}</td>
                        <td class="center atasan">{{ $penilaian['atasan_langsung']->nilai_core_values ?? 0 }}</td>
                        @if($hasAtasanPenilai)
                        <td class="center penilai">{{ $penilaian['atasan_penilai']->nilai_core_values ?? 0 }}</td>
                        @endif
                        <td class="center rata">
                            @php
                                $v = array_filter([
                                    $penilaian['self']->nilai_core_values ?? null,
                                    $penilaian['atasan_langsung']->nilai_core_values ?? null,
                                    $hasAtasanPenilai ? ($penilaian['atasan_penilai']->nilai_core_values ?? null) : null,
                                ], 'is_numeric');
                                echo count($v) > 0 ? round(array_sum($v) / count($v), 2) : '-';
                            @endphp
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:right;">NILAI AKHIR</td>
                        <td>{{ $hasil->nilai_self ?? 0 }}</td>
                        <td>{{ $hasil->nilai_atasan_langsung ?? 0 }}</td>
                        @if($hasAtasanPenilai)
                        <td>{{ $hasil->nilai_atasan_penilai ?? 0 }}</td>
                        @endif
                        <td style="font-size:14px; color:#1F4E79;">{{ $hasil->nilai_akhir }}</td>
                    </tr>
                    <tr>
                        <td colspan="{{ $hasAtasanPenilai ? 6 : 5 }}" class="center">
                            PREDIKAT:
                            <span class="summary-badge"
                                  style="color: {{ $hasil->predikat->warna_text ?? '#000' }};
                                         background-color: {{ $hasil->predikat->warna_latar ?? '#ccc' }};">
                                {{ $hasil->predikat->nama ?? '-' }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ==================== E. KOMENTAR ==================== --}}
    @php
        $buildKomentar = function($detail) use ($indikatorKPI, $indikatorKompetensi, $indikatorCore) {
            $result = ['kpi' => [], 'kompetensi' => [], 'core_values' => []];
            if (!is_array($detail)) return $result;

            foreach ($indikatorKPI as $i => $ind) {
                $kom = getKomentarFromDetail($detail, 'kpi', $i);
                if ($kom) $result['kpi'][] = ['no' => $i + 1, 'nama' => $ind->indikator ?? '-', 'komentar' => $kom];
            }
            foreach ($indikatorKompetensi as $i => $ind) {
                $kom = getKomentarFromDetail($detail, 'kompetensi', $i);
                if ($kom) $result['kompetensi'][] = ['no' => $i + 1, 'nama' => $ind->indikator ?? '-', 'komentar' => $kom];
            }
            foreach ($indikatorCore as $i => $ind) {
                $kom = getKomentarFromDetail($detail, 'core_values', $i);
                if ($kom) $result['core_values'][] = ['no' => $i + 1, 'nama' => $ind->indikator ?? '-', 'komentar' => $kom];
            }
            return $result;
        };

        $komentarSelf    = $buildKomentar($detailSelf);
        $komentarAtasan  = $buildKomentar($detailAtasan);
        $komentarPenilai = $hasAtasanPenilai ? $buildKomentar($detailPenilai) : ['kpi'=>[], 'kompetensi'=>[], 'core_values'=>[]];

        $cekAda = function($k) { return !empty($k['kpi']) || !empty($k['kompetensi']) || !empty($k['core_values']); };
        $adaKomentar = $cekAda($komentarSelf) || $cekAda($komentarAtasan) || $cekAda($komentarPenilai);
    @endphp

    <div class="section-title">E. KOMENTAR / CATATAN</div>
    <div class="laporan-card" style="padding: 20px;">
        @if($adaKomentar)
            @if($cekAda($komentarSelf))
            <div class="komentar-box">
                <div class="komentar-nama">
                    SELF ASSESSMENT
                    <span class="komentar-level level-self">Diri Sendiri</span>
                    - {{ $pegawai->nama }}
                </div>
                @if(!empty($komentarSelf['kpi']))
                    <div class="komentar-subsection">
                        <strong>KPI:</strong>
                        @foreach($komentarSelf['kpi'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($komentarSelf['kompetensi']))
                    <div class="komentar-subsection">
                        <strong>Kompetensi:</strong>
                        @foreach($komentarSelf['kompetensi'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($komentarSelf['core_values']))
                    <div class="komentar-subsection">
                        <strong>Core Values:</strong>
                        @foreach($komentarSelf['core_values'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif

            @if($cekAda($komentarAtasan))
            <div class="komentar-box">
                <div class="komentar-nama">
                    ATASAN LANGSUNG
                    <span class="komentar-level level-atasan">Atasan Langsung</span>
                    - {{ $penilaian['atasan_langsung']->penilai->nama ?? '-' }}
                </div>
                @if(!empty($komentarAtasan['kpi']))
                    <div class="komentar-subsection">
                        <strong>KPI:</strong>
                        @foreach($komentarAtasan['kpi'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($komentarAtasan['kompetensi']))
                    <div class="komentar-subsection">
                        <strong>Kompetensi:</strong>
                        @foreach($komentarAtasan['kompetensi'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($komentarAtasan['core_values']))
                    <div class="komentar-subsection">
                        <strong>Core Values:</strong>
                        @foreach($komentarAtasan['core_values'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif

            @if($cekAda($komentarPenilai))
            <div class="komentar-box">
                <div class="komentar-nama">
                    ATASAN PENILAI
                    <span class="komentar-level level-penilai">Atasan Penilai</span>
                    - {{ $penilaian['atasan_penilai']->penilai->nama ?? '-' }}
                </div>
                @if(!empty($komentarPenilai['kpi']))
                    <div class="komentar-subsection">
                        <strong>KPI:</strong>
                        @foreach($komentarPenilai['kpi'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($komentarPenilai['kompetensi']))
                    <div class="komentar-subsection">
                        <strong>Kompetensi:</strong>
                        @foreach($komentarPenilai['kompetensi'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if(!empty($komentarPenilai['core_values']))
                    <div class="komentar-subsection">
                        <strong>Core Values:</strong>
                        @foreach($komentarPenilai['core_values'] as $k)
                            <div class="komentar-item">
                                <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                                <span class="komentar-isi"> - {{ $k['komentar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif
        @else
            <div style="padding: 12px; background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 4px; text-align: center; color: #9ca3af; font-style: italic; font-size: 13px;">
                Belum ada komentar dari pegawai, atasan langsung, maupun atasan penilai.
            </div>
        @endif
    </div>

    {{-- ==================== FOOTER ==================== --}}
    <div style="text-align: center; font-size: 11px; color: #9ca3af; padding: 16px 0; border-top: 1px solid #e5e7eb; margin-top: 20px;">
        Dokumen ini dicetak dari sistem KPI {{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }} pada {{ date('d F Y H:i') }}
    </div>

</div>
@endsection