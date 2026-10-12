<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penilaian Kinerja</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Times New Roman', Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.3;
            padding: 1.3cm;
        }

        /* ==================== HEADER ==================== */
        .header {
            text-align: center;
            border-bottom: 3px double #1F4E79;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header .logo { max-height: 45px; margin-bottom: 3px; }
        .header h1 {
            font-size: 13px;
            margin: 2px 0;
            color: #1F4E79;
            text-transform: uppercase;
        }
        .header p { margin: 2px 0; color: #555; font-size: 8px; }
        .header .info { margin-top: 3px; font-size: 7.5px; }
        .header .info span { margin: 0 4px; }

        /* ==================== SECTION ==================== */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            color: #1F4E79;
            margin: 8px 0 4px 0;
            padding-bottom: 2px;
            border-bottom: 1px solid #1F4E79;
        }

        /* ==================== TABEL GABUNGAN ==================== */
        table.gabung-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            font-size: 7px;
        }
        .gabung-table th, .gabung-table td {
            border: 1px solid #666;
            padding: 2px 3px;
            vertical-align: middle;
        }
        .gabung-table th {
            background-color: #1F4E79;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 6.5px;
        }
        .gabung-table thead tr.subheader th {
            background-color: #374151;
            font-size: 6.5px;
            padding: 2px 3px;
        }
        .gabung-table tbody tr { page-break-inside: avoid; }
        .gabung-table thead { display: table-header-group; }
        .gabung-table tbody { display: table-row-group; }

        .gabung-table td.self { background: #eff6ff; }
        .gabung-table td.atasan { background: #f0fdf4; }
        .gabung-table td.penilai { background: #fef3c7; }
        .gabung-table td.rata { background: #f3e8ff; font-weight: bold; }
        .gabung-table td.center { text-align: center; }
        .gabung-table td.bold { font-weight: bold; }

        .gabung-table tfoot td {
            background: #e5e7eb;
            font-weight: bold;
            text-align: center;
            font-size: 7px;
        }

        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-green { color: #2E7D32; }
        .text-blue { color: #1565C0; }
        .text-yellow { color: #E65100; }
        .text-purple { color: #6A1B9A; }

        /* ==================== SUMMARY ==================== */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 3px 0;
            margin: 6px 0;
            table-layout: fixed;
        }
        .summary-table td {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 4px;
            text-align: center;
            background: #f9f9f9;
            vertical-align: middle;
        }
        .summary-card .label { font-size: 6.5px; color: #666; margin-bottom: 1px; }
        .summary-card .value { font-size: 11px; font-weight: bold; }
        .bg-blue { background: #e8f0fe; }
        .bg-green { background: #e8f5e9; }
        .bg-yellow { background: #fff8e1; }
        .bg-purple { background: #f3e5f5; }
        .badge {
            padding: 1px 5px;
            border-radius: 6px;
            font-size: 6px;
            font-weight: bold;
            display: inline-block;
        }

        /* ==================== KOMENTAR ==================== */
        .komentar-box {
            border: 1px solid #ccc;
            padding: 5px 7px;
            margin-bottom: 4px;
            background: #fafafa;
            border-radius: 3px;
        }
        .komentar-box .penilai-nama {
            font-weight: bold;
            color: #1F4E79;
            font-size: 7.5px;
            margin-bottom: 3px;
            padding-bottom: 2px;
            border-bottom: 1px dotted #ccc;
        }
        .komentar-box .komentar-text {
            font-size: 7px;
            color: #444;
            line-height: 1.3;
        }
        .komentar-level {
            padding: 1px 5px;
            border-radius: 8px;
            font-size: 6px;
            font-weight: bold;
            margin-left: 3px;
        }
        .level-self { background: #e3f2fd; color: #1565C0; }
        .level-atasan { background: #e8f5e9; color: #2E7D32; }
        .level-penilai { background: #fff3e0; color: #E65100; }

        .komentar-subsection {
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 1px dotted #e5e7eb;
        }
        .komentar-subsection:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .komentar-subsection strong {
            display: block;
            font-size: 7.5px;
            color: #1F4E79;
            margin-bottom: 2px;
            font-weight: bold;
        }
        .komentar-item {
            padding-left: 6px;
            font-size: 7px;
            line-height: 1.3;
            margin-bottom: 1px;
        }
        .komentar-indikator {
            font-weight: 600;
            color: #374151;
        }
        .komentar-isi {
            font-style: italic;
            color: #555;
            padding-left: 3px;
        }

        /* ==================== TTD ==================== */
        .ttd-wrapper {
            margin-top: 15px;
            width: 100%;
            page-break-inside: avoid;
        }
        .ttd-table { width: 100%; border-collapse: collapse; border: none; }
        .ttd-table td {
            border: none;
            text-align: center;
            padding: 5px 10px;
            vertical-align: top;
            width: 33.33%;
        }
        .ttd-table.two-cols td { width: 50%; }
        .ttd-table .ttd-title { font-weight: bold; font-size: 8px; margin-bottom: 2px; }
        .ttd-table .ttd-sub { font-size: 7px; color: #555; margin-bottom: 4px; min-height: 18px; }
        .ttd-table .ttd-space { height: 45px; position: relative; }
        .ttd-table .ttd-img { max-height: 45px; max-width: 100%; }
        .ttd-table .ttd-line { border-bottom: 1px solid #333; width: 80%; margin: 0 auto 3px auto; }
        .ttd-table .ttd-name { font-size: 7.5px; font-weight: bold; margin-top: 2px; }
        .ttd-table .ttd-jabatan { font-size: 6.5px; color: #666; margin-top: 1px; }

        /* ==================== FOOTER ==================== */
        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 6.5px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 4px;
        }
        .no-data {
            color: #999;
            font-style: italic;
            font-size: 7.5px;
            padding: 3px 0;
        }
    </style>
</head>
<body>

@php
    /* ==================== AMBIL DETAIL PER LEVEL ==================== */
    $detailSelf    = isset($penilaian['self'])            ? ($penilaian['self']->detail_indikator            ?? null) : null;
    $detailAtasan  = isset($penilaian['atasan_langsung']) ? ($penilaian['atasan_langsung']->detail_indikator  ?? null) : null;
    $detailPenilai = isset($penilaian['atasan_penilai'])  ? ($penilaian['atasan_penilai']->detail_indikator   ?? null) : null;

    if (is_string($detailSelf))    $detailSelf    = json_decode($detailSelf, true);
    if (is_string($detailAtasan))  $detailAtasan  = json_decode($detailAtasan, true);
    if (is_string($detailPenilai)) $detailPenilai = json_decode($detailPenilai, true);

    if (!is_array($detailSelf))    $detailSelf    = [];
    if (!is_array($detailAtasan))  $detailAtasan  = [];
    if (!is_array($detailPenilai)) $detailPenilai = [];

    /* ==================== AMBIL INDIKATOR ==================== */
    $indikatorKPI         = \App\Models\IndikatorKPI::where('jabatan_id', $pegawai->jabatan_id)->orderBy('id')->get();
    $indikatorKompetensi  = \App\Models\IndikatorKompetensi::where('jabatan_id', $pegawai->jabatan_id)->orderBy('id')->get();
    $indikatorCore        = \App\Models\IndikatorCoreValue::where('jabatan_id', $pegawai->jabatan_id)->orderBy('id')->get();

    /* ==================== HELPER ==================== */
    if (!function_exists('hitungRata')) {
        function hitungRata($self, $atasan, $penilai) {
            $arr = array_filter([$self, $atasan, $penilai], 'is_numeric');
            return count($arr) > 0 ? round(array_sum($arr) / count($arr), 2) : '-';
        }
    }

    if (!function_exists('getNilai')) {
        function getNilai($detail, $kategori, $index) {
            if (!is_array($detail) || !isset($detail[$kategori]) || !is_array($detail[$kategori])) return '-';
            if (!isset($detail[$kategori][$index])) return '-';
            $val = $detail[$kategori][$index];
            return ($val === null || $val === '') ? '-' : $val;
        }
    }

    if (!function_exists('getKomentarIndikator')) {
        function getKomentarIndikator($detail, $kategori, $index) {
            if (!is_array($detail)) return null;
            $key = 'komentar_' . $kategori;
            if (!isset($detail[$key])) return null;

            if (is_array($detail[$key])) {
                return $detail[$key][$index] ?? null;
            }
            if (is_string($detail[$key]) && $index === 0) {
                return $detail[$key];
            }
            return null;
        }
    }

    /* ==================== BUILD KOMENTAR PER LEVEL ==================== */
    $buildKomentar = function($detail) use ($indikatorKPI, $indikatorKompetensi, $indikatorCore) {
        $result = ['kpi' => [], 'kompetensi' => [], 'core_values' => []];
        if (!is_array($detail)) return $result;

        foreach ($indikatorKPI as $i => $ind) {
            $kom = getKomentarIndikator($detail, 'kpi', $i);
            if (!empty(trim($kom ?? ''))) {
                $result['kpi'][] = [
                    'no' => $i + 1,
                    'nama' => $ind->indikator ?? $ind->nama ?? '-',
                    'komentar' => trim($kom),
                ];
            }
        }

        foreach ($indikatorKompetensi as $i => $ind) {
            $kom = getKomentarIndikator($detail, 'kompetensi', $i);
            if (!empty(trim($kom ?? ''))) {
                $result['kompetensi'][] = [
                    'no' => $i + 1,
                    'nama' => $ind->indikator ?? $ind->nama ?? '-',
                    'komentar' => trim($kom),
                ];
            }
        }

        foreach ($indikatorCore as $i => $ind) {
            $kom = getKomentarIndikator($detail, 'core_values', $i);
            if (!empty(trim($kom ?? ''))) {
                $result['core_values'][] = [
                    'no' => $i + 1,
                    'nama' => $ind->indikator ?? $ind->nama ?? '-',
                    'komentar' => trim($kom),
                ];
            }
        }

        return $result;
    };

    $komentarSelf    = $buildKomentar($detailSelf);
    $komentarAtasan  = $buildKomentar($detailAtasan);
    $komentarPenilai = $hasAtasanPenilai ? $buildKomentar($detailPenilai) : ['kpi' => [], 'kompetensi' => [], 'core_values' => []];

    $cekAda = function($kom) {
        return !empty($kom['kpi']) || !empty($kom['kompetensi']) || !empty($kom['core_values']);
    };

    $adaKomentar = $cekAda($komentarSelf) || $cekAda($komentarAtasan) || $cekAda($komentarPenilai);
@endphp

{{-- ==================== HEADER ==================== --}}
<div class="header">
    @if($logoData)
        <img src="{{ $logoData }}" alt="Logo" class="logo">
    @endif
    <h1>LAPORAN PENILAIAN KINERJA</h1>
    <p>{{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }}</p>
    <div class="info">
        <span><strong>Periode:</strong> {{ $periode->nama }}</span>
        <span><strong>Pegawai:</strong> {{ $pegawai->nama }}</span>
        <span><strong>NIP:</strong> {{ $pegawai->nip }}</span>
        <span><strong>Jabatan:</strong> {{ $pegawai->jabatan->nama ?? '-' }}</span>
        <span><strong>Tanggal:</strong> {{ date('d F Y H:i') }}</span>
    </div>
</div>

{{-- ==================== RINGKASAN ==================== --}}
<table class="summary-table">
    <tr>
        <td class="summary-card bg-blue">
            <div class="label">Self Assessment</div>
            <div class="value text-blue">{{ $hasil->nilai_self ?? 0 }}</div>
        </td>
        <td class="summary-card bg-green">
            <div class="label">Atasan Langsung</div>
            <div class="value text-green">{{ $hasil->nilai_atasan_langsung ?? 0 }}</div>
        </td>
        @if($hasAtasanPenilai)
        <td class="summary-card bg-yellow">
            <div class="label">Atasan Penilai</div>
            <div class="value text-yellow">{{ $hasil->nilai_atasan_penilai ?? 0 }}</div>
        </td>
        @endif
        <td class="summary-card bg-purple">
            <div class="label">NILAI AKHIR</div>
            <div class="value text-purple">{{ $hasil->nilai_akhir }}</div>
            <div style="margin-top:2px;">
                <span class="badge" style="color: {{ $hasil->predikat->warna_text ?? '#000' }}; background-color: {{ $hasil->predikat->warna_latar ?? '#ccc' }}">
                    {{ $hasil->predikat->nama ?? '-' }}
                </span>
            </div>
        </td>
    </tr>
</table>

{{-- ==================== A. KPI ==================== --}}
<div class="section-title">A. INDIKATOR KINERJA PEGAWAI (KPI)</div>
<table class="gabung-table">
    <thead>
        <tr>
            <th rowspan="2" width="4%">No</th>
            <th rowspan="2" width="28%">Indikator</th>
            <th rowspan="2" width="8%">Target</th>
            <th rowspan="2" width="6%">Bobot</th>
            <th colspan="{{ $hasAtasanPenilai ? 3 : 2 }}">Nilai</th>
            <th rowspan="2" width="9%">Rata-rata</th>
        </tr>
        <tr class="subheader">
            <th width="11%">Self</th>
            <th width="11%">Atasan Lgsg</th>
            @if($hasAtasanPenilai)
            <th width="11%">Atasan Nilai</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($indikatorKPI as $i => $ind)
            @php
                $nSelf    = getNilai($detailSelf,    'kpi', $i);
                $nAtasan  = getNilai($detailAtasan,  'kpi', $i);
                $nPenilai = $hasAtasanPenilai ? getNilai($detailPenilai, 'kpi', $i) : null;
                $rata     = hitungRata($nSelf, $nAtasan, $hasAtasanPenilai ? $nPenilai : null);
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
            <tr><td colspan="{{ $hasAtasanPenilai ? 8 : 7 }}" class="center no-data">Belum ada indikator KPI</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align: right;">RATA-RATA KPI</td>
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

{{-- ==================== B. KOMPETENSI ==================== --}}
<div class="section-title">B. KOMPETENSI</div>
<table class="gabung-table">
    <thead>
        <tr>
            <th rowspan="2" width="4%">No</th>
            <th rowspan="2" width="28%">Kompetensi</th>
            <th rowspan="2" width="8%">Skala</th>
            <th rowspan="2" width="6%">Bobot</th>
            <th colspan="{{ $hasAtasanPenilai ? 3 : 2 }}">Nilai</th>
            <th rowspan="2" width="9%">Rata-rata</th>
        </tr>
        <tr class="subheader">
            <th width="11%">Self</th>
            <th width="11%">Atasan Lgsg</th>
            @if($hasAtasanPenilai)
            <th width="11%">Atasan Nilai</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($indikatorKompetensi as $i => $ind)
            @php
                $nSelf    = getNilai($detailSelf,    'kompetensi', $i);
                $nAtasan  = getNilai($detailAtasan,  'kompetensi', $i);
                $nPenilai = $hasAtasanPenilai ? getNilai($detailPenilai, 'kompetensi', $i) : null;
                $rata     = hitungRata($nSelf, $nAtasan, $hasAtasanPenilai ? $nPenilai : null);
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
            <tr><td colspan="{{ $hasAtasanPenilai ? 8 : 7 }}" class="center no-data">Belum ada indikator Kompetensi</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align: right;">RATA-RATA KOMPETENSI</td>
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

{{-- ==================== C. CORE VALUES ==================== --}}
<div class="section-title">C. CORE VALUES</div>
<table class="gabung-table">
    <thead>
        <tr>
            <th rowspan="2" width="4%">No</th>
            <th rowspan="2" width="28%">Core Value</th>
            <th rowspan="2" width="8%">Skala</th>
            <th rowspan="2" width="6%">Bobot</th>
            <th colspan="{{ $hasAtasanPenilai ? 3 : 2 }}">Nilai</th>
            <th rowspan="2" width="9%">Rata-rata</th>
        </tr>
        <tr class="subheader">
            <th width="11%">Self</th>
            <th width="11%">Atasan Lgsg</th>
            @if($hasAtasanPenilai)
            <th width="11%">Atasan Nilai</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($indikatorCore as $i => $ind)
            @php
                $nSelf    = getNilai($detailSelf,    'core_values', $i);
                $nAtasan  = getNilai($detailAtasan,  'core_values', $i);
                $nPenilai = $hasAtasanPenilai ? getNilai($detailPenilai, 'core_values', $i) : null;
                $rata     = hitungRata($nSelf, $nAtasan, $hasAtasanPenilai ? $nPenilai : null);
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
            <tr><td colspan="{{ $hasAtasanPenilai ? 8 : 7 }}" class="center no-data">Belum ada indikator Core Values</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align: right;">RATA-RATA CORE VALUES</td>
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

{{-- ==================== D. REKAPITULASI ==================== --}}
<div class="section-title">D. REKAPITULASI NILAI</div>
<table class="gabung-table">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="35%">Komponen</th>
            <th width="15%">Self</th>
            <th width="15%">Atasan Langsung</th>
            @if($hasAtasanPenilai)
            <th width="15%">Atasan Penilai</th>
            @endif
            <th width="15%">Rata-rata</th>
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
            <td colspan="2" style="text-align: right;">NILAI AKHIR</td>
            <td>{{ $hasil->nilai_self ?? 0 }}</td>
            <td>{{ $hasil->nilai_atasan_langsung ?? 0 }}</td>
            @if($hasAtasanPenilai)
            <td>{{ $hasil->nilai_atasan_penilai ?? 0 }}</td>
            @endif
            <td style="font-size: 8px; color: #1F4E79;">{{ $hasil->nilai_akhir }}</td>
        </tr>
        <tr>
            <td colspan="{{ $hasAtasanPenilai ? 6 : 5 }}" class="center">
                PREDIKAT:
                <span class="badge" style="color: {{ $hasil->predikat->warna_text ?? '#000' }}; background-color: {{ $hasil->predikat->warna_latar ?? '#ccc' }}">
                    {{ $hasil->predikat->nama ?? '-' }}
                </span>
            </td>
        </tr>
    </tfoot>
</table>

{{-- ==================== E. KOMENTAR ==================== --}}
<div class="section-title">E. KOMENTAR / CATATAN</div>

@if($adaKomentar)

    {{-- SELF ASSESSMENT --}}
    @if($cekAda($komentarSelf))
    <div class="komentar-box">
        <div class="penilai-nama">
            SELF ASSESSMENT
            <span class="komentar-level level-self">Diri Sendiri</span>
            - {{ $pegawai->nama }}
        </div>
        <div class="komentar-text">
            @if(!empty($komentarSelf['kpi']))
                <div class="komentar-subsection">
                    <strong>KPI:</strong>
                    @foreach($komentarSelf['kpi'] as $k)
                        <div class="komentar-item">
                            <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
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
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
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
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ATASAN LANGSUNG --}}
    @if($cekAda($komentarAtasan))
    <div class="komentar-box">
        <div class="penilai-nama">
            ATASAN LANGSUNG
            <span class="komentar-level level-atasan">Atasan Langsung</span>
            - {{ $penilaian['atasan_langsung']->penilai->nama ?? '-' }}
        </div>
        <div class="komentar-text">
            @if(!empty($komentarAtasan['kpi']))
                <div class="komentar-subsection">
                    <strong>KPI:</strong>
                    @foreach($komentarAtasan['kpi'] as $k)
                        <div class="komentar-item">
                            <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
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
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
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
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ATASAN PENILAI --}}
    @if($cekAda($komentarPenilai))
    <div class="komentar-box">
        <div class="penilai-nama">
            ATASAN PENILAI
            <span class="komentar-level level-penilai">Atasan Penilai</span>
            - {{ $penilaian['atasan_penilai']->penilai->nama ?? '-' }}
        </div>
        <div class="komentar-text">
            @if(!empty($komentarPenilai['kpi']))
                <div class="komentar-subsection">
                    <strong>KPI:</strong>
                    @foreach($komentarPenilai['kpi'] as $k)
                        <div class="komentar-item">
                            <span class="komentar-indikator">{{ $k['no'] }}. {{ $k['nama'] }}</span>
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
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
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
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
                            <span class="komentar-isi">- {{ $k['komentar'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

@else
    <div style="padding: 8px 10px; background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 4px; text-align: center; color: #9ca3af; font-style: italic; font-size: 7.5px;">
        Belum ada komentar dari pegawai, atasan langsung, maupun atasan penilai.
    </div>
@endif

{{-- ==================== TANDA TANGAN ==================== --}}
<div class="ttd-wrapper">
    @if($hasAtasanPenilai && $atasanPenilai)
    <table class="ttd-table">
        <tr>
            <td>
                <div class="ttd-title">Pegawai yang Dinilai,</div>
                <div class="ttd-sub">{{ $pegawai->nama }}<br>
                    <span style="font-size:6.5px; color:#888;">{{ $pegawai->jabatan->nama ?? 'Pegawai' }}</span>
                </div>
                <div class="ttd-space">
                    @if($ttdPegawaiData)
                        <img src="{{ $ttdPegawaiData }}" class="ttd-img" alt="TTD Pegawai">
                    @endif
                </div>
                <div class="ttd-line"></div>
                <div class="ttd-name">{{ $pegawai->nama }}</div>
                <div class="ttd-jabatan">NIP: {{ $pegawai->nip ?? '-' }}</div>
            </td>
            <td>
                <div class="ttd-title">Mengetahui,</div>
                <div class="ttd-sub">{{ $atasanLangsung->nama ?? '-' }}<br>
                    <span style="font-size:6.5px; color:#888;">{{ $atasanLangsung->jabatan->nama ?? 'Atasan Langsung' }}</span>
                </div>
                <div class="ttd-space">
                    @if($ttdAtasanLangsungData)
                        <img src="{{ $ttdAtasanLangsungData }}" class="ttd-img" alt="TTD Atasan Langsung">
                    @endif
                </div>
                <div class="ttd-line"></div>
                <div class="ttd-name">{{ $atasanLangsung->nama ?? '-' }}</div>
                <div class="ttd-jabatan">NIP: {{ $atasanLangsung->nip ?? '-' }}</div>
            </td>
            <td>
                <div class="ttd-title">Mengetahui,</div>
                <div class="ttd-sub">{{ $atasanPenilai->nama ?? '-' }}<br>
                    <span style="font-size:6.5px; color:#888;">{{ $atasanPenilai->jabatan->nama ?? 'Atasan Penilai' }}</span>
                </div>
                <div class="ttd-space">
                    @if($ttdAtasanPenilaiData)
                        <img src="{{ $ttdAtasanPenilaiData }}" class="ttd-img" alt="TTD Atasan Penilai">
                    @endif
                </div>
                <div class="ttd-line"></div>
                <div class="ttd-name">{{ $atasanPenilai->nama ?? '-' }}</div>
                <div class="ttd-jabatan">NIP: {{ $atasanPenilai->nip ?? '-' }}</div>
            </td>
        </tr>
    </table>
    @else
    <table class="ttd-table two-cols" style="width: 70%; margin: 0 auto;">
        <tr>
            <td style="width: 50%;">
                <div class="ttd-title">Pegawai yang Dinilai,</div>
                <div class="ttd-sub">{{ $pegawai->nama }}<br>
                    <span style="font-size:6.5px; color:#888;">{{ $pegawai->jabatan->nama ?? 'Pegawai' }}</span>
                </div>
                <div class="ttd-space">
                    @if($ttdPegawaiData)
                        <img src="{{ $ttdPegawaiData }}" class="ttd-img" alt="TTD Pegawai">
                    @endif
                </div>
                <div class="ttd-line"></div>
                <div class="ttd-name">{{ $pegawai->nama }}</div>
                <div class="ttd-jabatan">NIP: {{ $pegawai->nip ?? '-' }}</div>
            </td>
            <td style="width: 50%;">
                <div class="ttd-title">Mengetahui,</div>
                <div class="ttd-sub">{{ $atasanLangsung->nama ?? '-' }}<br>
                    <span style="font-size:6.5px; color:#888;">{{ $atasanLangsung->jabatan->nama ?? 'Atasan Langsung' }}</span>
                </div>
                <div class="ttd-space">
                    @if($ttdAtasanLangsungData)
                        <img src="{{ $ttdAtasanLangsungData }}" class="ttd-img" alt="TTD Atasan Langsung">
                    @endif
                </div>
                <div class="ttd-line"></div>
                <div class="ttd-name">{{ $atasanLangsung->nama ?? '-' }}</div>
                <div class="ttd-jabatan">NIP: {{ $atasanLangsung->nip ?? '-' }}</div>
            </td>
        </tr>
    </table>
    @endif
</div>

{{-- ==================== FOOTER ==================== --}}
<div class="footer">
    <p>Dokumen ini dicetak dari sistem KPI {{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }} pada {{ date('d F Y H:i') }}</p>
    <p>&copy; {{ date('Y') }} {{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }} - All Rights Reserved</p>
</div>

</body>
</html>