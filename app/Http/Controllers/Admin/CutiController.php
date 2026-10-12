<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\CutiApproval;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CutiController extends Controller
{
    /**
     * Tampilkan daftar semua cuti
     */
    public function index(Request $request)
    {
        $query = Cuti::with(['pegawai', 'approvals.approver', 'finalApprovedBy']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->jenis_cuti) {
            $query->where('jenis_cuti', $request->jenis_cuti);
        }
        if ($request->search) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        $cuti = $query->orderBy('created_at', 'desc')->paginate(15);
        $pegawai = Pegawai::where('status', 'aktif')->orderBy('nama')->get();

        // Ambil saldo cuti tahun ini untuk semua pegawai
        $saldoList = SaldoCuti::where('tahun', date('Y'))
            ->pluck('sisa_hari', 'pegawai_id')
            ->toArray();

        return view('admin.cuti.index', compact('cuti', 'pegawai', 'saldoList'));
    }

    /**
     * Form create cuti baru
     */
    public function create()
    {
        $pegawai = Pegawai::where('status', 'aktif')->orderBy('nama')->get();
        return view('admin.cuti.create', compact('pegawai'));
    }

    /**
     * Simpan cuti baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,khusus,lainnya',
            'tanggal_mulai' => 'required|date',
            'lama_hari' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'required|exists:pegawai,id',
            'final_index' => 'required|integer|min:0',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $lamaHari = (int) $request->lama_hari;
        $tanggalSelesai = Cuti::hitungTanggalSelesai($tanggalMulai, $lamaHari);
        $tanggalMasukKerja = Cuti::hitungTanggalMasukKerja($tanggalSelesai);

        if ($request->jenis_cuti === 'tahunan') {
            $saldo = SaldoCuti::where('pegawai_id', $request->pegawai_id)
                ->where('tahun', $tanggalMulai->year)->first();
            if (!$saldo || $saldo->sisa_hari < $lamaHari) {
                return back()->with('error', 'Saldo cuti tidak mencukupi!')->withInput();
            }
        }

        foreach ($request->approvers as $approverId) {
            if ($approverId == $request->pegawai_id) {
                return back()->with('error', 'Approver tidak boleh pemohon sendiri!')->withInput();
            }
        }

        if ($request->final_index < 0 || $request->final_index >= count($request->approvers)) {
            return back()->with('error', 'Index final approver tidak valid!')->withInput();
        }

        DB::beginTransaction();
        try {
            $cuti = Cuti::create([
                'pegawai_id' => $request->pegawai_id,
                'jenis_cuti' => $request->jenis_cuti,
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
                'tanggal_masuk_kerja' => $tanggalMasukKerja->format('Y-m-d'),
                'lama_hari' => $lamaHari,
                'keterangan' => $request->keterangan,
                'status' => Cuti::STATUS_PROSES,
                'current_approval_level' => 1,
            ]);

            foreach ($request->approvers as $index => $approverId) {
                $isFinal = ($index === (int) $request->final_index);
                $isFirst = ($index === 0);

                CutiApproval::create([
                    'cuti_id' => $cuti->id,
                    'level' => $index + 1,
                    'approver_id' => $approverId,
                    'requested_by' => $request->pegawai_id,
                    'status' => $isFirst ? 'pending' : 'menunggu',
                    'is_final' => $isFinal,
                    'is_planned' => true,
                ]);

                if ($isFirst) {
                    $approver = Pegawai::find($approverId);
                    if ($approver) {
                        try {
                            $approver->notify(new \App\Notifications\CutiBaruNotification($cuti, 1));
                        } catch (\Exception $e) {
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.cuti.index')
                ->with('success', "Pengajuan cuti berhasil dibuat! Durasi: {$lamaHari} hari kerja");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Detail cuti
     */
    public function show($id)
    {
        $cuti = Cuti::with([
            'pegawai.jabatan', 'pegawai.kantor',
            'approvals.approver', 'approvals.requestedBy', 'finalApprovedBy'
        ])->findOrFail($id);
        return view('admin.cuti.show', compact('cuti'));
    }

    /**
     * Form edit cuti
     */
    public function edit($id)
    {
        $cuti = Cuti::findOrFail($id);
        if (!in_array($cuti->status, [Cuti::STATUS_PENDING, Cuti::STATUS_PROSES])) {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Hanya cuti Pending/Proses yang dapat diedit!');
        }
        $pegawai = Pegawai::where('status', 'aktif')->orderBy('nama')->get();
        return view('admin.cuti.edit', compact('cuti', 'pegawai'));
    }

    /**
     * Update cuti
     */
    public function update(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        if (!in_array($cuti->status, [Cuti::STATUS_PENDING, Cuti::STATUS_PROSES])) {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Tidak dapat mengubah cuti yang sudah final!');
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,khusus,lainnya',
            'tanggal_mulai' => 'required|date',
            'lama_hari' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $lamaHari = (int) $request->lama_hari;
        $tanggalSelesai = Cuti::hitungTanggalSelesai($tanggalMulai, $lamaHari);
        $tanggalMasukKerja = Cuti::hitungTanggalMasukKerja($tanggalSelesai);

        $cuti->update([
            'pegawai_id' => $request->pegawai_id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
            'tanggal_masuk_kerja' => $tanggalMasukKerja->format('Y-m-d'),
            'lama_hari' => $lamaHari,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.cuti.index')->with('success', 'Cuti berhasil diupdate!');
    }

    /**
     * Hapus cuti
     */
    public function destroy($id)
    {
        $cuti = Cuti::findOrFail($id);
        if ($cuti->status === Cuti::STATUS_DISETUJUI) {
            return back()->with('error', 'Tidak dapat menghapus cuti yang sudah disetujui final!');
        }
        $nama = $cuti->pegawai->nama ?? 'Pegawai';
        $cuti->delete();
        return redirect()->route('admin.cuti.index')->with('success', "Cuti {$nama} berhasil dihapus!");
    }

    /**
     * Batalkan cuti
     */
    public function cancel($id)
    {
        $cuti = Cuti::findOrFail($id);
        if ($cuti->status === Cuti::STATUS_DISETUJUI && $cuti->jenis_cuti === 'tahunan') {
            $saldo = SaldoCuti::where('pegawai_id', $cuti->pegawai_id)
                ->where('tahun', Carbon::parse($cuti->tanggal_mulai)->year)->first();
            if ($saldo) {
                $saldo->update([
                    'digunakan' => $saldo->digunakan - $cuti->lama_hari,
                    'sisa_hari' => $saldo->sisa_hari + $cuti->lama_hari,
                ]);
            }
        }
        $cuti->update(['status' => Cuti::STATUS_DIBATALKAN]);
        return redirect()->route('admin.cuti.index')->with('success', 'Cuti berhasil dibatalkan!');
    }

    /**
     * Approve cuti (admin override)
     */
    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);
        if ($cuti->status != Cuti::STATUS_PROSES) {
            return redirect()->route('admin.cuti.index')->with('error', 'Cuti tidak dalam status proses!');
        }

        DB::beginTransaction();
        try {
            foreach ($cuti->approvals as $ap) {
                if (in_array($ap->status, ['pending', 'menunggu'])) {
                    $ap->update([
                        'status' => 'disetujui',
                        'catatan' => 'Disetujui oleh admin',
                        'approved_at' => Carbon::now(),
                    ]);
                }
            }
            $cuti->update([
                'status' => Cuti::STATUS_DISETUJUI,
                'final_approved_by' => auth()->user()->pegawai_id ?? null,
                'final_approved_at' => Carbon::now(),
            ]);
            if ($cuti->jenis_cuti === 'tahunan') {
                $saldo = SaldoCuti::where('pegawai_id', $cuti->pegawai_id)
                    ->where('tahun', Carbon::parse($cuti->tanggal_mulai)->year)->first();
                if ($saldo) {
                    $saldo->update([
                        'digunakan' => $saldo->digunakan + $cuti->lama_hari,
                        'sisa_hari' => $saldo->sisa_hari - $cuti->lama_hari,
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('admin.cuti.index')->with('success', 'Cuti disetujui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Reject cuti (admin override)
     */
    public function reject(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        if ($cuti->status != Cuti::STATUS_PROSES) {
            return redirect()->route('admin.cuti.index')->with('error', 'Cuti tidak dalam status proses!');
        }
        DB::beginTransaction();
        try {
            foreach ($cuti->approvals as $ap) {
                if ($ap->status == 'pending') {
                    $ap->update([
                        'status' => 'ditolak',
                        'catatan' => $request->catatan ?? 'Ditolak oleh admin',
                        'approved_at' => Carbon::now(),
                    ]);
                }
            }
            $cuti->update(['status' => Cuti::STATUS_DITOLAK]);
            DB::commit();
            return redirect()->route('admin.cuti.index')->with('success', 'Cuti ditolak!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * ==================== VERIFIKASI SURAT DOKTER ====================
     */

    public function approveSuratDokter($id)
    {
        $cuti = Cuti::findOrFail($id);

        if (!$cuti->surat_dokter) {
            return back()->with('error', 'Surat dokter belum diupload.');
        }

        $cuti->update([
            'surat_dokter_status' => 'disetujui',
        ]);

        return redirect()->route('admin.cuti.show', $cuti->id)
            ->with('success', 'Surat dokter berhasil diverifikasi & disetujui!');
    }

    public function rejectSuratDokter(Request $request, $id)
    {
        $request->validate([
            'catatan_surat' => 'required|string|max:500',
        ], [
            'catatan_surat.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $cuti = Cuti::findOrFail($id);

        if (!$cuti->surat_dokter) {
            return back()->with('error', 'Surat dokter belum diupload.');
        }

        $cuti->update([
            'surat_dokter_status' => 'ditolak',
            'keterangan' => ($cuti->keterangan ? $cuti->keterangan . "\n\n" : '')
                          . '[Surat Dokter Ditolak] ' . $request->catatan_surat,
        ]);

        return redirect()->route('admin.cuti.show', $cuti->id)
            ->with('success', 'Surat dokter ditolak. Pegawai dapat mengupload ulang.');
    }

    /**
     * ==================== LAPORAN ====================
     */
    public function laporan(Request $request)
    {
        $query = Cuti::with(['pegawai.jabatan', 'finalApprovedBy'])
            ->where('status', Cuti::STATUS_DISETUJUI);

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }
        if ($request->pegawai_id) $query->where('pegawai_id', $request->pegawai_id);
        if ($request->jenis_cuti) $query->where('jenis_cuti', $request->jenis_cuti);

        $cuti = $query->orderBy('final_approved_at', 'desc')->paginate(20);
        $pegawai = Pegawai::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.cuti.laporan', compact('cuti', 'pegawai'));
    }

    public function cetakLaporan(Request $request)
    {
        $query = Cuti::with(['pegawai.jabatan', 'finalApprovedBy'])
            ->where('status', Cuti::STATUS_DISETUJUI);

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }
        if ($request->pegawai_id) $query->where('pegawai_id', $request->pegawai_id);
        if ($request->jenis_cuti) $query->where('jenis_cuti', $request->jenis_cuti);

        $cuti = $query->orderBy('tanggal_mulai')->get();
        $bpr = \App\Models\BPR::first();

        $pdf = Pdf::loadView('admin.cuti.laporan-pdf', compact('cuti', 'bpr', 'request'));
        $pdf->setPaper('a4', 'landscape');

        $pdf->setOption('margin-top', 40);
        $pdf->setOption('margin-right', 40);
        $pdf->setOption('margin-bottom', 40);
        $pdf->setOption('margin-left', 40);

        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream('Laporan_Cuti_' . date('Ymd') . '.pdf');
    }

    /**
     * Preview Surat Cuti (PDF stream)
     */
    public function cetakSurat($id)
    {
        $cuti = Cuti::with([
            'pegawai.jabatan', 'pegawai.kantor',
            'approvals.approver', 'approvals.requestedBy', 'finalApprovedBy'
        ])->findOrFail($id);

        if ($cuti->status !== Cuti::STATUS_DISETUJUI) {
            return redirect()->route('admin.cuti.show', $cuti->id)
                ->with('error', 'Surat cuti hanya untuk cuti yang disetujui final!');
        }

        $bpr = \App\Models\BPR::first();

        $pdf = Pdf::loadView('admin.cuti.surat-pdf', compact('cuti', 'bpr'));

        $pdf->setPaper('a4', 'portrait');

        $pdf->setOption('margin-top', 72);
        $pdf->setOption('margin-right', 72);
        $pdf->setOption('margin-bottom', 72);
        $pdf->setOption('margin-left', 72);

        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times-Roman');

        $namaFile = 'Surat_Cuti_' . str_replace(' ', '_', $cuti->pegawai->nama)
                    . '_' . $cuti->tanggal_mulai->format('Ymd') . '.pdf';
        return $pdf->stream($namaFile);
    }

    /**
     * Download Surat Cuti (PDF download)
     */
    public function downloadSurat($id)
    {
        $cuti = Cuti::with([
            'pegawai.jabatan', 'pegawai.kantor',
            'approvals.approver', 'approvals.requestedBy', 'finalApprovedBy'
        ])->findOrFail($id);

        if ($cuti->status !== Cuti::STATUS_DISETUJUI) {
            return redirect()->route('admin.cuti.show', $cuti->id)
                ->with('error', 'Surat cuti hanya untuk cuti yang disetujui final!');
        }

        $bpr = \App\Models\BPR::first();

        $pdf = Pdf::loadView('admin.cuti.surat-pdf', compact('cuti', 'bpr'));

        $pdf->setPaper('a4', 'portrait');

        $pdf->setOption('margin-top', 72);
        $pdf->setOption('margin-right', 72);
        $pdf->setOption('margin-bottom', 72);
        $pdf->setOption('margin-left', 72);

        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times-Roman');

        $namaFile = 'Surat_Cuti_' . str_replace(' ', '_', $cuti->pegawai->nama)
                    . '_' . $cuti->tanggal_mulai->format('Ymd') . '.pdf';
        return $pdf->download($namaFile);
    }

    /**
     * Download surat dokter
     */
    public function downloadSuratDokter($id)
    {
        $cuti = Cuti::findOrFail($id);

        if (!$cuti->surat_dokter) {
            return back()->with('error', 'Cuti ini tidak memiliki surat dokter.');
        }

        $path = storage_path('app/public/' . $cuti->surat_dokter);

        if (!file_exists($path)) {
            return back()->with('error', 'File surat dokter tidak ditemukan.');
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $namaFile = 'Surat_Dokter_' . str_replace(' ', '_', $cuti->pegawai->nama)
                    . '_' . $cuti->tanggal_mulai->format('Ymd') . '.' . $ext;

        return response()->download($path, $namaFile);
    }

    /**
     * ==================== SALDO CUTI ====================
     */

    /**
     * Daftar Saldo Cuti Pegawai
     */
    public function saldo(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $pegawaiList = Pegawai::where('status', 'aktif')
            ->with('jabatan')
            ->orderBy('nama')
            ->get();

        $saldoList = SaldoCuti::where('tahun', $tahun)
            ->pluck('sisa_hari', 'pegawai_id')
            ->toArray();

        $saldoDetail = SaldoCuti::where('tahun', $tahun)
            ->pluck('total_hari', 'pegawai_id')
            ->toArray();

        $digunakanList = SaldoCuti::where('tahun', $tahun)
            ->pluck('digunakan', 'pegawai_id')
            ->toArray();

        $tahunList = SaldoCuti::selectRaw('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (empty($tahunList)) {
            $tahunList = [date('Y')];
        }

        return view('admin.cuti.saldo', compact(
            'pegawaiList', 'saldoList', 'saldoDetail', 'digunakanList',
            'tahun', 'tahunList'
        ));
    }

    /**
     * Form edit saldo cuti pegawai
     */
    public function editSaldo($pegawaiId, Request $request)
    {
        $pegawai = Pegawai::with('jabatan')->findOrFail($pegawaiId);
        $tahun = $request->tahun ?? date('Y');

        $saldo = SaldoCuti::where('pegawai_id', $pegawaiId)
            ->where('tahun', $tahun)
            ->first();

        if (!$saldo) {
            $saldo = new SaldoCuti([
                'pegawai_id' => $pegawaiId,
                'tahun' => $tahun,
                'total_hari' => 12,
                'digunakan' => 0,
                'sisa_hari' => 12,
            ]);
        }

        $cutiDisetujui = Cuti::where('pegawai_id', $pegawaiId)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', Cuti::STATUS_DISETUJUI)
            ->whereYear('tanggal_mulai', $tahun)
            ->sum('lama_hari');

        return view('admin.cuti.saldo-edit', compact(
            'pegawai', 'saldo', 'tahun', 'cutiDisetujui'
        ));
    }

    /**
     * Update saldo cuti pegawai
     */
    public function updateSaldo(Request $request, $pegawaiId)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
            'total_hari' => 'required|integer|min:0|max:365',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $tahun = $request->tahun;
            $totalHari = (int) $request->total_hari;

            $cutiDisetujui = Cuti::where('pegawai_id', $pegawaiId)
                ->where('jenis_cuti', 'tahunan')
                ->where('status', Cuti::STATUS_DISETUJUI)
                ->whereYear('tanggal_mulai', $tahun)
                ->sum('lama_hari');

            $digunakan = (int) $cutiDisetujui;
            $sisaHari = $totalHari - $digunakan;

            if ($sisaHari < 0) {
                return back()->with('error',
                    "Total hari ({$totalHari}) lebih kecil dari cuti yang sudah digunakan ({$digunakan})!"
                )->withInput();
            }

            SaldoCuti::updateOrCreate(
                [
                    'pegawai_id' => $pegawaiId,
                    'tahun' => $tahun,
                ],
                [
                    'total_hari' => $totalHari,
                    'digunakan' => $digunakan,
                    'sisa_hari' => $sisaHari,
                    'keterangan' => $request->keterangan,
                ]
            );

            $pegawai = Pegawai::findOrFail($pegawaiId);

            return redirect()->route('admin.cuti.saldo', ['tahun' => $tahun])
                ->with('success', "Saldo cuti {$pegawai->nama} tahun {$tahun} berhasil diupdate!");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update saldo: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Reset saldo cuti semua pegawai untuk tahun tertentu
     */
    public function resetSaldo(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2100',
            'total_hari' => 'required|integer|min:0|max:365',
        ]);

        try {
            $tahun = $request->tahun;
            $totalHari = (int) $request->total_hari;
            $pegawaiList = Pegawai::where('status', 'aktif')->get();

            $count = 0;
            foreach ($pegawaiList as $pegawai) {
                $cutiDisetujui = Cuti::where('pegawai_id', $pegawai->id)
                    ->where('jenis_cuti', 'tahunan')
                    ->where('status', Cuti::STATUS_DISETUJUI)
                    ->whereYear('tanggal_mulai', $tahun)
                    ->sum('lama_hari');

                SaldoCuti::updateOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'tahun' => $tahun,
                    ],
                    [
                        'total_hari' => $totalHari,
                        'digunakan' => $cutiDisetujui,
                        'sisa_hari' => $totalHari - $cutiDisetujui,
                        'keterangan' => "Reset saldo cuti tahun {$tahun}",
                    ]
                );
                $count++;
            }

            return redirect()->route('admin.cuti.saldo', ['tahun' => $tahun])
                ->with('success', "Saldo cuti {$count} pegawai tahun {$tahun} berhasil di-reset!");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reset saldo: ' . $e->getMessage());
        }
    }
}