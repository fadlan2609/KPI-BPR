<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PegawaiKeluarga;
use App\Models\PegawaiAnak;
use App\Models\PegawaiPendidikan;
use App\Models\AnakPendidikan;
use App\Models\KeluargaPendidikan;
use App\Models\BPR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfilPegawaiController extends Controller
{
    /**
     * Halaman profil pegawai
     */
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        $pegawai->load([
            'jabatan',
            'kantor',
            'keluarga.pendidikan', // ✅ load pendidikan keluarga
            'anak.pendidikan',
            'pendidikan',
        ]);

        return view('pegawai.profil.index', compact('pegawai', 'user'));
    }

    /**
     * Update data profil pegawai (data pribadi + TTD + foto + keluarga + anak + pendidikan)
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return back()->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        $request->validate([
            // Data utama
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status_pernikahan' => 'nullable|string|max:50',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'nama_pasangan' => 'nullable|string|max:255',
            'jumlah_anak' => 'nullable|integer|min:0',

            // TTD
            'ttd_type' => 'nullable|in:gambar,barcode',
            'ttd_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',

            // Foto Profil
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Keluarga
            'keluarga' => 'nullable|array',
            'keluarga.*.nama' => 'nullable|string|max:255',
            'keluarga.*.nik' => 'nullable|string|max:20',
            'keluarga.*.tempat_lahir' => 'nullable|string|max:100',
            'keluarga.*.tanggal_lahir' => 'nullable|date',
            'keluarga.*.jenis_kelamin' => 'nullable|in:L,P',
            'keluarga.*.pendidikan_terakhir' => 'nullable|string|max:100',
            'keluarga.*.pekerjaan' => 'nullable|string|max:100',
            'keluarga.*.no_telp' => 'nullable|string|max:20',
            'keluarga.*.hubungan' => 'nullable|in:istri,suami',
            'keluarga.*.pendidikan' => 'nullable|array',
            'keluarga.*.pendidikan.*.jenjang' => 'nullable|string|max:20',
            'keluarga.*.pendidikan.*.nama_sekolah' => 'nullable|string|max:255',
            'keluarga.*.pendidikan.*.jurusan' => 'nullable|string|max:255',
            'keluarga.*.pendidikan.*.kota' => 'nullable|string|max:100',
            'keluarga.*.pendidikan.*.tahun_masuk' => 'nullable|integer',
            'keluarga.*.pendidikan.*.tahun_lulus' => 'nullable|integer',

            // Anak
            'anak' => 'nullable|array',
            'anak.*.nama' => 'nullable|string|max:255',
            'anak.*.nik' => 'nullable|string|max:20',
            'anak.*.tanggal_lahir' => 'nullable|date',
            'anak.*.jenis_kelamin' => 'nullable|in:L,P',
            'anak.*.urutan_anak' => 'nullable|integer|min:1',
            'anak.*.status_anak' => 'nullable|string|max:50',
            'anak.*.pendidikan' => 'nullable|array',
            'anak.*.pendidikan.*.jenjang' => 'nullable|string|max:20',
            'anak.*.pendidikan.*.nama_sekolah' => 'nullable|string|max:255',
            'anak.*.pendidikan.*.kota' => 'nullable|string|max:100',
            'anak.*.pendidikan.*.tahun_masuk' => 'nullable|integer',
            'anak.*.pendidikan.*.tahun_lulus' => 'nullable|integer',

            // Pendidikan Pegawai
            'pendidikan' => 'nullable|array',
            'pendidikan.*.jenjang' => 'nullable|string|max:20',
            'pendidikan.*.nama_sekolah' => 'nullable|string|max:255',
            'pendidikan.*.jurusan' => 'nullable|string|max:255',
            'pendidikan.*.kota' => 'nullable|string|max:100',
            'pendidikan.*.tahun_masuk' => 'nullable|integer',
            'pendidikan.*.tahun_lulus' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except([
                'ttd_image', 'foto_profil', '_token', '_method',
                'keluarga', 'anak', 'pendidikan'
            ]);

            // Handle upload TTD baru
            if ($request->hasFile('ttd_image')) {
                if ($pegawai->ttd_image && Storage::disk('public')->exists($pegawai->ttd_image)) {
                    Storage::disk('public')->delete($pegawai->ttd_image);
                }

                $data['ttd_image'] = $request->file('ttd_image')->store('pegawai/ttd', 'public');
                $data['ttd_type'] = $request->ttd_type ?? 'gambar';
            }

            // Handle hapus TTD
            if ($request->has('hapus_ttd') && $request->hapus_ttd == 1) {
                if ($pegawai->ttd_image && Storage::disk('public')->exists($pegawai->ttd_image)) {
                    Storage::disk('public')->delete($pegawai->ttd_image);
                }
                $data['ttd_image'] = null;
                $data['ttd_type'] = null;
            }

            // ==================== FOTO PROFIL ====================
            if ($request->hasFile('foto_profil')) {
                if ($pegawai->foto_profil && Storage::disk('public')->exists($pegawai->foto_profil)) {
                    Storage::disk('public')->delete($pegawai->foto_profil);
                }
                $data['foto_profil'] = $request->file('foto_profil')->store('pegawai/foto', 'public');
            }

            if ($request->has('hapus_foto') && $request->hapus_foto == 1) {
                if ($pegawai->foto_profil && Storage::disk('public')->exists($pegawai->foto_profil)) {
                    Storage::disk('public')->delete($pegawai->foto_profil);
                }
                $data['foto_profil'] = null;
            }

            $pegawai->update($data);

            // ==================== KELUARGA ====================
            if ($request->has('keluarga')) {
                foreach ($pegawai->keluarga as $k) {
                    $k->pendidikan()->delete();
                }
                $pegawai->keluarga()->delete();

                foreach ($request->keluarga as $k) {
                    if (empty($k['nama'])) continue;
                    $keluarga = $pegawai->keluarga()->create([
                        'hubungan' => $k['hubungan'] ?? 'istri',
                        'nama' => $k['nama'],
                        'nik' => $k['nik'] ?? null,
                        'tempat_lahir' => $k['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $k['tanggal_lahir'] ?? null,
                        'jenis_kelamin' => $k['jenis_kelamin'] ?? 'P',
                        'pendidikan_terakhir' => $k['pendidikan_terakhir'] ?? null,
                        'pekerjaan' => $k['pekerjaan'] ?? null,
                        'no_telp' => $k['no_telp'] ?? null,
                    ]);

                    // ✅ Pendidikan keluarga
                    if (!empty($k['pendidikan']) && is_array($k['pendidikan'])) {
                        foreach ($k['pendidikan'] as $p) {
                            if (empty($p['nama_sekolah'])) continue;
                            $keluarga->pendidikan()->create([
                                'jenjang' => $p['jenjang'] ?? 'S1',
                                'nama_sekolah' => $p['nama_sekolah'],
                                'jurusan' => $p['jurusan'] ?? null,
                                'kota' => $p['kota'] ?? null,
                                'tahun_masuk' => $p['tahun_masuk'] ?? null,
                                'tahun_lulus' => $p['tahun_lulus'] ?? null,
                            ]);
                        }
                    }
                }
            }

            // ==================== ANAK ====================
            if ($request->has('anak')) {
                foreach ($pegawai->anak as $a) {
                    $a->pendidikan()->delete();
                }
                $pegawai->anak()->delete();

                foreach ($request->anak as $a) {
                    if (empty($a['nama'])) continue;
                    $anak = $pegawai->anak()->create([
                        'nama' => $a['nama'],
                        'nik' => $a['nik'] ?? null,
                        'tempat_lahir' => $a['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $a['tanggal_lahir'] ?? null,
                        'jenis_kelamin' => $a['jenis_kelamin'] ?? 'L',
                        'urutan_anak' => $a['urutan_anak'] ?? 1,
                        'status_anak' => $a['status_anak'] ?? 'kandung',
                    ]);

                    if (!empty($a['pendidikan']) && is_array($a['pendidikan'])) {
                        foreach ($a['pendidikan'] as $p) {
                            if (empty($p['nama_sekolah'])) continue;
                            $anak->pendidikan()->create([
                                'jenjang' => $p['jenjang'] ?? 'SD',
                                'nama_sekolah' => $p['nama_sekolah'],
                                'kota' => $p['kota'] ?? null,
                                'tahun_masuk' => $p['tahun_masuk'] ?? null,
                                'tahun_lulus' => $p['tahun_lulus'] ?? null,
                            ]);
                        }
                    }
                }
            }

            // ==================== PENDIDIKAN PEGAWAI ====================
            if ($request->has('pendidikan')) {
                $pegawai->pendidikan()->delete();
                foreach ($request->pendidikan as $p) {
                    if (empty($p['nama_sekolah'])) continue;
                    $pegawai->pendidikan()->create([
                        'jenjang' => $p['jenjang'] ?? 'S1',
                        'nama_sekolah' => $p['nama_sekolah'],
                        'jurusan' => $p['jurusan'] ?? null,
                        'kota' => $p['kota'] ?? null,
                        'tahun_masuk' => $p['tahun_masuk'] ?? null,
                        'tahun_lulus' => $p['tahun_lulus'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('pegawai.profil.index')
                ->with('success', 'Profil berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai!']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('pegawai.profil.index')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Cetak CV Pegawai (PDF) — hanya untuk dirinya sendiri
     */
    public function cetakCV()
    {
        try {
            $user = Auth::user();
            $pegawai = $user->pegawai;

            if (!$pegawai) {
                return redirect()->route('login')
                    ->with('error', 'Akun tidak terhubung dengan pegawai!');
            }

            $pegawai->load([
                'jabatan',
                'kantor',
                'atasanLangsung.jabatan',
                'keluarga.pendidikan', // ✅ load pendidikan keluarga
                'anak.pendidikan',
                'pendidikan',
            ]);

            $bpr = BPR::first();

            $pdf = Pdf::loadView('admin.pegawai.cv-pdf', compact('pegawai', 'bpr'));
            $pdf->setPaper('a4', 'portrait');

            $namaFile = 'CV_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $pegawai->nama) . '.pdf';

            return $pdf->stream($namaFile, ['Attachment' => false]);

        } catch (\Exception $e) {
            return redirect()->route('pegawai.profil.index')
                ->with('error', 'Gagal cetak CV: ' . $e->getMessage());
        }
    }
}