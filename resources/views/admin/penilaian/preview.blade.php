@extends('layouts.admin')

@section('title', 'Preview Penilaian')

@php
    // TTD PEGAWAI
    $ttdPegawaiData = null;
    if ($pegawai && $pegawai->ttd_image) {
        $p = storage_path('app/public/' . $pegawai->ttd_image);
        if (file_exists($p)) {
            $e = strtolower(pathinfo($p, PATHINFO_EXTENSION));
            if ($e === 'jpg') $e = 'jpeg';
            $ttdPegawaiData = 'data:image/' . $e . ';base64,' . base64_encode(file_get_contents($p));
        }
    }

    // TTD ATASAN LANGSUNG
    $ttdAtasanLangsungData = null;
    if ($atasanLangsung && $atasanLangsung->ttd_image) {
        $p = storage_path('app/public/' . $atasanLangsung->ttd_image);
        if (file_exists($p)) {
            $e = strtolower(pathinfo($p, PATHINFO_EXTENSION));
            if ($e === 'jpg') $e = 'jpeg';
            $ttdAtasanLangsungData = 'data:image/' . $e . ';base64,' . base64_encode(file_get_contents($p));
        }
    }

    // TTD ATASAN PENILAI
    $ttdAtasanPenilaiData = null;
    if ($atasanPenilai && $atasanPenilai->ttd_image) {
        $p = storage_path('app/public/' . $atasanPenilai->ttd_image);
        if (file_exists($p)) {
            $e = strtolower(pathinfo($p, PATHINFO_EXTENSION));
            if ($e === 'jpg') $e = 'jpeg';
            $ttdAtasanPenilaiData = 'data:image/' . $e . ';base64,' . base64_encode(file_get_contents($p));
        }
    }

    $showAtasanPenilai = $hasAtasanPenilai && $atasanPenilai;

    // Predikat
    $predikat = null;
    if ($hasil) {
        if ($hasil->predikat) {
            $predikat = $hasil->predikat;
        } elseif ($hasil->predikat_id) {
            $predikat = \App\Models\PredikatKinerja::find($hasil->predikat_id);
        } else {
            $predikat = \App\Models\PredikatKinerja::where('batas_bawah', '<=', $hasil->nilai_akhir)
                ->where('batas_atas', '>=', $hasil->nilai_akhir)->first();
        }
    }
@endphp

@section('content')
<style>
    .pv-wrap { max-width: 900px; margin: 0 auto; padding: 24px 16px; }
    .pv-head { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; margin-bottom: 24px; }
    .pv-head h1 { font-size: 22px; font-weight: 700; color: #111827; margin: 0; }
    .pv-head p { font-size: 13px; color: #6b7280; margin: 4px 0 0 0; }
    .pv-btn { display: inline-flex; align-items: center; padding: 9px 18px; font-size: 13px; font-weight: 600; border-radius: 6px; text-decoration: none; transition: all 0.15s; }
    .pv-btn-back { background: #fff; color: #374151; border: 1px solid #d1d5db; }
    .pv-btn-back:hover { background: #f9fafb; }
    .pv-btn-pdf { background: #b91c1c; color: #fff; border: none; }
    .pv-btn-pdf:hover { background: #991b1b; }

    .pv-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 20px; }
    .pv-card-head { padding: 12px 20px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 11px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.5px; }
    .pv-card-body { padding: 20px; }

    .pv-info-table { width: 100%; border-collapse: collapse; }
    .pv-info-table td { padding: 16px 20px; vertical-align: top; width: 50%; }
    .pv-info-table td:first-child { border-right: 1px solid #e5e7eb; }
    .pv-info-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .pv-info-value { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 2px; }
    .pv-info-sub { font-size: 13px; color: #6b7280; }

    .pv-nilai-table { width: 100%; border-collapse: collapse; }
    .pv-nilai-table td { padding: 20px 12px; text-align: center; vertical-align: middle; width: 25%; border-right: 1px solid #e5e7eb; }
    .pv-nilai-table td:last-child { border-right: none; }
    .pv-nilai-label { font-size: 11px; color: #6b7280; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.3px; }
    .pv-nilai-value { font-size: 22px; font-weight: 700; color: #111827; }
    .pv-nilai-akhir { background: #1e293b; }
    .pv-nilai-akhir .pv-nilai-label { color: #cbd5e1; }
    .pv-nilai-akhir .pv-nilai-value { color: #fff; font-size: 26px; }

    .pv-predikat-row { padding: 14px 20px; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
    .pv-predikat-label { font-size: 13px; color: #374151; font-weight: 500; }
    .pv-predikat-badge { display: inline-block; padding: 5px 14px; font-size: 12px; font-weight: 700; border-radius: 20px; }

    .pv-level-table { width: 100%; border-collapse: collapse; }
    .pv-level-table tr { border-bottom: 1px solid #f3f4f6; }
    .pv-level-table tr:last-child { border-bottom: none; }
    .pv-level-table td { padding: 14px 20px; vertical-align: middle; }
    .pv-level-name { font-size: 14px; font-weight: 600; color: #111827; }
    .pv-level-sub { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .pv-level-badge { display: inline-block; padding: 3px 10px; font-size: 11px; font-weight: 600; border-radius: 12px; background: #dcfce7; color: #15803d; }

    .pv-ttd-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .pv-ttd-table td { padding: 24px 16px; text-align: center; vertical-align: top; border-right: 1px solid #e5e7eb; }
    .pv-ttd-table td:last-child { border-right: none; }
    .pv-ttd-role { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .pv-ttd-sub { font-size: 11px; color: #9ca3af; margin-bottom: 12px; }
    .pv-ttd-img-box { height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; }
    .pv-ttd-img { max-height: 60px; max-width: 120px; object-fit: contain; }
    .pv-ttd-placeholder { color: #e5e7eb; font-size: 28px; }
    .pv-ttd-line { border-bottom: 1px solid #9ca3af; width: 80%; margin: 0 auto 6px auto; }
    .pv-ttd-name { font-size: 13px; font-weight: 700; color: #111827; }
    .pv-ttd-nip { font-size: 11px; color: #6b7280; margin-top: 2px; }

    .pv-empty { padding: 60px 20px; text-align: center; }
    .pv-empty-icon { font-size: 40px; color: #d1d5db; margin-bottom: 12px; }
    .pv-empty-title { font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 4px; }
    .pv-empty-sub { font-size: 13px; color: #6b7280; }
</style>

<div class="pv-wrap">

    {{-- HEADER --}}
    <div class="pv-head">
        <div>
            <h1>Preview Penilaian</h1>
            <p>Ringkasan hasil penilaian kinerja pegawai</p>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('admin.penilaian.cetak', ['periode' => $periode->id]) }}" class="pv-btn pv-btn-back">
                <i class="fas fa-arrow-left" style="margin-right:6px;"></i> Kembali
            </a>
            <a href="{{ route('admin.penilaian.preview-pdf', ['pegawai' => $pegawai->id, 'periode' => $periode->id]) }}"
               target="_blank" class="pv-btn pv-btn-pdf">
                <i class="fas fa-file-pdf" style="margin-right:6px;"></i> Buka PDF
            </a>
        </div>
    </div>

    {{-- INFO PEGAWAI & PERIODE --}}
    <div class="pv-card">
        <table class="pv-info-table">
            <tr>
                <td>
                    <div class="pv-info-label">Pegawai</div>
                    <div class="pv-info-value">{{ $pegawai->nama ?? '-' }}</div>
                    <div class="pv-info-sub">{{ $pegawai->nip ?? '-' }}</div>
                    @if($pegawai->jabatan)
                        <div class="pv-info-sub" style="margin-top:2px;">{{ $pegawai->jabatan->nama }}</div>
                    @endif
                </td>
                <td>
                    <div class="pv-info-label">Periode Penilaian</div>
                    <div class="pv-info-value">{{ $periode->nama ?? '-' }}</div>
                    <div class="pv-info-sub">
                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if($hasil)

    {{-- RINGKASAN NILAI --}}
    <div class="pv-card">
        <div class="pv-card-head">Ringkasan Nilai</div>
        <table class="pv-nilai-table">
            <tr>
                <td>
                    <div class="pv-nilai-label">Self Assessment</div>
                    <div class="pv-nilai-value">{{ number_format($hasil->nilai_self, 2) }}</div>
                </td>
                <td>
                    <div class="pv-nilai-label">Atasan Langsung</div>
                    <div class="pv-nilai-value">{{ number_format($hasil->nilai_atasan_langsung, 2) }}</div>
                </td>
                <td>
                    <div class="pv-nilai-label">Atasan Penilai</div>
                    <div class="pv-nilai-value">{{ number_format($hasil->nilai_atasan_penilai, 2) }}</div>
                </td>
                <td class="pv-nilai-akhir">
                    <div class="pv-nilai-label">Nilai Akhir</div>
                    <div class="pv-nilai-value">{{ number_format($hasil->nilai_akhir, 2) }}</div>
                </td>
            </tr>
        </table>

        <div class="pv-predikat-row">
            <span class="pv-predikat-label">Predikat Kinerja</span>
            @if($predikat)
                <span class="pv-predikat-badge"
                      style="background-color: {{ $predikat->warna_latar ?? '#64748b' }}; color: {{ $predikat->warna_text ?? '#ffffff' }};">
                    {{ $predikat->nama }}
                </span>
            @else
                <span style="font-size:13px; color:#9ca3af; font-style:italic;">Belum ada predikat</span>
            @endif
        </div>
    </div>

    {{-- PENILAIAN PER LEVEL --}}
    <div class="pv-card">
        <div class="pv-card-head">Penilaian Per Level</div>
        <table class="pv-level-table">
            @if(isset($penilaian['self']))
            <tr>
                <td style="width:70%;">
                    <div class="pv-level-name">Self Assessment</div>
                    <div class="pv-level-sub">{{ $penilaian['self']->penilai->nama ?? '-' }}</div>
                </td>
                <td style="text-align:right;">
                    <span class="pv-level-badge"><i class="fas fa-check" style="margin-right:4px;"></i> Selesai</span>
                </td>
            </tr>
            @endif

            @if(isset($penilaian['atasan_langsung']))
            <tr>
                <td>
                    <div class="pv-level-name">Atasan Langsung</div>
                    <div class="pv-level-sub">{{ $penilaian['atasan_langsung']->penilai->nama ?? '-' }}</div>
                </td>
                <td style="text-align:right;">
                    <span class="pv-level-badge"><i class="fas fa-check" style="margin-right:4px;"></i> Selesai</span>
                </td>
            </tr>
            @endif

            @if($showAtasanPenilai && isset($penilaian['atasan_penilai']))
            <tr>
                <td>
                    <div class="pv-level-name">Atasan Penilai</div>
                    <div class="pv-level-sub">{{ $penilaian['atasan_penilai']->penilai->nama ?? '-' }}</div>
                </td>
                <td style="text-align:right;">
                    <span class="pv-level-badge"><i class="fas fa-check" style="margin-right:4px;"></i> Selesai</span>
                </td>
            </tr>
            @endif
        </table>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="pv-card">
        <div class="pv-card-head">Tanda Tangan</div>
        <table class="pv-ttd-table">
            <tr>
                {{-- PEGAWAI --}}
                <td>
                    <div class="pv-ttd-role">Pegawai</div>
                    <div class="pv-ttd-sub">Self Assessment</div>
                    <div class="pv-ttd-img-box">
                        @if($ttdPegawaiData)
                            <img src="{{ $ttdPegawaiData }}" class="pv-ttd-img" alt="TTD">
                        @else
                            <span class="pv-ttd-placeholder"><i class="fas fa-signature"></i></span>
                        @endif
                    </div>
                    <div class="pv-ttd-line"></div>
                    <div class="pv-ttd-name">{{ $pegawai->nama ?? '-' }}</div>
                    <div class="pv-ttd-nip">NIP: {{ $pegawai->nip ?? '-' }}</div>
                </td>

                {{-- ATASAN LANGSUNG --}}
                <td>
                    <div class="pv-ttd-role">Atasan Langsung</div>
                    <div class="pv-ttd-sub">&nbsp;</div>
                    <div class="pv-ttd-img-box">
                        @if($ttdAtasanLangsungData)
                            <img src="{{ $ttdAtasanLangsungData }}" class="pv-ttd-img" alt="TTD">
                        @else
                            <span class="pv-ttd-placeholder"><i class="fas fa-signature"></i></span>
                        @endif
                    </div>
                    <div class="pv-ttd-line"></div>
                    <div class="pv-ttd-name">{{ $atasanLangsung->nama ?? '-' }}</div>
                    <div class="pv-ttd-nip">NIP: {{ $atasanLangsung->nip ?? '-' }}</div>
                </td>

                {{-- ATASAN PENILAI --}}
                @if($showAtasanPenilai)
                <td>
                    <div class="pv-ttd-role">Atasan Penilai</div>
                    <div class="pv-ttd-sub">&nbsp;</div>
                    <div class="pv-ttd-img-box">
                        @if($ttdAtasanPenilaiData)
                            <img src="{{ $ttdAtasanPenilaiData }}" class="pv-ttd-img" alt="TTD">
                        @else
                            <span class="pv-ttd-placeholder"><i class="fas fa-signature"></i></span>
                        @endif
                    </div>
                    <div class="pv-ttd-line"></div>
                    <div class="pv-ttd-name">{{ $atasanPenilai->nama ?? '-' }}</div>
                    <div class="pv-ttd-nip">NIP: {{ $atasanPenilai->nip ?? '-' }}</div>
                </td>
                @endif
            </tr>
        </table>
    </div>

    @else

    <div class="pv-card pv-empty">
        <div class="pv-empty-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="pv-empty-title">Belum Ada Hasil Penilaian</div>
        <div class="pv-empty-sub">Penilaian untuk pegawai ini belum difinalisasi pada periode tersebut.</div>
    </div>

    @endif

</div>
@endsection