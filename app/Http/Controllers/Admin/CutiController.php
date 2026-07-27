<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use App\Helpers\CutiHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cuti = Cuti::with(['pegawai', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.cuti.index', compact('cuti'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawai = Pegawai::where('status', 'aktif')->get();
        return view('admin.cuti.create', compact('pegawai'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,khusus,lainnya',
            'tanggal_mulai' => 'required|date',
            'lama_hari' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $lamaHari = (int) $request->lama_hari;
        
        // Hitung tanggal selesai berdasarkan lama hari kerja
        $tanggalSelesai = CutiHelper::hitungTanggalSelesai($tanggalMulai, $lamaHari);

        // Cek saldo cuti (hanya untuk cuti tahunan)
        if ($request->jenis_cuti == 'tahunan') {
            $tahun = $tanggalMulai->year;
            $saldo = SaldoCuti::where('pegawai_id', $request->pegawai_id)
                ->where('tahun', $tahun)
                ->first();

            if (!$saldo) {
                return redirect()->back()
                    ->with('error', 'Saldo cuti tidak ditemukan untuk tahun ' . $tahun . '!')
                    ->withInput();
            }

            if ($saldo->sisa_hari < $lamaHari) {
                return redirect()->back()
                    ->with('error', 'Saldo cuti tidak mencukupi! Sisa: ' . $saldo->sisa_hari . ' hari, Dibutuhkan: ' . $lamaHari . ' hari')
                    ->withInput();
            }
        }

        // Buat pengajuan cuti
        $cuti = Cuti::create([
            'pegawai_id' => $request->pegawai_id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
            'lama_hari' => $lamaHari,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dibuat! (Durasi: ' . $lamaHari . ' hari kerja)');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cuti = Cuti::with(['pegawai', 'approvedBy', 'pegawai.jabatan'])
            ->findOrFail($id);
            
        // Hitung detail hari
        $tanggalMulai = Carbon::parse($cuti->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($cuti->tanggal_selesai);
        
        $detailHari = [];
        $current = $tanggalMulai->copy();
        $hariKerja = 0;
        $hariLibur = 0;
        
        while ($current <= $tanggalSelesai) {
            $isWeekend = $current->isWeekend();
            if ($isWeekend) {
                $hariLibur++;
            } else {
                $hariKerja++;
            }
            
            $detailHari[] = [
                'tanggal' => $current->format('Y-m-d'),
                'hari' => $current->translatedFormat('l'),
                'is_weekend' => $isWeekend,
                'is_kerja' => !$isWeekend,
                'format' => $current->format('d/m/Y')
            ];
            $current->addDay();
        }
        
        $totalHariKalender = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        
        return view('admin.cuti.show', compact('cuti', 'detailHari', 'totalHariKalender', 'hariLibur', 'hariKerja'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cuti = Cuti::findOrFail($id);
        
        // Hanya cuti dengan status pending yang bisa diedit
        if ($cuti->status != 'pending') {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Hanya pengajuan dengan status Pending yang dapat diedit!');
        }
        
        $pegawai = Pegawai::where('status', 'aktif')->get();
        
        return view('admin.cuti.edit', compact('cuti', 'pegawai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        
        if ($cuti->status != 'pending') {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Tidak dapat mengubah pengajuan yang sudah diproses!');
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,khusus,lainnya',
            'tanggal_mulai' => 'required|date',
            'lama_hari' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $lamaHari = (int) $request->lama_hari;
        
        // Hitung tanggal selesai berdasarkan lama hari kerja
        $tanggalSelesai = CutiHelper::hitungTanggalSelesai($tanggalMulai, $lamaHari);

        // Cek saldo cuti (hanya untuk cuti tahunan)
        if ($request->jenis_cuti == 'tahunan') {
            $tahun = $tanggalMulai->year;
            $saldo = SaldoCuti::where('pegawai_id', $request->pegawai_id)
                ->where('tahun', $tahun)
                ->first();

            if (!$saldo) {
                return redirect()->back()
                    ->with('error', 'Saldo cuti tidak ditemukan untuk tahun ' . $tahun . '!')
                    ->withInput();
            }

            if ($saldo->sisa_hari < $lamaHari) {
                return redirect()->back()
                    ->with('error', 'Saldo cuti tidak mencukupi! Sisa: ' . $saldo->sisa_hari . ' hari, Dibutuhkan: ' . $lamaHari . ' hari')
                    ->withInput();
            }
        }

        $cuti->update([
            'pegawai_id' => $request->pegawai_id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
            'lama_hari' => $lamaHari,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil diupdate! (Durasi: ' . $lamaHari . ' hari kerja)');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cuti = Cuti::findOrFail($id);
        
        // Cek apakah cuti sudah disetujui
        if ($cuti->status == 'disetujui') {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Tidak dapat menghapus cuti yang sudah disetujui!');
        }
        
        // Cek apakah cuti sudah ditolak
        if ($cuti->status == 'ditolak') {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Tidak dapat menghapus cuti yang sudah ditolak!');
        }

        $namaPegawai = $cuti->pegawai->nama;
        $cuti->delete();

        return redirect()->route('admin.cuti.index')
            ->with('success', 'Pengajuan cuti ' . $namaPegawai . ' berhasil dihapus!');
    }

    /**
     * Approve cuti
     */
    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Pengajuan sudah diproses!');
        }

        $cuti->update([
            'status' => 'disetujui',
            'approved_by' => auth()->user()->pegawai_id,
            'approved_at' => Carbon::now(),
        ]);

        // Update saldo cuti (hanya untuk cuti tahunan)
        if ($cuti->jenis_cuti == 'tahunan') {
            $tahun = Carbon::parse($cuti->tanggal_mulai)->year;
            $saldo = SaldoCuti::where('pegawai_id', $cuti->pegawai_id)
                ->where('tahun', $tahun)
                ->first();

            if ($saldo) {
                $saldo->update([
                    'digunakan' => $saldo->digunakan + $cuti->lama_hari,
                    'sisa_hari' => $saldo->sisa_hari - $cuti->lama_hari,
                ]);
            }
        }

        return redirect()->route('admin.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil disetujui! (Durasi: ' . $cuti->lama_hari . ' hari kerja)');
    }

    /**
     * Reject cuti
     */
    public function reject($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Pengajuan sudah diproses!');
        }

        $cuti->update([
            'status' => 'ditolak',
            'approved_by' => auth()->user()->pegawai_id,
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.cuti.index')
            ->with('success', 'Pengajuan cuti ditolak!');
    }

    /**
     * Reopen / Batalkan penolakan cuti
     */
    public function reopen($id)
    {
        $cuti = Cuti::findOrFail($id);

        // Hanya bisa membatalkan penolakan
        if ($cuti->status != 'ditolak') {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Hanya pengajuan yang ditolak yang dapat dibuka kembali!');
        }

        $cuti->update([
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('admin.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dibuka kembali! Status kembali ke Pending.');
    }

    /**
     * Batalkan persetujuan cuti
     */
    public function cancelApproval($id)
    {
        $cuti = Cuti::findOrFail($id);

        // Hanya bisa membatalkan persetujuan
        if ($cuti->status != 'disetujui') {
            return redirect()->route('admin.cuti.index')
                ->with('error', 'Hanya pengajuan yang disetujui yang dapat dibatalkan persetujuannya!');
        }

        // Kembalikan saldo cuti (untuk cuti tahunan)
        if ($cuti->jenis_cuti == 'tahunan') {
            $tahun = Carbon::parse($cuti->tanggal_mulai)->year;
            $saldo = SaldoCuti::where('pegawai_id', $cuti->pegawai_id)
                ->where('tahun', $tahun)
                ->first();

            if ($saldo) {
                $saldo->update([
                    'digunakan' => $saldo->digunakan - $cuti->lama_hari,
                    'sisa_hari' => $saldo->sisa_hari + $cuti->lama_hari,
                ]);
            }
        }

        $cuti->update([
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('admin.cuti.index')
            ->with('success', 'Persetujuan cuti berhasil dibatalkan! Status kembali ke Pending.');
    }

    /**
     * Display saldo cuti for a pegawai
     */
    public function saldo($pegawaiId)
    {
        $pegawai = Pegawai::findOrFail($pegawaiId);
        $saldo = SaldoCuti::where('pegawai_id', $pegawaiId)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('admin.cuti.saldo', compact('pegawai', 'saldo'));
    }

    /**
     * Get saldo cuti via AJAX
     */
    public function getSaldo($pegawaiId)
    {
        $tahun = Carbon::now()->year;
        $saldo = SaldoCuti::where('pegawai_id', $pegawaiId)
            ->where('tahun', $tahun)
            ->first();

        return response()->json([
            'saldo' => $saldo ? [
                'total_hari' => $saldo->total_hari,
                'digunakan' => $saldo->digunakan,
                'sisa_hari' => $saldo->sisa_hari,
            ] : null
        ]);
    }

    /**
     * Calculate date range based on start date and days
     */
    public function calculateDateRange(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'lama_hari' => 'required|integer|min:1',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $lamaHari = (int) $request->lama_hari;
        
        $tanggalSelesai = CutiHelper::hitungTanggalSelesai($tanggalMulai, $lamaHari);
        
        $hariKerja = CutiHelper::hitungHariKerja($tanggalMulai, $tanggalSelesai);
        $hariLibur = CutiHelper::hitungHariLibur($tanggalMulai, $tanggalSelesai);
        $totalHari = CutiHelper::hitungTotalHari($tanggalMulai, $tanggalSelesai);
        
        return response()->json([
            'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
            'tanggal_selesai_format' => $tanggalSelesai->format('d/m/Y'),
            'total_hari_kalender' => $totalHari,
            'hari_kerja' => $hariKerja,
            'hari_libur' => $hariLibur,
        ]);
    }
}