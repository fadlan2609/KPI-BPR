@extends('layouts.admin')

@section('title', 'Permintaan Edit Riwayat')

@section('content')
<div class="bg-white rounded-lg shadow p-6">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Permintaan Edit Riwayat</h1>
            <p class="text-sm text-gray-500">Review permintaan edit dari pegawai</p>
        </div>
        <a href="{{ route('admin.riwayat-jabatan.index') }}"
           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="mb-4 flex gap-2">
        <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
        <a href="{{ route('admin.riwayat-jabatan.permintaan') }}"
           class="px-4 py-2 bg-gray-400 text-white rounded-md text-sm">Reset</a>
    </form>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($permintaan as $p)
            <div class="border rounded-lg p-4 
                @if($p->status == 'pending') bg-yellow-50 border-yellow-300
                @elseif($p->status == 'disetujui') bg-green-50 border-green-300
                @else bg-red-50 border-red-300
                @endif">
                <div class="flex justify-between items-start flex-wrap gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-gray-900">{{ $p->pegawai->nama ?? '-' }}</h3>
                            <span class="px-2 py-0.5 text-xs rounded-full
                                @if($p->status == 'pending') bg-yellow-200 text-yellow-800
                                @elseif($p->status == 'disetujui') bg-green-200 text-green-800
                                @else bg-red-200 text-red-800
                                @endif">
                                {{ $p->status_label }}
                            </span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                {{ $p->jenis_label }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 mt-2">
                            <strong>Alasan:</strong> {{ $p->alasan }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="far fa-clock mr-1"></i>
                            Diajukan: {{ $p->created_at->format('d/m/Y H:i') }}
                        </p>

                        @if($p->catatan_admin)
                            <p class="text-sm text-gray-700 mt-2">
                                <strong>Catatan Admin:</strong> {{ $p->catatan_admin }}
                            </p>
                        @endif

                        @if($p->approvedBy)
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-user-check mr-1"></i>
                                Diproses oleh: {{ $p->approvedBy->nama }}
                            </p>
                        @endif
                    </div>

                    @if($p->status == 'pending')
                        <div class="flex gap-2">
                            <form action="{{ route('admin.riwayat-jabatan.permintaan.approve', $p->id) }}"
                                  method="POST" class="form-approve">
                                @csrf
                                <button type="button"
                                        onclick="openApproveModal(this)"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </button>
                            </form>

                            <form action="{{ route('admin.riwayat-jabatan.permintaan.reject', $p->id) }}"
                                  method="POST" class="form-reject">
                                @csrf
                                <button type="button"
                                        onclick="openRejectModal(this)"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Tidak ada permintaan edit</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $permintaan->withQueryString()->links() }}</div>
</div>

{{-- ============================================================
     MODAL APPROVE — CUSTOM
     ============================================================ --}}
<div id="approveModal" class="app-modal-overlay">
    <div class="app-modal-box">
        {{-- Icon --}}
        <div class="app-modal-icon" style="background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);">
            <i class="fas fa-check"></i>
        </div>

        {{-- Title --}}
        <h3 class="app-modal-title">Setujui Permintaan?</h3>

        {{-- Message --}}
        <p class="app-modal-message">
            Setelah disetujui, pegawai akan mendapat izin untuk mengedit riwayat jabatannya selama <strong>7 hari</strong>.
        </p>

        {{-- Actions --}}
        <div class="app-modal-actions">
            <button type="button" onclick="closeApproveModal()" class="app-modal-btn app-modal-btn-cancel">
                Batal
            </button>
            <button type="button" onclick="confirmApprove()" class="app-modal-btn app-modal-btn-approve">
                <i class="fas fa-check mr-1"></i>
                Ya, Setujui
            </button>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL REJECT — CUSTOM
     ============================================================ --}}
<div id="rejectModal" class="app-modal-overlay">
    <div class="app-modal-box">
        {{-- Icon --}}
        <div class="app-modal-icon" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
            <i class="fas fa-times"></i>
        </div>

        {{-- Title --}}
        <h3 class="app-modal-title">Tolak Permintaan?</h3>

        {{-- Message --}}
        <p class="app-modal-message mb-3">
            Berikan alasan penolakan agar pegawai tahu kenapa permintaannya ditolak.
        </p>

        {{-- Input Catatan --}}
        <div class="app-modal-input-wrap">
            <label class="app-modal-label">Alasan Penolakan <span style="color:#ef4444;">*</span></label>
            <textarea id="rejectCatatan" rows="3" class="app-modal-textarea"
                      placeholder="Contoh: Data sudah benar, tidak perlu diubah"></textarea>
            <p id="rejectError" class="app-modal-error" style="display:none;">
                <i class="fas fa-exclamation-circle mr-1"></i>
                Alasan penolakan wajib diisi!
            </p>
        </div>

        {{-- Actions --}}
        <div class="app-modal-actions">
            <button type="button" onclick="closeRejectModal()" class="app-modal-btn app-modal-btn-cancel">
                Batal
            </button>
            <button type="button" onclick="confirmReject()" class="app-modal-btn app-modal-btn-reject">
                <i class="fas fa-times mr-1"></i>
                Ya, Tolak
            </button>
        </div>
    </div>
</div>

{{-- Hidden inputs untuk form action --}}
<input type="hidden" id="approveActionUrl" value="">
<input type="hidden" id="rejectActionUrl" value="">

<style>
    /* ============================================================
       MODAL OVERLAY
       ============================================================ */
    .app-modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(17, 24, 39, 0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: modalFadeIn 0.2s ease-out;
    }

    .app-modal-overlay.active {
        display: flex;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes modalSlideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.96);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* ============================================================
       MODAL BOX
       ============================================================ */
    .app-modal-box {
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        max-width: 440px;
        width: 100%;
        padding: 32px 24px 24px;
        text-align: center;
        animation: modalSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }

    /* ============================================================
       ICON
       ============================================================ */
    .app-modal-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }

    .app-modal-icon i {
        color: white;
        font-size: 30px;
    }

    /* ============================================================
       TITLE & MESSAGE
       ============================================================ */
    .app-modal-title {
        font-size: 20px;
        font-weight: 800;
        color: #111827;
        margin: 0 0 10px 0;
        line-height: 1.3;
    }

    .app-modal-message {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.6;
        margin: 0 0 24px 0;
    }

    .app-modal-message strong {
        color: #111827;
    }

    /* ============================================================
       INPUT AREA
       ============================================================ */
    .app-modal-input-wrap {
        text-align: left;
        margin-bottom: 20px;
    }

    .app-modal-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 6px;
    }

    .app-modal-textarea {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        color: #111827;
        resize: none;
        transition: all 0.2s;
        outline: none;
    }

    .app-modal-textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .app-modal-error {
        color: #ef4444;
        font-size: 12px;
        font-weight: 600;
        margin-top: 6px;
    }

    /* ============================================================
       BUTTONS
       ============================================================ */
    .app-modal-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .app-modal-btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .app-modal-btn-cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .app-modal-btn-cancel:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }

    .app-modal-btn-approve {
        background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
        color: white;
        box-shadow: 0 4px 12px -2px rgba(34, 197, 94, 0.4);
    }

    .app-modal-btn-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px -4px rgba(34, 197, 94, 0.5);
    }

    .app-modal-btn-reject {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: white;
        box-shadow: 0 4px 12px -2px rgba(239, 68, 68, 0.4);
    }

    .app-modal-btn-reject:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px -4px rgba(239, 68, 68, 0.5);
    }

    .app-modal-btn:active {
        transform: translateY(0);
    }
</style>

<script>
    // ============================================================
    // MODAL APPROVE
    // ============================================================
    let approveForm = null;

    function openApproveModal(btn) {
        approveForm = btn.closest('form');
        document.getElementById('approveModal').classList.add('active');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.remove('active');
        approveForm = null;
    }

    function confirmApprove() {
        if (approveForm) {
            approveForm.submit();
        }
    }

    // ============================================================
    // MODAL REJECT
    // ============================================================
    let rejectForm = null;

    function openRejectModal(btn) {
        rejectForm = btn.closest('form');
        document.getElementById('rejectCatatan').value = '';
        document.getElementById('rejectError').style.display = 'none';
        document.getElementById('rejectModal').classList.add('active');

        // Fokus ke textarea
        setTimeout(() => {
            document.getElementById('rejectCatatan').focus();
        }, 100);
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('active');
        rejectForm = null;
    }

    function confirmReject() {
        const catatan = document.getElementById('rejectCatatan').value.trim();

        if (!catatan) {
            document.getElementById('rejectError').style.display = 'block';
            document.getElementById('rejectCatatan').focus();
            return;
        }

        if (rejectForm) {
            // Tambahkan input hidden catatan_admin
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'catatan_admin';
            hidden.value = catatan;

            // Hapus dulu kalau sudah ada
            rejectForm.querySelectorAll('input[name="catatan_admin"]').forEach(el => el.remove());

            rejectForm.appendChild(hidden);
            rejectForm.submit();
        }
    }

    // ============================================================
    // CLOSE ON OUTSIDE CLICK
    // ============================================================
    document.getElementById('approveModal').addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });

    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });

    // ============================================================
    // CLOSE ON ESC
    // ============================================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeApproveModal();
            closeRejectModal();
        }
    });

    // ============================================================
    // ENTER UNTUK SUBMIT DI TEXTAREA REJECT
    // ============================================================
    document.getElementById('rejectCatatan').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
            confirmReject();
        }
    });
</script>
@endsection