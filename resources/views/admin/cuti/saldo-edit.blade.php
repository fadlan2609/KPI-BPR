@extends('layouts.admin')

@section('title', 'Edit Saldo Cuti')

@push('styles')
<style>
    .se-wrap { max-width: 700px; margin: 0 auto; padding: 24px 16px; }
    .se-header { padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; margin-bottom: 24px; }
    .se-header h1 { font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; }
    .se-header p { font-size: 13px; color: #64748b; margin: 0; }

    .se-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 16px; }
    .se-card-head { padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.5px; }
    .se-card-body { padding: 20px; }

    .se-info-table { width: 100%; border-collapse: collapse; }
    .se-info-table td { padding: 8px 0; font-size: 13px; vertical-align: top; }
    .se-info-table td:first-child { font-weight: 600; color: #64748b; width: 130px; }
    .se-info-table td:last-child { color: #1e293b; }

    .se-label { display: block; font-size: 11px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }
    .se-input { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #334155; font-family: inherit; }
    .se-input:focus { outline: none; border-color: #047857; box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.1); }
    .se-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }

    .se-alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
    .se-alert-info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
    .se-alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
    .se-alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .se-alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; }

    .se-summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 8px; }
    .se-summary-item { padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; }
    .se-summary-label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 600; margin: 0 0 4px 0; }
    .se-summary-value { font-size: 20px; font-weight: 700; margin: 0; }
    .se-summary-value.green { color: #047857; }
    .se-summary-value.yellow { color: #b45309; }
    .se-summary-value.blue { color: #1d4ed8; }
    .se-summary-value.red { color: #b91c1c; }

    .se-actions { display: flex; gap: 8px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid #e2e8f0; margin-top: 16px; }
    .se-btn { display: inline-flex; align-items: center; padding: 10px 18px; font-size: 13px; font-weight: 600; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; transition: all 0.15s; }
    .se-btn-back { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
    .se-btn-back:hover { background: #f8fafc; }
    .se-btn-primary { background: #047857; color: #fff; }
    .se-btn-primary:hover { background: #065f46; }
</style>
@endpush

@section('content')
<div class="se-wrap">

    {{-- HEADER --}}
    <div class="se-header">
        <h1>✏️ Edit Saldo Cuti</h1>
        <p>Atur total cuti tahunan dan sisa cuti pegawai</p>
    </div>

    {{-- ALERT --}}
    @if(session('error'))
        <div class="se-alert se-alert-danger">
            <strong>❌ Error:</strong> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="se-alert se-alert-success">
            <strong>✅ Sukses:</strong> {{ session('success') }}
        </div>
    @endif

    {{-- INFO PEGAWAI --}}
    <div class="se-card">
        <div class="se-card-head">Informasi Pegawai</div>
        <div class="se-card-body">
            <table class="se-info-table">
                <tr>
                    <td>Nama</td>
                    <td><strong>{{ $pegawai->nama }}</strong></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>{{ $pegawai->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>{{ $pegawai->jabatan->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tahun</td>
                    <td><strong>{{ $tahun }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- SUMMARY SAAT INI --}}
    @php
        $totalSaatIni = $saldo->total_hari ?? 12;
        $sisaSaatIni = $totalSaatIni - $cutiDisetujui;
    @endphp

    <div class="se-card">
        <div class="se-card-head">Ringkasan Saldo Tahun {{ $tahun }}</div>
        <div class="se-card-body">
            <div class="se-summary">
                <div class="se-summary-item">
                    <p class="se-summary-label">Total Cuti</p>
                    <p class="se-summary-value blue">{{ $totalSaatIni }} <span style="font-size: 12px; font-weight: 400;">hari</span></p>
                </div>
                <div class="se-summary-item">
                    <p class="se-summary-label">Sudah Terpakai</p>
                    <p class="se-summary-value yellow">{{ $cutiDisetujui }} <span style="font-size: 12px; font-weight: 400;">hari</span></p>
                </div>
                <div class="se-summary-item">
                    <p class="se-summary-label">Sisa Cuti</p>
                    <p class="se-summary-value {{ $sisaSaatIni > 2 ? 'green' : 'red' }}">
                        {{ $sisaSaatIni }} <span style="font-size: 12px; font-weight: 400;">hari</span>
                    </p>
                </div>
            </div>

            <div class="se-alert se-alert-info" style="margin-top: 16px; margin-bottom: 0;">
                <strong>ℹ️ Info:</strong> "Sudah terpakai" dihitung dari cuti tahunan berstatus <strong>disetujui</strong> pada tahun {{ $tahun }}.
                Sisa cuti akan otomatis dihitung ulang setelah Anda simpan.
            </div>
        </div>
    </div>

    {{-- FORM EDIT --}}
    <form method="POST" action="{{ route('admin.cuti.saldo.update', $pegawai->id) }}">
        @csrf
        @method('PUT')

        <div class="se-card">
            <div class="se-card-head">Form Edit Saldo</div>
            <div class="se-card-body">

                <div style="margin-bottom: 16px;">
                    <label class="se-label">Tahun</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $tahun) }}"
                           min="2000" max="2100" required class="se-input">
                    <p class="se-hint">Tahun berlaku saldo cuti</p>
                </div>

                <div style="margin-bottom: 16px;">
                    <label class="se-label">Total Hari Cuti / Tahun</label>
                    <input type="number" name="total_hari"
                           value="{{ old('total_hari', $saldo->total_hari ?? 12) }}"
                           min="0" max="365" required class="se-input">
                    <p class="se-hint">
                        Default: 12 hari.
                        Total tidak boleh lebih kecil dari cuti yang sudah terpakai (<strong>{{ $cutiDisetujui }} hari</strong>).
                    </p>
                </div>

                <div style="margin-bottom: 16px;">
                    <label class="se-label">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3" class="se-input"
                              placeholder="Contoh: Penyesuaian saldo cuti tahun 2026">{{ old('keterangan', $saldo->keterangan ?? '') }}</textarea>
                </div>

                <div class="se-alert se-alert-warning">
                    <strong>⚠️ Perhatian:</strong>
                    Kalau Anda mengubah total hari, sisa cuti akan otomatis dihitung ulang:
                    <br><br>
                    <code style="background: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                        Sisa = Total - Cuti Terpakai
                    </code>
                </div>

            </div>
        </div>

        <div class="se-actions">
            <a href="{{ route('admin.cuti.saldo', ['tahun' => $tahun]) }}" class="se-btn se-btn-back">
                ← Batal
            </a>
            <button type="submit" class="se-btn se-btn-primary">
                💾 Simpan Saldo
            </button>
        </div>
    </form>

</div>
@endsection