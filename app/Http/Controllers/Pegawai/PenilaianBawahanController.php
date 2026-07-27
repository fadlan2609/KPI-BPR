<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\PenilaianKPI;
use App\Models\BobotPenilaian;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PenilaianBawahanController extends Controller
{
    protected $penilaianService;

    public function __construct(PenilaianService $penilaianService)
    {
        $this->penilaianService = $penilaianService;
    }

    /**
     * Display list of bawahan to be assessed
     * Mencakup bawahan langsung dan bawahan tidak langsung (atasan penilai)
     */
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return view('pegawai.penilaian-bawahan.index', [
                'periodeAktif' => null,
                'bawahan' => collect(),
                'progressData' => [],
                'message' => 'Belum ada periode penilaian aktif'
            ]);
        }

        // ============================================================
        // 1. BAWAHAN LANGSUNG (ATASAN LANGSUNG)
        // ============================================================
        $bawahanLangsung = $pegawai->bawahanLangsung()
            ->where('status', 'aktif')
            ->get()
            ->map(function($item) {
                $item->level_penilai = 'atasan_langsung';
                $item->level_label = 'Atasan Langsung';
                return $item;
            });

        // ============================================================
        // 2. BAWAHAN TIDAK LANGSUNG (ATASAN PENILAI)
        // ============================================================
        $bawahanTidakLangsung = collect();
        
        $bawahanLangsungIds = $bawahanLangsung->pluck('id')->toArray();
        
        if (!empty($bawahanLangsungIds)) {
            $bawahanTidakLangsung = Pegawai::whereIn('atasan_langsung_id', $bawahanLangsungIds)
                ->where('status', 'aktif')
                ->get()
                ->map(function($item) {
                    $item->level_penilai = 'atasan_penilai';
                    $item->level_label = 'Atasan Penilai';
                    return $item;
                });
        }

        // ============================================================
        // 3. GABUNGKAN SEMUA BAWAHAN
        // ============================================================
        $bawahan = $bawahanLangsung->merge($bawahanTidakLangsung);

        if ($bawahan->isEmpty()) {
            return redirect()->route('pegawai.dashboard')
                ->with('info', 'Anda tidak memiliki bawahan yang perlu dinilai.');
        }

        // Get progress untuk setiap bawahan
        $progressData = [];
        foreach ($bawahan as $b) {
            $progress = $this->penilaianService->getProgress($b->id, $periodeAktif->id);
            $progressData[$b->id] = $progress;
        }

        // Urutkan berdasarkan level (Atasan Langsung dulu, baru Atasan Penilai)
        $bawahan = $bawahan->sortBy(function($item) {
            return $item->level_penilai == 'atasan_langsung' ? 0 : 1;
        });

        return view('pegawai.penilaian-bawahan.index', compact(
            'bawahan',
            'periodeAktif',
            'progressData',
            'pegawai'
        ));
    }

    /**
     * Show form to assess a bawahan
     */
    public function create($pegawaiId)
    {
        $user = Auth::user();
        $penilai = $user->pegawai;

        if (!$penilai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        // Cek apakah pegawai adalah bawahan (langsung atau tidak langsung)
        $pegawai = Pegawai::where('id', $pegawaiId)
            ->where('status', 'aktif')
            ->first();

        if (!$pegawai) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Pegawai tidak ditemukan!');
        }

        // Cek apakah penilai berhak menilai pegawai ini
        $isBawahanLangsung = $pegawai->atasan_langsung_id == $penilai->id;
        $isBawahanTidakLangsung = false;
        
        $bawahanLangsungIds = $penilai->bawahanLangsung()->pluck('id')->toArray();
        if (in_array($pegawai->atasan_langsung_id, $bawahanLangsungIds)) {
            $isBawahanTidakLangsung = true;
        }

        if (!$isBawahanLangsung && !$isBawahanTidakLangsung) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Anda tidak memiliki akses untuk menilai pegawai ini!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        // Tentukan level penilai
        $levelPenilai = $isBawahanLangsung ? 'atasan_langsung' : 'atasan_penilai';

        // Cek apakah sudah menilai
        $sudahMenilai = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('penilai_id', $penilai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', $levelPenilai)
            ->exists();

        if ($sudahMenilai) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('info', 'Anda sudah menilai pegawai ini.');
        }

        // Get indikator untuk jabatan
        $indikator = $this->penilaianService->getIndikatorForJabatan($pegawai->jabatan_id);

        // Get bobot
        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $periodeAktif->id)
            ->first();

        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
            ];
        }

        // Tampilkan self assessment pegawai sebagai referensi
        $selfAssessment = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', 'self')
            ->first();

        return view('pegawai.penilaian-bawahan.create', compact(
            'pegawai',
            'penilai',
            'periodeAktif',
            'indikator',
            'bobot',
            'selfAssessment',
            'levelPenilai'
        ));
    }

    /**
     * Store penilaian untuk bawahan
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $penilai = $user->pegawai;

        if (!$penilai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'level_penilai' => 'required|in:atasan_langsung,atasan_penilai',
            'nilai_kpi' => 'required|array|min:1',
            'nilai_kpi.*' => 'required|numeric|min:0|max:100',
            'nilai_kompetensi' => 'required|array|min:1',
            'nilai_kompetensi.*' => 'required|numeric|min:1|max:5',
            'nilai_core_values' => 'required|array|min:1',
            'nilai_core_values.*' => 'required|numeric|min:1|max:5',
            'komentar_kpi' => 'nullable|array',
            'komentar_kompetensi' => 'nullable|array',
            'komentar_core_values' => 'nullable|array',
        ]);

        try {
            $data = $request->all();
            $data['pegawai_id'] = $request->pegawai_id;
            $data['penilai_id'] = $penilai->id;
            $data['level_penilai'] = $request->level_penilai;

            // Detail dengan komentar
            $detail = [
                'kpi' => $request->nilai_kpi,
                'kompetensi' => $request->nilai_kompetensi,
                'core_values' => $request->nilai_core_values,
                'komentar_kpi' => $request->komentar_kpi ?? [],
                'komentar_kompetensi' => $request->komentar_kompetensi ?? [],
                'komentar_core_values' => $request->komentar_core_values ?? [],
            ];

            $penilaian = $this->penilaianService->savePenilaianWithDetail($data, $detail);

            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('success', 'Penilaian untuk ' . $penilaian->pegawai->nama . ' berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show detail penilaian bawahan
     */
    public function show($pegawaiId)
    {
        $user = Auth::user();
        $penilai = $user->pegawai;

        if (!$penilai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $pegawai = Pegawai::find($pegawaiId);

        if (!$pegawai) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Pegawai tidak ditemukan!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        // Cek apakah penilai berhak melihat
        $isBawahanLangsung = $pegawai->atasan_langsung_id == $penilai->id;
        $bawahanLangsungIds = $penilai->bawahanLangsung()->pluck('id')->toArray();
        $isBawahanTidakLangsung = in_array($pegawai->atasan_langsung_id, $bawahanLangsungIds);

        if (!$isBawahanLangsung && !$isBawahanTidakLangsung) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Anda tidak memiliki akses!');
        }

        $levelPenilai = $isBawahanLangsung ? 'atasan_langsung' : 'atasan_penilai';

        $penilaian = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('penilai_id', $penilai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', $levelPenilai)
            ->first();

        if (!$penilaian) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('info', 'Anda belum menilai pegawai ini.');
        }

        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $periodeAktif->id)
            ->first();

        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
            ];
        }

        $detail = json_decode($penilaian->detail_indikator, true);
        $indikator = $this->penilaianService->getIndikatorForJabatan($pegawai->jabatan_id);

        return view('pegawai.penilaian-bawahan.show', compact(
            'pegawai',
            'penilai',
            'periodeAktif',
            'penilaian',
            'bobot',
            'detail',
            'indikator',
            'levelPenilai'
        ));
    }

    /**
     * Edit penilaian bawahan (jika belum final)
     */
    public function edit($pegawaiId)
    {
        $user = Auth::user();
        $penilai = $user->pegawai;

        if (!$penilai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $pegawai = Pegawai::find($pegawaiId);

        if (!$pegawai) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Pegawai tidak ditemukan!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        $isBawahanLangsung = $pegawai->atasan_langsung_id == $penilai->id;
        $bawahanLangsungIds = $penilai->bawahanLangsung()->pluck('id')->toArray();
        $isBawahanTidakLangsung = in_array($pegawai->atasan_langsung_id, $bawahanLangsungIds);

        if (!$isBawahanLangsung && !$isBawahanTidakLangsung) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Anda tidak memiliki akses!');
        }

        $levelPenilai = $isBawahanLangsung ? 'atasan_langsung' : 'atasan_penilai';

        $penilaian = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('penilai_id', $penilai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', $levelPenilai)
            ->first();

        if (!$penilaian) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('info', 'Anda belum menilai pegawai ini.');
        }

        // Cek apakah sudah final
        $hasil = \App\Models\HasilPenilaian::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeAktif->id)
            ->where('status', 'final')
            ->first();

        if ($hasil) {
            return redirect()->route('pegawai.penilaian-bawahan.show', $pegawaiId)
                ->with('error', 'Penilaian sudah final, tidak dapat diedit!');
        }

        $indikator = $this->penilaianService->getIndikatorForJabatan($pegawai->jabatan_id);
        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $periodeAktif->id)
            ->first();

        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
            ];
        }

        $detail = json_decode($penilaian->detail_indikator, true);

        return view('pegawai.penilaian-bawahan.edit', compact(
            'pegawai',
            'penilai',
            'periodeAktif',
            'penilaian',
            'indikator',
            'bobot',
            'detail',
            'levelPenilai'
        ));
    }

    /**
     * Update penilaian bawahan
     */
    public function update(Request $request, $pegawaiId)
    {
        $user = Auth::user();
        $penilai = $user->pegawai;

        if (!$penilai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        $request->validate([
            'level_penilai' => 'required|in:atasan_langsung,atasan_penilai',
            'nilai_kpi' => 'required|array|min:1',
            'nilai_kpi.*' => 'required|numeric|min:0|max:100',
            'nilai_kompetensi' => 'required|array|min:1',
            'nilai_kompetensi.*' => 'required|numeric|min:1|max:5',
            'nilai_core_values' => 'required|array|min:1',
            'nilai_core_values.*' => 'required|numeric|min:1|max:5',
            'komentar_kpi' => 'nullable|array',
            'komentar_kompetensi' => 'nullable|array',
            'komentar_core_values' => 'nullable|array',
        ]);

        try {
            $penilaian = PenilaianKPI::where('pegawai_id', $pegawaiId)
                ->where('penilai_id', $penilai->id)
                ->where('periode_id', $periodeAktif->id)
                ->where('level_penilai', $request->level_penilai)
                ->first();

            if (!$penilaian) {
                return redirect()->route('pegawai.penilaian-bawahan.index')
                    ->with('error', 'Data penilaian tidak ditemukan!');
            }

            $avgKPI = array_sum($request->nilai_kpi) / count($request->nilai_kpi);
            $avgKompetensi = array_sum($request->nilai_kompetensi) / count($request->nilai_kompetensi);
            $avgCoreValues = array_sum($request->nilai_core_values) / count($request->nilai_core_values);

            $bobot = BobotPenilaian::where('jabatan_id', $pegawaiId->jabatan_id)
                ->where('periode_id', $periodeAktif->id)
                ->first();

            if (!$bobot) {
                $bobot = (object) [
                    'bobot_kpi' => 70,
                    'bobot_kompetensi' => 15,
                    'bobot_core_values' => 15,
                ];
            }

            $kompetensiKonversi = ($avgKompetensi / 5) * 100;
            $coreKonversi = ($avgCoreValues / 5) * 100;
            
            $nilaiTotal = ($avgKPI * $bobot->bobot_kpi / 100) +
                          ($kompetensiKonversi * $bobot->bobot_kompetensi / 100) +
                          ($coreKonversi * $bobot->bobot_core_values / 100);

            $nilaiTotal = round($nilaiTotal, 2);

            $penilaian->update([
                'nilai_kpi' => round($avgKPI, 2),
                'nilai_kompetensi' => round($avgKompetensi, 2),
                'nilai_core_values' => round($avgCoreValues, 2),
                'nilai_total' => $nilaiTotal,
                'detail_indikator' => json_encode([
                    'kpi' => $request->nilai_kpi,
                    'kompetensi' => $request->nilai_kompetensi,
                    'core_values' => $request->nilai_core_values,
                    'komentar_kpi' => $request->komentar_kpi ?? [],
                    'komentar_kompetensi' => $request->komentar_kompetensi ?? [],
                    'komentar_core_values' => $request->komentar_core_values ?? [],
                ]),
                'status' => 'submitted',
                'submitted_at' => Carbon::now(),
            ]);

            return redirect()->route('pegawai.penilaian-bawahan.show', $pegawaiId)
                ->with('success', 'Penilaian berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui penilaian: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete penilaian bawahan (jika belum final)
     */
    public function destroy($pegawaiId)
    {
        $user = Auth::user();
        $penilai = $user->pegawai;

        if (!$penilai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        // Cek apakah sudah final
        $hasil = \App\Models\HasilPenilaian::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeAktif->id)
            ->where('status', 'final')
            ->first();

        if ($hasil) {
            return redirect()->route('pegawai.penilaian-bawahan.show', $pegawaiId)
                ->with('error', 'Penilaian sudah final, tidak dapat dihapus!');
        }

        try {
            $penilaian = PenilaianKPI::where('pegawai_id', $pegawaiId)
                ->where('penilai_id', $penilai->id)
                ->where('periode_id', $periodeAktif->id)
                ->whereIn('level_penilai', ['atasan_langsung', 'atasan_penilai'])
                ->first();

            if ($penilaian) {
                $penilaian->delete();
            }

            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('success', 'Penilaian berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Gagal menghapus penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Print penilaian bawahan
     */
    public function print($pegawaiId)
    {
        $user = Auth::user();
        $penilai = $user->pegawai;

        if (!$penilai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $pegawai = Pegawai::find($pegawaiId);

        if (!$pegawai) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Pegawai tidak ditemukan!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        $isBawahanLangsung = $pegawai->atasan_langsung_id == $penilai->id;
        $bawahanLangsungIds = $penilai->bawahanLangsung()->pluck('id')->toArray();
        $isBawahanTidakLangsung = in_array($pegawai->atasan_langsung_id, $bawahanLangsungIds);

        if (!$isBawahanLangsung && !$isBawahanTidakLangsung) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('error', 'Anda tidak memiliki akses!');
        }

        $levelPenilai = $isBawahanLangsung ? 'atasan_langsung' : 'atasan_penilai';

        $penilaian = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('penilai_id', $penilai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', $levelPenilai)
            ->first();

        if (!$penilaian) {
            return redirect()->route('pegawai.penilaian-bawahan.index')
                ->with('info', 'Anda belum menilai pegawai ini.');
        }

        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $periodeAktif->id)
            ->first();

        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
            ];
        }

        $detail = json_decode($penilaian->detail_indikator, true);
        $indikator = $this->penilaianService->getIndikatorForJabatan($pegawai->jabatan_id);

        $bpr = \App\Models\BPR::first();

        return view('pegawai.penilaian-bawahan.print', compact(
            'pegawai',
            'penilai',
            'periodeAktif',
            'penilaian',
            'bobot',
            'detail',
            'indikator',
            'levelPenilai',
            'bpr'
        ));
    }
}