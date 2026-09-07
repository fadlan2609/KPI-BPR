<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePenilaian;
use App\Models\HasilPenilaian;
use App\Models\PerhitunganBonus;
use App\Models\RiwayatKenaikanGaji;
use App\Models\Cuti;
use App\Models\BPR;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPenilaianExport;
use App\Exports\LaporanGajiExport;
use App\Exports\LaporanBonusExport;
use App\Exports\LaporanCutiExport;

class LaporanController extends Controller
{
    /**
     * Display laporan index
     */
    public function index()
    {
        return view('admin.laporan.index');
    }

    /**
     * Display laporan penilaian
     */
    public function penilaian(Request $request)
    {
        $periodeId = $request->periode_id;
        $periodeList = PeriodePenilaian::orderBy('created_at', 'desc')->get();
        
        if (!$periodeId) {
            return view('admin.laporan.penilaian', [
                'periodeList' => $periodeList,
                'periode' => null,
                'data' => [],
                'summary' => []
            ]);
        }

        $periode = PeriodePenilaian::findOrFail($periodeId);
        $data = HasilPenilaian::with(['pegawai', 'pegawai.jabatan', 'predikat'])
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        $summary = [
            'total' => $data->count(),
            'sangat_baik' => $data->where('predikat.nama', 'Sangat Baik')->count(),
            'baik' => $data->where('predikat.nama', 'Baik')->count(),
            'cukup' => $data->where('predikat.nama', 'Cukup')->count(),
            'kurang' => $data->where('predikat.nama', 'Kurang')->count(),
            'rata_rata' => $data->avg('nilai_akhir') ?? 0,
        ];

        return view('admin.laporan.penilaian', compact('periodeList', 'periode', 'data', 'summary'));
    }

    /**
     * Download laporan penilaian (PDF/Excel)
     */
    public function downloadPenilaian(Request $request)
    {
        $periodeId = $request->periode_id;
        $format = $request->format ?? 'pdf';

        if (!$periodeId) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan!');
        }

        $periode = PeriodePenilaian::findOrFail($periodeId);

        if ($format == 'excel') {
            return Excel::download(
                new LaporanPenilaianExport($periodeId),
                "laporan_penilaian_" . str_replace(' ', '_', $periode->nama) . ".xlsx"
            );
        }

        // PDF
        $data = HasilPenilaian::with(['pegawai', 'pegawai.jabatan', 'predikat'])
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        $bpr = BPR::first();

        $pdf = Pdf::loadView('admin.laporan.pdf-penilaian', compact('periode', 'data', 'bpr'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download("laporan_penilaian_" . str_replace(' ', '_', $periode->nama) . ".pdf");
    }

    /**
     * Display laporan gaji
     */
    public function gaji(Request $request)
    {
        $periodeId = $request->periode_id;
        $periodeList = PeriodePenilaian::orderBy('created_at', 'desc')->get();
        
        if (!$periodeId) {
            return view('admin.laporan.gaji', [
                'periodeList' => $periodeList,
                'periode' => null,
                'data' => [],
                'summary' => []
            ]);
        }

        $periode = PeriodePenilaian::findOrFail($periodeId);
        $data = RiwayatKenaikanGaji::with(['pegawai', 'pegawai.jabatan'])
            ->where('periode_id', $periodeId)
            ->get();

        $summary = [
            'total' => $data->count(),
            'total_kenaikan' => $data->sum('persentase'),
            'rata_rata' => $data->avg('persentase') ?? 0,
            'total_nominal' => $data->sum('nominal_kenaikan'),
        ];

        return view('admin.laporan.gaji', compact('periodeList', 'periode', 'data', 'summary'));
    }

    /**
     * Download laporan gaji (PDF/Excel)
     */
    public function downloadGaji(Request $request)
    {
        $periodeId = $request->periode_id;
        $format = $request->format ?? 'pdf';

        if (!$periodeId) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan!');
        }

        $periode = PeriodePenilaian::findOrFail($periodeId);

        if ($format == 'excel') {
            return Excel::download(
                new LaporanGajiExport($periodeId),
                "laporan_gaji_" . str_replace(' ', '_', $periode->nama) . ".xlsx"
            );
        }

        // PDF
        $data = RiwayatKenaikanGaji::with(['pegawai', 'pegawai.jabatan'])
            ->where('periode_id', $periodeId)
            ->get();

        $bpr = BPR::first();

        $pdf = Pdf::loadView('admin.laporan.pdf-gaji', compact('periode', 'data', 'bpr'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download("laporan_gaji_" . str_replace(' ', '_', $periode->nama) . ".pdf");
    }

    /**
     * Display laporan bonus
     */
    public function bonus(Request $request)
    {
        $periodeId = $request->periode_id;
        $periodeList = PeriodePenilaian::orderBy('created_at', 'desc')->get();
        
        if (!$periodeId) {
            return view('admin.laporan.bonus', [
                'periodeList' => $periodeList,
                'periode' => null,
                'data' => [],
                'summary' => []
            ]);
        }

        $periode = PeriodePenilaian::findOrFail($periodeId);
        $data = PerhitunganBonus::with(['pegawai', 'pegawai.jabatan'])
            ->where('periode_id', $periodeId)
            ->get();

        $summary = [
            'total' => $data->count(),
            'total_bonus' => $data->sum('bonus_akhir'),
            'rata_rata' => $data->avg('bonus_akhir') ?? 0,
            'total_pegawai' => $data->where('bonus_akhir', '>', 0)->count(),
        ];

        return view('admin.laporan.bonus', compact('periodeList', 'periode', 'data', 'summary'));
    }

    /**
     * Download laporan bonus (PDF/Excel)
     */
    public function downloadBonus(Request $request)
    {
        $periodeId = $request->periode_id;
        $format = $request->format ?? 'pdf';

        if (!$periodeId) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan!');
        }

        $periode = PeriodePenilaian::findOrFail($periodeId);

        if ($format == 'excel') {
            return Excel::download(
                new LaporanBonusExport($periodeId),
                "laporan_bonus_" . str_replace(' ', '_', $periode->nama) . ".xlsx"
            );
        }

        // PDF
        $data = PerhitunganBonus::with(['pegawai', 'pegawai.jabatan'])
            ->where('periode_id', $periodeId)
            ->get();

        $bpr = BPR::first();

        $pdf = Pdf::loadView('admin.laporan.pdf-bonus', compact('periode', 'data', 'bpr'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download("laporan_bonus_" . str_replace(' ', '_', $periode->nama) . ".pdf");
    }

    /**
     * Display laporan cuti
     */
    public function cuti(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $status = $request->status;
        
        $query = Cuti::with(['pegawai', 'pegawai.jabatan']);
        
        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total' => $data->count(),
            'disetujui' => $data->where('status', 'disetujui')->count(),
            'ditolak' => $data->where('status', 'ditolak')->count(),
            'pending' => $data->where('status', 'pending')->count(),
            'dibatalkan' => $data->where('status', 'dibatalkan')->count(),
            'total_hari' => $data->where('status', 'disetujui')->sum('lama_hari'),
        ];

        // List tahun untuk filter
        $tahunList = Cuti::selectRaw('YEAR(created_at) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (empty($tahunList)) {
            $tahunList = [date('Y')];
        }

        return view('admin.laporan.cuti', compact('data', 'summary', 'tahun', 'tahunList', 'status'));
    }

    /**
     * Download laporan cuti (PDF/Excel)
     */
    public function downloadCuti(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $format = $request->format ?? 'pdf';

        $query = Cuti::with(['pegawai', 'pegawai.jabatan']);
        
        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total' => $data->count(),
            'disetujui' => $data->where('status', 'disetujui')->count(),
            'ditolak' => $data->where('status', 'ditolak')->count(),
            'pending' => $data->where('status', 'pending')->count(),
            'total_hari' => $data->where('status', 'disetujui')->sum('lama_hari'),
        ];

        $bpr = BPR::first();

        if ($format == 'excel') {
            return Excel::download(
                new LaporanCutiExport($tahun),
                "laporan_cuti_" . $tahun . ".xlsx"
            );
        }

        // PDF
        $pdf = Pdf::loadView('admin.laporan.pdf-cuti', compact('data', 'summary', 'tahun', 'bpr'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download("laporan_cuti_" . $tahun . ".pdf");
    }
}