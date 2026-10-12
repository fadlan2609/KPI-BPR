<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatJabatan;
use App\Models\PermintaanEditRiwayat;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Kantor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with(['jabatan', 'kantor'])->where('status', 'aktif');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        $pegawai = $query->orderBy('nama')->paginate(20);

        foreach ($pegawai as $p) {
            $p->total_riwayat = RiwayatJabatan::where('pegawai_id', $p->id)->count();
        }

        $permintaanPending = PermintaanEditRiwayat::with('pegawai')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.riwayat-jabatan.index', compact('pegawai', 'permintaanPending'));
    }

    public function permintaan(Request $request)
    {
        $query = PermintaanEditRiwayat::with(['pegawai', 'approvedBy']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $permintaan = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.riwayat-jabatan.permintaan', compact('permintaan'));
    }

    public function approvePermintaan($id)
    {
        $permintaan = PermintaanEditRiwayat::findOrFail($id);

        if ($permintaan->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses!');
        }

        $adminPegawai = auth()->user()->pegawai;

        $permintaan->update([
            'status' => 'disetujui',
            'approved_by' => $adminPegawai->id ?? null,
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.riwayat-jabatan.permintaan')
            ->with('success', 'Permintaan disetujui! Pegawai dapat edit riwayat selama 7 hari.');
    }

    public function rejectPermintaan(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ]);

        $permintaan = PermintaanEditRiwayat::findOrFail($id);

        if ($permintaan->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses!');
        }

        $adminPegawai = auth()->user()->pegawai;

        $permintaan->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
            'approved_by' => $adminPegawai->id ?? null,
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.riwayat-jabatan.permintaan')
            ->with('success', 'Permintaan ditolak!');
    }

    public function create(Request $request)
    {
        $pegawaiId = $request->pegawai_id;
        $pegawai = Pegawai::findOrFail($pegawaiId);

        $jabatan = Jabatan::orderBy('nama')->get();
        $kantor = Kantor::orderBy('nama')->get();

        return view('admin.riwayat-jabatan.create', compact('pegawai', 'jabatan', 'kantor'));
    }

    public function edit($id)
    {
        $riwayat = RiwayatJabatan::with(['pegawai', 'jabatan', 'kantor'])->findOrFail($id);

        $pegawai = $riwayat->pegawai;
        $jabatan = Jabatan::orderBy('nama')->get();
        $kantor = Kantor::orderBy('nama')->get();

        return view('admin.riwayat-jabatan.edit', compact('riwayat', 'pegawai', 'jabatan', 'kantor'));
    }

    public function show($pegawaiId)
    {
        $pegawai = Pegawai::with(['jabatan', 'kantor'])->findOrFail($pegawaiId);

        $riwayat = RiwayatJabatan::with(['jabatan', 'kantor'])
            ->where('pegawai_id', $pegawaiId)
            ->orderBy('tahun_mulai', 'asc')
            ->get();

        $jabatan = Jabatan::orderBy('nama')->get();
        $kantor = Kantor::orderBy('nama')->get();

        return view('admin.riwayat-jabatan.show', compact('pegawai', 'riwayat', 'jabatan', 'kantor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'kantor_id' => 'nullable|exists:kantor,id',
            'status_karyawan' => 'nullable|in:magang,kontrak,tetap,honorer,outsourcing,lainnya',
            'jenis_kontrak' => 'nullable|string|max:100',
            'tahun_mulai' => 'required|integer|min:1980|max:' . date('Y'),
            'tahun_selesai' => 'nullable|integer|min:1980|max:' . (date('Y') + 5),
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($request->tahun_selesai && $request->tahun_selesai < $request->tahun_mulai) {
            return back()->with('error', 'Tahun selesai tidak boleh lebih kecil dari tahun mulai!')
                ->withInput();
        }

        RiwayatJabatan::create([
            'pegawai_id' => $request->pegawai_id,
            'jabatan_id' => $request->jabatan_id,
            'kantor_id' => $request->kantor_id,
            'status_karyawan' => $request->status_karyawan,
            'jenis_kontrak' => $request->jenis_kontrak,
            'tahun_mulai' => $request->tahun_mulai,
            'tahun_selesai' => $request->tahun_selesai,
            'keterangan' => $request->keterangan,
            'status' => $request->tahun_selesai ? 'selesai' : 'aktif',
        ]);

        return redirect()->route('admin.riwayat-jabatan.show', $request->pegawai_id)
            ->with('success', 'Riwayat jabatan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $riwayat = RiwayatJabatan::findOrFail($id);

        $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'kantor_id' => 'nullable|exists:kantor,id',
            'status_karyawan' => 'nullable|in:magang,kontrak,tetap,honorer,outsourcing,lainnya',
            'jenis_kontrak' => 'nullable|string|max:100',
            'tahun_mulai' => 'required|integer|min:1980|max:' . date('Y'),
            'tahun_selesai' => 'nullable|integer|min:1980|max:' . (date('Y') + 5),
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($request->tahun_selesai && $request->tahun_selesai < $request->tahun_mulai) {
            return back()->with('error', 'Tahun selesai tidak boleh lebih kecil dari tahun mulai!')
                ->withInput();
        }

        $riwayat->update([
            'jabatan_id' => $request->jabatan_id,
            'kantor_id' => $request->kantor_id,
            'status_karyawan' => $request->status_karyawan,
            'jenis_kontrak' => $request->jenis_kontrak,
            'tahun_mulai' => $request->tahun_mulai,
            'tahun_selesai' => $request->tahun_selesai,
            'keterangan' => $request->keterangan,
            'status' => $request->tahun_selesai ? 'selesai' : 'aktif',
        ]);

        return redirect()->route('admin.riwayat-jabatan.show', $riwayat->pegawai_id)
            ->with('success', 'Riwayat jabatan berhasil diupdate!');
    }

    public function destroy($id)
    {
        $riwayat = RiwayatJabatan::findOrFail($id);
        $pegawaiId = $riwayat->pegawai_id;

        $riwayat->delete();

        return redirect()->route('admin.riwayat-jabatan.show', $pegawaiId)
            ->with('success', 'Riwayat jabatan berhasil dihapus!');
    }

    /**
     * Preview CV Mutasi Pegawai (PDF)
     */
    public function cetakCV($pegawaiId)
    {
        $pegawai = Pegawai::with(['jabatan', 'kantor'])->findOrFail($pegawaiId);

        $riwayat = RiwayatJabatan::with(['jabatan', 'kantor'])
            ->where('pegawai_id', $pegawaiId)
            ->orderBy('tahun_mulai', 'asc')
            ->get();

        $bpr = \App\Models\BPR::first();

        $pdf = Pdf::loadView('admin.riwayat-jabatan.cv-pdf', compact('pegawai', 'riwayat', 'bpr'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times-Roman');

        $namaFile = 'CV_Mutasi_' . str_replace(' ', '_', $pegawai->nama) . '.pdf';

        return $pdf->stream($namaFile);
    }

    /**
     * Download CV Mutasi Pegawai (PDF)
     */
    public function downloadCV($pegawaiId)
    {
        $pegawai = Pegawai::with(['jabatan', 'kantor'])->findOrFail($pegawaiId);

        $riwayat = RiwayatJabatan::with(['jabatan', 'kantor'])
            ->where('pegawai_id', $pegawaiId)
            ->orderBy('tahun_mulai', 'asc')
            ->get();

        $bpr = \App\Models\BPR::first();

        $pdf = Pdf::loadView('admin.riwayat-jabatan.cv-pdf', compact('pegawai', 'riwayat', 'bpr'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times-Roman');

        $namaFile = 'CV_Mutasi_' . str_replace(' ', '_', $pegawai->nama) . '.pdf';

        return $pdf->download($namaFile);
    }
}