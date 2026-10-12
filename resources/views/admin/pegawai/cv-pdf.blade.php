<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CV - {{ $pegawai->nama }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .wrapper-table { width: 100%; border-collapse: collapse; }
        .wrapper-table > tbody > tr > td { padding: 1.5cm; vertical-align: top; }

        .header { text-align: center; border-bottom: 3px double #1F4E79; padding-bottom: 8px; margin-bottom: 12px; }
        .header .logo { max-height: 50px; margin-bottom: 4px; }
        .header h1 { font-size: 16px; font-weight: bold; color: #1F4E79; text-transform: uppercase; }
        .header p { font-size: 10px; color: #555; }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1F4E79;
            background: #f0f4f8;
            padding: 4px 8px;
            margin: 12px 0 6px 0;
            border-left: 4px solid #1F4E79;
        }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .data-table td { padding: 3px 5px; font-size: 10px; vertical-align: top; }
        .data-table .label { width: 130px; font-weight: bold; color: #555; }

        table.list-table { width: 100%; border-collapse: collapse; margin: 4px 0 8px 0; }
        .list-table th, .list-table td { border: 1px solid #999; padding: 3px 5px; font-size: 9px; }
        .list-table th { background: #1F4E79; color: #fff; font-weight: bold; text-align: center; }

        .anak-block { border: 1px solid #ccc; padding: 6px 8px; margin-bottom: 6px; }
        .anak-title { font-weight: bold; color: #1F4E79; margin-bottom: 4px; }

        .ttd-block { margin-top: 20px; text-align: right; font-size: 10px; }
        .ttd-image { max-height: 70px; margin: 6px 0; }
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

                // Foto Profil
                $fotoData = null;
                if ($pegawai->foto_profil) {
                    $p = storage_path('app/public/' . $pegawai->foto_profil);
                    if (file_exists($p)) {
                        $e = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                        if ($e === 'jpg') $e = 'jpeg';
                        $fotoData = 'data:image/' . $e . ';base64,' . base64_encode(file_get_contents($p));
                    }
                }

                // TTD
                $ttdData = null;
                if ($pegawai->ttd_image) {
                    $p = storage_path('app/public/' . $pegawai->ttd_image);
                    if (file_exists($p)) {
                        $e = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                        if ($e === 'jpg') $e = 'jpeg';
                        $ttdData = 'data:image/' . $e . ';base64,' . base64_encode(file_get_contents($p));
                    }
                }

                // Posisi crop
                $fotoCx = ($pegawai->foto_crop_x ?? 0.5) * 100;
                $fotoCy = ($pegawai->foto_crop_y ?? 0.5) * 100;
                $fotoCz = $pegawai->foto_crop_zoom ?? 1;
                $fotoCr = $pegawai->foto_crop_rotate ?? 0;
            @endphp            {{-- HEADER --}}
            <div class="header">
                @if($logoData)
                    <img src="{{ $logoData }}" alt="Logo" class="logo">
                @endif
                <h1>{{ $bpr->nama_bpr ?? 'BPRS AMANAH BANGSA' }}</h1>
                <p>Curriculum Vitae Pegawai</p>
            </div>

            {{-- A. DATA PRIBADI + FOTO --}}
            <div class="section-title">A. DATA PRIBADI</div>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    @if($fotoData)
                    <td style="width: 130px; vertical-align: top; padding-right: 12px;">
                        <div style="width: 110px; height: 140px; overflow: hidden; border: 1px solid #999; border-radius: 4px;">
                            <img src="{{ $fotoData }}"
                                 style="width: 100%; height: 100%; object-fit: cover;
                                        object-position: {{ $fotoCx }}% {{ $fotoCy }}%;
                                        transform: scale({{ $fotoCz }}) rotate({{ $fotoCr }}deg);">
                        </div>
                    </td>
                    @endif

                    <td style="vertical-align: top;">
                        <table class="data-table">
                            <tr><td class="label">Nama Lengkap</td><td>: <strong>{{ strtoupper($pegawai->nama) }}</strong></td></tr>
                            <tr><td class="label">NIP</td><td>: {{ $pegawai->nip ?? '-' }}</td></tr>
                            <tr><td class="label">Jabatan</td><td>: {{ $pegawai->jabatan->nama ?? '-' }}</td></tr>
                            <tr><td class="label">Kantor</td><td>: {{ $pegawai->kantor->nama ?? '-' }}</td></tr>
                            <tr><td class="label">Jenis Kelamin</td><td>: {{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                            <tr><td class="label">Tempat, Tgl Lahir</td><td>: {{ $pegawai->tempat_lahir ?? '-' }}, {{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('d F Y') : '-' }}</td></tr>
                            <tr><td class="label">Alamat</td><td>: {{ $pegawai->alamat ?? '-' }}</td></tr>
                            <tr><td class="label">No. Telepon</td><td>: {{ $pegawai->no_hp ?? '-' }}</td></tr>
                            <tr><td class="label">Email</td><td>: {{ $pegawai->email ?? '-' }}</td></tr>
                            <tr><td class="label">Mulai Bekerja</td><td>: {{ $pegawai->tanggal_masuk ? $pegawai->tanggal_masuk->format('d F Y') : '-' }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- B. PENDIDIKAN PEGAWAI --}}
            @if($pegawai->pendidikan->count() > 0)
            <div class="section-title">B. RIWAYAT PENDIDIKAN</div>
            <table class="list-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Jenjang</th>
                        <th width="30%">Nama Sekolah</th>
                        <th width="20%">Jurusan</th>
                        <th width="15%">Kota</th>
                        <th width="15%">Tahun</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawai->pendidikan as $i => $p)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td>{{ $p->jenjang }}</td>
                        <td>{{ $p->nama_sekolah }}</td>
                        <td>{{ $p->jurusan ?? '-' }}</td>
                        <td>{{ $p->kota ?? '-' }}</td>
                        <td style="text-align: center;">{{ $p->tahun_masuk ?? '-' }} - {{ $p->tahun_lulus ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            {{-- C. KELUARGA --}}
            @if($pegawai->keluarga->count() > 0)
            <div class="section-title">C. DATA KELUARGA</div>
            @foreach($pegawai->keluarga as $k)
            <table class="data-table">
                <tr><td class="label">Status</td><td>: {{ ucfirst($k->hubungan) }}</td></tr>
                <tr><td class="label">Nama</td><td>: <strong>{{ strtoupper($k->nama) }}</strong></td></tr>
                <tr><td class="label">NIK</td><td>: {{ $k->nik ?? '-' }}</td></tr>
                <tr><td class="label">Tempat, Tgl Lahir</td><td>: {{ $k->tempat_lahir ?? '-' }}, {{ $k->tanggal_lahir ? $k->tanggal_lahir->format('d F Y') : '-' }}</td></tr>
                <tr><td class="label">Pendidikan</td><td>: {{ $k->pendidikan_terakhir ?? '-' }}</td></tr>
                <tr><td class="label">Pekerjaan</td><td>: {{ $k->pekerjaan ?? '-' }}</td></tr>
                <tr><td class="label">No. Telp</td><td>: {{ $k->no_telp ?? '-' }}</td></tr>
            </table>

            @if($k->pendidikan->count() > 0)
            <table class="list-table" style="margin-top: 4px;">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Jenjang</th>
                        <th width="35%">Nama Sekolah</th>
                        <th width="20%">Jurusan</th>
                        <th width="15%">Kota</th>
                        <th width="10%">Tahun</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($k->pendidikan as $i => $p)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td>{{ $p->jenjang }}</td>
                        <td>{{ $p->nama_sekolah }}</td>
                        <td>{{ $p->jurusan ?? '-' }}</td>
                        <td>{{ $p->kota ?? '-' }}</td>
                        <td style="text-align: center;">{{ $p->tahun_masuk ?? '-' }} - {{ $p->tahun_lulus ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            @endforeach
            @endif

            {{-- D. ANAK --}}
            @if($pegawai->anak->count() > 0)
            <div class="section-title">D. DATA ANAK</div>
            @foreach($pegawai->anak as $a)
            <div class="anak-block">
                <div class="anak-title">Anak ke-{{ $a->urutan_anak }}: {{ strtoupper($a->nama) }}</div>
                <table class="data-table">
                    <tr><td class="label">NIK</td><td>: {{ $a->nik ?? '-' }}</td></tr>
                    <tr><td class="label">Tempat, Tgl Lahir</td><td>: {{ $a->tempat_lahir ?? '-' }}, {{ $a->tanggal_lahir ? $a->tanggal_lahir->format('d F Y') : '-' }}</td></tr>
                    <tr><td class="label">Jenis Kelamin</td><td>: {{ $a->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                    <tr><td class="label">Status</td><td>: {{ ucfirst($a->status_anak ?? '-') }}</td></tr>
                </table>

                @if($a->pendidikan->count() > 0)
                <table class="list-table" style="margin-top: 4px;">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Jenjang</th>
                            <th width="40%">Nama Sekolah</th>
                            <th width="20%">Kota</th>
                            <th width="20%">Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($a->pendidikan as $i => $p)
                        <tr>
                            <td style="text-align: center;">{{ $i + 1 }}</td>
                            <td>{{ $p->jenjang }}</td>
                            <td>{{ $p->nama_sekolah }}</td>
                            <td>{{ $p->kota ?? '-' }}</td>
                            <td style="text-align: center;">{{ $p->tahun_masuk ?? '-' }} - {{ $p->tahun_lulus ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            @endforeach
            @endif

            {{-- TTD OTOMATIS --}}
            <div class="ttd-block">
                <p>Sinaksak, {{ date('d F Y') }}</p>

                @if($ttdData)
                    <img src="{{ $ttdData }}" alt="TTD" class="ttd-image">
                @else
                    <p style="margin-top: 40px;">&nbsp;</p>
                @endif

                <p><strong>{{ strtoupper($pegawai->nama) }}</strong></p>
                <p style="font-size: 9px; color: #555;">{{ $pegawai->jabatan->nama ?? '' }}</p>
            </div>

        </td>
    </tr>
</table>

</body>
</html>