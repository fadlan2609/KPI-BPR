<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\PenilaianKPI;
use App\Models\HasilPenilaian;
use App\Models\BobotPenilaian;
use App\Models\BPR;
use App\Models\Jabatan;
use App\Models\PredikatKinerja;
use App\Models\IndikatorKPI;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenilaianExport;

class PenilaianController extends Controller
{
    protected $penilaianService;

    public function __construct(PenilaianService $penilaianService)
    {
        $this->penilaianService = $penilaianService;
    }

    /**
     * Display list of penilaian
     */
    public function index()
    {
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        if (!$periodeAktif) {
            return view('admin.penilaian.index', [
                'periodeAktif' => null,
                'pegawai' => [],
                'progressData' => [],
                'message' => 'Belum ada periode penilaian aktif'
            ]);
        }

        $pegawai = Pegawai::with(['jabatan', 'kantor'])
            ->where('status', 'aktif')
            ->paginate(15);

        $progressData = [];
        foreach ($pegawai as $p) {
            $progress = $this->penilaianService->getProgress($p->id, $periodeAktif->id);
            $progressData[$p->id] = $progress;
        }

        return view('admin.penilaian.index', compact('pegawai', 'periodeAktif', 'progressData'));
    }

    /**
     * Show progress checklist
     */
    public function progress()
    {
        $periode = PeriodePenilaian::where('is_active', true)->first();
        
        if (!$periode) {
            return view('admin.penilaian.progress', [
                'periode' => null,
                'progress' => [],
                'message' => 'Belum ada periode penilaian aktif'
            ]);
        }

        $bpr = BPR::first();
        $jabatanCount = Jabatan::count();
        $pegawaiCount = Pegawai::where('status', 'aktif')->count();
        $predikatCount = PredikatKinerja::count();
        $indikatorCount = IndikatorKPI::count();
        
        $selfAssessmentCount = PenilaianKPI::where('periode_id', $periode->id)
            ->where('level_penilai', 'self')
            ->count();
        
        $selfAssessmentTotal = Pegawai::where('status', 'aktif')->count();
        
        $laporanCount = HasilPenilaian::where('periode_id', $periode->id)
            ->where('status', 'final')
            ->count();

        $progress = [
            'bpr' => [
                'completed' => $bpr && $bpr->nama_bpr,
                'label' => 'Melengkapi Informasi BPR',
                'description' => 'Lengkapi Identitas dan Logo BPR di menu Informasi BPR > Identitas BPR',
                'last_updated' => $bpr?->updated_at?->format('d M Y H:i') ?? '-',
                'button_text' => 'Selesai Melengkapi Informasi BPR',
                'button_url' => route('admin.bpr.index'),
            ],
            'jabatan' => [
                'completed' => $jabatanCount > 0,
                'label' => 'Melengkapi Jabatan Pegawai',
                'description' => "BPR Anda memiliki {$jabatanCount} jabatan pegawai",
                'button_text' => 'Selesai Melengkapi Jabatan Pegawai',
                'button_url' => route('admin.jabatan.index'),
            ],
            'pegawai' => [
                'completed' => $pegawaiCount > 0,
                'label' => 'Melengkapi Daftar Pegawai',
                'description' => "BPR Anda memiliki {$pegawaiCount} pegawai",
                'button_text' => 'Selesai Melengkapi Daftar Pegawai',
                'button_url' => route('admin.pegawai.index'),
            ],
            'predikat' => [
                'completed' => $predikatCount > 0,
                'label' => 'Meninjau Daftar Predikat Kinerja Pegawai',
                'description' => 'Meninjau Daftar Predikat Kinerja Pegawai di menu KPI > Master Predikat Kinerja Pegawai',
                'button_text' => 'Selesai Meninjau Predikat',
                'button_url' => route('admin.predikat.index'),
            ],
            'indikator' => [
                'completed' => $indikatorCount > 0,
                'label' => 'Melengkapi Indikator Penilaian Per Jabatan',
                'description' => "Terdapat {$indikatorCount} indikator penilaian untuk {$jabatanCount} jabatan",
                'button_text' => 'Selesai Melengkapi Indikator Penilaian',
                'button_url' => route('admin.indikator.index'),
            ],
            'self_assessment' => [
                'completed' => $selfAssessmentCount > 0,
                'label' => 'Memulai Self Assessment Kinerja oleh Pegawai',
                'description' => "{$selfAssessmentCount} dari {$selfAssessmentTotal} pegawai telah mengisi self assessment",
                'button_text' => 'Selesai Self Assessment',
                'button_url' => route('admin.penilaian.index'),
            ],
            'laporan' => [
                'completed' => $laporanCount > 0,
                'label' => 'Mencetak Laporan Kinerja Pegawai',
                'description' => "{$laporanCount} laporan telah dicetak",
                'button_text' => 'Selesai Cetak Laporan',
                'button_url' => route('admin.penilaian.cetak'),
            ],
        ];

        return view('admin.penilaian.progress', compact('progress', 'periode'));
    }

    /**
     * Show form to create penilaian
     */
    public function create($pegawaiId, $periodeId)
    {
        $pegawai = Pegawai::with(['jabatan', 'atasanLangsung'])->findOrFail($pegawaiId);
        $periode = PeriodePenilaian::findOrFail($periodeId);
        
        $existing = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->where('penilai_id', Auth::user()->pegawai_id)
            ->first();

        if ($existing) {
            return redirect()->route('admin.penilaian.index')
                ->with('warning', 'Anda sudah melakukan penilaian untuk pegawai ini');
        }

        $indikator = $this->penilaianService->getIndikatorForJabatan($pegawai->jabatan_id);
        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $periodeId)
            ->first();

        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
            ];
        }

        $userPegawai = Auth::user()->pegawai;
        $levelPenilai = $this->determineLevelPenilai($pegawai, $userPegawai);

        return view('admin.penilaian.create', compact(
            'pegawai', 'periode', 'indikator', 'bobot', 'levelPenilai'
        ));
    }

    /**
     * Store penilaian
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'periode_id' => 'required|exists:periode_penilaian,id',
            'level_penilai' => 'required|in:self,atasan_langsung,atasan_penilai',
            'nilai_kpi' => 'required|array|min:1',
            'nilai_kpi.*' => 'required|numeric|min:0|max:100',
            'nilai_kompetensi' => 'required|array|min:1',
            'nilai_kompetensi.*' => 'required|numeric|min:0|max:100',
            'nilai_core_values' => 'required|array|min:1',
            'nilai_core_values.*' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $data = $request->all();
            $data['penilai_id'] = Auth::user()->pegawai_id;
            $penilaian = $this->penilaianService->savePenilaian($data);

            return redirect()->route('admin.penilaian.index')
                ->with('success', 'Penilaian berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show detail penilaian
     */
    public function show($id)
    {
        $penilaian = PenilaianKPI::with(['pegawai', 'penilai', 'periode'])
            ->findOrFail($id);

        return view('admin.penilaian.show', compact('penilaian'));
    }

    /**
     * Finalize all penilaian
     */
    public function finalize($periodeId)
    {
        $periode = PeriodePenilaian::findOrFail($periodeId);
        $pegawai = Pegawai::where('status', 'aktif')->get();

        $count = 0;
        foreach ($pegawai as $p) {
            try {
                $this->penilaianService->finalizePenilaian($p->id, $periodeId);
                $count++;
            } catch (\Exception $e) {
                // Skip if error
            }
        }

        return redirect()->route('admin.penilaian.index')
            ->with('success', "Berhasil memfinalisasi {$count} penilaian!");
    }

    /**
     * Remove the specified penilaian from storage.
     */
    public function destroy($id)
    {
        try {
            $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
            
            if (!$periodeAktif) {
                return redirect()->route('admin.penilaian.index')
                    ->with('error', 'Tidak ada periode penilaian aktif!');
            }

            // Hapus penilaian KPI (termasuk yang final)
            $deleted = PenilaianKPI::where('pegawai_id', $id)
                ->where('periode_id', $periodeAktif->id)
                ->delete();

            // Hapus hasil penilaian (termasuk yang final)
            HasilPenilaian::where('pegawai_id', $id)
                ->where('periode_id', $periodeAktif->id)
                ->delete();

            if ($deleted) {
                return redirect()->route('admin.penilaian.index')
                    ->with('success', 'Data penilaian (termasuk final) berhasil dihapus!');
            } else {
                return redirect()->route('admin.penilaian.index')
                    ->with('warning', 'Tidak ada data penilaian untuk pegawai ini.');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('admin.penilaian.index')
                ->with('error', 'Gagal menghapus penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Display cetak hasil penilaian
     */
    public function cetak(Request $request)
    {
        $periodeId = $request->periode;
        
        // Ambil semua periode untuk dropdown
        $periodeList = PeriodePenilaian::orderBy('created_at', 'desc')->get();
        
        if (!$periodeId) {
            $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
            if ($periodeAktif) {
                $periodeId = $periodeAktif->id;
            }
        }

        $periode = $periodeId ? PeriodePenilaian::find($periodeId) : null;
        
        $data = collect();
        $statistik = [
            'total' => 0,
            'sangat_baik' => 0,
            'baik' => 0,
            'cukup' => 0,
            'kurang' => 0,
            'rata_rata' => 0,
        ];
        
        if ($periode) {
            $data = HasilPenilaian::with(['pegawai', 'pegawai.jabatan', 'predikat'])
                ->where('periode_id', $periodeId)
                ->where('status', 'final')
                ->get();
                
            $statistik = [
                'total' => $data->count(),
                'sangat_baik' => $data->where('predikat.nama', 'Sangat Baik')->count(),
                'baik' => $data->where('predikat.nama', 'Baik')->count(),
                'cukup' => $data->where('predikat.nama', 'Cukup')->count(),
                'kurang' => $data->where('predikat.nama', 'Kurang')->count(),
                'rata_rata' => $data->avg('nilai_akhir') ?? 0,
            ];
        }

        return view('admin.penilaian.cetak', compact('periodeList', 'periode', 'data', 'statistik'));
    }

    /**
     * Preview PDF single pegawai - DETEKSI OTOMATIS ATASAN PENILAI
     */
    public function previewPDF($pegawaiId, $periodeId)
    {
        $pegawai = Pegawai::with([
            'jabatan', 
            'atasanLangsung',
            'atasanLangsung.jabatan',
            'atasanLangsung.jabatan.atasan'
        ])->findOrFail($pegawaiId);
        
        $periode = PeriodePenilaian::findOrFail($periodeId);
        
        $hasil = HasilPenilaian::with(['predikat'])
            ->where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->first();

        if (!$hasil) {
            return redirect()->route('admin.penilaian.cetak')
                ->with('error', 'Penilaian untuk pegawai ini belum final!');
        }

        $bpr = BPR::first();
        
        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->get()
            ->keyBy('level_penilai');

        // ===== DETEKSI ATASAN PENILAI =====
        $atasanPenilai = null;
        $atasanLangsung = null;
        $hasAtasanPenilai = false;
        
        if ($pegawai->atasanLangsung) {
            $atasanLangsung = $pegawai->atasanLangsung;
            
            if ($atasanLangsung->jabatan && $atasanLangsung->jabatan->atasan) {
                $jabatanPenilai = $atasanLangsung->jabatan->atasan;
                $atasanPenilai = Pegawai::where('jabatan_id', $jabatanPenilai->id)
                    ->where('status', 'aktif')
                    ->first();
                if ($atasanPenilai) {
                    $hasAtasanPenilai = true;
                }
            }
        }

        // ===== CEK APAKAH ADA PENILAIAN DARI ATASAN PENILAI =====
        // Jika tidak ada atasan penilai di database, tapi ada penilaian dari atasan penilai
        if (!$hasAtasanPenilai && isset($penilaian['atasan_penilai'])) {
            $hasAtasanPenilai = true;
            // Ambil data atasan penilai dari penilaian
            $atasanPenilai = $penilaian['atasan_penilai']->penilai;
        }

        $pdf = Pdf::loadView('admin.penilaian.pdf-single', compact(
            'pegawai', 
            'periode', 
            'hasil', 
            'bpr', 
            'penilaian',
            'atasanLangsung',
            'atasanPenilai',
            'hasAtasanPenilai'
        ));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Laporan_Penilaian_' . $pegawai->nama . '_' . str_replace(' ', '_', $periode->nama) . '.pdf');
    }

    /**
     * Preview all PDF
     */
    public function previewAllPDF($periodeId)
    {
        $periode = PeriodePenilaian::findOrFail($periodeId);
        $data = HasilPenilaian::with(['pegawai', 'pegawai.jabatan', 'predikat'])
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        $bpr = BPR::first();

        $pdf = Pdf::loadView('admin.penilaian.pdf-cetak', compact('periode', 'data', 'bpr'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('Laporan_Penilaian_' . str_replace(' ', '_', $periode->nama) . '.pdf');
    }

    /**
     * Download PDF hasil penilaian
     */
    public function cetakPDF($periodeId)
    {
        $periode = PeriodePenilaian::findOrFail($periodeId);
        $data = HasilPenilaian::with(['pegawai', 'pegawai.jabatan', 'predikat'])
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        $bpr = BPR::first();

        $pdf = Pdf::loadView('admin.penilaian.pdf-cetak', compact('periode', 'data', 'bpr'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('Laporan_Penilaian_' . str_replace(' ', '_', $periode->nama) . '.pdf');
    }

    /**
     * Download Excel hasil penilaian
     */
    public function cetakExcel($periodeId)
    {
        $periode = PeriodePenilaian::findOrFail($periodeId);
        
        return Excel::download(new PenilaianExport($periodeId), 'Laporan_Penilaian_' . str_replace(' ', '_', $periode->nama) . '.xlsx');
    }

    /**
     * Download single pegawai - DETEKSI OTOMATIS ATASAN PENILAI
     */
    public function cetakSingle($pegawaiId, $periodeId)
    {
        $pegawai = Pegawai::with([
            'jabatan', 
            'atasanLangsung',
            'atasanLangsung.jabatan',
            'atasanLangsung.jabatan.atasan'
        ])->findOrFail($pegawaiId);
        
        $periode = PeriodePenilaian::findOrFail($periodeId);
        
        $hasil = HasilPenilaian::with(['predikat'])
            ->where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->first();

        if (!$hasil) {
            return redirect()->route('admin.penilaian.cetak')
                ->with('error', 'Penilaian untuk pegawai ini belum final!');
        }

        $bpr = BPR::first();
        
        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->get()
            ->keyBy('level_penilai');

        // ===== DETEKSI ATASAN PENILAI =====
        $atasanPenilai = null;
        $atasanLangsung = null;
        $hasAtasanPenilai = false;
        
        if ($pegawai->atasanLangsung) {
            $atasanLangsung = $pegawai->atasanLangsung;
            
            if ($atasanLangsung->jabatan && $atasanLangsung->jabatan->atasan) {
                $jabatanPenilai = $atasanLangsung->jabatan->atasan;
                $atasanPenilai = Pegawai::where('jabatan_id', $jabatanPenilai->id)
                    ->where('status', 'aktif')
                    ->first();
                if ($atasanPenilai) {
                    $hasAtasanPenilai = true;
                }
            }
        }

        if (!$hasAtasanPenilai && isset($penilaian['atasan_penilai'])) {
            $hasAtasanPenilai = true;
            $atasanPenilai = $penilaian['atasan_penilai']->penilai;
        }

        $pdf = Pdf::loadView('admin.penilaian.pdf-single', compact(
            'pegawai', 
            'periode', 
            'hasil', 
            'bpr', 
            'penilaian',
            'atasanLangsung',
            'atasanPenilai',
            'hasAtasanPenilai'
        ));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Laporan_Penilaian_' . $pegawai->nama . '_' . str_replace(' ', '_', $periode->nama) . '.pdf');
    }

    /**
     * Preview single (HTML)
     */
    public function preview($pegawaiId, $periodeId)
    {
        $pegawai = Pegawai::with([
            'jabatan', 
            'atasanLangsung',
            'atasanLangsung.jabatan',
            'atasanLangsung.jabatan.atasan'
        ])->findOrFail($pegawaiId);
        
        $periode = PeriodePenilaian::findOrFail($periodeId);
        
        $hasil = HasilPenilaian::with(['predikat'])
            ->where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->first();

        if (!$hasil) {
            return redirect()->route('admin.penilaian.cetak')
                ->with('error', 'Penilaian untuk pegawai ini belum final!');
        }

        $bpr = BPR::first();
        
        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->get()
            ->keyBy('level_penilai');

        $atasanPenilai = null;
        $atasanLangsung = null;
        $hasAtasanPenilai = false;
        
        if ($pegawai->atasanLangsung) {
            $atasanLangsung = $pegawai->atasanLangsung;
            
            if ($atasanLangsung->jabatan && $atasanLangsung->jabatan->atasan) {
                $jabatanPenilai = $atasanLangsung->jabatan->atasan;
                $atasanPenilai = Pegawai::where('jabatan_id', $jabatanPenilai->id)
                    ->where('status', 'aktif')
                    ->first();
                if ($atasanPenilai) {
                    $hasAtasanPenilai = true;
                }
            }
        }

        if (!$hasAtasanPenilai && isset($penilaian['atasan_penilai'])) {
            $hasAtasanPenilai = true;
            $atasanPenilai = $penilaian['atasan_penilai']->penilai;
        }

        return view('admin.penilaian.preview', compact(
            'pegawai', 
            'periode', 
            'hasil', 
            'bpr', 
            'penilaian',
            'atasanLangsung',
            'atasanPenilai',
            'hasAtasanPenilai'
        ));
    }

    /**
     * Preview all (HTML)
     */
    public function previewAll($periodeId)
    {
        $periode = PeriodePenilaian::findOrFail($periodeId);
        $data = HasilPenilaian::with(['pegawai', 'pegawai.jabatan', 'predikat'])
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        $bpr = BPR::first();

        return view('admin.penilaian.preview-all', compact('periode', 'data', 'bpr'));
    }

    /**
     * Determine level penilai
     */
    private function determineLevelPenilai($pegawai, $penilai)
    {
        if ($pegawai->id === $penilai->id) {
            return 'self';
        }

        if ($pegawai->atasan_langsung_id === $penilai->id) {
            return 'atasan_langsung';
        }

        $atasanLangsung = $pegawai->atasanLangsung;
        if ($atasanLangsung && $atasanLangsung->atasan_langsung_id === $penilai->id) {
            return 'atasan_penilai';
        }

        return 'self';
    }
}