<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Permohonan Cuti</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }

        .wrapper-table { width: 100%; border-collapse: collapse; }
        .wrapper-table > tbody > tr > td {
            padding: 1.5cm 1.5cm 1.2cm 1.5cm;
            vertical-align: top;
        }

        /* HEADER */
        .header-form {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-form .logo {
            max-height: 50px;
            margin-bottom: 4px;
        }
        .header-form h1 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 4px 0;
        }
        .header-form p {
            font-size: 10px;
            margin: 2px 0;
        }

        .form-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            margin: 12px 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* DATA PEMOHON */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table td { padding: 3px 0; font-size: 11px; vertical-align: top; }
        .data-table .label { width: 160px; }
        .data-table .titik { width: 12px; text-align: center; }
        .data-table .value { font-weight: bold; }

        /* BOX CUTI */
        .cuti-box {
            border: 1px solid #000;
            padding: 8px 12px;
            margin: 10px 0;
        }
        .cuti-box .row { display: block; margin-bottom: 4px; font-size: 11px; }
        .cuti-box .row strong { display: inline-block; min-width: 170px; }

        /* CHECKBOX */
        .checkbox-group {
            margin: 8px 0;
            padding: 6px 10px;
            border: 1px solid #000;
        }
        .checkbox-item {
            display: block;
            margin-bottom: 3px;
            font-size: 11px;
        }
        .checkbox-item .box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            text-align: center;
            line-height: 14px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 6px;
            vertical-align: middle;
        }
        .checkbox-item .box.checked {
            background: #000;
            color: #fff;
        }

        /* ALASAN */
        .alasan-box {
            margin: 10px 0;
            padding: 6px 10px;
            border: 1px solid #000;
        }
        .alasan-box strong { display: inline-block; min-width: 100px; }

        /* SISA CUTI */
        .sisa-info {
            margin: 12px 0;
            padding: 8px 12px;
            background: #f5f5f5;
            border: 1px solid #000;
        }
        .sisa-info .title {
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 11px;
        }
        .sisa-info table { width: 100%; }
        .sisa-info td { padding: 2px 0; font-size: 11px; vertical-align: top; }
        .sisa-info td:nth-child(2) { width: 12px; text-align: center; }

        /* TTD SECTION */
        .ttd-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .ttd-grid td {
            vertical-align: top;
            padding: 6px 8px;
            text-align: center;
        }

        .ttd-title {
            font-size: 11px;
            margin-bottom: 4px;
        }
        .ttd-space {
            height: 60px;
            text-align: center;
            padding-top: 4px;
        }
        .ttd-img {
            max-height: 55px;
            max-width: 100%;
        }
        .ttd-line {
            border-bottom: 1px solid #000;
            width: 85%;
            margin: 0 auto 4px auto;
        }
        .ttd-name {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .ttd-jabatan {
            font-size: 10px;
            margin-top: 1px;
        }

        /* PEMOHON */
        .pemohon-block {
            width: 220px;
            margin-top: 12px;
        }

        /* PENGGANTI */
        .pengganti-info {
            margin-top: 8px;
            font-size: 10px;
            font-style: italic;
        }
        .pengganti-info strong {
            font-style: normal;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

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

    // Helper: ambil TTD pegawai
    $getTtd = function ($pegawai) {
        if (!$pegawai || !$pegawai->ttd_image) return null;
        $p = storage_path('app/public/' . $pegawai->ttd_image);
        if (!file_exists($p)) return null;
        $e = strtolower(pathinfo($p, PATHINFO_EXTENSION));
        if ($e === 'jpg') $e = 'jpeg';
        return 'data:image/' . $e . ';base64,' . base64_encode(file_get_contents($p));
    };

    // TTD Pemohon
    $ttdPemohon = $getTtd($cuti->pegawai);

    // Saldo cuti
    $saldo = \App\Models\SaldoCuti::where('pegawai_id', $cuti->pegawai_id)
        ->where('tahun', \Carbon\Carbon::parse($cuti->tanggal_mulai)->year)
        ->first();
    $tahun = \Carbon\Carbon::parse($cuti->tanggal_mulai)->year;
    $sisaCuti = $saldo->sisa_hari ?? 0;

    // Jenis cuti
    $jenisCutiLabel = match($cuti->jenis_cuti) {
        'tahunan' => 'Cuti Tahunan',
        'sakit' => 'Cuti Sakit',
        'melahirkan' => 'Cuti Melahirkan',
        'khusus' => 'Cuti Khusus',
        'lainnya' => 'Cuti Lainnya',
        default => ucfirst($cuti->jenis_cuti),
    };

    $isTahunan = $cuti->jenis_cuti === 'tahunan';
    $isSakit = $cuti->jenis_cuti === 'sakit';
    $isBesar = $cuti->jenis_cuti === 'besar';
    $isKhusus = in_array($cuti->jenis_cuti, ['khusus', 'melahirkan']);

    // ==================== AMBIL DATA APPROVALS ====================
    $approvals = $cuti->approvals->sortBy('level')->values();

    // Approver 1 = pengganti
    $approver1 = $approvals->get(0);
    $approver1Pegawai = $approver1 ? $approver1->approver : null;

    // Approver selanjutnya (dari level 2 dst)
    $approverSelanjutnya = $approvals->slice(1);
@endphp

<table class="wrapper-table">
    <tr>
        <td>

            {{-- ==================== HEADER ==================== --}}
            <div class="header-form">
                @if($logoData)
                    <img src="{{ $logoData }}" alt="Logo" class="logo">
                @endif
                <h1>{{ $bpr->nama_bpr ?? 'PT. BPRS AMANAH BANGSA' }}</h1>
                @if($bpr && $bpr->alamat)
                    <p>{{ $bpr->alamat }}</p>
                @endif
            </div>

            <div class="form-title">Formulir Permohonan Cuti</div>

            {{-- ==================== DATA PEMOHON ==================== --}}
            <table class="data-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="titik">:</td>
                    <td class="value">{{ strtoupper($cuti->pegawai->nama ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="label">Mulai Bekerja</td>
                    <td class="titik">:</td>
                    <td>{{ $cuti->pegawai->tanggal_masuk ? \Carbon\Carbon::parse($cuti->pegawai->tanggal_masuk)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Bagian</td>
                    <td class="titik">:</td>
                    <td>{{ $cuti->pegawai->jabatan->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="titik">:</td>
                    <td>{{ $cuti->pegawai->jabatan->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat Selama Cuti</td>
                    <td class="titik">:</td>
                    <td>{{ $cuti->pegawai->alamat ?? '-' }}</td>
                </tr>
            </table>

            {{-- ==================== BOX CUTI ==================== --}}
            <div class="cuti-box">
                <div class="row">
                    <strong>Jumlah Hari (Kerja) Cuti</strong>:
                    {{ $cuti->lama_hari }} Hari
                    ( {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d F Y') }}
                    @if($cuti->tanggal_mulai != $cuti->tanggal_selesai)
                        s.d. {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d F Y') }}
                    @endif
                    )
                </div>
                <div class="row">
                    <strong>Masuk Kerja Setelah Cuti</strong>:
                    {{ $cuti->tanggal_masuk_kerja ? \Carbon\Carbon::parse($cuti->tanggal_masuk_kerja)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            {{-- ==================== JENIS CUTI ==================== --}}
            <div class="checkbox-group">
                <div style="font-weight:bold; margin-bottom:6px;">Jenis Cuti :</div>
                <span class="checkbox-item">
                    <span class="box {{ $isTahunan ? 'checked' : '' }}">{!! $isTahunan ? 'X' : '&nbsp;' !!}</span>
                    a). Cuti Tahunan
                </span>
                <span class="checkbox-item">
                    <span class="box {{ $isSakit ? 'checked' : '' }}">{!! $isSakit ? 'X' : '&nbsp;' !!}</span>
                    b). Cuti Sakit
                </span>
                <span class="checkbox-item">
                    <span class="box {{ $isBesar ? 'checked' : '' }}">{!! $isBesar ? 'X' : '&nbsp;' !!}</span>
                    c). Cuti Besar
                </span>
                <span class="checkbox-item">
                    <span class="box {{ $isKhusus ? 'checked' : '' }}">{!! $isKhusus ? 'X' : '&nbsp;' !!}</span>
                    d). Cuti Khusus / Lain-Lain
                </span>
                <div style="font-size:10px; font-style:italic; margin-top:4px; margin-left:18px;">
                    Seperti Cuti Menikah, Cuti Melahirkan, Cuti Hamil, Cuti Kematian Keluarga,
                    dan Cuti Lainnya yang Disetujui Bank.
                </div>
            </div>

            {{-- ==================== ALASAN CUTI ==================== --}}
            <div class="alasan-box">
                <strong>Alasan Cuti</strong>:
                {{ $cuti->keterangan ?? $jenisCutiLabel }}
            </div>

            {{-- ==================== VERIFIKASI PERSONALIA ==================== --}}
            <div class="sisa-info">
                <div class="title">Verifikasi Personalia</div>
                <table>
                    <tr>
                        <td>Sisa Cuti Tahun {{ $tahun }}</td>
                        <td>:</td>
                        <td><strong>{{ $sisaCuti }} hari</strong></td>
                    </tr>
                </table>
            </div>

            {{-- ==================== KETERANGAN CUTI + TTD ==================== --}}
            <table style="width:100%; border-collapse:collapse; margin-top:12px;">
                <tr>
                    <td style="width:50%; vertical-align:top; padding-right:16px;">
                        <div class="sisa-info" style="background:#fff;">
                            <div class="title">Dengan demikian cuti yang berhak digunakan adalah sebagai berikut :</div>
                            <table>
                                <tr>
                                    <td>Cuti Tahun {{ $tahun }}</td>
                                    <td>:</td>
                                    <td><strong>{{ str_pad($sisaCuti, 2, '0', STR_PAD_LEFT) }} Hari</strong></td>
                                </tr>
                                <tr>
                                    <td>Cuti yang Akan digunakan</td>
                                    <td>:</td>
                                    <td><strong>{{ str_pad($cuti->lama_hari, 2, '0', STR_PAD_LEFT) }} Hari</strong></td>
                                </tr>
                                <tr>
                                    <td>Sisa Cuti Tahun {{ $tahun }}</td>
                                    <td>:</td>
                                    <td><strong>{{ str_pad(max(0, $sisaCuti - $cuti->lama_hari), 2, '0', STR_PAD_LEFT) }} Hari</strong></td>
                                </tr>
                            </table>

                            {{-- ==================== PENGGANTI (APPROVER 1) — TANPA JABATAN ==================== --}}
                            @if($approver1Pegawai)
                            <div class="pengganti-info">
                                Selama cuti semua tugas digantikan oleh Sdr :
                                <strong>{{ $approver1Pegawai->nama ?? '-' }}</strong>
                            </div>
                            @endif
                        </div>

                        {{-- ==================== PEMOHON ==================== --}}
                        <div class="pemohon-block">
                            <div style="font-size:11px;">Sinaksak, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                            <div style="font-size:11px; margin-top:4px;">Pemohon</div>
                            <div class="ttd-space" style="height:70px; text-align:left;">
                                @if($ttdPemohon)
                                    <img src="{{ $ttdPemohon }}" class="ttd-img" alt="TTD Pemohon">
                                @endif
                            </div>
                            <div class="ttd-line" style="width:80%; margin:0 0 4px 0;"></div>
                            <div class="ttd-name" style="text-align:left;">{{ strtoupper($cuti->pegawai->nama ?? '-') }}</div>
                            <div class="ttd-jabatan" style="text-align:left;">{{ $cuti->pegawai->jabatan->nama ?? '' }}</div>
                        </div>
                    </td>

                    <td style="width:50%; vertical-align:top;">
                        {{-- ==================== APPROVAL BERANTAI ==================== --}}
                        <div style="text-align:center; font-size:11px; margin-bottom:8px;">
                            Diketahui / Menyetujui :
                        </div>

                        {{-- ==================== APPROVER 1: PENGGANTI (TANPA JABATAN) ==================== --}}
                        @if($approver1Pegawai)
                            @php
                                $ttdA1 = $getTtd($approver1Pegawai);
                            @endphp
                            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                                <tr>
                                    <td style="text-align:center; vertical-align:top; padding:6px;">
                                        <div class="ttd-space" style="height:60px;">
                                            @if($ttdA1)
                                                <img src="{{ $ttdA1 }}" class="ttd-img" alt="TTD Pengganti">
                                            @endif
                                        </div>
                                        <div class="ttd-line"></div>
                                        <div class="ttd-name">{{ strtoupper($approver1Pegawai->nama ?? '-') }}</div>
                                        {{-- ❌ TIDAK ADA JABATAN di sini --}}
                                    </td>
                                </tr>
                            </table>
                        @endif

                        {{-- ==================== APPROVER 2+: DENGAN JABATAN ==================== --}}
                        @if($approverSelanjutnya->count() > 0)
                            <table class="ttd-grid">
                                @foreach($approverSelanjutnya->chunk(2) as $chunk)
                                <tr>
                                    @foreach($chunk as $approval)
                                        @php
                                            $pgw = $approval->approver;
                                            $ttdImg = $getTtd($pgw);
                                        @endphp
                                        <td style="width:50%;">
                                            <div class="ttd-space">
                                                @if($ttdImg)
                                                    <img src="{{ $ttdImg }}" class="ttd-img" alt="TTD">
                                                @endif
                                            </div>
                                            <div class="ttd-line"></div>
                                            <div class="ttd-name">{{ strtoupper($pgw->nama ?? '-') }}</div>
                                            <div class="ttd-jabatan">{{ $pgw->jabatan->nama ?? '' }}</div>
                                        </td>
                                    @endforeach
                                    {{-- Padding kalau ganjil --}}
                                    @if($chunk->count() < 2)
                                        <td style="width:50%;"></td>
                                    @endif
                                </tr>
                                @endforeach
                            </table>
                        @endif

                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>