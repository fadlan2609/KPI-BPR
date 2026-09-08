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
            max-height: 55px;
            margin-bottom: 5px;
        }
        .header h1 {
            font-size: 15px;
            margin: 2px 0;
            color: #1F4E79;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            color: #555;
            font-size: 10px;
        }
        .header .info {
            margin-top: 5px;
            font-size: 9px;
        }
        .header .info span {
            margin: 0 6px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1F4E79;
            margin: 10px 0 5px 0;
            padding-bottom: 2px;
            border-bottom: 1px solid #1F4E79;
        }
        .sub-section {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin: 6px 0 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            font-size: 8px;
        }
        th, td {
            border: 1px solid #999;
            padding: 2px 4px;
            text-align: left;
        }
        th {
            background-color: #1F4E79;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-green { color: #2E7D32; }
        .text-blue { color: #1565C0; }
        .text-yellow { color: #E65100; }
        .text-purple { color: #6A1B9A; }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
            margin: 8px 0;
        }
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            text-align: center;
            background: #f9f9f9;
        }
        .summary-card .label {
            font-size: 7px;
            color: #666;
        }
        .summary-card .value {
            font-size: 13px;
            font-weight: bold;
        }
        .bg-blue { background: #e8f0fe; }
        .bg-green { background: #e8f5e9; }
        .bg-yellow { background: #fff8e1; }
        .bg-purple { background: #f3e5f5; }
        .badge {
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: bold;
            display: inline-block;
        }
        .penilai-info {
            font-size: 8px;
            color: #555;
            margin-bottom: 3px;
            padding: 2px 6px;
            background: #f5f5f5;
            border-radius: 3px;
            display: inline-block;
        }
        .level-badge {
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: bold;
            display: inline-block;
            margin-left: 5px;
        }
        .level-self { background: #e3f2fd; color: #1565C0; }
        .level-atasan { background: #e8f5e9; color: #2E7D32; }
        .level-penilai { background: #fff3e0; color: #E65100; }

        .ttd-wrapper {
            margin-top: 25px;
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
            padding: 8px 12px;
            vertical-align: top;
        }
        .ttd-table .ttd-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 3px;
        }
        .ttd-table .ttd-sub {
            font-size: 9px;
            color: #555;
            margin-bottom: 15px;
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
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 7px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
        .no-data {
            color: #999;
            font-style: italic;
            font-size: 8px;
            padding: 5px;
        }
        .ttd-hide {
            display: none;
        }
    </style>
</head>
<body>

    <!-- ==================== HEADER ==================== -->
    <div class="header">
        @php
            use App\Helpers\AppHelper;
            $logoBase64 = AppHelper::getLogoBase64();
        @endphp
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
        @else
            <h1 style="font-size:14px; color:#1F4E79;">{{ AppHelper::getBprName() }}</h1>
        @endif
        <h1>LAPORAN PENILAIAN KINERJA</h1>
        <p>{{ $bpr->nama_bpr ?? AppHelper::getBprName() }}</p>
        <div class="info">
            <span><strong>Periode:</strong> {{ $periode->nama }}</span>
            <span><strong>Pegawai:</strong> {{ $pegawai->nama }}</span>
            <span><strong>NIP:</strong> {{ $pegawai->nip }}</span>
            <span><strong>Jabatan:</strong> {{ $pegawai->jabatan->nama ?? '-' }}</span>
        </div>
        <div class="info">
            <span><strong>Tanggal Cetak:</strong> {{ date('d F Y H:i') }}</span>
        </div>
    </div>

    <!-- ==================== RINGKASAN ==================== -->
    <div class="summary-grid">
        <div class="summary-card bg-blue">
            <div class="label">Self Assessment</div>
            <div class="value text-blue">{{ $hasil->nilai_self ?? 0 }}</div>
        </div>
        <div class="summary-card bg-green">
            <div class="label">Atasan Langsung</div>
            <div class="value text-green">{{ $hasil->nilai_atasan_langsung ?? 0 }}</div>
        </div>
        <div class="summary-card bg-yellow">
            <div class="label">Atasan Penilai</div>
            <div class="value text-yellow">{{ $hasil->nilai_atasan_penilai ?? 0 }}</div>
        </div>
        <div class="summary-card bg-purple">
            <div class="label">NILAI AKHIR</div>
            <div class="value text-purple">{{ $hasil->nilai_akhir }}</div>
            <div style="margin-top:2px;">
                <span class="badge" style="color: {{ $hasil->predikat->warna_text ?? '#000' }}; background-color: {{ $hasil->predikat->warna_latar ?? '#ccc' }}">
                    {{ $hasil->predikat->nama ?? '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- ==================== LEVEL 1: SELF ASSESSMENT ==================== -->
    <div class="section-title">
        A. SELF ASSESSMENT
        <span class="level-badge level-self">Diri Sendiri</span>
    </div>
    <div class="penilai-info">
        <strong>Penilai:</strong> {{ $pegawai->nama }} | 
        <strong>Nilai Total:</strong> {{ $penilaian['self']->nilai_total ?? 0 }}
    </div>

    @php
        $detailSelf = isset($penilaian['self']) ? json_decode($penilaian['self']->detail_indikator, true) : null;
        $indikatorKPI = \App\Models\IndikatorKPI::where('jabatan_id', $pegawai->jabatan_id)->get();
        $indikatorKompetensi = \App\Models\IndikatorKompetensi::where('jabatan_id', $pegawai->jabatan_id)->get();
        $indikatorCore = \App\Models\IndikatorCoreValue::where('jabatan_id', $pegawai->jabatan_id)->get();
    @endphp

    @if($detailSelf)
        @if(isset($detailSelf['kpi']) && count($detailSelf['kpi']) > 0)
        <div class="sub-section">KPI</div>
        <table>
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="35%">Indikator</th>
                    <th width="10%">Target</th>
                    <th width="8%">Bobot</th>
                    <th width="8%">Nilai</th>
                    <th width="35%">Komentar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailSelf['kpi'] as $index => $nilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $indikatorKPI[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                    <td class="text-center">{{ $indikatorKPI[$index]->target ?? '-' }} {{ $indikatorKPI[$index]->satuan ?? '' }}</td>
                    <td class="text-center">{{ $indikatorKPI[$index]->bobot ?? 0 }}%</td>
                    <td class="text-center text-bold">{{ $nilai }}</td>
                    <td>{{ $detailSelf['komentar_kpi'][$index] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(isset($detailSelf['kompetensi']) && count($detailSelf['kompetensi']) > 0)
        <div class="sub-section">Kompetensi</div>
        <table>
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="45%">Indikator</th>
                    <th width="10%">Skala</th>
                    <th width="8%">Bobot</th>
                    <th width="8%">Nilai</th>
                    <th width="25%">Komentar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailSelf['kompetensi'] as $index => $nilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $indikatorKompetensi[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                    <td class="text-center">1-{{ $indikatorKompetensi[$index]->skala_maksimal ?? 5 }}</td>
                    <td class="text-center">{{ $indikatorKompetensi[$index]->bobot ?? 0 }}%</td>
                    <td class="text-center text-bold">{{ $nilai }}</td>
                    <td>{{ $detailSelf['komentar_kompetensi'][$index] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(isset($detailSelf['core_values']) && count($detailSelf['core_values']) > 0)
        <div class="sub-section">Core Values</div>
        <table>
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="45%">Indikator</th>
                    <th width="10%">Skala</th>
                    <th width="8%">Bobot</th>
                    <th width="8%">Nilai</th>
                    <th width="25%">Komentar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailSelf['core_values'] as $index => $nilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $indikatorCore[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                    <td class="text-center">1-{{ $indikatorCore[$index]->skala_maksimal ?? 5 }}</td>
                    <td class="text-center">{{ $indikatorCore[$index]->bobot ?? 0 }}%</td>
                    <td class="text-center text-bold">{{ $nilai }}</td>
                    <td>{{ $detailSelf['komentar_core_values'][$index] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @else
        <p class="no-data">Tidak ada data Self Assessment</p>
    @endif

    <!-- ==================== LEVEL 2: ATASAN LANGSUNG ==================== -->
    <div class="section-title">
        B. ATASAN LANGSUNG
        <span class="level-badge level-atasan">Atasan Langsung</span>
    </div>
    @if(isset($penilaian['atasan_langsung']))
        <div class="penilai-info">
            <strong>Penilai:</strong> {{ $penilaian['atasan_langsung']->penilai->nama ?? '-' }} |
            <strong>Nilai Total:</strong> {{ $penilaian['atasan_langsung']->nilai_total ?? 0 }}
        </div>

        @php
            $detailAtasan = json_decode($penilaian['atasan_langsung']->detail_indikator, true);
        @endphp

        @if($detailAtasan)
            @if(isset($detailAtasan['kpi']) && count($detailAtasan['kpi']) > 0)
            <div class="sub-section">KPI</div>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="35%">Indikator</th>
                        <th width="10%">Target</th>
                        <th width="8%">Bobot</th>
                        <th width="8%">Nilai</th>
                        <th width="35%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailAtasan['kpi'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikatorKPI[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">{{ $indikatorKPI[$index]->target ?? '-' }} {{ $indikatorKPI[$index]->satuan ?? '' }}</td>
                        <td class="text-center">{{ $indikatorKPI[$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detailAtasan['komentar_kpi'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if(isset($detailAtasan['kompetensi']) && count($detailAtasan['kompetensi']) > 0)
            <div class="sub-section">Kompetensi</div>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="45%">Indikator</th>
                        <th width="10%">Skala</th>
                        <th width="8%">Bobot</th>
                        <th width="8%">Nilai</th>
                        <th width="25%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailAtasan['kompetensi'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikatorKompetensi[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">1-{{ $indikatorKompetensi[$index]->skala_maksimal ?? 5 }}</td>
                        <td class="text-center">{{ $indikatorKompetensi[$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detailAtasan['komentar_kompetensi'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if(isset($detailAtasan['core_values']) && count($detailAtasan['core_values']) > 0)
            <div class="sub-section">Core Values</div>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="45%">Indikator</th>
                        <th width="10%">Skala</th>
                        <th width="8%">Bobot</th>
                        <th width="8%">Nilai</th>
                        <th width="25%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailAtasan['core_values'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikatorCore[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">1-{{ $indikatorCore[$index]->skala_maksimal ?? 5 }}</td>
                        <td class="text-center">{{ $indikatorCore[$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detailAtasan['komentar_core_values'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        @endif
    @else
        <p class="no-data">Belum ada penilaian dari Atasan Langsung</p>
    @endif

    <!-- ==================== LEVEL 3: ATASAN PENILAI (HANYA JIKA ADA) ==================== -->
    @if($hasAtasanPenilai)
    <div class="section-title">
        C. ATASAN PENILAI
        <span class="level-badge level-penilai">Atasan Penilai</span>
    </div>
    @if(isset($penilaian['atasan_penilai']))
        <div class="penilai-info">
            <strong>Penilai:</strong> {{ $penilaian['atasan_penilai']->penilai->nama ?? '-' }} |
            <strong>Nilai Total:</strong> {{ $penilaian['atasan_penilai']->nilai_total ?? 0 }}
        </div>

        @php
            $detailPenilai = json_decode($penilaian['atasan_penilai']->detail_indikator, true);
        @endphp

        @if($detailPenilai)
            @if(isset($detailPenilai['kpi']) && count($detailPenilai['kpi']) > 0)
            <div class="sub-section">KPI</div>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="35%">Indikator</th>
                        <th width="10%">Target</th>
                        <th width="8%">Bobot</th>
                        <th width="8%">Nilai</th>
                        <th width="35%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailPenilai['kpi'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikatorKPI[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">{{ $indikatorKPI[$index]->target ?? '-' }} {{ $indikatorKPI[$index]->satuan ?? '' }}</td>
                        <td class="text-center">{{ $indikatorKPI[$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detailPenilai['komentar_kpi'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if(isset($detailPenilai['kompetensi']) && count($detailPenilai['kompetensi']) > 0)
            <div class="sub-section">Kompetensi</div>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="45%">Indikator</th>
                        <th width="10%">Skala</th>
                        <th width="8%">Bobot</th>
                        <th width="8%">Nilai</th>
                        <th width="25%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailPenilai['kompetensi'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikatorKompetensi[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">1-{{ $indikatorKompetensi[$index]->skala_maksimal ?? 5 }}</td>
                        <td class="text-center">{{ $indikatorKompetensi[$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detailPenilai['komentar_kompetensi'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if(isset($detailPenilai['core_values']) && count($detailPenilai['core_values']) > 0)
            <div class="sub-section">Core Values</div>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="45%">Indikator</th>
                        <th width="10%">Skala</th>
                        <th width="8%">Bobot</th>
                        <th width="8%">Nilai</th>
                        <th width="25%">Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailPenilai['core_values'] as $index => $nilai)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikatorCore[$index]->indikator ?? 'Indikator ' . ($index + 1) }}</td>
                        <td class="text-center">1-{{ $indikatorCore[$index]->skala_maksimal ?? 5 }}</td>
                        <td class="text-center">{{ $indikatorCore[$index]->bobot ?? 0 }}%</td>
                        <td class="text-center text-bold">{{ $nilai }}</td>
                        <td>{{ $detailPenilai['komentar_core_values'][$index] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        @endif
    @else
        <p class="no-data">Belum ada penilaian dari Atasan Penilai</p>
    @endif
    @endif

    <!-- ==================== TTD (HANYA MENAMPILKAN YANG ADA) ==================== -->
    <div class="ttd-wrapper">
        <table class="ttd-table">
            <tr>
                <!-- TTD ATASAN LANGSUNG (SELALU ADA) -->
                <td>
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-sub">
                        @if($atasanLangsung)
                            {{ $atasanLangsung->nama }}<br>
                            <span style="font-size:8px; color:#888;">{{ $atasanLangsung->jabatan->nama ?? 'Atasan Langsung' }}</span>
                        @else
                            Atasan Langsung
                        @endif
                    </div>
                    <div class="ttd-space"></div>
                    <div class="ttd-line"></div>
                    <div class="ttd-name">(____________________)</div>
                    <div class="ttd-date">Tanggal: ________</div>
                </td>

                <!-- TTD ATASAN PENILAI (HANYA JIKA ADA) -->
                <td>
                    <div class="ttd-title">Mengetahui,</div>
                    <div class="ttd-sub">
                        @if($hasAtasanPenilai && $atasanPenilai)
                            {{ $atasanPenilai->nama }}<br>
                            <span style="font-size:8px; color:#888;">{{ $atasanPenilai->jabatan->nama ?? 'Atasan Penilai' }}</span>
                        @else
                            <span style="color:#999;">- Tidak Ada -</span>
                        @endif
                    </div>
                    <div class="ttd-space"></div>
                    <div class="ttd-line {{ !$hasAtasanPenilai ? 'ttd-hide' : '' }}"></div>
                    <div class="ttd-name {{ !$hasAtasanPenilai ? 'ttd-hide' : '' }}">(____________________)</div>
                    <div class="ttd-date {{ !$hasAtasanPenilai ? 'ttd-hide' : '' }}">Tanggal: ________</div>
                </td>

                <!-- TTD PEGAWAI YANG DINILAI -->
                <td>
                    <div class="ttd-title">Pegawai yang Dinilai,</div>
                    <div class="ttd-sub">{{ $pegawai->nama }}</div>
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