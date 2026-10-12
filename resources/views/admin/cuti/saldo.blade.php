@extends('layouts.admin')

@section('title', 'Saldo Cuti Pegawai')

@push('styles')
<style>
    .sc-wrap { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }
    .sc-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; margin-bottom: 20px; }
    .sc-header h1 { font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; }
    .sc-header p { font-size: 13px; color: #64748b; margin: 0; }
    .sc-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .sc-btn { display: inline-flex; align-items: center; padding: 8px 14px; font-size: 12px; font-weight: 600; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; transition: all 0.15s; }
    .sc-btn-back { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
    .sc-btn-back:hover { background: #f8fafc; }
    .sc-btn-primary { background: #047857; color: #fff; }
    .sc-btn-primary:hover { background: #065f46; }
    .sc-btn-warning { background: #b45309; color: #fff; }
    .sc-btn-warning:hover { background: #92400e; }

    .sc-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 16px; }
    .sc-card-head { padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.5px; }
    .sc-card-body { padding: 20px; }

    .sc-filter { display: flex; gap: 12px; align-items: end; }
    .sc-label { display: block; font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }
    .sc-select { padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; color: #334155; background: #fff; min-width: 150px; }

    .sc-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .sc-table thead th { background: #f8fafc; padding: 10px 12px; text-align: left; font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 1px solid #e2e8f0; }
    .sc-table tbody td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .sc-table tbody tr:hover { background: #f8fafc; }

    .sc-nama { font-size: 13px; font-weight: 600; color: #1e293b; }
    .sc-nip { font-size: 11px; color: #64748b; margin-top: 2px; }
    .sc-jabatan { font-size: 11px; color: #94a3b8; margin-top: 2px; }

    .sc-progress { background: #f1f5f9; height: 8px; border-radius: 4px; overflow: hidden; margin: 4px 0; }
    .sc-progress-bar { height: 100%; background: linear-gradient(90deg, #10b981, #047857); border-radius: 4px; transition: width 0.3s; }
    .sc-progress-bar.warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .sc-progress-bar.danger { background: linear-gradient(90deg, #ef4444, #b91c1c); }

    .sc-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid transparent; }
    .sc-badge-green { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .sc-badge-yellow { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .sc-badge-red { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .sc-badge-gray { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }

    .sc-empty { padding: 60px 20px; text-align: center; }
    .sc-empty-icon { font-size: 42px; margin-bottom: 12px; }
    .sc-empty-title { font-size: 14px; font-weight: 700; color: #334155; margin: 0 0 4px 0; }
    .sc-empty-sub { font-size: 12px; color: #94a3b8; margin: 0; }

    .sc-footer { margin-top: 24px; text-align: center; font-size: 11px; color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="sc-wrap">

    {{-- HEADER --}}
    <div class="sc-header">
        <div>
            <h1>Saldo Cuti Pegawai</h1>
            <p>Kelola sisa cuti tahunan setiap pegawai</p>
        </div>
        <div class="sc-actions">
            <a href="{{ route('admin.cuti.index') }}" class="sc-btn sc-btn-back">
                ← Kembali ke Cuti
            </a>
            <button type="button" onclick="document.getElementById('modal-reset').style.display='flex'" class="sc-btn sc-btn-warning">
                🔄 Reset Semua
            </button>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="sc-card">
        <div class="sc-card-head">Filter Tahun</div>
        <div class="sc-card-body">
            <form method="GET" action="{{ route('admin.cuti.saldo') }}" class="sc-filter">
                <div>
                    <label class="sc-label">Tahun</label>
                    <select name="tahun" class="sc-select" onchange="this.form.submit()">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="sc-btn sc-btn-primary">Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY --}}
    @php
        $totalPegawai = count($pegawaiList);
        $totalSisa = array_sum($saldoList);
        $rataRata = $totalPegawai > 0 ? round($totalSisa / $totalPegawai, 1) : 0;
        $pegawaiHabis = 0;
        foreach ($pegawaiList as $p) {
            if (($saldoList[$p->id] ?? 0) <= 0) $pegawaiHabis++;
        }
    @endphp

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 16px;">
        <div class="sc-card" style="margin: 0;">
            <div class="sc-card-body" style="padding: 16px;">
                <p style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 4px 0;">Total Pegawai</p>
                <p style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">{{ $totalPegawai }}</p>
            </div>
        </div>
        <div class="sc-card" style="margin: 0;">
            <div class="sc-card-body" style="padding: 16px;">
                <p style="font-size: 11px; font-weight: 600; color: #047857; text-transform: uppercase; margin: 0 0 4px 0;">Total Sisa Hari</p>
                <p style="font-size: 24px; font-weight: 700; color: #047857; margin: 0;">{{ $totalSisa }}</p>
            </div>
        </div>
        <div class="sc-card" style="margin: 0;">
            <div class="sc-card-body" style="padding: 16px;">
                <p style="font-size: 11px; font-weight: 600; color: #b45309; text-transform: uppercase; margin: 0 0 4px 0;">Rata-rata Sisa</p>
                <p style="font-size: 24px; font-weight: 700; color: #b45309; margin: 0;">{{ $rataRata }} <span style="font-size: 12px; font-weight: 400;">hari</span></p>
            </div>
        </div>
        <div class="sc-card" style="margin: 0;">
            <div class="sc-card-body" style="padding: 16px;">
                <p style="font-size: 11px; font-weight: 600; color: #b91c1c; text-transform: uppercase; margin: 0 0 4px 0;">Saldo Habis</p>
                <p style="font-size: 24px; font-weight: 700; color: #b91c1c; margin: 0;">{{ $pegawaiHabis }}</p>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="sc-card">
        <div class="sc-card-head" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Daftar Saldo Cuti — Tahun {{ $tahun }}</span>
            <span style="font-weight: 500; text-transform: none; letter-spacing: 0;">{{ $totalPegawai }} pegawai</span>
        </div>

        <div style="overflow-x: auto;">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Pegawai</th>
                        <th style="text-align: center; width: 80px;">Total</th>
                        <th style="text-align: center; width: 80px;">Terpakai</th>
                        <th style="text-align: center; width: 80px;">Sisa</th>
                        <th style="width: 200px;">Progress</th>
                        <th style="text-align: center; width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawaiList as $index => $p)
                    @php
                        $total = $saldoDetail[$p->id] ?? 12;
                        $digunakan = $digunakanList[$p->id] ?? 0;
                        $sisa = $saldoList[$p->id] ?? 0;
                        $persen = $total > 0 ? round(($sisa / $total) * 100) : 0;
                        
                        if ($persen > 60) $barClass = '';
                        elseif ($persen > 25) $barClass = 'warning';
                        else $barClass = 'danger';
                        
                        if ($sisa <= 0) {
                            $badgeClass = 'sc-badge-red';
                        } elseif ($sisa <= 3) {
                            $badgeClass = 'sc-badge-yellow';
                        } else {
                            $badgeClass = 'sc-badge-green';
                        }
                    @endphp
                    <tr>
                        <td style="color: #64748b;">{{ $index + 1 }}</td>
                        <td>
                            <div class="sc-nama">{{ $p->nama }}</div>
                            <div class="sc-nip">{{ $p->nip ?? '-' }}</div>
                            @if($p->jabatan)
                                <div class="sc-jabatan">{{ $p->jabatan->nama }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: 700; color: #1e293b;">{{ $total }}</span>
                            <span style="font-size: 11px; color: #64748b;">hari</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: 700; color: #b45309;">{{ $digunakan }}</span>
                            <span style="font-size: 11px; color: #64748b;">hari</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="sc-badge {{ $badgeClass }}">
                                {{ $sisa }} hari
                            </span>
                        </td>
                        <td>
                            <div class="sc-progress">
                                <div class="sc-progress-bar {{ $barClass }}" style="width: {{ $persen }}%;"></div>
                            </div>
                            <div style="font-size: 10px; color: #64748b; text-align: right;">{{ $persen }}%</div>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.cuti.saldo.edit', ['pegawai' => $p->id, 'tahun' => $tahun]) }}"
                               class="sc-btn sc-btn-primary" style="padding: 6px 12px; font-size: 11px;">
                                ✏️ Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="sc-empty">
                                <div class="sc-empty-icon">👥</div>
                                <p class="sc-empty-title">Belum ada pegawai aktif</p>
                                <p class="sc-empty-sub">Tambahkan pegawai terlebih dahulu</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="sc-footer">
        Sistem KPI BPRS Amanah Bangsa · {{ date('d F Y H:i') }} WIB
    </div>

</div>

{{-- ==================== MODAL RESET SEMUA ==================== --}}
<div id="modal-reset" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 12px; padding: 24px; max-width: 400px; width: 90%;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Reset Saldo Cuti Semua Pegawai</h3>
        <p style="font-size: 13px; color: #64748b; margin: 0 0 16px 0;">
            Fitur ini akan mengatur ulang saldo cuti <strong>semua pegawai aktif</strong> untuk tahun yang dipilih. 
            Cuti yang sudah disetujui akan tetap dihitung sebagai "terpakai".
        </p>
        <form method="POST" action="{{ route('admin.cuti.saldo.reset') }}">
            @csrf
            <div style="margin-bottom: 12px;">
                <label class="sc-label">Tahun</label>
                <input type="number" name="tahun" value="{{ $tahun }}" min="2000" max="2100" required
                       style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label class="sc-label">Total Hari Cuti / Tahun</label>
                <input type="number" name="total_hari" value="12" min="0" max="365" required
                       style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                <p style="font-size: 11px; color: #94a3b8; margin: 4px 0 0 0;">Default: 12 hari</p>
            </div>
            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modal-reset').style.display='none'" 
                        class="sc-btn sc-btn-back">
                    Batal
                </button>
                <button type="submit" class="sc-btn sc-btn-warning">
                    🔄 Reset Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection