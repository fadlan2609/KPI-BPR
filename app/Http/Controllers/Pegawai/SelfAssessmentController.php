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

class SelfAssessmentController extends Controller
{
    protected $penilaianService;

    public function __construct(PenilaianService $penilaianService)
    {
        $this->penilaianService = $penilaianService;
    }

    /**
     * Display self assessment page
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
            return view('pegawai.self-assessment.index', [
                'periodeAktif' => null,
                'pegawai' => $pegawai,
                'message' => 'Belum ada periode penilaian aktif'
            ]);
        }

        // Cek apakah sudah mengisi self assessment
        $sudahMengisi = PenilaianKPI::where('pegawai_id', $pegawai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', 'self')
            ->exists();

        if ($sudahMengisi) {
            return redirect()->route('pegawai.self-assessment.result')
                ->with('info', 'Anda sudah mengisi self assessment untuk periode ini.');
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

        return view('pegawai.self-assessment.create', compact(
            'pegawai',
            'periodeAktif',
            'indikator',
            'bobot'
        ));
    }

    /**
     * Store self assessment
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        $request->validate([
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
            $data['pegawai_id'] = $pegawai->id;
            $data['penilai_id'] = $pegawai->id;
            $data['level_penilai'] = 'self';

            $detail = [
                'kpi' => $request->nilai_kpi,
                'kompetensi' => $request->nilai_kompetensi,
                'core_values' => $request->nilai_core_values,
                'komentar_kpi' => $request->komentar_kpi ?? [],
                'komentar_kompetensi' => $request->komentar_kompetensi ?? [],
                'komentar_core_values' => $request->komentar_core_values ?? [],
            ];

            $penilaian = $this->penilaianService->savePenilaianWithDetail($data, $detail);

            return redirect()->route('pegawai.self-assessment.result')
                ->with('success', 'Self assessment berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan self assessment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display self assessment result
     */
    public function result()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return view('pegawai.self-assessment.result', [
                'periodeAktif' => null,
                'pegawai' => $pegawai,
                'penilaian' => null,
                'detail' => null,
                'indikator' => null,
                'bobot' => null,
                'message' => 'Belum ada periode penilaian aktif'
            ]);
        }

        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', 'self')
            ->first();

        if (!$penilaian) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('info', 'Silakan isi self assessment terlebih dahulu.');
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

        $indikator = $this->penilaianService->getIndikatorForJabatan($pegawai->jabatan_id);
        $detail = json_decode($penilaian->detail_indikator, true);

        return view('pegawai.self-assessment.result', compact(
            'pegawai',
            'periodeAktif',
            'penilaian',
            'bobot',
            'detail',
            'indikator'
        ));
    }

    /**
     * Edit self assessment (CEK APAKAH SUDAH DINILAI ATASAN)
     */
    public function edit()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        // ============================================================
        // CEK APAKAH SUDAH DINILAI OLEH ATASAN
        // ============================================================
        $sudahDinilaiAtasan = PenilaianKPI::where('pegawai_id', $pegawai->id)
            ->where('periode_id', $periodeAktif->id)
            ->whereIn('level_penilai', ['atasan_langsung', 'atasan_penilai'])
            ->exists();

        if ($sudahDinilaiAtasan) {
            return redirect()->route('pegawai.self-assessment.result')
                ->with('error', 'Self assessment tidak dapat diedit karena sudah dinilai oleh atasan!');
        }

        // Cek apakah sudah mengisi self assessment
        $penilaian = PenilaianKPI::where('pegawai_id', $pegawai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', 'self')
            ->first();

        if (!$penilaian) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('info', 'Silakan isi self assessment terlebih dahulu.');
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

        return view('pegawai.self-assessment.edit', compact(
            'pegawai',
            'periodeAktif',
            'penilaian',
            'indikator',
            'bobot',
            'detail'
        ));
    }

    /**
     * Update self assessment (CEK APAKAH SUDAH DINILAI ATASAN)
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        // ============================================================
        // CEK APAKAH SUDAH DINILAI OLEH ATASAN
        // ============================================================
        $sudahDinilaiAtasan = PenilaianKPI::where('pegawai_id', $pegawai->id)
            ->where('periode_id', $periodeAktif->id)
            ->whereIn('level_penilai', ['atasan_langsung', 'atasan_penilai'])
            ->exists();

        if ($sudahDinilaiAtasan) {
            return redirect()->route('pegawai.self-assessment.result')
                ->with('error', 'Self assessment tidak dapat diedit karena sudah dinilai oleh atasan!');
        }

        $request->validate([
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
            $penilaian = PenilaianKPI::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->where('level_penilai', 'self')
                ->first();

            if (!$penilaian) {
                return redirect()->route('pegawai.self-assessment.index')
                    ->with('error', 'Data penilaian tidak ditemukan!');
            }

            $avgKPI = array_sum($request->nilai_kpi) / count($request->nilai_kpi);
            $avgKompetensi = array_sum($request->nilai_kompetensi) / count($request->nilai_kompetensi);
            $avgCoreValues = array_sum($request->nilai_core_values) / count($request->nilai_core_values);

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

            return redirect()->route('pegawai.self-assessment.result')
                ->with('success', 'Self assessment berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui self assessment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete self assessment (CEK APAKAH SUDAH DINILAI ATASAN)
     */
    public function destroy()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        // ============================================================
        // CEK APAKAH SUDAH DINILAI OLEH ATASAN
        // ============================================================
        $sudahDinilaiAtasan = PenilaianKPI::where('pegawai_id', $pegawai->id)
            ->where('periode_id', $periodeAktif->id)
            ->whereIn('level_penilai', ['atasan_langsung', 'atasan_penilai'])
            ->exists();

        if ($sudahDinilaiAtasan) {
            return redirect()->route('pegawai.self-assessment.result')
                ->with('error', 'Self assessment tidak dapat dihapus karena sudah dinilai oleh atasan!');
        }

        try {
            $penilaian = PenilaianKPI::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->where('level_penilai', 'self')
                ->first();

            if ($penilaian) {
                $penilaian->delete();
            }

            return redirect()->route('pegawai.self-assessment.index')
                ->with('success', 'Self assessment berhasil dibatalkan!');

        } catch (\Exception $e) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('error', 'Gagal membatalkan self assessment: ' . $e->getMessage());
        }
    }

    /**
     * Print self assessment
     */
    public function print()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('error', 'Belum ada periode penilaian aktif!');
        }

        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawai->id)
            ->where('periode_id', $periodeAktif->id)
            ->where('level_penilai', 'self')
            ->first();

        if (!$penilaian) {
            return redirect()->route('pegawai.self-assessment.index')
                ->with('info', 'Silakan isi self assessment terlebih dahulu.');
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

        return view('pegawai.self-assessment.print', compact(
            'pegawai',
            'periodeAktif',
            'penilaian',
            'indikator',
            'bobot',
            'detail'
        ));
    }
}