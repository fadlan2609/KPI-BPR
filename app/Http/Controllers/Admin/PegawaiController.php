<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Kantor;
use App\Models\User;
use App\Models\SaldoCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
        // Ambil parameter filter
        $search = $request->search;
        $jabatan = $request->jabatan;
        $status = $request->status;

        // Query dengan filter
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

        // Proses setiap pegawai untuk mendapatkan atasan penilai
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
            
            // Hitung total bawahan tidak langsung
            $totalBawahan = 0;
            foreach ($p->bawahanLangsung as $bawahan) {
                $totalBawahan += $bawahan->bawahanLangsung->count();
            }
            $p->total_bawahan_tidak_langsung = $totalBawahan;
        }

        // Data untuk dropdown filter
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

        return view('admin.pegawai.create', compact('jabatanOptions', 'kantorOptions', 'pegawaiOptions'));
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
        ]);

        DB::beginTransaction();

        try {
            $pegawai = Pegawai::create($request->all());

            // Auto create saldo cuti untuk tahun berjalan
            $tahun = date('Y');
            SaldoCuti::create([
                'pegawai_id' => $pegawai->id,
                'tahun' => $tahun,
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
            'saldoCuti'
        ])->findOrFail($id);

        // Ambil Atasan Penilai (Atasan dari Atasan Langsung)
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
        $pegawai = Pegawai::findOrFail($id);
        $jabatanOptions = Jabatan::all();
        $kantorOptions = Kantor::all();
        $pegawaiOptions = Pegawai::where('status', 'aktif')->where('id', '!=', $id)->get();

        return view('admin.pegawai.edit', compact('pegawai', 'jabatanOptions', 'kantorOptions', 'pegawaiOptions'));
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
        ]);

        $pegawai->update($request->all());

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        DB::beginTransaction();

        try {
            // Cek apakah ada user yang terkait
            if ($pegawai->user) {
                $pegawai->user->delete();
            }

            // Hapus saldo cuti
            SaldoCuti::where('pegawai_id', $id)->delete();

            // Hapus gaji pokok
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

        // Cek apakah sudah punya user
        if ($pegawai->user) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Pegawai sudah memiliki akun user!');
        }

        $user = User::create([
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

        // Cari atasan dari atasan langsung (2 level di atas)
        if ($pegawai->jabatan && $pegawai->jabatan->atasan) {
            $jabatanAtasan = $pegawai->jabatan->atasan;

            // Cari atasan dari atasan langsung (2 level di atas)
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

        // Atasan Langsung
        if ($pegawai->atasanLangsung) {
            $hierarchy['atasan_langsung'] = [
                'id' => $pegawai->atasanLangsung->id,
                'nama' => $pegawai->atasanLangsung->nama,
                'nip' => $pegawai->atasanLangsung->nip,
                'jabatan' => $pegawai->atasanLangsung->jabatan->nama ?? '-'
            ];

            // Atasan Penilai (Atasan dari Atasan Langsung)
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

        // Bawahan Langsung
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

        // Jika status diubah menjadi tidak aktif, nonaktifkan user juga
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
     * Get hierarchy for dropdown (for AJAX - showing atasan penilai)
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
            // Atasan Langsung
            if ($pegawai->atasanLangsung) {
                $data['atasan_langsung'] = [
                    'id' => $pegawai->atasanLangsung->id,
                    'nama' => $pegawai->atasanLangsung->nama,
                    'jabatan' => $pegawai->atasanLangsung->jabatan->nama ?? '-'
                ];
                $data['semua_atasan'][] = $data['atasan_langsung'];

                // Atasan Penilai
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