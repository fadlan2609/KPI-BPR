<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\CutiApproval;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CutiPegawaiController extends Controller
{
    /**
     * Halaman cuti pegawai
     */
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $cuti = Cuti::with(['approvals.approver', 'finalApprovedBy'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $saldoCuti = SaldoCuti::where('pegawai_id', $pegawai->id)
            ->where('tahun', date('Y'))
            ->first();

        $pegawaiLain = Pegawai::where('status', 'aktif')
            ->where('id', '!=', $pegawai->id)
            ->orderBy('nama')
            ->get();

        return view('pegawai.cuti.index', compact('cuti', 'pegawai', 'saldoCuti', 'pegawaiLain'));
    }

    /**
     * Simpan pengajuan cuti baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        // ==================== VALIDASI ====================
        $request->validate([
            'jenis_cuti'    => 'required|in:tahunan,sakit,melahirkan,khusus,lainnya',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'lama_hari'     => 'required|integer|min:1',
            'keterangan'    => 'nullable|string|max:500',
            'approvers'     => 'required|array|min:1',
            'approvers.*'   => 'required|exists:pegawai,id',
            'final_index'   => 'required|integer|min:0',
        ], [
            'approvers.required' => 'Minimal harus ada 1 approver.',
            'approvers.*.exists' => 'Approver yang dipilih tidak valid.',
        ]);

        // Validasi tambahan: final_index tidak boleh melebihi jumlah approver
        if ($request->final_index >= count($request->approvers)) {
            return back()->with('error', 'Index final approver tidak valid!')->withInput();
        }

        // Validasi: approver tidak boleh pemohon sendiri
        foreach ($request->approvers as $approverId) {
            if ((int) $approverId === (int) $pegawai->id) {
                return back()->with('error', 'Approver tidak boleh diri sendiri!')->withInput();
            }
        }

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $lamaHari = (int) $request->lama_hari;

        // ==================== CEK SALDO CUTI TAHUNAN ====================
        if ($request->jenis_cuti === 'tahunan') {
            $saldo = SaldoCuti::where('pegawai_id', $pegawai->id)
                ->where('tahun', $tanggalMulai->year)
                ->first();

            if (!$saldo || $saldo->sisa_hari < $lamaHari) {
                return back()
                    ->with('error', 'Saldo cuti tidak mencukupi! Sisa: ' . ($saldo->sisa_hari ?? 0) . ' hari.')
                    ->withInput();
            }
        }

        // ==================== HITUNG TANGGAL ====================
        $tanggalSelesai = Cuti::hitungTanggalSelesai($tanggalMulai, $lamaHari);
        $tanggalMasukKerja = Cuti::hitungTanggalMasukKerja($tanggalSelesai);

        DB::beginTransaction();
        try {
            $cuti = Cuti::create([
                'pegawai_id'             => $pegawai->id,
                'jenis_cuti'             => $request->jenis_cuti,
                'tanggal_mulai'          => $tanggalMulai->format('Y-m-d'),
                'tanggal_selesai'        => $tanggalSelesai->format('Y-m-d'),
                'tanggal_masuk_kerja'    => $tanggalMasukKerja->format('Y-m-d'),
                'lama_hari'              => $lamaHari,
                'keterangan'             => $request->keterangan,
                'status'                 => Cuti::STATUS_PROSES,
                'current_approval_level' => 1,
            ]);

            // Simpan approval berantai
            foreach ($request->approvers as $level => $approverId) {
                $isFinal = ((int) $request->final_index === (int) $level) ? 1 : 0;

                CutiApproval::create([
                    'cuti_id'      => $cuti->id,
                    'level'        => $level + 1,
                    'approver_id'  => $approverId,
                    'requested_by' => $pegawai->id,
                    'status'       => 'pending',
                    'is_final'     => $isFinal,
                    'is_planned'   => 1,
                ]);
            }

            DB::commit();

            try {
                if (class_exists('\App\Helpers\NotifHelper')) {
                    \App\Helpers\NotifHelper::notifyAdmins(
                        new \App\Notifications\CutiBaruAdminNotification($cuti)
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Gagal kirim notif cuti baru: ' . $e->getMessage());
            }

            return redirect()->route('pegawai.cuti.index')
                ->with('success', 'Pengajuan cuti berhasil dikirim! Menunggu approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Batalkan cuti sendiri
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $cuti = Cuti::where('pegawai_id', $pegawai->id)->findOrFail($id);

        if ($cuti->status === Cuti::STATUS_DISETUJUI) {
            return back()->with('error', 'Cuti yang sudah disetujui tidak dapat dibatalkan!');
        }

        DB::beginTransaction();
        try {
            // Kembalikan saldo kalau cuti tahunan yang dibatalkan
            if ($cuti->jenis_cuti === 'tahunan' && $cuti->status === Cuti::STATUS_PROSES) {
                $saldo = SaldoCuti::where('pegawai_id', $cuti->pegawai_id)
                    ->where('tahun', Carbon::parse($cuti->tanggal_mulai)->year)
                    ->first();
                if ($saldo) {
                    $saldo->update([
                        'digunakan' => max(0, $saldo->digunakan - $cuti->lama_hari),
                        'sisa_hari' => $saldo->sisa_hari + $cuti->lama_hari,
                    ]);
                }
            }

            $cuti->update(['status' => Cuti::STATUS_DIBATALKAN]);

            DB::commit();
            return redirect()->route('pegawai.cuti.index')
                ->with('success', 'Cuti berhasil dibatalkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan cuti: ' . $e->getMessage());
        }
    }

    /**
     * Hitung tanggal selesai & masuk kerja (AJAX)
     */
    public function calculateDate(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'lama_hari'     => 'required|integer|min:1',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $lamaHari = (int) $request->lama_hari;

        $tanggalSelesai = Cuti::hitungTanggalSelesai($tanggalMulai, $lamaHari);
        $tanggalMasukKerja = Cuti::hitungTanggalMasukKerja($tanggalSelesai);

        Carbon::setLocale('id');

        return response()->json([
            'tanggal_selesai'            => $tanggalSelesai->format('Y-m-d'),
            'tanggal_selesai_format'     => $tanggalSelesai->translatedFormat('l, d F Y'),
            'tanggal_masuk_kerja'        => $tanggalMasukKerja->format('Y-m-d'),
            'tanggal_masuk_kerja_format' => $tanggalMasukKerja->translatedFormat('l, d F Y'),
        ]);
    }

    // ==================== UPLOAD SURAT DOKTER ====================

    public function uploadSuratDokterForm($id)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $cuti = Cuti::where('pegawai_id', $pegawai->id)->findOrFail($id);

        if ($cuti->jenis_cuti !== 'sakit') {
            return redirect()->route('pegawai.cuti.index')
                ->with('error', 'Upload surat dokter hanya untuk cuti sakit!');
        }

        return view('pegawai.cuti.upload-surat-dokter', compact('cuti', 'pegawai'));
    }

    public function uploadSuratDokter(Request $request, $id)
    {
        $request->validate([
            'surat_dokter' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'surat_dokter.required' => 'File surat dokter wajib diupload.',
            'surat_dokter.mimes'    => 'Format file harus PDF, JPG, JPEG, PNG, atau WEBP.',
            'surat_dokter.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $user = Auth::user();
        $pegawai = $user->pegawai;

        $cuti = Cuti::where('pegawai_id', $pegawai->id)->findOrFail($id);

        if ($cuti->jenis_cuti !== 'sakit') {
            return back()->with('error', 'Upload surat dokter hanya untuk cuti sakit!');
        }

        try {
            DB::beginTransaction();

            if ($cuti->surat_dokter) {
                $oldPath = storage_path('app/public/' . $cuti->surat_dokter);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $folder = storage_path('app/public/cuti/surat-dokter');
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $file = $request->file('surat_dokter');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move($folder, $filename);
            $path = 'cuti/surat-dokter/' . $filename;

            // ENUM pakai 'menunggu'
            $cuti->update([
                'surat_dokter'             => $path,
                'surat_dokter_status'      => 'menunggu',
                'surat_dokter_uploaded_at' => now(),
            ]);

            DB::commit();

            try {
                if (class_exists('\App\Helpers\NotifHelper')) {
                    \App\Helpers\NotifHelper::notifyAdmins(
                        new \App\Notifications\SuratDokterUploadedNotification($cuti)
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Gagal kirim notif surat dokter: ' . $e->getMessage());
            }

            return redirect()->route('pegawai.cuti.index')
                ->with('success', 'Surat dokter berhasil diupload! Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Upload surat dokter error: ' . $e->getMessage());
            return back()->with('error', 'Gagal upload surat dokter: ' . $e->getMessage());
        }
    }

    // ==================== APPROVAL ====================

    public function approvalIndex()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $menunggu = Cuti::with(['pegawai', 'approvals'])
            ->whereHas('approvals', function ($q) use ($pegawai) {
                $q->where('approver_id', $pegawai->id)->where('status', 'pending');
            })
            ->where('status', Cuti::STATUS_PROSES)
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'menunggu_page');

        $riwayat = Cuti::with(['pegawai', 'approvals'])
            ->whereHas('approvals', function ($q) use ($pegawai) {
                $q->where('approver_id', $pegawai->id)
                  ->whereIn('status', ['disetujui', 'ditolak']);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'riwayat_page');

        return view('pegawai.cuti.approval-index', compact('menunggu', 'riwayat'));
    }

    public function approvalShow($id)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $cuti = Cuti::with(['pegawai.jabatan', 'approvals.approver', 'approvals.requestedBy'])
            ->findOrFail($id);

        $myApproval = $cuti->approvals
            ->where('approver_id', $pegawai->id)
            ->where('status', 'pending')
            ->first();

        $pegawaiLain = Pegawai::where('status', 'aktif')
            ->where('id', '!=', $pegawai->id)
            ->orderBy('nama')
            ->get();

        return view('pegawai.cuti.approval-show', compact('cuti', 'myApproval', 'pegawaiLain'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'catatan'          => 'nullable|string|max:500',
            'next_approver_id' => 'nullable|exists:pegawai,id',
            'is_final'         => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $pegawai = $user->pegawai;

        $cuti = Cuti::findOrFail($id);

        $myApproval = $cuti->approvals
            ->where('approver_id', $pegawai->id)
            ->where('status', 'pending')
            ->first();

        if (!$myApproval) {
            return back()->with('error', 'Anda tidak memiliki akses untuk approve cuti ini!');
        }

        DB::beginTransaction();
        try {
            $myApproval->update([
                'status'      => 'disetujui',
                'catatan'     => $request->catatan,
                'approved_at' => Carbon::now(),
            ]);

            $isFinalLevel = $myApproval->is_final || $request->is_final;

            if (!$isFinalLevel && $request->next_approver_id) {
                CutiApproval::create([
                    'cuti_id'      => $cuti->id,
                    'level'        => $myApproval->level + 1,
                    'approver_id'  => $request->next_approver_id,
                    'requested_by' => $pegawai->id,
                    'status'       => 'pending',
                ]);

                $cuti->update([
                    'current_approval_level' => $myApproval->level + 1,
                ]);

                DB::commit();
                return redirect()->route('pegawai.cuti.approval.index')
                    ->with('success', 'Cuti berhasil di-approve dan diteruskan ke approver berikutnya!');
            }

            // FINAL APPROVAL
            $cuti->update([
                'status'            => Cuti::STATUS_DISETUJUI,
                'final_approved_by' => $pegawai->id,
                'final_approved_at' => Carbon::now(),
            ]);

            if ($cuti->jenis_cuti === 'tahunan') {
                $saldo = SaldoCuti::where('pegawai_id', $cuti->pegawai_id)
                    ->where('tahun', Carbon::parse($cuti->tanggal_mulai)->year)
                    ->first();
                if ($saldo) {
                    $saldo->update([
                        'digunakan' => $saldo->digunakan + $cuti->lama_hari,
                        'sisa_hari' => $saldo->sisa_hari - $cuti->lama_hari,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('pegawai.cuti.approval.index')
                ->with('success', 'Cuti berhasil di-approve FINAL!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $pegawai = $user->pegawai;

        $cuti = Cuti::findOrFail($id);

        $myApproval = $cuti->approvals
            ->where('approver_id', $pegawai->id)
            ->where('status', 'pending')
            ->first();

        if (!$myApproval) {
            return back()->with('error', 'Anda tidak memiliki akses untuk reject cuti ini!');
        }

        DB::beginTransaction();
        try {
            $myApproval->update([
                'status'      => 'ditolak',
                'catatan'     => $request->catatan,
                'approved_at' => Carbon::now(),
            ]);

            $cuti->update([
                'status' => Cuti::STATUS_DITOLAK,
            ]);

            DB::commit();
            return redirect()->route('pegawai.cuti.approval.index')
                ->with('success', 'Cuti berhasil ditolak!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}