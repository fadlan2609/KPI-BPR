<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Kantor;
use App\Models\User;
use App\Models\SaldoCuti;
use App\Models\PegawaiKeluarga;
use App\Models\PegawaiAnak;
use App\Models\PegawaiPendidikan;
use App\Models\AnakPendidikan;
use App\Models\KeluargaPendidikan;
use App\Models\BPR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PegawaiImport;
use App\Exports\PegawaiExport;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $jabatan = $request->jabatan;
        $status = $request->status;

        $pegawai = Pegawai::with([
            'jabatan',
            'kantor',
            'atasanLangsung',
            'user',
            'bawahanLangsung',
            'atasanLangsung.jabatan.atasan'
        ])
        ->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('no_hp', 'LIKE', "%{$search}%");
            });
        })
        ->when($jabatan, function ($query, $jabatan) {
            return $query->where('jabatan_id', $jabatan);
        })
        ->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        foreach ($pegawai as $p) {
            $p->atasan_penilai = null;
            if ($p->atasanLangsung && $p->atasanLangsung->jabatan) {
                $jabatanAtasan = $p->atasanLangsung->jabatan->atasan;
                if ($jabatanAtasan) {
                    $p->atasan_penilai = Pegawai::where('jabatan_id', $jabatanAtasan->id)
                        ->where('status', 'aktif')
                        ->first();
                }
            }

            $totalBawahan = 0;
            foreach ($p->bawahanLangsung as $bawahan) {
                $totalBawahan += $bawahan->bawahanLangsung->count();
            }
            $p->total_bawahan_tidak_langsung = $totalBawahan;
        }

        $jabatanOptions = Jabatan::all();
        $kantorOptions = Kantor::all();
        $pegawaiOptions = Pegawai::where('status', 'aktif')->get();

        return view('admin.pegawai.index', compact(
            'pegawai',
            'jabatanOptions',
            'kantorOptions',
            'pegawaiOptions',
            'search',
            'jabatan',
            'status'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jabatanOptions = Jabatan::all();
        $kantorOptions = Kantor::all();
        $pegawaiOptions = Pegawai::where('status', 'aktif')->get();

        $jabatan = $jabatanOptions;
        $kantor = $kantorOptions;

        return view('admin.pegawai.create', compact(
            'jabatanOptions', 'kantorOptions', 'pegawaiOptions',
            'jabatan', 'kantor'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|string|max:50|unique:pegawai',
            'jabatan_id' => 'required|exists:jabatan,id',
            'kantor_id' => 'required|exists:kantor,id',
            'status' => 'required|in:aktif,keluar,mengundurkan_diri',
            'atasan_langsung_id' => 'nullable|exists:pegawai,id',
            'tanggal_lahir' => 'nullable|date',
            'tanggal_masuk' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status_pernikahan' => 'nullable|string|max:20',
            'status_karyawan' => 'nullable|string|max:50',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'nama_pasangan' => 'nullable|string|max:100',
            'jumlah_anak' => 'nullable|integer|min:0',
            'nik' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'ttd_type' => 'nullable|in:gambar,barcode',
            'ttd_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->except(['ttd_image', 'foto_profil', '_token']);

            if ($request->hasFile('ttd_image')) {
                $data['ttd_image'] = $request->file('ttd_image')->store('pegawai/ttd', 'public');
                $data['ttd_type'] = $request->ttd_type ?? 'gambar';
            }

            if ($request->hasFile('foto_profil')) {
                $data['foto_profil'] = $request->file('foto_profil')->store('pegawai/foto', 'public');
                $data['foto_crop_x'] = $request->foto_crop_x ?? 0.5;
                $data['foto_crop_y'] = $request->foto_crop_y ?? 0.5;
                $data['foto_crop_zoom'] = $request->foto_crop_zoom ?? 1;
                $data['foto_crop_rotate'] = $request->foto_crop_rotate ?? 0;
            }

            $pegawai = Pegawai::create($data);

            SaldoCuti::create([
                'pegawai_id' => $pegawai->id,
                'tahun' => date('Y'),
                'total_hari' => 12,
                'digunakan' => 0,
                'sisa_hari' => 12,
            ]);

            DB::commit();

            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Pegawai berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan pegawai: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pegawai = Pegawai::with([
            'jabatan',
            'kantor',
            'atasanLangsung',
            'user',
            'gajiPokokAktif',
            'bawahanLangsung',
            'saldoCuti',
            'keluarga.pendidikan',
            'anak.pendidikan',
            'pendidikan'
        ])->findOrFail($id);

        $atasanPenilai = null;
        if ($pegawai->atasanLangsung && $pegawai->atasanLangsung->jabatan) {
            $jabatanAtasan = $pegawai->atasanLangsung->jabatan->atasan;
            if ($jabatanAtasan) {
                $atasanPenilai = Pegawai::where('jabatan_id', $jabatanAtasan->id)
                    ->where('status', 'aktif')
                    ->first();
            }
        }

        return view('admin.pegawai.show', compact('pegawai', 'atasanPenilai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pegawai = Pegawai::with([
            'jabatan',
            'kantor',
            'atasanLangsung',
            'user',
            'keluarga.pendidikan',
            'anak.pendidikan',
            'pendidikan'
        ])->findOrFail($id);

        $jabatanOptions = Jabatan::all();
        $kantorOptions = Kantor::all();
        $pegawaiOptions = Pegawai::where('status', 'aktif')
            ->where('id', '!=', $id)
            ->get();

        $jabatan = $jabatanOptions;
        $kantor = $kantorOptions;

        return view('admin.pegawai.edit', compact(
            'pegawai',
            'jabatanOptions',
            'kantorOptions',
            'pegawaiOptions',
            'jabatan',
            'kantor'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|string|max:50|unique:pegawai,nip,' . $id,
            'jabatan_id' => 'required|exists:jabatan,id',
            'kantor_id' => 'required|exists:kantor,id',
            'status' => 'required|in:aktif,keluar,mengundurkan_diri',
            'atasan_langsung_id' => 'nullable|exists:pegawai,id',
            'tanggal_lahir' => 'nullable|date',
            'tanggal_masuk' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status_pernikahan' => 'nullable|string|max:20',
            'status_karyawan' => 'nullable|string|max:50',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'nama_pasangan' => 'nullable|string|max:100',
            'jumlah_anak' => 'nullable|integer|min:0',
            'nik' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'ttd_type' => 'nullable|in:gambar,barcode',
            'ttd_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['ttd_image', 'foto_profil', '_token', '_method']);

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
                $data['foto_crop_x'] = $request->foto_crop_x ?? 0.5;
                $data['foto_crop_y'] = $request->foto_crop_y ?? 0.5;
                $data['foto_crop_zoom'] = $request->foto_crop_zoom ?? 1;
                $data['foto_crop_rotate'] = $request->foto_crop_rotate ?? 0;
            }

            if ($request->has('hapus_foto') && $request->hapus_foto == 1) {
                if ($pegawai->foto_profil && Storage::disk('public')->exists($pegawai->foto_profil)) {
                    Storage::disk('public')->delete($pegawai->foto_profil);
                }
                $data['foto_profil'] = null;
                $data['foto_crop_x'] = null;
                $data['foto_crop_y'] = null;
                $data['foto_crop_zoom'] = 1;
                $data['foto_crop_rotate'] = 0;
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
                        'alamat' => $k['alamat'] ?? null,
                        'no_telp' => $k['no_telp'] ?? null,
                    ]);

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
                        'pendidikan_terakhir' => $a['pendidikan_terakhir'] ?? null,
                        'sekolah_sekarang' => $a['sekolah_sekarang'] ?? null,
                        'pekerjaan' => $a['pekerjaan'] ?? null,
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
                        'no_ijazah' => $p['no_ijazah'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Pegawai berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        DB::beginTransaction();

        try {
            if ($pegawai->foto_profil && Storage::disk('public')->exists($pegawai->foto_profil)) {
                Storage::disk('public')->delete($pegawai->foto_profil);
            }

            if ($pegawai->ttd_image && Storage::disk('public')->exists($pegawai->ttd_image)) {
                Storage::disk('public')->delete($pegawai->ttd_image);
            }

            foreach ($pegawai->keluarga as $k) {
                $k->pendidikan()->delete();
            }
            $pegawai->keluarga()->delete();

            foreach ($pegawai->anak as $a) {
                $a->pendidikan()->delete();
            }
            $pegawai->anak()->delete();
            $pegawai->pendidikan()->delete();

            if ($pegawai->user) {
                $pegawai->user->delete();
            }

            SaldoCuti::where('pegawai_id', $id)->delete();
            $pegawai->gajiPokok()->delete();
            $pegawai->delete();

            DB::commit();

            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Pegawai berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Gagal menghapus pegawai: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete pegawai
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pegawai,id'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->ids as $id) {
                $pegawai = Pegawai::find($id);
                if ($pegawai) {
                    if ($pegawai->foto_profil && Storage::disk('public')->exists($pegawai->foto_profil)) {
                        Storage::disk('public')->delete($pegawai->foto_profil);
                    }

                    if ($pegawai->ttd_image && Storage::disk('public')->exists($pegawai->ttd_image)) {
                        Storage::disk('public')->delete($pegawai->ttd_image);
                    }

                    foreach ($pegawai->keluarga as $k) {
                        $k->pendidikan()->delete();
                    }
                    $pegawai->keluarga()->delete();

                    foreach ($pegawai->anak as $a) {
                        $a->pendidikan()->delete();
                    }
                    $pegawai->anak()->delete();
                    $pegawai->pendidikan()->delete();

                    if ($pegawai->user) {
                        $pegawai->user->delete();
                    }
                    SaldoCuti::where('pegawai_id', $id)->delete();
                    $pegawai->gajiPokok()->delete();
                    $pegawai->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' pegawai berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cetak CV Lengkap Pegawai (PDF) — dengan TTD & foto otomatis
     */
    public function cetakCV($id)
    {
        try {
            $pegawai = Pegawai::with([
                'jabatan',
                'kantor',
                'atasanLangsung.jabatan',
                'keluarga.pendidikan',
                'anak.pendidikan',
                'pendidikan',
            ])->findOrFail($id);

            $bpr = BPR::first();

            $pdf = Pdf::loadView('admin.pegawai.cv-pdf', compact('pegawai', 'bpr'));
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'sans-serif',
                'chroot' => [storage_path('app/public')],
            ]);

            $namaFile = 'CV_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $pegawai->nama) . '.pdf';

            return $pdf->stream($namaFile, ['Attachment' => false]);

        } catch (\Exception $e) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Gagal cetak CV: ' . $e->getMessage());
        }
    }

    /**
     * Reset password for pegawai
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $pegawai = Pegawai::findOrFail($id);

        if (!$pegawai->user) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Pegawai belum memiliki akun user!');
        }

        $pegawai->user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Password untuk ' . $pegawai->nama . ' berhasil direset!');
    }

    /**
     * Create user account for pegawai
     */
    public function createUser(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $pegawai = Pegawai::findOrFail($id);

        if ($pegawai->user) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Pegawai sudah memiliki akun user!');
        }

        User::create([
            'pegawai_id' => $pegawai->id,
            'name' => $pegawai->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pegawai',
            'is_active' => true,
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Akun user untuk ' . $pegawai->nama . ' berhasil dibuat! Username: ' . $request->username);
    }

    /**
     * Delete user account from pegawai
     */
    public function deleteUser($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        if (!$pegawai->user) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak memiliki akun user!'
            ], 404);
        }

        $pegawai->user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun user berhasil dihapus!'
        ]);
    }

    /**
     * Import pegawai from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        try {
            Excel::import(new PegawaiImport, $request->file('file'));
            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Data pegawai berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel for import
     */
    public function template()
    {
        return Excel::download(new PegawaiExport, 'template_pegawai.xlsx');
    }

    /**
     * Export pegawai to Excel
     */
    public function export(Request $request)
    {
        $pegawai = Pegawai::with(['jabatan', 'kantor'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        return Excel::download(new PegawaiExport($pegawai), 'data_pegawai.xlsx');
    }

    /**
     * Get pegawai by jabatan (for AJAX)
     */
    public function getByJabatan($jabatanId)
    {
        $pegawai = Pegawai::where('jabatan_id', $jabatanId)
            ->where('status', 'aktif')
            ->select('id', 'nama', 'nip')
            ->get();

        return response()->json($pegawai);
    }

    /**
     * Get atasan for pegawai (for AJAX)
     */
    public function getAtasan($id)
    {
        $pegawai = Pegawai::with(['jabatan.atasan'])->findOrFail($id);

        $atasan = null;
        if ($pegawai->jabatan && $pegawai->jabatan->atasan) {
            $atasan = Pegawai::where('jabatan_id', $pegawai->jabatan->atasan->id)
                ->where('status', 'aktif')
                ->select('id', 'nama', 'nip')
                ->first();
        }

        return response()->json($atasan);
    }

    /**
     * Get atasan penilai (atasan dari atasan langsung) - for AJAX
     */
    public function getAtasanPenilai($id)
    {
        $pegawai = Pegawai::with(['jabatan.atasan.atasan'])->findOrFail($id);

        $atasanPenilai = null;

        if ($pegawai->jabatan && $pegawai->jabatan->atasan) {
            $jabatanAtasan = $pegawai->jabatan->atasan;

            if ($jabatanAtasan && $jabatanAtasan->atasan) {
                $jabatanPenilai = $jabatanAtasan->atasan;
                $atasanPenilai = Pegawai::where('jabatan_id', $jabatanPenilai->id)
                    ->where('status', 'aktif')
                    ->select('id', 'nama', 'nip')
                    ->first();
            }
        }

        return response()->json([
            'atasan_penilai' => $atasanPenilai ? [
                'id' => $atasanPenilai->id,
                'nama' => $atasanPenilai->nama,
                'nip' => $atasanPenilai->nip,
                'jabatan' => $atasanPenilai->jabatan->nama ?? '-'
            ] : null
        ]);
    }

    /**
     * Get all pegawai for dropdown (for AJAX)
     */
    public function getOptions()
    {
        $pegawai = Pegawai::where('status', 'aktif')
            ->select('id', 'nama', 'nip')
            ->orderBy('nama')
            ->get();

        return response()->json($pegawai);
    }

    /**
     * Get detail pegawai (for AJAX)
     */
    public function getDetail($id)
    {
        $pegawai = Pegawai::with(['jabatan', 'kantor', 'atasanLangsung', 'user'])
            ->findOrFail($id);

        return response()->json($pegawai);
    }

    /**
     * Get hierarchical structure for pegawai (for AJAX)
     */
    public function getHierarchy($id)
    {
        $pegawai = Pegawai::with(['jabatan', 'atasanLangsung', 'bawahanLangsung'])->findOrFail($id);

        $hierarchy = [
            'pegawai' => [
                'id' => $pegawai->id,
                'nama' => $pegawai->nama,
                'nip' => $pegawai->nip,
                'jabatan' => $pegawai->jabatan->nama ?? '-'
            ],
            'atasan_langsung' => null,
            'atasan_penilai' => null,
            'bawahan' => []
        ];

        if ($pegawai->atasanLangsung) {
            $hierarchy['atasan_langsung'] = [
                'id' => $pegawai->atasanLangsung->id,
                'nama' => $pegawai->atasanLangsung->nama,
                'nip' => $pegawai->atasanLangsung->nip,
                'jabatan' => $pegawai->atasanLangsung->jabatan->nama ?? '-'
            ];

            if ($pegawai->atasanLangsung->jabatan && $pegawai->atasanLangsung->jabatan->atasan) {
                $jabatanPenilai = $pegawai->atasanLangsung->jabatan->atasan;
                $atasanPenilai = Pegawai::where('jabatan_id', $jabatanPenilai->id)
                    ->where('status', 'aktif')
                    ->first();
                if ($atasanPenilai) {
                    $hierarchy['atasan_penilai'] = [
                        'id' => $atasanPenilai->id,
                        'nama' => $atasanPenilai->nama,
                        'nip' => $atasanPenilai->nip,
                        'jabatan' => $atasanPenilai->jabatan->nama ?? '-'
                    ];
                }
            }
        }

        foreach ($pegawai->bawahanLangsung as $bawahan) {
            $hierarchy['bawahan'][] = [
                'id' => $bawahan->id,
                'nama' => $bawahan->nama,
                'nip' => $bawahan->nip,
                'jabatan' => $bawahan->jabatan->nama ?? '-'
            ];
        }

        return response()->json($hierarchy);
    }

    /**
     * Update status pegawai (for AJAX)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aktif,keluar,mengundurkan_diri'
        ]);

        $pegawai = Pegawai::findOrFail($id);

        if ($request->status != 'aktif' && $pegawai->user) {
            $pegawai->user->update(['is_active' => false]);
        } elseif ($request->status == 'aktif' && $pegawai->user) {
            $pegawai->user->update(['is_active' => true]);
        }

        $pegawai->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status pegawai berhasil diperbarui!'
        ]);
    }

    /**
     * Get saldo cuti pegawai (for AJAX)
     */
    public function getSaldoCuti($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $saldo = SaldoCuti::where('pegawai_id', $id)
            ->orderBy('tahun', 'desc')
            ->get();

        return response()->json([
            'pegawai' => $pegawai->nama,
            'saldo' => $saldo
        ]);
    }

    /**
     * Get pegawai with hierarchy for tree view (for AJAX)
     */
    public function getTree()
    {
        $pegawai = Pegawai::with(['jabatan', 'bawahanLangsung.jabatan'])
            ->whereNull('atasan_langsung_id')
            ->where('status', 'aktif')
            ->get();

        $tree = [];
        foreach ($pegawai as $root) {
            $tree[] = $this->buildTree($root);
        }

        return response()->json($tree);
    }

    /**
     * Build tree structure for hierarchy
     */
    private function buildTree($pegawai)
    {
        $node = [
            'id' => $pegawai->id,
            'nama' => $pegawai->nama,
            'nip' => $pegawai->nip,
            'jabatan' => $pegawai->jabatan->nama ?? '-',
            'children' => []
        ];

        foreach ($pegawai->bawahanLangsung as $bawahan) {
            $node['children'][] = $this->buildTree($bawahan);
        }

        return $node;
    }

    /**
     * API: Get pegawai list for dropdown with filter
     */
    public function apiList(Request $request)
    {
        $query = Pegawai::where('status', 'aktif');

        if ($request->jabatan_id) {
            $query->where('jabatan_id', $request->jabatan_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'LIKE', "%{$request->search}%")
                  ->orWhere('nip', 'LIKE', "%{$request->search}%");
            });
        }

        $pegawai = $query->select('id', 'nama', 'nip', 'jabatan_id')
            ->orderBy('nama')
            ->get();

        return response()->json($pegawai);
    }

    /**
     * API: Get pegawai by jabatan
     */
    public function apiByJabatan(Request $request)
    {
        $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id'
        ]);

        $pegawai = Pegawai::where('jabatan_id', $request->jabatan_id)
            ->where('status', 'aktif')
            ->select('id', 'nama', 'nip')
            ->orderBy('nama')
            ->get();

        return response()->json($pegawai);
    }

    /**
     * Get hierarchy for dropdown (for AJAX)
     */
    public function getHierarchyDropdown($pegawaiId = null)
    {
        $pegawai = null;
        if ($pegawaiId) {
            $pegawai = Pegawai::with(['jabatan.atasan.atasan'])->find($pegawaiId);
        }

        $data = [
            'pegawai' => $pegawai ? [
                'id' => $pegawai->id,
                'nama' => $pegawai->nama,
                'nip' => $pegawai->nip,
                'jabatan' => $pegawai->jabatan->nama ?? '-'
            ] : null,
            'atasan_langsung' => null,
            'atasan_penilai' => null,
            'semua_atasan' => []
        ];

        if ($pegawai) {
            if ($pegawai->atasanLangsung) {
                $data['atasan_langsung'] = [
                    'id' => $pegawai->atasanLangsung->id,
                    'nama' => $pegawai->atasanLangsung->nama,
                    'jabatan' => $pegawai->atasanLangsung->jabatan->nama ?? '-'
                ];
                $data['semua_atasan'][] = $data['atasan_langsung'];

                if ($pegawai->atasanLangsung->jabatan && $pegawai->atasanLangsung->jabatan->atasan) {
                    $jabatanPenilai = $pegawai->atasanLangsung->jabatan->atasan;
                    $atasanPenilai = Pegawai::where('jabatan_id', $jabatanPenilai->id)
                        ->where('status', 'aktif')
                        ->first();
                    if ($atasanPenilai) {
                        $data['atasan_penilai'] = [
                            'id' => $atasanPenilai->id,
                            'nama' => $atasanPenilai->nama,
                            'jabatan' => $atasanPenilai->jabatan->nama ?? '-'
                        ];
                        $data['semua_atasan'][] = $data['atasan_penilai'];
                    }
                }
            }
        }

        return response()->json($data);
    }
}