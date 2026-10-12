@extends('layouts.pegawai')

@section('title', 'Riwayat Jabatan')

@section('content')

{{-- ============================================================
     INLINE STYLE
     ============================================================ --}}
<style>
    .rj-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .rj-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .rj-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
        flex-shrink: 0;
    }

    .rj-icon-wrap i {
        color: white;
        font-size: 22px;
    }

    .rj-title {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        margin: 0;
        line-height: 1.2;
    }

    .rj-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-top: 2px;
    }

    .rj-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .rj-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        color: white !important;
    }

    .rj-btn:hover {
        transform: translateY(-1px);
    }

    .rj-btn-purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        box-shadow: 0 4px 12px -2px rgba(139, 92, 246, 0.4);
    }

    .rj-btn-green {
        background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
        box-shadow: 0 4px 12px -2px rgba(34, 197, 94, 0.4);
    }

    .rj-btn-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        box-shadow: 0 4px 12px -2px rgba(59, 130, 246, 0.4);
    }

    .rj-btn-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 12px -2px rgba(245, 158, 11, 0.4);
    }

    /* Alert */
    .rj-alert {
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
        font-size: 14px;
        line-height: 1.5;
    }

    .rj-alert-success {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-left: 4px solid #22c55e;
        color: #166534;
    }

    .rj-alert-warning {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border-left: 4px solid #f59e0b;
        color: #92400e;
    }

    .rj-alert-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
    }

    /* Card */
    .rj-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.08);
        border: 1px solid #f3f4f6;
        overflow: hidden;
    }

    .rj-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(90deg, #f9fafb 0%, #ffffff 100%);
    }

    .rj-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .rj-card-title i {
        color: #6366f1;
    }

    .rj-card-count {
        font-size: 12px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 4px 12px;
        border-radius: 9999px;
        font-weight: 600;
    }

    /* Timeline Item */
    .rj-timeline {
        padding: 24px;
    }

    .rj-item {
        position: relative;
        padding-left: 40px;
        padding-bottom: 24px;
    }

    .rj-item:last-child {
        padding-bottom: 0;
    }

    .rj-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 28px;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .rj-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid white;
        box-shadow: 0 0 0 2px #e5e7eb;
        z-index: 2;
    }

    .rj-dot-active {
        background: #22c55e;
    }

    .rj-dot-normal {
        background: #6366f1;
    }

    .rj-item-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 18px;
        border: 1px solid #f3f4f6;
        transition: all 0.2s;
    }

    .rj-item-card:hover {
        background: #f3f4f6;
        border-color: #e5e7eb;
    }

    .rj-item-card.is-active {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 2px solid #22c55e;
    }

    .rj-item-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .rj-item-jabatan {
        font-size: 17px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .rj-badge-active {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        background: #22c55e;
        color: white;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .rj-badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid;
        margin-bottom: 10px;
    }

    .rj-item-info {
        font-size: 14px;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 4px 0;
    }

    .rj-item-info i {
        color: #9ca3af;
        width: 16px;
    }

    .rj-periode-box {
        text-align: right;
        background: white;
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .rj-periode {
        font-size: 14px;
        font-weight: 800;
        color: #4f46e5;
        margin: 0;
        line-height: 1.2;
    }

    .rj-durasi {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 3px;
    }

    .rj-keterangan {
        background: white;
        border-left: 3px solid #6366f1;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-style: italic;
        color: #4b5563;
        margin-top: 12px;
    }

    .rj-actions-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
    }

    .rj-btn-sm {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        color: white !important;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
    }

    .rj-btn-sm:hover {
        transform: translateY(-1px);
    }

    /* Empty */
    .rj-empty {
        padding: 60px 20px;
        text-align: center;
    }

    .rj-empty-icon {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .rj-empty-icon i {
        font-size: 44px;
        color: #d1d5db;
    }

    .rj-empty-title {
        font-size: 18px;
        font-weight: 700;
        color: #374151;
        margin: 0 0 8px 0;
    }

    .rj-empty-desc {
        font-size: 14px;
        color: #6b7280;
        margin: 0 auto 20px;
        max-width: 400px;
    }

    /* Info Box */
    .rj-info {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 13px;
        color: #1e40af;
        line-height: 1.5;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .rj-info i {
        flex-shrink: 0;
        margin-top: 2px;
    }
</style>

<div style="max-width: 900px; margin: 0 auto;">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="rj-header">
        <div class="rj-header-left">
            <div class="rj-icon-wrap">
                <i class="fas fa-briefcase"></i>
            </div>
            <div>
                <h1 class="rj-title">Riwayat Jabatan</h1>
                <p class="rj-subtitle">Perjalanan karir Anda di BPRS Amanah Bangsa</p>
            </div>
        </div>

        <div class="rj-actions">
            <a href="{{ route('pegawai.riwayat-jabatan.cetak-cv') }}" target="_blank" class="rj-btn rj-btn-purple">
                <i class="fas fa-file-pdf"></i>
                <span>Cetak CV</span>
            </a>

            <a href="{{ route('pegawai.riwayat-jabatan.download-cv') }}" class="rj-btn rj-btn-green">
                <i class="fas fa-download"></i>
                <span>Download CV</span>
            </a>

            @if($bisaEdit)
                <a href="{{ route('pegawai.riwayat-jabatan.create') }}" class="rj-btn rj-btn-blue">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Riwayat</span>
                </a>
            @else
                <button onclick="openRequestModal()" class="rj-btn rj-btn-orange">
                    <i class="fas fa-edit"></i>
                    <span>Ajukan Izin Edit</span>
                </button>
            @endif
        </div>
    </div>

    {{-- ============================================================
         ALERT
         ============================================================ --}}
    @if($bisaEdit)
        <div class="rj-alert rj-alert-success">
            <div class="rj-alert-icon" style="background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <strong>Anda punya izin edit!</strong>
                <div>Anda dapat menambah dan mengedit riwayat jabatan selama 7 hari sejak izin disetujui.</div>
            </div>
        </div>
    @endif

    @if($permintaanPending->count() > 0)
        <div class="rj-alert rj-alert-warning">
            <div class="rj-alert-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <strong>Permintaan Edit Sedang Ditunggu</strong>
                <div>Anda memiliki {{ $permintaanPending->count() }} permintaan edit yang menunggu persetujuan admin.</div>
            </div>
        </div>
    @endif

    {{-- ============================================================
         CARD TIMELINE
         ============================================================ --}}
    <div class="rj-card">
        <div class="rj-card-header">
            <h3 class="rj-card-title">
                <i class="fas fa-history"></i>
                Timeline Perjalanan Karir
            </h3>
            <span class="rj-card-count">Total: {{ $riwayat->count() }}</span>
        </div>

        <div class="rj-timeline">
            @forelse($riwayat as $r)
                <div class="rj-item">
                    <div class="rj-dot {{ $r->is_current ? 'rj-dot-active' : 'rj-dot-normal' }}"></div>

                    <div class="rj-item-card {{ $r->is_current ? 'is-active' : '' }}">
                        <div class="rj-item-top">
                            <div style="flex: 1; min-width: 0;">
                                <h4 class="rj-item-jabatan">
                                    {{ $r->jabatan->nama ?? '-' }}
                                    @if($r->is_current)
                                        <span class="rj-badge-active">
                                            <i class="fas fa-circle" style="font-size: 6px;"></i>
                                            AKTIF
                                        </span>
                                    @endif
                                </h4>

                                @if($r->status_karyawan)
                                    @php $sk = $r->status_karyawan_color; @endphp
                                    <div class="rj-badge-status"
                                         style="background: {{ $sk['bg'] }}; color: {{ $sk['text'] }}; border-color: {{ $sk['border'] }};">
                                        <i class="fas fa-id-badge"></i>
                                        {{ $r->status_karyawan_label }}
                                        @if($r->jenis_kontrak)
                                            — {{ $r->jenis_kontrak }}
                                        @endif
                                    </div>
                                @endif

                                <div class="rj-item-info">
                                    <i class="fas fa-building"></i>
                                    <span>{{ $r->kantor->nama ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="rj-periode-box">
                                <p class="rj-periode">{{ $r->periode_label }}</p>
                                <p class="rj-durasi">{{ $r->durasi }}</p>
                            </div>
                        </div>

                        @if($r->keterangan)
                            <div class="rj-keterangan">
                                "{{ $r->keterangan }}"
                            </div>
                        @endif

                        @if($bisaEdit)
                            <div class="rj-actions-inline">
                                <a href="{{ route('pegawai.riwayat-jabatan.edit', $r->id) }}"
                                   class="rj-btn-sm"
                                   style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rj-empty">
                    <div class="rj-empty-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3 class="rj-empty-title">Belum Ada Riwayat</h3>
                    <p class="rj-empty-desc">Riwayat jabatan Anda akan muncul di sini.</p>

                    @if($bisaEdit)
                        <a href="{{ route('pegawai.riwayat-jabatan.create') }}" class="rj-btn rj-btn-blue">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Riwayat Pertama</span>
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    {{-- Info --}}
    <div class="rj-info" style="margin-top: 20px;">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Informasi:</strong> Data riwayat jabatan dikelola oleh admin.
            Jika Anda ingin mengubah data, silakan ajukan permintaan edit ke admin terlebih dahulu.
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL REQUEST IZIN EDIT
     ============================================================ --}}
<div id="requestModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(17, 24, 39, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: white; border-radius: 16px; max-width: 480px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); overflow: hidden;">
        <form action="{{ route('pegawai.riwayat-jabatan.request-edit') }}" method="POST">
            @csrf

            <div style="padding: 20px 24px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-edit" style="color: white; font-size: 18px;"></i>
                </div>
                <div>
                    <h3 style="font-size: 17px; font-weight: 800; color: #111827; margin: 0;">Ajukan Izin Edit</h3>
                    <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0;">Permintaan akan direview admin</p>
                </div>
            </div>

            <div style="padding: 20px 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 8px;">
                    Alasan Permintaan <span style="color: #ef4444;">*</span>
                </label>
                <textarea name="alasan" rows="4" required
                          style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; resize: none; outline: none;"
                          placeholder="Contoh: Ada kesalahan tahun mulai, seharusnya 2016 bukan 2015"></textarea>
                <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">
                    Jika disetujui, Anda dapat edit riwayat selama <strong>7 hari</strong>.
                </p>
            </div>

            <div style="padding: 16px 24px; background: #f9fafb; border-top: 1px solid #f3f4f6; display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="closeRequestModal()"
                        style="padding: 10px 18px; background: #e5e7eb; color: #374151; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer;">
                    Batal
                </button>
                <button type="submit"
                        style="padding: 10px 20px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px -2px rgba(59, 130, 246, 0.4);">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Permintaan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRequestModal() {
        document.getElementById('requestModal').style.display = 'flex';
    }
    function closeRequestModal() {
        document.getElementById('requestModal').style.display = 'none';
    }
</script>

@endsection