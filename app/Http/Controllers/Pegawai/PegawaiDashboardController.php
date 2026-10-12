<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use App\Models\PenilaianKPI;
use App\Models\HasilPenilaian;
use App\Models\Cuti;
use App\Models\SaldoCuti;
use App\Models\BPR;
use App\Models\IndikatorKPI;
use App\Models\IndikatorKompetensi;
use App\Models\IndikatorCoreValue;
use App\Models\BobotPenilaian;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PegawaiDashboardController extends Controller
{
    protected $penilaianService;

    public function __construct(PenilaianService $penilaianService)
    {
        $this->penilaianService = $penilaianService;
    }

    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $progress = null;
        if ($periodeAktif) {
            $progress = $this->penilaianService->getProgress($pegawai->id, $periodeAktif->id);
        }

        $hasilTerakhir = HasilPenilaian::with(['predikat', 'periode'])
            ->where('pegawai_id', $pegawai->id)
            ->where('status', 'final')
            ->orderBy('created_at', 'desc')
            ->first();

        $saldoCuti = SaldoCuti::where('pegawai_id', $pegawai->id)
            ->where('tahun', date('Y'))
            ->first();

        $totalCuti = Cuti::where('pegawai_id', $pegawai->id)
            ->whereYear('created_at', date('Y'))
            ->count();

        $cutiPending = Cuti::where('pegawai_id', $pegawai->id)
            ->where('status', 'pending')
            ->count();

        return view('pegawai.dashboard', compact(
            'pegawai',
            'periodeAktif',
            'progress',
            'hasilTerakhir',
            'saldoCuti',
            'totalCuti',
            'cutiPending'
        ));
    }

    public function hasilPenilaian()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $hasil = HasilPenilaian::with(['predikat', 'periode'])
            ->where('pegawai_id', $pegawai->id)
            ->where('status', 'final')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pegawai.hasil-penilaian', compact('hasil', 'pegawai'));
    }

    /**
     * Preview laporan HTML — gabungan 3 level
     */
    public function cetakLaporan($hasilId)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $hasil = HasilPenilaian::with(['predikat', 'periode'])
            ->where('id', $hasilId)
            ->where('pegawai_id', $pegawai->id)
            ->where('status', 'final')
            ->first();

        if (!$hasil) {
            return redirect()->route('pegawai.hasil-penilaian')
                ->with('error', 'Laporan tidak ditemukan!');
        }

        // ✅ Ambil periode dari relasi hasil
        $periode = $hasil->periode;

        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawai->id)
            ->where('periode_id', $hasil->periode_id)
            ->get()
            ->keyBy('level_penilai');

        $indikatorKPI = IndikatorKPI::where('jabatan_id', $pegawai->jabatan_id)->get();
        $indikatorKompetensi = IndikatorKompetensi::where('jabatan_id', $pegawai->jabatan_id)->get();
        $indikatorCore = IndikatorCoreValue::where('jabatan_id', $pegawai->jabatan_id)->get();

        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $hasil->periode_id)
            ->first();

        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
                'bobot_self' => 20,
                'bobot_atasan_langsung' => 50,
                'bobot_atasan_penilai' => 30,
            ];
        }

        $bpr = BPR::first();

        return view('pegawai.cetak-laporan', compact(
            'pegawai',
            'periode',              // ✅ DITAMBAHKAN
            'hasil',
            'penilaian',
            'indikatorKPI',
            'indikatorKompetensi',
            'indikatorCore',
            'bobot',
            'bpr'
        ));
    }

    /**
     * Stream PDF — gabungan 3 level
     */
    public function cetakLaporanPDF($hasilId)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $hasil = HasilPenilaian::with(['predikat', 'periode'])
            ->where('id', $hasilId)
            ->where('pegawai_id', $pegawai->id)
            ->where('status', 'final')
            ->first();

        if (!$hasil) {
            return redirect()->route('pegawai.hasil-penilaian')
                ->with('error', 'Laporan tidak ditemukan!');
        }

        // ✅ Ambil periode
        $periode = $hasil->periode;

        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawai->id)
            ->where('periode_id', $hasil->periode_id)
            ->get()
            ->keyBy('level_penilai');

        $indikatorKPI = IndikatorKPI::where('jabatan_id', $pegawai->jabatan_id)->get();
        $indikatorKompetensi = IndikatorKompetensi::where('jabatan_id', $pegawai->jabatan_id)->get();
        $indikatorCore = IndikatorCoreValue::where('jabatan_id', $pegawai->jabatan_id)->get();

        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $hasil->periode_id)
            ->first();

        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
                'bobot_self' => 20,
                'bobot_atasan_langsung' => 50,
                'bobot_atasan_penilai' => 30,
            ];
        }

        $bpr = BPR::first();

        // Atasan langsung & atasan penilai
        $atasanLangsung = isset($penilaian['atasan_langsung']) ? $penilaian['atasan_langsung']->penilai : null;
        $atasanPenilai  = isset($penilaian['atasan_penilai'])  ? $penilaian['atasan_penilai']->penilai  : null;
        $hasAtasanPenilai = isset($penilaian['atasan_penilai']);

        // TTD & Logo
        $ttdPegawaiData         = $this->getBase64File($pegawai->ttd_image ?? null);
        $ttdAtasanLangsungData  = $atasanLangsung ? $this->getBase64File($atasanLangsung->ttd_image ?? null) : null;
        $ttdAtasanPenilaiData   = $atasanPenilai  ? $this->getBase64File($atasanPenilai->ttd_image ?? null)  : null;
        $logoData               = $bpr ? $this->getBase64File($bpr->logo ?? null) : null;

        $pdf = Pdf::loadView('pegawai.laporan-pdf', compact(
            'pegawai',
            'periode',              // ✅ DITAMBAHKAN
            'hasil',
            'penilaian',
            'indikatorKPI',
            'indikatorKompetensi',
            'indikatorCore',
            'bobot',
            'bpr',
            'atasanLangsung',
            'atasanPenilai',
            'hasAtasanPenilai',
            'ttdPegawaiData',
            'ttdAtasanLangsungData',
            'ttdAtasanPenilaiData',
            'logoData'
        ));
        $pdf->setPaper('a4', 'portrait');

        $namaPegawai = preg_replace('/[^A-Za-z0-9_\-]/', '_', $pegawai->nama ?? 'Pegawai');
        $namaPeriode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $hasil->periode->nama ?? 'Periode');
        $namaFile = 'Laporan_Penilaian_' . $namaPegawai . '_' . $namaPeriode . '.pdf';

        return $pdf->stream($namaFile, ['Attachment' => false]);
    }

    public function cuti()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $cuti = Cuti::where('pegawai_id', $pegawai->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $saldoCuti = SaldoCuti::where('pegawai_id', $pegawai->id)
            ->where('tahun', date('Y'))
            ->first();

        return view('pegawai.cuti', compact('cuti', 'pegawai', 'saldoCuti'));
    }

    public function storeCuti(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,khusus,lainnya',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $lamaHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        if ($request->jenis_cuti == 'tahunan') {
            $saldo = SaldoCuti::where('pegawai_id', $pegawai->id)
                ->where('tahun', date('Y'))
                ->first();

            if (!$saldo) {
                return redirect()->back()
                    ->with('error', 'Saldo cuti tidak ditemukan!')
                    ->withInput();
            }

            if ($saldo->sisa_hari < $lamaHari) {
                return redirect()->back()
                    ->with('error', 'Saldo cuti tidak mencukupi! Sisa: ' . $saldo->sisa_hari . ' hari')
                    ->withInput();
            }
        }

        Cuti::create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_hari' => $lamaHari,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
        ]);

        return redirect()->route('pegawai.cuti')
            ->with('success', 'Pengajuan cuti berhasil dikirim!');
    }

    public function saldoCuti()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum terhubung dengan data pegawai!');
        }

        $saldo = SaldoCuti::where('pegawai_id', $pegawai->id)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('pegawai.saldo-cuti', compact('saldo', 'pegawai'));
    }

    /**
     * Helper: Convert file ke base64
     */
    private function getBase64File($path)
    {
        if (!$path) return null;

        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) return null;

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if ($ext === 'jpg') $ext = 'jpeg';

        return 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($fullPath));
    }
}