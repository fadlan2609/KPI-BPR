<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Reward & Punishment - {{ $pegawai->nama }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Times New Roman", serif; font-size: 10pt; color: #000; }
        .wrapper { width: 100%; border-collapse: collapse; }
        .wrapper > tbody > tr > td { padding: 1.5cm; vertical-align: top; }
        table { border-collapse: collapse; width: 100%; }
        .kop { border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 12px; }
        .kop-logo { width: 80px; text-align: center; }
        .kop-logo img { width: 70px; height: 70px; object-fit: contain; }
        .kop-text { text-align: center; }
        .kop-text .nama { font-size: 15pt; font-weight: bold; text-transform: uppercase; }
        .kop-text .alamat { font-size: 9pt; margin-top: 3px; }
        .judul { text-align: center; margin: 15px 0; }
        .judul h1 { font-size: 14pt; font-weight: bold; text-decoration: underline; }
        .info td { padding: 2px 4px; font-size: 10pt; }
        .info .label { width: 130px; font-weight: bold; }
        .section-title { font-size: 11pt; font-weight: bold; background: #1F4E79; color: white; padding: 4px 10px; margin: 12px 0 6px; }
        .data-table { width: 100%; margin-bottom: 10px; }
        .data-table th { background: #e8f0fe; padding: 5px; border: 1px solid #999; font-size: 9pt; text-align: left; }
        .data-table td { padding: 4px 5px; border: 1px solid #ccc; font-size: 9.5pt; }
        .badge { display: inline-block; padding: 1px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; }
        .badge-reward { background: #dcfce7; color: #166534; }
        .badge-punishment { background: #fee2e2; color: #991b1b; }
        .ttd-wrapper { margin-top: 30px; page-break-inside: avoid; }
        .ttd-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 10px; }
        .ttd-space { height: 60px; }
        .ttd-img { height: 55px; }
        .footer { margin-top: 20px; text-align: center; font-size: 8pt; color: #888; padding-top: 5px; border-top: 1px solid #ccc; }
    </style>
</head>
<body>
<table class="wrapper">
    <tr>
        <td>
            @php
                $logoData = null;
                if ($bpr && $bpr->logo) {
                    $p = storage_path('app/public/' . $bpr->logo);
                    if (file_exists($p)) {
                        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                        if ($ext === 'jpg') $ext = 'jpeg';
                        $logoData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($p));
                    }
                }

                $ttdPegawaiData = null;
                if ($pegawai->ttd_image) {
                    $p = storage_path('app/public/' . $pegawai->ttd_image);
                    if (file_exists($p)) {
                        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                        if ($ext === 'jpg') $ext = 'jpeg';
                        $ttdPegawaiData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($p));
                    }
                }

                $ttdPimpinanData = null;
                $ttdPimpinanNama = $bpr->ttd_nama ?? null;
                $ttdPimpinanJabatan = $bpr->ttd_jabatan ?? null;

                // Cari atasan penilai
                $atasanPenilai = null;
                if ($pegawai->atasanLangsung && $pegawai->atasanLangsung->jabatan) {
                    $jabatanAtasan = $pegawai->atasanLangsung->jabatan->atasan;
                    if ($jabatanAtasan) {
                        $atasanPenilai = \App\Models\Pegawai::where('jabatan_id', $jabatanAtasan->id)->where('status', 'aktif')->first();
                    }
                }
                if ($atasanPenilai && $atasanPenilai->ttd_image) {
                    $p = storage_path('app/public/' . $atasanPenilai->ttd_image);
                    if (file_exists($p)) {
                        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                        if ($ext === 'jpg') $ext = 'jpeg';
                        $ttdPimpinanData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($p));
                        $ttdPimpinanNama = $atasanPenilai->nama;
                        $ttdPimpinanJabatan = $atasanPenilai->jabatan->nama ?? null;
                    }
                }
                if (!$ttdPimpinanData && $bpr && $bpr->ttd_image) {
                    $p = storage_path('app/public/' . $bpr->ttd_image);
                    if (file_exists($p)) {
                        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                        if ($ext === 'jpg') $ext = 'jpeg';
                        $ttdPimpinanData = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($p));
                    }
                }
                if (!$ttdPimpinanNama) $ttdPimpinanNama = '____________________';
                if (!$ttdPimpinanJabatan) $ttdPimpinanJabatan = 'Pimpinan';
            @endphp

            {{-- KOP --}}
            <div class="kop">
                <table>
                    <tr>
                        <td class="kop-logo">@if($logoData)<img src="{{ $logoData }}">@endif</td>
                        <td class="kop-text">
                            <div class="nama">{{ $bpr->nama_bpr ?? 'BPRS AMANAH BANGSA' }}</div>
                            <div class="alamat">{{ $bpr->alamat ?? '' }}</div>
                            @if($bpr && $bpr->no_telp)<div class="alamat">Telp: {{ $bpr->no_telp }}</div>@endif
                        </td>
                        <td style="width: 80px;"></td>
                    </tr>
                </table>
            </div>

            {{-- JUDUL --}}
            <div class="judul">
                <h1>LAPORAN REWARD & PUNISHMENT</h1>
            </div>

            {{-- INFO PEGAWAI --}}
            <table class="info" style="margin-bottom: 15px;">
                <tr><td class="label">Nama</td><td>: <strong>{{ $pegawai->nama }}</strong></td></tr>
                <tr><td class="label">NIP</td><td>: {{ $pegawai->nip }}</td></tr>
                <tr><td class="label">Jabatan</td><td>: {{ $pegawai->jabatan->nama ?? '-' }}</td></tr>
                <tr><td class="label">Kantor</td><td>: {{ $pegawai->kantor->nama ?? '-' }}</td></tr>
            </table>

            {{-- RINGKASAN --}}
            <table style="margin-bottom: 12px; border: 2px solid #1F4E79;">
                <tr>
                    <td style="padding: 8px; text-align: center; background: #e8f0fe; width: 33.33%;">
                        <div style="font-size: 9pt; color: #555;">TOTAL REWARD</div>
                        <div style="font-size: 16pt; font-weight: bold; color: #166534;">{{ $stats['total_reward'] }}</div>
                    </td>
                    <td style="padding: 8px; text-align: center; background: #e8f0fe; width: 33.33%;">
                        <div style="font-size: 9pt; color: #555;">TOTAL PUNISHMENT</div>
                        <div style="font-size: 16pt; font-weight: bold; color: #991b1b;">{{ $stats['total_punishment'] }}</div>
                    </td>
                    <td style="padding: 8px; text-align: center; background: #e8f0fe;">
                        <div style="font-size: 9pt; color: #555;">TOTAL NOMINAL</div>
                        <div style="font-size: 11pt; font-weight: bold; color: #1F4E79;">
                            Rp {{ number_format($stats['total_nominal_reward'] + $stats['total_nominal_punishment'], 0, ',', '.') }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- TABEL DATA --}}
            <div class="section-title">DAFTAR REWARD & PUNISHMENT</div>
            @if($data->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th width="80">Kategori</th>
                            <th>Tipe</th>
                            <th>Judul</th>
                            <th width="70">Tanggal</th>
                            <th width="90">Nominal</th>
                            <th width="70">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $item)
                            <tr>
                                <td style="text-align: center;">{{ $i + 1 }}</td>
                                <td style="text-align: center;">
                                    <span class="badge {{ $item->kategori === 'reward' ? 'badge-reward' : 'badge-punishment' }}">
                                        {{ $item->kategori === 'reward' ? 'Reward' : 'Punishment' }}
                                    </span>
                                </td>
                                <td>{{ $item->tipe_label }}</td>
                                <td>
                                    <strong>{{ $item->judul }}</strong>
                                    @if($item->keterangan)
                                        <div style="font-size: 8.5pt; color: #666; font-style: italic;">{{ $item->keterangan }}</div>
                                    @endif
                                </td>
                                <td style="text-align: center;">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td style="text-align: right;">{{ $item->nominal_format ?? '-' }}</td>
                                <td style="text-align: center;">{{ $item->status_color['label'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; padding: 20px; color: #999;">Belum ada data</p>
            @endif

            {{-- TTD --}}
            <div class="ttd-wrapper">
                <table class="ttd-table">
                    <tr>
                        <td>
                            <div>Pegawai yang bersangkutan,</div>
                            <div class="ttd-space">
                                @if($ttdPegawaiData)<img src="{{ $ttdPegawaiData }}" class="ttd-img">@endif
                            </div>
                            <div style="font-weight: bold; text-decoration: underline;">{{ $pegawai->nama }}</div>
                            <div style="font-size: 9pt;">NIP: {{ $pegawai->nip }}</div>
                        </td>
                        <td>
                            <div>{{ date('d F Y') }}</div>
                            <div>Pimpinan,</div>
                            <div class="ttd-space">
                                @if($ttdPimpinanData)<img src="{{ $ttdPimpinanData }}" class="ttd-img">@endif
                            </div>
                            <div style="font-weight: bold; text-decoration: underline;">{{ $ttdPimpinanNama }}</div>
                            <div style="font-size: 9pt;">{{ $ttdPimpinanJabatan }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                Dicetak dari Sistem KPI {{ $bpr->nama_bpr ?? 'BPRS Amanah Bangsa' }} pada {{ date('d/m/Y H:i') }}
            </div>
        </td>
    </tr>
</table>
</body>
</html>