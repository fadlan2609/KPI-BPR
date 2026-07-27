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
     * Preview laporan lengkap - 3 level penilai dengan KPI, Kompetensi, Core Values
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

        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawai->id)
            ->where('periode_id', $hasil->periode_id)
            ->get()
            ->keyBy('level_penilai');

        $detailSelf = $penilaian->has('self') ? json_decode($penilaian['self']->detail_indikator, true) : null;
        $detailAtasan = $penilaian->has('atasan_langsung') ? json_decode($penilaian['atasan_langsung']->detail_indikator, true) : null;
        $detailPenilai = $penilaian->has('atasan_penilai') ? json_decode($penilaian['atasan_penilai']->detail_indikator, true) : null;

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

        return view('pegawai.laporan-preview', compact(
            'pegawai',
            'hasil',
            'penilaian',
            'detailSelf',
            'detailAtasan',
            'detailPenilai',
            'indikatorKPI',
            'indikatorKompetensi',
            'indikatorCore',
            'bobot',
            'bpr'
        ));
    }

    /**
     * Tampilkan PDF laporan lengkap di browser (PREVIEW) - BUKAN DOWNLOAD LANGSUNG
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

        $penilaian = PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawai->id)
            ->where('periode_id', $hasil->periode_id)
            ->get()
            ->keyBy('level_penilai');

        $detailSelf = $penilaian->has('self') ? json_decode($penilaian['self']->detail_indikator, true) : null;
        $detailAtasan = $penilaian->has('atasan_langsung') ? json_decode($penilaian['atasan_langsung']->detail_indikator, true) : null;
        $detailPenilai = $penilaian->has('atasan_penilai') ? json_decode($penilaian['atasan_penilai']->detail_indikator, true) : null;

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

        $pdf = Pdf::loadView('pegawai.laporan-pdf', compact(
            'pegawai',
            'hasil',
            'penilaian',
            'detailSelf',
            'detailAtasan',
            'detailPenilai',
            'indikatorKPI',
            'indikatorKompetensi',
            'indikatorCore',
            'bobot',
            'bpr'
        ));
        $pdf->setPaper('a4', 'portrait');

        // STREAM = TAMPIL DI BROWSER (PREVIEW), BUKAN DOWNLOAD LANGSUNG
        return $pdf->stream('Laporan_Penilaian_' . $pegawai->nama . '_' . str_replace(' ', '_', $hasil->periode->nama) . '.pdf');
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
}