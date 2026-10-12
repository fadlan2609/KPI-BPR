<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CV Mutasi - {{ $pegawai->nama }}</title>
    <style>
        /* PAGE SETUP */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.3;
        }

        /* WRAPPER — PADDING 1.5cm (MARGIN SEBENARNYA) */
        .wrapper {
            width: 100%;
            border-collapse: collapse;
        }

        .wrapper > tbody > tr > td {
            padding: 1.5cm;
            vertical-align: top;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        /* KOP SURAT */
        .kop {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }

        .kop td {
            vertical-align: middle;
            padding: 0;
        }

        .kop-logo {
            width: 75px;
            text-align: center;
        }

        .kop-logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
            padding: 0 8px;
        }

        .kop-text .nama-bpr {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.15;
        }

        .kop-text .alamat {
            font-size: 8pt;
            margin-top: 2px;
            color: #333;
        }

        .kop-text .kontak {
            font-size: 8pt;
            margin-top: 1px;
            color: #333;
        }

        /* JUDUL */
        .judul {
            text-align: center;
            margin: 10px 0 12px 0;
            page-break-after: avoid;
        }

        .judul h1 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 1px;
            margin: 0 0 2px 0;
        }

        .judul .subtitle {
            font-size: 9.5pt;
        }

        /* PROFIL */
        .profil-section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .profil-table td {
            padding: 1px 4px;
            vertical-align: top;
            font-size: 10pt;
            line-height: 1.35;
        }

        .profil-table .label { width: 130px; font-weight: bold; }
        .profil-table .titik { width: 12px; }

        /* SECTION TITLE */
        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            background: #1F4E79;
            color: white;
            padding: 4px 10px;
            margin: 10px 0 6px 0;
            page-break-after: avoid;
        }

        /* RINGKASAN GRID */
        .ringkasan-grid {
            width: 100%;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .ringkasan-grid td {
            width: 33.33%;
            padding: 6px;
            text-align: center;
            border: 2px solid #1F4E79;
            background: #e8f0fe;
        }

        .ringkasan-grid .label {
            font-size: 8pt;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .ringkasan-grid .value {
            font-size: 14pt;
            font-weight: bold;
            color: #1F4E79;
        }

        /* TABEL RIWAYAT */
        .riwayat-table {
            width: 100%;
            margin-bottom: 8px;
        }

        .riwayat-table thead {
            display: table-header-group;
            background: #e8f0fe;
        }

        .riwayat-table th {
            padding: 4px 5px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            border: 1px solid #999;
            color: #1F4E79;
        }

        .riwayat-table td {
            padding: 4px 5px;
            border: 1px solid #ccc;
            font-size: 9.5pt;
            vertical-align: top;
            line-height: 1.3;
        }

        .riwayat-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .riwayat-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .riwayat-table .no { width: 25px; text-align: center; }
        .riwayat-table .periode { width: 85px; text-align: center; font-weight: bold; color: #1F4E79; }
        .riwayat-table .kantor { width: 90px; }
        .riwayat-table .status { width: 90px; text-align: center; }

        /* BADGE */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-magang { background: #fef3c7; color: #92400e; }
        .badge-kontrak { background: #dbeafe; color: #1e40af; }
        .badge-tetap { background: #dcfce7; color: #166534; }
        .badge-honorer { background: #f3e8ff; color: #6b21a8; }
        .badge-outsourcing { background: #fed7aa; color: #9a3412; }
        .badge-lainnya { background: #f3f4f6; color: #374151; }

        /* TIMELINE */
        .timeline-item {
            position: relative;
            padding-left: 22px;
            padding-bottom: 8px;
            border-left: 2px solid #1F4E79;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 3px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #1F4E79;
            border: 2px solid white;
        }

        .timeline-item.current::before {
            background: #22c55e;
        }

        .timeline-content { padding-left: 3px; }

        .timeline-jabatan {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1F4E79;
        }

        .timeline-periode {
            font-size: 9pt;
            color: #666;
            margin: 1px 0 2px 0;
        }

        .timeline-kantor {
            font-size: 9.5pt;
            color: #333;
        }

        .timeline-status { margin-top: 2px; }

        .timeline-keterangan {
            font-size: 9pt;
            font-style: italic;
            color: #555;
            margin-top: 3px;
            padding: 3px 6px;
            border-left: 3px solid #ccc;
            background: #fafafa;
        }

        /* TANDA TANGAN */
        .ttd-wrapper {
            margin-top: 20px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .ttd-table {
            width: 100%;
        }

        .ttd-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
        }

        .ttd-label {
            font-size: 10pt;
            margin-bottom: 3px;
        }

        .ttd-space {
            height: 60px;
            margin: 3px 0;
        }

        .ttd-img {
            height: 55px;
            width: auto;
            display: block;
            margin: 0 auto;
        }

        .barcode-img {
            height: 35px;
            width: auto;
            display: block;
            margin: 0 auto;
        }

        .ttd-nama {
            font-size: 10pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 3px;
        }

        .ttd-jabatan {
            font-size: 9pt;
            font-style: italic;
            margin-top: 1px;
        }

        .ttd-nip {
            font-size: 9pt;
            margin-top: 1px;
        }

        /* FOOTER */
        .footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 7.5pt;
            color: #888;
            page-break-inside: avoid;
            break-inside: avoid;
        }
    </style>
</head>
<body>

@php
    // Logo
    $logoData = null;
    if ($bpr && $bpr->logo) {
        $logoPath = storage_path('app/public/' . $bpr->logo);
        if (file_exists($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            if ($ext === 'jpg') $ext = 'jpeg';
            $logoData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }

    // TTD Pegawai
    $ttdPegawaiData = null;
    $ttdPegawaiType = $pegawai->ttd_type ?? null;
    if ($pegawai->ttd_image) {
        $ttdPath = storage_path('app/public/' . $pegawai->ttd_image);
        if (file_exists($ttdPath)) {
            $ext = strtolower(pathinfo($ttdPath, PATHINFO_EXTENSION));
            if ($ext === 'jpg') $ext = 'jpeg';
            $ttdPegawaiData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($ttdPath));
        }
    }

    // TTD Pimpinan
    $ttdPimpinanData = null;
    $ttdPimpinanNama = null;
    $ttdPimpinanJabatan = null;
    $barcodePimpinanData = null;

    $atasanPenilai = null;
    if ($pegawai->atasanLangsung && $pegawai->atasanLangsung->jabatan) {
        $jabatanAtasan = $pegawai->atasanLangsung->jabatan->atasan;
        if ($jabatanAtasan) {
            $atasanPenilai = \App\Models\Pegawai::where('jabatan_id', $jabatanAtasan->id)
                ->where('status', 'aktif')
                ->first();
        }
    }

    if ($atasanPenilai && $atasanPenilai->ttd_image) {
        $ttdPath = storage_path('app/public/' . $atasanPenilai->ttd_image);
        if (file_exists($ttdPath)) {
            $ext = strtolower(pathinfo($ttdPath, PATHINFO_EXTENSION));
            if ($ext === 'jpg') $ext = 'jpeg';
            $ttdPimpinanData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($ttdPath));
            $ttdPimpinanNama = $atasanPenilai->nama;
            $ttdPimpinanJabatan = $atasanPenilai->jabatan->nama ?? null;
        }
    }

    if (!$ttdPimpinanData && $bpr && $bpr->ttd_image) {
        $ttdPath = storage_path('app/public/' . $bpr->ttd_image);
        if (file_exists($ttdPath)) {
            $ext = strtolower(pathinfo($ttdPath, PATHINFO_EXTENSION));
            if ($ext === 'jpg') $ext = 'jpeg';
            $ttdPimpinanData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($ttdPath));
        }
    }

    if (!$ttdPimpinanNama) $ttdPimpinanNama = $bpr->ttd_nama ?? '____________________';
    if (!$ttdPimpinanJabatan) $ttdPimpinanJabatan = $bpr->ttd_jabatan ?? 'Pimpinan';

    if ($bpr && $bpr->barcode_ttd) {
        $barcodePath = storage_path('app/public/' . $bpr->barcode_ttd);
        if (file_exists($barcodePath)) {
            $ext = strtolower(pathinfo($barcodePath, PATHINFO_EXTENSION));
            if ($ext === 'jpg') $ext = 'jpeg';
            $barcodePimpinanData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($barcodePath));
        }
    }

    $totalTahun = 0;
    foreach ($riwayat as $r) {
        $selesai = $r->tahun_selesai ?? date('Y');
        $totalTahun += ($selesai - $r->tahun_mulai);
    }
@endphp

{{-- ============================================================
     WRAPPER TABLE — PADDING 1.5cm
     ============================================================ --}}
<table class="wrapper">
    <tr>
        <td>

            {{-- KOP SURAT --}}
            <div class="kop">
                <table>
                    <tr>
                        <td class="kop-logo">
                            @if($logoData)
                                <img src="{{ $logoData }}" alt="Logo">
                            @endif
                        </td>
                        <td class="kop-text">
                            <div class="nama-bpr">{{ $bpr->nama_bpr ?? 'BPRS AMANAH BANGSA' }}</div>
                            <div class="alamat">{{ $bpr->alamat ?? '' }}</div>
                            <div class="kontak">
                                @if($bpr && $bpr->no_telp) Telp: {{ $bpr->no_telp }} @endif
                                @if($bpr && $bpr->email) | Email: {{ $bpr->email }} @endif
                                @if($bpr && $bpr->website) | {{ $bpr->website }} @endif
                            </div>
                        </td>
                        <td style="width: 75px;"></td>
                    </tr>
                </table>
            </div>

            {{-- JUDUL --}}
            <div class="judul">
                <h1>DAFTAR RIWAYAT JABATAN</h1>
                <div class="subtitle">(Curriculum Vitae Mutasi Pegawai)</div>
            </div>

            {{-- PROFIL --}}
            <div class="profil-section">
                <table class="profil-table">
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="titik">:</td>
                        <td><strong>{{ $pegawai->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">NIP</td>
                        <td class="titik">:</td>
                        <td>{{ $pegawai->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jabatan Saat Ini</td>
                        <td class="titik">:</td>
                        <td>{{ $pegawai->jabatan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kantor</td>
                        <td class="titik">:</td>
                        <td>{{ $pegawai->kantor->nama ?? '-' }}</td>
                    </tr>
                    @if($pegawai->tanggal_lahir)
                    <tr>
                        <td class="label">Tanggal Lahir</td>
                        <td class="titik">:</td>
                        <td>{{ $pegawai->tanggal_lahir->translatedFormat('d F Y') }}</td>
                    </tr>
                    @endif
                    @if($pegawai->jenis_kelamin)
                    <tr>
                        <td class="label">Jenis Kelamin</td>
                        <td class="titik">:</td>
                        <td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    @endif
                    @if($pegawai->email)
                    <tr>
                        <td class="label">Email</td>
                        <td class="titik">:</td>
                        <td>{{ $pegawai->email }}</td>
                    </tr>
                    @endif
                    @if($pegawai->no_hp)
                    <tr>
                        <td class="label">No. HP</td>
                        <td class="titik">:</td>
                        <td>{{ $pegawai->no_hp }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- RINGKASAN --}}
            <table class="ringkasan-grid">
                <tr>
                    <td>
                        <div class="label">Total Riwayat</div>
                        <div class="value">{{ $riwayat->count() }}</div>
                    </td>
                    <td>
                        <div class="label">Total Pengalaman</div>
                        <div class="value">{{ $totalTahun }} th</div>
                    </td>
                    <td>
                        <div class="label">Jabatan Aktif</div>
                        <div class="value">{{ $riwayat->where('tahun_selesai', null)->count() }}</div>
                    </td>
                </tr>
            </table>

            {{-- TABEL RIWAYAT --}}
            <div class="section-title">DAFTAR RIWAYAT JABATAN</div>

            @if($riwayat->count() > 0)
                <table class="riwayat-table">
                    <thead>
                        <tr>
                            <th class="no">No</th>
                            <th class="periode">Periode</th>
                            <th>Jabatan</th>
                            <th class="kantor">Kantor</th>
                            <th class="status">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $index => $r)
                            <tr>
                                <td class="no">{{ $index + 1 }}</td>
                                <td class="periode">{{ $r->periode_label }}</td>
                                <td>
                                    <strong>{{ $r->jabatan->nama ?? '-' }}</strong>
                                    @if($r->keterangan)
                                        <div style="font-size: 8.5pt; color: #666; font-style: italic; margin-top: 2px;">
                                            {{ $r->keterangan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="kantor">{{ $r->kantor->nama ?? '-' }}</td>
                                <td class="status">
                                    @php $badgeClass = 'badge-' . ($r->status_karyawan ?? 'lainnya'); @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $r->status_karyawan_label }}
                                    </span>
                                    @if($r->jenis_kontrak)
                                        <div style="font-size: 7.5pt; color: #666; margin-top: 2px;">
                                            {{ $r->jenis_kontrak }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #999; padding: 15px; border: 1px dashed #ccc;">
                    Belum ada data riwayat jabatan
                </p>
            @endif

            {{-- TIMELINE --}}
            @if($riwayat->count() > 0)
                <div class="section-title" style="margin-top: 12px;">TIMELINE PERJALANAN KARIR</div>

                <div style="margin-top: 8px;">
                    @foreach($riwayat as $r)
                        <div class="timeline-item {{ $r->is_current ? 'current' : '' }}">
                            <div class="timeline-content">
                                <div class="timeline-jabatan">{{ $r->jabatan->nama ?? '-' }}</div>
                                <div class="timeline-periode">
                                    {{ $r->periode_label }} ({{ $r->durasi }})
                                </div>
                                <div class="timeline-kantor">
                                    {{ $r->kantor->nama ?? '-' }}
                                </div>
                                <div class="timeline-status">
                                    @php $badgeClass = 'badge-' . ($r->status_karyawan ?? 'lainnya'); @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $r->status_karyawan_label }}
                                    </span>
                                    @if($r->jenis_kontrak)
                                        <span style="font-size: 8.5pt; color: #666; margin-left: 4px;">
                                            {{ $r->jenis_kontrak }}
                                        </span>
                                    @endif
                                </div>
                                @if($r->keterangan)
                                    <div class="timeline-keterangan">
                                        "{{ $r->keterangan }}"
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- TANDA TANGAN --}}
            <div class="ttd-wrapper">
                <table class="ttd-table">
                    <tr>
                        <td>
                            <div class="ttd-label">Pegawai yang bersangkutan,</div>

                            <div class="ttd-space">
                                @if($ttdPegawaiData)
                                    @if($ttdPegawaiType === 'barcode')
                                        <img src="{{ $ttdPegawaiData }}" class="barcode-img" alt="Barcode">
                                    @else
                                        <img src="{{ $ttdPegawaiData }}" class="ttd-img" alt="TTD Pegawai">
                                    @endif
                                @endif
                            </div>

                            <div class="ttd-nama">{{ $pegawai->nama }}</div>
                            <div class="ttd-nip">NIP: {{ $pegawai->nip ?? '-' }}</div>
                            <div class="ttd-jabatan">{{ $pegawai->jabatan->nama ?? '-' }}</div>
                        </td>

                        <td>
                            <div class="ttd-label">{{ date('d F Y') }}</div>
                            <div class="ttd-label">Pimpinan,</div>

                            <div class="ttd-space">
                                @if($ttdPimpinanData)
                                    <img src="{{ $ttdPimpinanData }}" class="ttd-img" alt="TTD Pimpinan">
                                @endif
                            </div>

                            <div class="ttd-nama">{{ $ttdPimpinanNama }}</div>
                            @if($ttdPimpinanJabatan)
                                <div class="ttd-jabatan">{{ $ttdPimpinanJabatan }}</div>
                            @endif

                            @if($barcodePimpinanData)
                                <div style="margin-top: 3px;">
                                    <img src="{{ $barcodePimpinanData }}" class="barcode-img" alt="Barcode">
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            {{-- FOOTER --}}
            <div class="footer">
                Dokumen ini dicetak dari Sistem KPI {{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }}
                pada {{ date('d/m/Y H:i') }}
            </div>

        </td>
    </tr>
</table>

</body>
</html>