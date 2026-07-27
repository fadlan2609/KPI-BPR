<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self Assessment - {{ $pegawai->nama }}</title>
    <style>
        /* ==================== RESET & BASE ==================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
            background: #f5f5f5;
            color: #333;
        }
        @page {
            size: A4;
            margin: 15mm;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print { display: none !important; }
        }

        /* ==================== CONTAINER ==================== */
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 25px 30px 25px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        @media print {
            .container {
                box-shadow: none;
                border-radius: 0;
                padding: 15mm 20mm 15mm 20mm;
            }
        }

        /* ==================== HEADER ==================== */
        .header {
            border-bottom: 3px double #1F4E79;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header-logo {
            height: 50px;
            width: auto;
            max-height: 50px;
            object-fit: contain;
        }
        .header-title h1 {
            font-size: 18px;
            color: #1F4E79;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.3;
        }
        .header-title h2 {
            font-size: 13px;
            color: #555;
            font-weight: normal;
            margin: 0;
        }
        .header-right {
            text-align: right;
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }
        .header-right strong {
            color: #333;
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #555;
        }
        .header-info span {
            margin-right: 20px;
        }
        .header-info strong {
            color: #333;
        }

        /* ==================== SECTION TITLE ==================== */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1F4E79;
            margin: 20px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 2px solid #1F4E79;
        }

        /* ==================== SUMMARY CARDS ==================== */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 12px 0 18px 0;
        }
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 8px;
            text-align: center;
            background: #f9f9f9;
        }
        .summary-card .label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            margin: 2px 0;
        }
        .summary-card .sub {
            font-size: 8px;
            color: #aaa;
        }
        .bg-blue { background: #e8f0fe; }
        .bg-green { background: #e8f5e9; }
        .bg-yellow { background: #fff8e1; }
        .bg-purple { background: #f3e5f5; }
        .text-blue { color: #1565C0; }
        .text-green { color: #2E7D32; }
        .text-yellow { color: #E65100; }
        .text-purple { color: #6A1B9A; }

        /* ==================== TABLES ==================== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px 7px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #1F4E79;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        td {
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        /* ==================== TTD ==================== */
        .ttd-wrapper {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .ttd-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 10px;
        }
        .ttd-item {
            text-align: center;
        }
        .ttd-item .ttd-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
        }
        .ttd-item .ttd-sub {
            font-size: 10px;
            color: #777;
            margin-bottom: 25px;
        }
        .ttd-item .ttd-line {
            border-bottom: 1px solid #333;
            width: 70%;
            margin: 0 auto 3px auto;
        }
        .ttd-item .ttd-name {
            font-size: 10px;
            margin-top: 3px;
        }
        .ttd-item .ttd-date {
            font-size: 9px;
            color: #888;
            margin-top: 2px;
        }

        /* ==================== FOOTER ==================== */
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        /* ==================== BUTTONS ==================== */
        .btn-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 15px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
            font-weight: 500;
        }
        .btn-print {
            background: #1F4E79;
            color: #fff;
        }
        .btn-print:hover {
            background: #0d3b5e;
        }
        .btn-back {
            background: #6c757d;
            color: #fff;
        }
        .btn-back:hover {
            background: #5a6268;
        }
        @media print {
            .btn-group { display: none !important; }
        }

        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 8px 14px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 12px;
        }
        .alert-info i { margin-right: 8px; }
        @media print {
            .alert-info { display: none !important; }
        }
        .no-data {
            color: #999;
            font-style: italic;
            font-size: 10px;
            padding: 4px 0;
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- ==================== BUTTONS ==================== -->
        <div class="btn-group no-print">
            <a href="{{ route('pegawai.self-assessment.result') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button class="btn btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>

        <!-- ==================== INFO ==================== -->
        <div class="alert-info no-print">
            <i class="fas fa-info-circle"></i>
            Klik tombol <strong>Cetak</strong> untuk mencetak dokumen ini.
        </div>

        <!-- ==================== HEADER ==================== -->
        @php
            $bpr = \App\Models\BPR::first();
            $logoUrl = $bpr && $bpr->logo ? asset('storage/' . $bpr->logo) : null;
        @endphp

        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="header-logo">
                    @endif
                    <div class="header-title">
                        <h1>SELF ASSESSMENT</h1>
                        <h2>{{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }}</h2>
                    </div>
                </div>
                <div class="header-right">
                    <div><strong>Tanggal Cetak:</strong><br>{{ date('d F Y') }}</div>
                    <div style="margin-top:4px;"><strong>Status:</strong> Final</div>
                </div>
            </div>
            <div class="header-info">
                <span><strong>Periode:</strong> {{ $periodeAktif->nama ?? '-' }}</span>
                <span><strong>Pegawai:</strong> {{ $pegawai->nama ?? '-' }}</span>
                <span><strong>NIP:</strong> {{ $pegawai->nip ?? '-' }}</span>
                <span><strong>Jabatan:</strong> {{ $pegawai->jabatan->nama ?? '-' }}</span>
            </div>
        </div>

        <!-- ==================== RINGKASAN ==================== -->
        @php
            $avgKPI = isset($detail['kpi']) ? array_sum($detail['kpi']) / count($detail['kpi']) : 0;
            $avgKompetensi = isset($detail['kompetensi']) ? array_sum($detail['kompetensi']) / count($detail['kompetensi']) : 0;
            $avgCore = isset($detail['core_values']) ? array_sum($detail['core_values']) / count($detail['core_values']) : 0;

            $kompetensiKonversi = ($avgKompetensi / 5) * 100;
            $coreKonversi = ($avgCore / 5) * 100;

            $bobotKPI = $bobot->bobot_kpi ?? 70;
            $bobotKompetensi = $bobot->bobot_kompetensi ?? 15;
            $bobotCore = $bobot->bobot_core_values ?? 15;

            $nilaiKPI = ($avgKPI * $bobotKPI / 100);
            $nilaiKompetensi = ($kompetensiKonversi * $bobotKompetensi / 100);
            $nilaiCore = ($coreKonversi * $bobotCore / 100);
            $nilaiTotal = $nilaiKPI + $nilaiKompetensi + $nilaiCore;
        @endphp

        <div class="summary-grid">
            <div class="summary-card bg-blue">
                <div class="label">KPI</div>
                <div class="value text-blue">{{ number_format($avgKPI, 2) }}</div>
                <div class="sub">× {{ $bobotKPI }}% = {{ number_format($nilaiKPI, 2) }}</div>
            </div>
            <div class="summary-card bg-green">
                <div class="label">Kompetensi</div>
                <div class="value text-green">{{ number_format($avgKompetensi, 2) }}</div>
                <div class="sub">× {{ $bobotKompetensi }}% = {{ number_format($nilaiKompetensi, 2) }}</div>
            </div>
            <div class="summary-card bg-yellow">
                <div class="label">Core Values</div>
                <div class="value text-yellow">{{ number_format($avgCore, 2) }}</div>
                <div class="sub">× {{ $bobotCore }}% = {{ number_format($nilaiCore, 2) }}</div>
            </div>
            <div class="summary-card bg-purple">
                <div class="label">TOTAL</div>
                <div class="value text-purple">{{ number_format($nilaiTotal, 2) }}</div>
                <div class="sub">{{ $penilaian->status ?? 'Submitted' }}</div>
            </div>
        </div>

        <!-- ==================== KPI ==================== -->
        <div class="section-title">A. PENILAIAN KPI (Bobot: {{ $bobotKPI }}%)</div>
        @if(isset($detail['kpi']) && count($detail['kpi']) > 0)
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="38%">Indikator</th>
                        <th width="15%">Target</th>
                        <th width="10%">Bobot</th>
                        <th width="10%">Nilai</th>
                        <th width="22%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detail['kpi'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikator['kpi'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">{{ $indikator['kpi'][$index]->target ?? '-' }} {{ $indikator['kpi'][$index]->satuan ?? '' }}</td>
                        <td class="text-center">{{ $indikator['kpi'][$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detail['komentar_kpi'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-data">Tidak ada data KPI</p>
        @endif

        <!-- ==================== KOMPETENSI ==================== -->
        <div class="section-title">B. PENILAIAN KOMPETENSI (Bobot: {{ $bobotKompetensi }}%)</div>
        <p style="font-size:9px; color:#888; margin-bottom:4px;">Skala 1-5 (Konversi ke 0-100 untuk perhitungan)</p>
        @if(isset($detail['kompetensi']) && count($detail['kompetensi']) > 0)
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="43%">Indikator</th>
                        <th width="10%">Skala</th>
                        <th width="10%">Bobot</th>
                        <th width="10%">Nilai</th>
                        <th width="22%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detail['kompetensi'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikator['kompetensi'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">1-{{ $indikator['kompetensi'][$index]->skala_maksimal ?? 5 }}</td>
                        <td class="text-center">{{ $indikator['kompetensi'][$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detail['komentar_kompetensi'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-data">Tidak ada data Kompetensi</p>
        @endif

        <!-- ==================== CORE VALUES ==================== -->
        <div class="section-title">C. PENILAIAN CORE VALUES (Bobot: {{ $bobotCore }}%)</div>
        <p style="font-size:9px; color:#888; margin-bottom:4px;">Skala 1-5 (Konversi ke 0-100 untuk perhitungan)</p>
        @if(isset($detail['core_values']) && count($detail['core_values']) > 0)
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="43%">Indikator</th>
                        <th width="10%">Skala</th>
                        <th width="10%">Bobot</th>
                        <th width="10%">Nilai</th>
                        <th width="22%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detail['core_values'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikator['core_values'][$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">1-{{ $indikator['core_values'][$index]->skala_maksimal ?? 5 }}</td>
                        <td class="text-center">{{ $indikator['core_values'][$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detail['komentar_core_values'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-data">Tidak ada data Core Values</p>
        @endif

        <!-- ==================== TTD ==================== -->
        <div class="ttd-wrapper">
            <div class="ttd-grid">
                <div class="ttd-item">
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-sub">Atasan Langsung</div>
                    <div class="ttd-line"></div>
                    <div class="ttd-name">(____________________)</div>
                    <div class="ttd-date">Tanggal: ________</div>
                </div>
                <div class="ttd-item">
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-sub">Atasan Penilai</div>
                    <div class="ttd-line"></div>
                    <div class="ttd-name">(____________________)</div>
                    <div class="ttd-date">Tanggal: ________</div>
                </div>
                <div class="ttd-item">
                    <div class="ttd-title">Pegawai yang Dinilai,</div>
                    <div class="ttd-sub">{{ $pegawai->nama ?? '' }}</div>
                    <div class="ttd-line"></div>
                    <div class="ttd-name">(____________________)</div>
                    <div class="ttd-date">Tanggal: ________</div>
                </div>
            </div>
        </div>

        <!-- ==================== FOOTER ==================== -->
        <div class="footer">
            <p>Dokumen ini dicetak dari sistem KPI BPRS Amanah Bangsa pada {{ date('d F Y H:i') }}</p>
            <p>&copy; {{ date('Y') }} BPRS Amanah Bangsa - All Rights Reserved</p>
        </div>

    </div>

</body>
</html>