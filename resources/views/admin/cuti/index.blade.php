@extends('layouts.admin')

@section('title', 'Manajemen Cuti')

@push('styles')
<style>
    .ct-wrap { max-width: 1300px; margin: 0 auto; padding: 24px 16px; }
    .ct-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; margin-bottom: 20px; }
    .ct-header h1 { font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; }
    .ct-header p { font-size: 13px; color: #64748b; margin: 0; }
    .ct-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .ct-btn { display: inline-flex; align-items: center; padding: 8px 14px; font-size: 12px; font-weight: 600; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; transition: all 0.15s; white-space: nowrap; }
    .ct-btn-back { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
    .ct-btn-back:hover { background: #f8fafc; }
    .ct-btn-primary { background: #047857; color: #fff; }
    .ct-btn-primary:hover { background: #065f46; }
    .ct-btn-blue { background: #1d4ed8; color: #fff; }
    .ct-btn-blue:hover { background: #1e40af; }
    .ct-btn-red { background: #b91c1c; color: #fff; }
    .ct-btn-red:hover { background: #991b1b; }
    .ct-btn-yellow { background: #b45309; color: #fff; }
    .ct-btn-yellow:hover { background: #92400e; }
    .ct-btn-gray { background: #64748b; color: #fff; }
    .ct-btn-gray:hover { background: #475569; }
    .ct-btn-sm { padding: 5px 10px; font-size: 11px; }

    .ct-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 16px; }
    .ct-card-head { padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.5px; }
    .ct-card-body { padding: 20px; }

    /* ==================== ALERT ==================== */
    .ct-alert { border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; display: flex; align-items: start; gap: 14px; }
    .ct-alert-danger { background: #fef2f2; border: 1px solid #fecaca; }
    .ct-alert-warning { background: #fffbeb; border: 1px solid #fde68a; }
    .ct-alert-icon { font-size: 28px; line-height: 1; flex-shrink: 0; }
    .ct-alert-content { flex: 1; }
    .ct-alert-title { font-size: 14px; font-weight: 700; margin: 0 0 4px 0; }
    .ct-alert-danger .ct-alert-title { color: #991b1b; }
    .ct-alert-warning .ct-alert-title { color: #92400e; }
    .ct-alert-desc { font-size: 12px; margin: 0 0 8px 0; }
    .ct-alert-danger .ct-alert-desc { color: #b91c1c; }
    .ct-alert-warning .ct-alert-desc { color: #b45309; }
    .ct-alert-list { margin: 8px 0 0 0; padding-left: 20px; font-size: 12px; }
    .ct-alert-danger .ct-alert-list li { color: #991b1b; margin-bottom: 3px; }
    .ct-alert-warning .ct-alert-list li { color: #92400e; margin-bottom: 3px; }
    .ct-alert-list a { color: inherit; font-weight: 700; text-decoration: underline; }

    /* ==================== FILTER ==================== */
    .ct-filter { width: 100%; border-collapse: collapse; }
    .ct-filter td { padding: 0 8px 0 0; vertical-align: bottom; }
    .ct-filter td:last-child { padding-right: 0; }
    .ct-label { display: block; font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }
    .ct-input, .ct-select { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; color: #334155; background: #fff; }

    /* ==================== TABEL ==================== */
    .ct-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .ct-table thead th { background: #f8fafc; padding: 10px 12px; text-align: left; font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 1px solid #e2e8f0; }
    .ct-table tbody td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .ct-table tbody tr:hover { background: #f8fafc; }
    .ct-table tbody tr.row-warning { background: #fffbeb; }
    .ct-table tbody tr.row-warning:hover { background: #fef3c7; }

    .ct-nama { font-size: 13px; font-weight: 600; color: #1e293b; }
    .ct-nip { font-size: 11px; color: #64748b; margin-top: 2px; }

    .ct-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; border: 1px solid transparent; white-space: nowrap; }
    .ct-badge-tahunan { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .ct-badge-sakit { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .ct-badge-melahirkan { background: #fdf2f8; color: #be185d; border-color: #fbcfe8; }
    .ct-badge-khusus { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .ct-badge-lainnya { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .ct-badge-pending { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .ct-badge-proses { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .ct-badge-disetujui { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .ct-badge-ditolak { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .ct-badge-dibatalkan { background: #f8fafc; color: #475569; border-color: #e2e8f0; }

    .ct-surat { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; border: 1px solid transparent; white-space: nowrap; }
    .ct-surat-ok { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .ct-surat-pending { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .ct-surat-tolak { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .ct-surat-none { background: #f8fafc; color: #94a3b8; border-color: #e2e8f0; }

    .ct-saldo { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .ct-saldo-green { background: #ecfdf5; color: #047857; }
    .ct-saldo-yellow { background: #fffbeb; color: #b45309; }
    .ct-saldo-red { background: #fef2f2; color: #b91c1c; }
    .ct-saldo-gray { background: #f8fafc; color: #94a3b8; }

    .ct-empty { padding: 60px 20px; text-align: center; }
    .ct-empty-icon { font-size: 42px; margin-bottom: 12px; }
    .ct-empty-title { font-size: 14px; font-weight: 700; color: #334155; margin: 0 0 4px 0; }
    .ct-empty-sub { font-size: 12px; color: #94a3b8; margin: 0; }

    .ct-footer { margin-top: 24px; text-align: center; font-size: 11px; color: #94a3b8; }

    /* AKSI BUTTONS */
    .ct-aksi { display: flex; gap: 4px; justify-content: center; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<div class="ct-wrap">

    {{-- ==================== HEADER ==================== --}}
    <div class="ct-header">
        <div>
            <h1>Manajemen Cuti</h1>
            <p>Kelola pengajuan dan persetujuan cuti pegawai</p>
        </div>
        <div class="ct-actions">
            <a href="{{ route('admin.cuti.saldo') }}" class="ct-btn ct-btn-blue">
                💰 Saldo Cuti
            </a>
            <a href="{{ route('admin.cuti.create') }}" class="ct-btn ct-btn-primary">
                ➕ Tambah Cuti
            </a>
        </div>
    </div>

    {{-- ==================== ALERT: CUTI SAKIT TANPA SURAT ==================== --}}
    @php
        $cutiSakitTanpaSurat = \App\Models\Cuti::with('pegawai')
            ->where('jenis_cuti', 'sakit')
            ->where(function ($q) {
                $q->whereNull('surat_dokter')->orWhere('surat_dokter', '');
            })
            ->whereIn('status', ['pending', 'proses', 'disetujui'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $jumlahTanpaSurat = $cutiSakitTanpaSurat->count();

        $cutiSakitSuratDitolak = \App\Models\Cuti::with('pegawai')
            ->where('jenis_cuti', 'sakit')
            ->where('surat_dokter_status', 'ditolak')
            ->whereIn('status', ['pending', 'proses', 'disetujui'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $jumlahDitolak = $cutiSakitSuratDitolak->count();
    @endphp

    @if($jumlahDitolak > 0)
    <div class="ct-alert ct-alert-danger">
        <div class="ct-alert-icon">🚫</div>
        <div class="ct-alert-content">
            <p class="ct-alert-title">{{ $jumlahDitolak }} Surat Dokter DITOLAK — Perlu Upload Ulang</p>
            <p class="ct-alert-desc">Pegawai berikut perlu upload ulang surat dokter:</p>
            <ul class="ct-alert-list">
                @foreach($cutiSakitSuratDitolak as $cs)
                <li>
                    <a href="{{ route('admin.cuti.show', $cs->id) }}">
                        {{ $cs->pegawai->nama ?? '-' }}
                    </a>
                    — Cuti Sakit {{ $cs->tanggal_mulai ? \Carbon\Carbon::parse($cs->tanggal_mulai)->format('d M Y') : '-' }}
                    ({{ $cs->lama_hari }} hari)
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    @if($jumlahTanpaSurat > 0)
    <div class="ct-alert ct-alert-warning">
        <div class="ct-alert-icon">⚠️</div>
        <div class="ct-alert-content">
            <p class="ct-alert-title">{{ $jumlahTanpaSurat }} Cuti Sakit Belum Upload Surat Dokter</p>
            <p class="ct-alert-desc">Pegawai berikut mengajukan cuti sakit tapi belum melampirkan surat dokter:</p>
            <ul class="ct-alert-list">
                @foreach($cutiSakitTanpaSurat as $cs)
                <li>
                    <a href="{{ route('admin.cuti.show', $cs->id) }}">
                        {{ $cs->pegawai->nama ?? '-' }}
                    </a>
                    — {{ $cs->tanggal_mulai ? \Carbon\Carbon::parse($cs->tanggal_mulai)->format('d M Y') : '-' }}
                    ({{ $cs->lama_hari }} hari)
                    — Status: <strong>{{ ucfirst($cs->status) }}</strong>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- ==================== FILTER ==================== --}}
    <div class="ct-card">
        <div class="ct-card-head">Filter Data</div>
        <div class="ct-card-body">
            <form action="{{ route('admin.cuti.index') }}" method="GET">
                <table class="ct-filter">
                    <tr>
                        <td style="width: 22%;">
                            <label class="ct-label">Status</label>
                            <select name="status" class="ct-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </td>
                        <td style="width: 22%;">
                            <label class="ct-label">Jenis Cuti</label>
                            <select name="jenis_cuti" class="ct-select">
                                <option value="">Semua Jenis</option>
                                <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                                <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                                <option value="melahirkan" {{ request('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                                <option value="khusus" {{ request('jenis_cuti') == 'khusus' ? 'selected' : '' }}>Cuti Khusus</option>
                                <option value="lainnya" {{ request('jenis_cuti') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </td>
                        <td style="width: 22%;">
                            <label class="ct-label">Cari Pegawai</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Nama / NIP..." class="ct-input">
                        </td>
                        <td style="width: 34%;">
                            <table style="width:100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding-right: 8px;">
                                        <button type="submit" class="ct-btn ct-btn-primary" style="width: 100%; justify-content: center;">
                                            🔍 Filter
                                        </button>
                                    </td>
                                    <td style="width: 50px;">
                                        <a href="{{ route('admin.cuti.index') }}" class="ct-btn ct-btn-back" style="justify-content: center; width: 100%;" title="Reset">↻</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    {{-- ==================== TABEL ==================== --}}
    <div class="ct-card">
        <div class="ct-card-head" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Daftar Pengajuan Cuti</span>
            <span style="font-weight: 500; text-transform: none; letter-spacing: 0;">{{ $cuti->total() }} data</span>
        </div>

        <div style="overflow-x: auto;">
            <table class="ct-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Pegawai</th>
                        <th>Jenis Cuti</th>
                        <th>Periode Cuti</th>
                        <th style="text-align: center; width: 90px;">Lama</th>
                        <th style="width: 130px;">Surat Dokter</th>
                        <th>Sisa Cuti</th>
                        <th>Status</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cuti as $index => $c)
                    @php
                        $perluPerhatian = false;
                        if ($c->jenis_cuti == 'sakit') {
                            if (empty($c->surat_dokter)) {
                                $perluPerhatian = true;
                            } elseif (($c->surat_dokter_status ?? '') == 'ditolak') {
                                $perluPerhatian = true;
                            }
                        }
                    @endphp
                    <tr class="{{ $perluPerhatian ? 'row-warning' : '' }}">
                        <td style="color: #64748b;">
                            {{ ($cuti->currentPage() - 1) * $cuti->perPage() + $index + 1 }}
                        </td>
                        <td>
                            <div class="ct-nama">{{ $c->pegawai->nama ?? '-' }}</div>
                            <div class="ct-nip">{{ $c->pegawai->nip ?? '-' }}</div>
                        </td>
                        <td>
                            @php
                                $jenisClass = match($c->jenis_cuti) {
                                    'tahunan' => 'ct-badge-tahunan',
                                    'sakit' => 'ct-badge-sakit',
                                    'melahirkan' => 'ct-badge-melahirkan',
                                    'khusus' => 'ct-badge-khusus',
                                    default => 'ct-badge-lainnya',
                                };
                            @endphp
                            <span class="ct-badge {{ $jenisClass }}">{{ ucfirst($c->jenis_cuti) }}</span>
                        </td>
                        <td>
                            <div style="font-size: 13px; color: #334155;">
                                {{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d M Y') : '-' }}
                            </div>
                            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                s.d. {{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d M Y') : '-' }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span style="display:inline-block; padding: 3px 10px; background: #f1f5f9; color: #334155; font-size: 12px; font-weight: 700; border-radius: 6px;">
                                {{ $c->lama_hari ?? 0 }} hari
                            </span>
                        </td>
                        <td>
                            @if($c->jenis_cuti == 'sakit')
                                @if(empty($c->surat_dokter))
                                    <span class="ct-surat ct-surat-none">
                                        ⚠️ Belum Upload
                                    </span>
                                @elseif(($c->surat_dokter_status ?? 'pending') == 'pending')
                                    <span class="ct-surat ct-surat-pending">
                                        ⏳ Menunggu
                                    </span>
                                @elseif($c->surat_dokter_status == 'disetujui')
                                    <span class="ct-surat ct-surat-ok">
                                        ✅ Disetujui
                                    </span>
                                @elseif($c->surat_dokter_status == 'ditolak')
                                    <span class="ct-surat ct-surat-tolak">
                                        ❌ Ditolak
                                    </span>
                                @else
                                    <span class="ct-surat ct-surat-none">
                                        ⚠️ Belum Upload
                                    </span>
                                @endif
                            @else
                                <span style="font-size: 11px; color: #cbd5e1;">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $sisa = $saldoList[$c->pegawai_id] ?? null;
                            @endphp
                            @if($sisa !== null)
                                @if($sisa > 6)
                                    <span class="ct-saldo ct-saldo-green">{{ $sisa }} hari</span>
                                @elseif($sisa > 2)
                                    <span class="ct-saldo ct-saldo-yellow">{{ $sisa }} hari</span>
                                @elseif($sisa > 0)
                                    <span class="ct-saldo ct-saldo-red">{{ $sisa }} hari</span>
                                @else
                                    <span class="ct-saldo ct-saldo-red">Habis</span>
                                @endif
                            @else
                                <span class="ct-saldo ct-saldo-gray">Belum di-set</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = match($c->status) {
                                    'pending' => 'ct-badge-pending',
                                    'proses' => 'ct-badge-proses',
                                    'disetujui' => 'ct-badge-disetujui',
                                    'ditolak' => 'ct-badge-ditolak',
                                    'dibatalkan' => 'ct-badge-dibatalkan',
                                    default => 'ct-badge-lainnya',
                                };
                            @endphp
                            <span class="ct-badge {{ $statusClass }}">{{ ucfirst($c->status ?? '-') }}</span>
                        </td>
                        <td style="text-align: center;">
                            <div class="ct-aksi">
                                {{-- DETAIL --}}
                                <a href="{{ route('admin.cuti.show', $c->id) }}"
                                   class="ct-btn ct-btn-blue ct-btn-sm" title="Detail">
                                    👁
                                </a>

                                {{-- BATALKAN — untuk pending & proses --}}
                                @if(in_array($c->status, ['pending', 'proses']))
                                <form action="{{ route('admin.cuti.cancel', $c->id) }}" method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Batalkan cuti {{ $c->pegawai->nama ?? '' }}?')">
                                    @csrf
                                    <button type="submit" class="ct-btn ct-btn-yellow ct-btn-sm" title="Batalkan">
                                        🚫
                                    </button>
                                </form>
                                @endif

                                {{-- HAPUS — untuk yang bukan disetujui --}}
                                @if($c->status !== 'disetujui')
                                <form action="{{ route('admin.cuti.destroy', $c->id) }}" method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Hapus cuti {{ $c->pegawai->nama ?? '' }}? Data akan hilang permanen.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ct-btn ct-btn-red ct-btn-sm" title="Hapus">
                                        🗑
                                    </button>
                                </form>
                                @endif

                                {{-- EDIT SALDO --}}
                                <a href="{{ route('admin.cuti.saldo.edit', ['pegawai' => $c->pegawai_id, 'tahun' => date('Y')]) }}"
                                   class="ct-btn ct-btn-primary ct-btn-sm" title="Edit Saldo">
                                    ✏️
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="ct-empty">
                                <div class="ct-empty-icon">📭</div>
                                <p class="ct-empty-title">Tidak ada data cuti</p>
                                <p class="ct-empty-sub">Belum ada pengajuan cuti sesuai filter yang dipilih</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cuti->count() > 0)
        <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
            {{ $cuti->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="ct-footer">
        Sistem KPI BPRS Amanah Bangsa · {{ date('d F Y H:i') }} WIB
    </div>

</div>
@endsection