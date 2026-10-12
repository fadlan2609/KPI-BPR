@extends('layouts.admin')

@section('title', 'Laporan Cuti Pegawai')

@push('styles')
<style>
    .lc-wrap { max-width: 1200px; margin: 0 auto; padding: 24px 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    .lc-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; margin-bottom: 20px; }
    .lc-header h1 { font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; }
    .lc-header p { font-size: 13px; color: #64748b; margin: 0; }
    .lc-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .lc-btn { display: inline-flex; align-items: center; padding: 8px 14px; font-size: 12px; font-weight: 600; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; transition: all 0.15s; }
    .lc-btn-back { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
    .lc-btn-back:hover { background: #f8fafc; }
    .lc-btn-excel { background: #047857; color: #fff; }
    .lc-btn-excel:hover { background: #065f46; }
    .lc-btn-pdf { background: #b91c1c; color: #fff; }
    .lc-btn-pdf:hover { background: #991b1b; }
    .lc-btn-print { background: #334155; color: #fff; }
    .lc-btn-print:hover { background: #1e293b; }

    .lc-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 16px; }
    .lc-card-head { padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.5px; }
    .lc-card-body { padding: 20px; }

    /* FILTER */
    .lc-filter-table { width: 100%; border-collapse: collapse; }
    .lc-filter-table td { padding: 0 8px 0 0; vertical-align: bottom; }
    .lc-filter-table td:last-child { padding-right: 0; }
    .lc-label { display: block; font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }
    .lc-select { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; color: #334155; background: #fff; }
    .lc-select:focus { outline: none; border-color: #047857; box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.1); }

    /* SUMMARY */
    .lc-summary-table { width: 100%; border-collapse: separate; border-spacing: 10px 0; table-layout: fixed; }
    .lc-summary-table td { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; vertical-align: top; }
    .lc-summary-label { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin: 0 0 6px 0; }
    .lc-summary-value { font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 2px 0; line-height: 1; }
    .lc-summary-sub { font-size: 11px; color: #94a3b8; margin: 0; }
    .lc-summary-pending { border-color: #fcd34d; }
    .lc-summary-pending .lc-summary-label { color: #b45309; }
    .lc-summary-pending .lc-summary-value { color: #b45309; }
    .lc-summary-approved { border-color: #6ee7b7; }
    .lc-summary-approved .lc-summary-label { color: #047857; }
    .lc-summary-approved .lc-summary-value { color: #047857; }
    .lc-summary-rejected { border-color: #fca5a5; }
    .lc-summary-rejected .lc-summary-label { color: #b91c1c; }
    .lc-summary-rejected .lc-summary-value { color: #b91c1c; }

    /* TABEL DATA */
    .lc-data-table { width: 100%; border-collapse: collapse; font-size: 13px; table-layout: fixed; }
    .lc-data-table thead th { background: #f8fafc; padding: 10px 12px; text-align: left; font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 1px solid #e2e8f0; }
    .lc-data-table thead th:nth-child(1) { width: 5%; }
    .lc-data-table thead th:nth-child(2) { width: 20%; }
    .lc-data-table thead th:nth-child(3) { width: 12%; }
    .lc-data-table thead th:nth-child(4) { width: 22%; }
    .lc-data-table thead th:nth-child(5) { width: 9%; }
    .lc-data-table thead th:nth-child(6) { width: 14%; }
    .lc-data-table thead th:nth-child(7) { width: 18%; }
    .lc-data-table tbody td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; word-wrap: break-word; }
    .lc-data-table tbody tr:hover { background: #f8fafc; }
    .lc-no { color: #64748b; font-size: 13px; }
    .lc-nama { font-size: 13px; font-weight: 600; color: #1e293b; }
    .lc-nip { font-size: 11px; color: #64748b; margin-top: 2px; }
    .lc-tanggal { font-size: 13px; color: #334155; }
    .lc-tanggal-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }
    .lc-hari { display: inline-block; padding: 3px 10px; background: #f1f5f9; color: #334155; font-size: 12px; font-weight: 700; border-radius: 6px; white-space: nowrap; }
    .lc-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; border: 1px solid transparent; white-space: nowrap; }
    .lc-badge-tahunan { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .lc-badge-sakit { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .lc-badge-melahirkan { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
    .lc-badge-khusus { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .lc-badge-lainnya { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .lc-badge-pending { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .lc-badge-proses { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .lc-badge-disetujui { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .lc-badge-ditolak { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .lc-badge-dibatalkan { background: #f8fafc; color: #475569; border-color: #e2e8f0; }

    /* EMPTY STATE */
    .lc-empty { padding: 60px 20px; text-align: center; }
    .lc-empty-icon { font-size: 42px; margin-bottom: 12px; }
    .lc-empty-title { font-size: 14px; font-weight: 700; color: #334155; margin: 0 0 4px 0; }
    .lc-empty-sub { font-size: 12px; color: #94a3b8; margin: 0; }

    /* FOOTER */
    .lc-footer { margin-top: 24px; text-align: center; font-size: 11px; color: #94a3b8; }

    /* PRINT HEADER — sembunyikan di layar */
    .lc-print-header { display: none; }

    /* RESPONSIVE — HANYA UNTUK SCREEN, BUKAN PRINT */
    @media screen and (max-width: 900px) {
        .lc-summary-table, .lc-summary-table tbody, .lc-summary-table tr, .lc-summary-table td { display: block; width: 100% !important; margin-bottom: 10px; }
        .lc-filter-table, .lc-filter-table tbody, .lc-filter-table tr, .lc-filter-table td { display: block; width: 100% !important; margin-bottom: 10px; }
        .lc-data-table { font-size: 11px; table-layout: auto; }
        .lc-data-table thead { display: none; }
        .lc-data-table tbody td { display: block; padding: 8px 12px; border-bottom: 1px solid #f1f5f9; }
    }

    /* ============================================================== */
    /* ==================== PRINT STYLE ============================== */
    /* ============================================================== */
    @media print {
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        .lc-actions,
        .lc-filter-card,
        .lc-no-print,
        nav, header, footer, aside,
        .sidebar, .navbar,
        .no-print,
        button[onclick*="print"] {
            display: none !important;
        }

        /* Sembunyikan header biasa — diganti dengan print-header */
        .lc-header {
            display: none !important;
        }

        /* TAMPILKAN PRINT HEADER dengan logo */
        .lc-print-header {
            display: block !important;
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .lc-print-header .logo-wrap {
            margin-bottom: 5px;
        }
        .lc-print-header img {
            max-height: 60px;
            width: auto;
        }
        .lc-print-header .bpr-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            margin: 3px 0;
            letter-spacing: 0.5px;
        }
        .lc-print-header .bpr-address {
            font-size: 9px;
            color: #333;
            margin: 2px 0;
        }
        .lc-print-header .doc-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
            text-transform: uppercase;
            text-decoration: underline;
            color: #000;
        }
        .lc-print-header .doc-subtitle {
            font-size: 10px;
            color: #333;
            margin-top: 3px;
        }

        body {
            background: #fff !important;
            font-size: 10px !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .lc-wrap {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .lc-card {
            border: 1px solid #999 !important;
            border-radius: 0 !important;
            margin-bottom: 8px !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }
        .lc-card-head {
            background: #f1f5f9 !important;
            border-bottom: 1px solid #999 !important;
            padding: 6px 10px !important;
            font-size: 10px !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact !important;
        }

        .lc-summary-table { border-spacing: 3px 0 !important; margin: 6px 0 !important; }
        .lc-summary-table td {
            padding: 6px !important;
            border: 1px solid #666 !important;
            border-radius: 0 !important;
            -webkit-print-color-adjust: exact !important;
        }
        .lc-summary-label { font-size: 8px !important; color: #000 !important; }
        .lc-summary-value { font-size: 14px !important; color: #000 !important; }

        /* TABEL DATA PRINT */
        .lc-data-table {
            font-size: 9px !important;
            page-break-inside: auto;
            table-layout: fixed !important;
            width: 100% !important;
        }
        .lc-data-table thead th:nth-child(1) { width: 4% !important; }
        .lc-data-table thead th:nth-child(2) { width: 20% !important; }
        .lc-data-table thead th:nth-child(3) { width: 12% !important; }
        .lc-data-table thead th:nth-child(4) { width: 22% !important; }
        .lc-data-table thead th:nth-child(5) { width: 8% !important; }
        .lc-data-table thead th:nth-child(6) { width: 14% !important; }
        .lc-data-table thead th:nth-child(7) { width: 20% !important; }

        .lc-data-table thead { display: table-header-group !important; }
        .lc-data-table tbody { display: table-row-group !important; }
        .lc-data-table tr { page-break-inside: avoid !important; }

        .lc-data-table thead th {
            background: #1F4E79 !important;
            color: #fff !important;
            border: 1px solid #333 !important;
            padding: 5px 6px !important;
            font-size: 8px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .lc-data-table tbody td {
            border: 1px solid #666 !important;
            padding: 4px 6px !important;
            background: #fff !important;
            color: #000 !important;
            word-wrap: break-word !important;
        }
        .lc-data-table tbody tr:hover { background: #fff !important; }

        .lc-badge {
            border: 1px solid #666 !important;
            font-size: 8px !important;
            padding: 1px 5px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .lc-hari {
            border: 1px solid #666 !important;
            padding: 2px 6px !important;
            background: #f1f5f9 !important;
            font-size: 9px !important;
            -webkit-print-color-adjust: exact !important;
        }

        .lc-nama { font-size: 10px !important; color: #000 !important; }
        .lc-nip { font-size: 8px !important; color: #444 !important; }
        .lc-tanggal { font-size: 9px !important; color: #000 !important; }
        .lc-tanggal-sub { font-size: 8px !important; color: #555 !important; }

        .lc-footer {
            margin-top: 12px !important;
            padding-top: 6px !important;
            border-top: 1px solid #999 !important;
            font-size: 8px !important;
            color: #333 !important;
        }

        .pagination, nav[role="navigation"] { display: none !important; }

        * {
            box-shadow: none !important;
            text-shadow: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="lc-wrap">

    {{-- ==================== PRINT HEADER (hanya muncul saat print) ==================== --}}
    @php
        $bprPrint = \App\Models\BPR::first();
        $logoPrintData = null;
        if ($bprPrint && $bprPrint->logo) {
            $logoPath = storage_path('app/public/' . $bprPrint->logo);
            if (file_exists($logoPath)) {
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                if ($ext === 'jpg') $ext = 'jpeg';
                $logoPrintData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
    @endphp

    <div class="lc-print-header">
        @if($logoPrintData)
            <div class="logo-wrap">
                <img src="{{ $logoPrintData }}" alt="Logo BPR">
            </div>
        @endif
        <div class="bpr-name">{{ $bprPrint->nama_bpr ?? 'BPRS AMANAH BANGSA' }}</div>
        @if($bprPrint && $bprPrint->alamat)
            <div class="bpr-address">{{ $bprPrint->alamat }}</div>
        @endif
        @if($bprPrint && $bprPrint->no_telp)
            <div class="bpr-address">
                Telp: {{ $bprPrint->no_telp }}
                @if($bprPrint->email) | Email: {{ $bprPrint->email }} @endif
            </div>
        @endif
        <div class="doc-title">Laporan Cuti Pegawai</div>
        <div class="doc-subtitle">
            Periode: Tahun {{ $tahun ?? date('Y') }}
            &nbsp;|&nbsp;
            Dicetak: {{ date('d F Y H:i') }} WIB
        </div>
    </div>

    {{-- ==================== HEADER (untuk layar) ==================== --}}
    <div class="lc-header">
        <div>
            <h1>Laporan Cuti Pegawai</h1>
            <p>Rekapitulasi pengajuan dan persetujuan cuti</p>
        </div>
        <div class="lc-actions lc-no-print">
            <a href="{{ route('admin.laporan.index') }}" class="lc-btn lc-btn-back">
                ← Kembali
            </a>
            <a href="{{ route('admin.laporan.download-cuti', array_merge(request()->query(), ['format' => 'excel'])) }}" class="lc-btn lc-btn-excel">
                📊 Excel
            </a>
            <a href="{{ route('admin.laporan.download-cuti', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank" class="lc-btn lc-btn-pdf">
                📄 PDF
            </a>
            <button onclick="window.print()" class="lc-btn lc-btn-print">
                🖨 Cetak
            </button>
        </div>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="lc-card lc-filter-card">
        <div class="lc-card-head">Filter Data</div>
        <div class="lc-card-body">
            <form action="{{ route('admin.laporan.cuti') }}" method="GET">
                <table class="lc-filter-table">
                    <tr>
                        <td style="width: 22%;">
                            <label class="lc-label">Tahun</label>
                            <select name="tahun" class="lc-select">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunList as $t)
                                    <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 22%;">
                            <label class="lc-label">Status</label>
                            <select name="status" class="lc-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </td>
                        <td style="width: 22%;">
                            <label class="lc-label">Jenis Cuti</label>
                            <select name="jenis_cuti" class="lc-select">
                                <option value="">Semua Jenis</option>
                                <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                                <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                                <option value="melahirkan" {{ request('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                                <option value="khusus" {{ request('jenis_cuti') == 'khusus' ? 'selected' : '' }}>Cuti Khusus</option>
                                <option value="lainnya" {{ request('jenis_cuti') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </td>
                        <td style="width: 34%;">
                            <table style="width:100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding-right: 8px;">
                                        <button type="submit" class="lc-btn lc-btn-excel" style="width: 100%; justify-content: center;">
                                            🔍 Terapkan
                                        </button>
                                    </td>
                                    <td style="width: 50px;">
                                        <a href="{{ route('admin.laporan.cuti') }}" class="lc-btn lc-btn-back" style="justify-content: center; width: 100%;" title="Reset">↻</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    {{-- ==================== SUMMARY ==================== --}}
    <table class="lc-summary-table">
        <tr>
            <td class="lc-summary-total">
                <p class="lc-summary-label">Total</p>
                <p class="lc-summary-value">{{ $summary['total'] ?? 0 }}</p>
                <p class="lc-summary-sub">Pengajuan</p>
            </td>
            <td class="lc-summary-pending">
                <p class="lc-summary-label">Pending</p>
                <p class="lc-summary-value">{{ $summary['pending'] ?? 0 }}</p>
                <p class="lc-summary-sub">Menunggu</p>
            </td>
            <td class="lc-summary-approved">
                <p class="lc-summary-label">Disetujui</p>
                <p class="lc-summary-value">{{ $summary['disetujui'] ?? 0 }}</p>
                <p class="lc-summary-sub">Approved</p>
            </td>
            <td class="lc-summary-rejected">
                <p class="lc-summary-label">Ditolak</p>
                <p class="lc-summary-value">{{ $summary['ditolak'] ?? 0 }}</p>
                <p class="lc-summary-sub">Rejected</p>
            </td>
            <td class="lc-summary-total">
                <p class="lc-summary-label">Total Hari</p>
                <p class="lc-summary-value">{{ $summary['total_hari'] ?? 0 }}</p>
                <p class="lc-summary-sub">Hari Disetujui</p>
            </td>
        </tr>
    </table>

    {{-- ==================== TABEL ==================== --}}
    <div class="lc-card">
        <div class="lc-card-head" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Daftar Pengajuan Cuti</span>
            <span style="font-weight: 500; text-transform: none; letter-spacing: 0;">{{ $data->total() }} data</span>
        </div>

        <div style="overflow-x: auto;">
            <table class="lc-data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Pegawai</th>
                        <th>Jenis Cuti</th>
                        <th>Periode Cuti</th>
                        <th style="text-align: center; width: 90px;">Lama</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $c)
                    <tr>
                        <td class="lc-no">
                            {{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}
                        </td>
                        <td>
                            <div class="lc-nama">{{ $c->pegawai->nama ?? '-' }}</div>
                            <div class="lc-nip">{{ $c->pegawai->nip ?? '-' }}</div>
                        </td>
                        <td>
                            @php
                                $jenisClass = match($c->jenis_cuti) {
                                    'tahunan' => 'lc-badge-tahunan',
                                    'sakit' => 'lc-badge-sakit',
                                    'melahirkan' => 'lc-badge-melahirkan',
                                    'khusus' => 'lc-badge-khusus',
                                    default => 'lc-badge-lainnya',
                                };
                            @endphp
                            <span class="lc-badge {{ $jenisClass }}">{{ ucfirst($c->jenis_cuti) }}</span>
                        </td>
                        <td>
                            <div class="lc-tanggal">{{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d M Y') : '-' }}</div>
                            <div class="lc-tanggal-sub">s.d. {{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d M Y') : '-' }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="lc-hari">{{ $c->lama_hari ?? 0 }} hari</span>
                        </td>
                        <td>
                            @php
                                $statusClass = match($c->status) {
                                    'pending' => 'lc-badge-pending',
                                    'proses' => 'lc-badge-proses',
                                    'disetujui' => 'lc-badge-disetujui',
                                    'ditolak' => 'lc-badge-ditolak',
                                    'dibatalkan' => 'lc-badge-dibatalkan',
                                    default => 'lc-badge-lainnya',
                                };
                            @endphp
                            <span class="lc-badge {{ $statusClass }}">{{ ucfirst($c->status ?? '-') }}</span>
                        </td>
                        <td>
                            <div class="lc-tanggal">{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d M Y') : '-' }}</div>
                            <div class="lc-tanggal-sub">{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('H:i') : '' }} WIB</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="lc-empty">
                                <div class="lc-empty-icon">📭</div>
                                <p class="lc-empty-title">Tidak ada data cuti</p>
                                <p class="lc-empty-sub">Belum ada pengajuan cuti sesuai filter yang dipilih</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($data->count() > 0)
        <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
            {{ $data->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- ==================== FOOTER ==================== --}}
    <div class="lc-footer">
        Dokumen ini dihasilkan dari Sistem KPI BPRS Amanah Bangsa · {{ date('d F Y H:i') }} WIB
    </div>

</div>
@endsection