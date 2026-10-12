<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Cuti</title>
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Times New Roman', Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.3;
        }

        /* ==================== WRAPPER TABLE ==================== */
        .wrapper-table {
            width: 100%;
            border-collapse: collapse;
        }
        .wrapper-table > tbody > tr > td {
            padding: 1.5cm 1.5cm 1.5cm 1.5cm;
            vertical-align: top;
        }

        /* ==================== HEADER ==================== */
        .header {
            text-align: center;
            border-bottom: 2px double #1F4E79;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header .logo {
            max-height: 45px;
            margin-bottom: 3px;
        }
        .header h1 {
            font-size: 14px;
            margin: 2px 0;
            color: #1F4E79;
            text-transform: uppercase;
        }
        .header p {
            font-size: 10px;
            color: #555;
            margin: 2px 0;
        }
        .header .info {
            margin-top: 4px;
            font-size: 9px;
            color: #444;
        }
        .header .info span {
            margin: 0 6px;
        }

        /* ==================== SUMMARY ==================== */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin: 8px 0;
            table-layout: fixed;
        }
        .summary-table td {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 6px;
            text-align: center;
            background: #f9f9f9;
        }
        .summary-table .label {
            font-size: 8px;
            color: #666;
            margin-bottom: 2px;
        }
        .summary-table .value {
            font-size: 14px;
            font-weight: bold;
        }
        .bg-blue { background: #e8f0fe; }
        .bg-yellow { background: #fff8e1; }
        .bg-green { background: #e8f5e9; }
        .bg-red { background: #ffebee; }
        .bg-purple { background: #f3e5f5; }
        .text-blue { color: #1565C0; }
        .text-yellow { color: #E65100; }
        .text-green { color: #2E7D32; }
        .text-red { color: #C62828; }
        .text-purple { color: #6A1B9A; }

        /* ==================== TABEL DATA ==================== */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 8px;
        }
        .data-table th, .data-table td {
            border: 1px solid #999;
            padding: 3px 5px;
            text-align: left;
            vertical-align: top;
        }
        .data-table th {
            background-color: #1F4E79;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
        }
        .data-table tr { page-break-inside: avoid; }
        .data-table thead { display: table-header-group; }
        .data-table tbody { display: table-row-group; }

        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .badge {
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: bold;
            display: inline-block;
        }

        /* ==================== FOOTER ==================== */
        .footer {
            margin-top: 12px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7px;
            color: #999;
        }
    </style>
</head>
<body>

<table class="wrapper-table">
    <tr>
        <td>

            @php
                // Logo BPR
                $logoData = null;
                if ($bpr && $bpr->logo) {
                    $p = storage_path('app/public/' . $bpr->logo);
                    if (file_exists($p)) {
                        $e = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                        if ($e === 'jpg') $e = 'jpeg';
                        $logoData = 'data:image/' . $e . ';base64,' . base64_encode(file_get_contents($p));
                    }
                }
            @endphp

            {{-- HEADER --}}
            <div class="header">
                @if($logoData)
                    <img src="{{ $logoData }}" alt="Logo" class="logo">
                @endif
                <h1>LAPORAN CUTI PEGAWAI</h1>
                <p>{{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }}</p>
                <div class="info">
                    <span><strong>Periode:</strong> Tahun {{ $tahun ?? date('Y') }}</span>
                    <span><strong>Tanggal Cetak:</strong> {{ date('d F Y H:i') }}</span>
                </div>
            </div>

            {{-- SUMMARY --}}
            <table class="summary-table">
                <tr>
                    <td class="bg-blue">
                        <div class="label">Total Pengajuan</div>
                        <div class="value text-blue">{{ $summary['total'] ?? 0 }}</div>
                    </td>
                    <td class="bg-yellow">
                        <div class="label">Pending</div>
                        <div class="value text-yellow">{{ $summary['pending'] ?? 0 }}</div>
                    </td>
                    <td class="bg-green">
                        <div class="label">Disetujui</div>
                        <div class="value text-green">{{ $summary['disetujui'] ?? 0 }}</div>
                    </td>
                    <td class="bg-red">
                        <div class="label">Ditolak</div>
                        <div class="value text-red">{{ $summary['ditolak'] ?? 0 }}</div>
                    </td>
                    <td class="bg-purple">
                        <div class="label">Total Hari Disetujui</div>
                        <div class="value text-purple">{{ $summary['total_hari'] ?? 0 }}</div>
                    </td>
                </tr>
            </table>

            {{-- TABEL DATA --}}
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="18%">Pegawai</th>
                        <th width="10%">Jenis Cuti</th>
                        <th width="20%">Periode Cuti</th>
                        <th width="8%">Lama</th>
                        <th width="12%">Status</th>
                        <th width="15%">Diajukan</th>
                        <th width="14%">Approver</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $c)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $c->pegawai->nama ?? '-' }}</strong><br>
                            <span style="font-size: 7px; color: #666;">{{ $c->pegawai->nip ?? '-' }}</span>
                        </td>
                        <td>{{ ucfirst($c->jenis_cuti) }}</td>
                        <td>
                            {{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') : '-' }}
                            s.d.
                            {{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center">{{ $c->lama_hari ?? 0 }} hari</td>
                        <td class="text-center">
                            @if($c->status == 'pending')
                                <span class="badge" style="background:#fff3cd;color:#856404;">Pending</span>
                            @elseif($c->status == 'proses')
                                <span class="badge" style="background:#cce5ff;color:#004085;">Proses</span>
                            @elseif($c->status == 'disetujui')
                                <span class="badge" style="background:#d4edda;color:#155724;">Disetujui</span>
                            @elseif($c->status == 'ditolak')
                                <span class="badge" style="background:#f8d7da;color:#721c24;">Ditolak</span>
                            @elseif($c->status == 'dibatalkan')
                                <span class="badge" style="background:#e2e3e5;color:#383d41;">Dibatalkan</span>
                            @else
                                <span class="badge" style="background:#e2e3e5;color:#383d41;">{{ $c->status }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            @if($c->finalApprovedBy)
                                {{ $c->finalApprovedBy->nama ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 15px; color: #999; font-style: italic;">
                            Tidak ada data cuti
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- FOOTER --}}
            <div class="footer">
                Dokumen ini dihasilkan dari Sistem KPI {{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }} pada {{ date('d F Y H:i') }}
            </div>

        </td>
    </tr>
</table>

</body>
</html>