<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\RiwayatJabatan;
use App\Models\PermintaanEditRiwayat;
use App\Models\Jabatan;
use App\Models\Kantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatJabatanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        $riwayat = RiwayatJabatan::with(['jabatan', 'kantor'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('tahun_mulai', 'asc')
            ->get();

        $permintaanPending = PermintaanEditRiwayat::where('pegawai_id', $pegawai->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $bisaEdit = $this->cekIzinEdit($pegawai->id);

        return view('pegawai.riwayat-jabatan.index', compact(
            'pegawai',
            'riwayat',
            'permintaanPending',
            'bisaEdit'
        ));
    }

    private function cekIzinEdit($pegawaiId)
    {
        return PermintaanEditRiwayat::where('pegawai_id', $pegawaiId)
            ->where('status', 'disetujui')
            ->where('updated_at', '>=', now()->subDays(7))
            ->exists();
    }

    public function create()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        if (!$this->cekIzinEdit($pegawai->id)) {
            return redirect()->route('pegawai.riwayat-jabatan.index')
                ->with('error', 'Anda belum memiliki izin untuk menambah riwayat. Silakan ajukan permintaan ke admin.');
        }

        $jabatan = Jabatan::orderBy('nama')->get();
        $kantor = Kantor::orderBy('nama')->get();

        return view('pegawai.riwayat-jabatan.create', compact('pegawai', 'jabatan', 'kantor'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        if (!$this->cekIzinEdit($pegawai->id)) {
            return redirect()->route('pegawai.riwayat-jabatan.index')
                ->with('error', 'Anda belum memiliki izin untuk menambah riwayat.');
        }

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

        RiwayatJabatan::create([
            'pegawai_id' => $pegawai->id,
            'jabatan_id' => $request->jabatan_id,
            'kantor_id' => $request->kantor_id,
            'status_karyawan' => $request->status_karyawan,
            'jenis_kontrak' => $request->jenis_kontrak,
            'tahun_mulai' => $request->tahun_mulai,
            'tahun_selesai' => $request->tahun_selesai,
            'keterangan' => $request->keterangan,
            'status' => $request->tahun_selesai ? 'selesai' : 'aktif',
        ]);

        return redirect()->route('pegawai.riwayat-jabatan.index')
            ->with('success', 'Riwayat jabatan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$this->cekIzinEdit($pegawai->id)) {
            return redirect()->route('pegawai.riwayat-jabatan.index')
                ->with('error', 'Anda belum memiliki izin edit. Silakan ajukan permintaan ke admin.');
        }

        $riwayat = RiwayatJabatan::where('pegawai_id', $pegawai->id)->findOrFail($id);

        $jabatan = Jabatan::orderBy('nama')->get();
        $kantor = Kantor::orderBy('nama')->get();

        return view('pegawai.riwayat-jabatan.edit', compact('pegawai', 'riwayat', 'jabatan', 'kantor'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$this->cekIzinEdit($pegawai->id)) {
            return redirect()->route('pegawai.riwayat-jabatan.index')
                ->with('error', 'Anda belum memiliki izin edit.');
        }

        $riwayat = RiwayatJabatan::where('pegawai_id', $pegawai->id)->findOrFail($id);

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

        return redirect()->route('pegawai.riwayat-jabatan.index')
            ->with('success', 'Riwayat jabatan berhasil diupdate!');
    }

    public function requestEdit(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        PermintaanEditRiwayat::create([
            'pegawai_id' => $pegawai->id,
            'jenis_permintaan' => 'edit',
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);

        return redirect()->route('pegawai.riwayat-jabatan.index')
            ->with('success', 'Permintaan edit berhasil dikirim! Menunggu persetujuan admin.');
    }

    /**
     * Preview CV sendiri (PDF)
     */
    public function cetakCV()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        $riwayat = RiwayatJabatan::with(['jabatan', 'kantor'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('tahun_mulai', 'asc')
            ->get();

        $bpr = \App\Models\BPR::first();

        $pdf = Pdf::loadView('pegawai.riwayat-jabatan.cv-pdf', compact('pegawai', 'riwayat', 'bpr'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times-Roman');

        $namaFile = 'CV_Mutasi_' . str_replace(' ', '_', $pegawai->nama) . '.pdf';

        return $pdf->stream($namaFile);
    }

    /**
     * Download CV sendiri (PDF)
     */
    public function downloadCV()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        $riwayat = RiwayatJabatan::with(['jabatan', 'kantor'])
            ->where('pegawai_id', $pegawai->id)
            ->orderBy('tahun_mulai', 'asc')
            ->get();

        $bpr = \App\Models\BPR::first();

        $pdf = Pdf::loadView('pegawai.riwayat-jabatan.cv-pdf', compact('pegawai', 'riwayat', 'bpr'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times-Roman');

        $namaFile = 'CV_Mutasi_' . str_replace(' ', '_', $pegawai->nama) . '.pdf';

        return $pdf->download($namaFile);
    }
}