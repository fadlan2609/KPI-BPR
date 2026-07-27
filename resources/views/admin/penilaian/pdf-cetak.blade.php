<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penilaian Kinerja</title>
    <style>
        body {
            font-family: 'Times New Roman', Arial, sans-serif;
            font-size: 10px;
            padding: 15px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1F4E79;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header .logo {
            max-height: 60px;
            margin-bottom: 5px;
        }
        .header h1 {
            font-size: 16px;
            margin: 2px 0;
            color: #1F4E79;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            color: #555;
            font-size: 11px;
        }
        .header .info {
            margin-top: 5px;
            font-size: 10px;
        }
        .header .info span {
            margin: 0 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #999;
            padding: 3px 5px;
            text-align: center;
        }
        th {
            background-color: #1F4E79;
            color: white;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
        }
        .text-bold {
            font-weight: bold;
        }
        .badge {
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        /* ===== TTD HORIZONTAL ===== */
        .ttd-wrapper {
            margin-top: 30px;
            width: 100%;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .ttd-table td {
            border: none;
            text-align: center;
            padding: 10px 15px;
            vertical-align: top;
            width: 33.33%;
        }
        .ttd-table .ttd-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 3px;
        }
        .ttd-table .ttd-sub {
            font-size: 9px;
            color: #555;
            margin-bottom: 20px;
        }
        .ttd-table .ttd-space {
            height: 40px;
        }
        .ttd-table .ttd-line {
            border-bottom: 1px solid #333;
            width: 70%;
            margin: 0 auto 3px auto;
        }
        .ttd-table .ttd-name {
            font-size: 9px;
            margin-top: 3px;
        }
        .ttd-table .ttd-date {
            font-size: 8px;
            color: #888;
            margin-top: 2px;
        }

        .page-break {
            page-break-after: always;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
            margin: 8px 0;
        }
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 5px;
            text-align: center;
            background: #f9f9f9;
        }
        .summary-card .label {
            font-size: 8px;
            color: #666;
        }
        .summary-card .value {
            font-size: 13px;
            font-weight: bold;
        }
        .bg-blue { background: #e8f0fe; }
        .bg-green { background: #e8f5e9; }
        .bg-yellow { background: #fff8e1; }
        .bg-red { background: #fce4ec; }
        .bg-purple { background: #f3e5f5; }
        .text-blue { color: #1565C0; }
        .text-green { color: #2E7D32; }
        .text-yellow { color: #E65100; }
        .text-red { color: #C62828; }
        .text-purple { color: #6A1B9A; }
    </style>
</head>
<body>

    <!-- ==================== HEADER DENGAN LOGO ==================== -->
    <div class="header">
        @if($bpr && $bpr->logo)
            <img src="{{ public_path('storage/' . $bpr->logo) }}" alt="Logo BPR" class="logo">
        @endif
        <h1>LAPORAN PENILAIAN KINERJA</h1>
        <p>{{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }}</p>
        <div class="info">
            <span><strong>Periode:</strong> {{ $periode->nama }}</span>
            <span><strong>Tanggal Cetak:</strong> {{ date('d F Y H:i') }}</span>
        </div>
    </div>

    <!-- ==================== RINGKASAN ==================== -->
    <div class="summary-grid">
        <div class="summary-card bg-blue">
            <div class="label">Total Pegawai</div>
            <div class="value text-blue">{{ $data->count() }}</div>
        </div>
        <div class="summary-card bg-green">
            <div class="label">Sangat Baik</div>
            <div class="value text-green">{{ $data->where('predikat.nama', 'Sangat Baik')->count() }}</div>
        </div>
        <div class="summary-card bg-yellow">
            <div class="label">Baik</div>
            <div class="value text-yellow">{{ $data->where('predikat.nama', 'Baik')->count() }}</div>
        </div>
        <div class="summary-card bg-red">
            <div class="label">Cukup</div>
            <div class="value text-red">{{ $data->where('predikat.nama', 'Cukup')->count() }}</div>
        </div>
        <div class="summary-card bg-purple">
            <div class="label">Rata-rata</div>
            <div class="value text-purple">{{ number_format($data->avg('nilai_akhir') ?? 0, 2) }}</div>
        </div>
    </div>

    <!-- ==================== TABLE ==================== -->
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="15%">Nama Pegawai</th>
                <th width="10%">NIP</th>
                <th width="15%">Jabatan</th>
                <th width="10%">Self</th>
                <th width="10%">Atasan</th>
                <th width="10%">Penilai</th>
                <th width="10%">Nilai Akhir</th>
                <th width="16%">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $d->pegawai->nama }}</td>
                <td>{{ $d->pegawai->nip }}</td>
                <td class="text-left">{{ $d->pegawai->jabatan->nama ?? '-' }}</td>
                <td>{{ $d->nilai_self ?? '-' }}</td>
                <td>{{ $d->nilai_atasan_langsung ?? '-' }}</td>
                <td>{{ $d->nilai_atasan_penilai ?? '-' }}</td>
                <td class="text-bold">{{ $d->nilai_akhir }}</td>
                <td>
                    <span class="badge" style="color: {{ $d->predikat->warna_text ?? '#000' }}; background-color: {{ $d->predikat->warna_latar ?? '#ccc' }}">
                        {{ $d->predikat->nama ?? '-' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center;">Belum ada data penilaian</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ==================== TTD ==================== -->
    <div class="ttd-wrapper">
        <table class="ttd-table">
            <tr>
                <td>
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-sub">Direktur Utama</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-line"></div>
                    <div class="ttd-name">(____________________)</div>
                    <div class="ttd-date">Tanggal: ________</div>
                </td>
                <td>
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-sub">Kabag SDM</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-line"></div>
                    <div class="ttd-name">(____________________)</div>
                    <div class="ttd-date">Tanggal: ________</div>
                </td>
                <td>
                    <div class="ttd-title">Disusun oleh,</div>
                    <div class="ttd-sub">Admin</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-line"></div>
                    <div class="ttd-name">(____________________)</div>
                    <div class="ttd-date">Tanggal: ________</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== FOOTER ==================== -->
    <div class="footer">
        <p>Dokumen ini dicetak dari sistem KPI BPRS Amanah Bangsa pada {{ date('d F Y H:i') }}</p>
        <p>&copy; {{ date('Y') }} BPRS Amanah Bangsa - All Rights Reserved</p>
    </div>

</body>
</html>