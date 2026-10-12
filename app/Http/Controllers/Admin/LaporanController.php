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
     * Display laporan cuti — semua data, sinkron dengan admin.cuti
     */
    public function cuti(Request $request)
    {
        $query = Cuti::with(['pegawai', 'approvals.approver', 'finalApprovedBy']);

        // Filter status — kalau tidak ada, tampilkan semua
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter jenis cuti
        if ($request->jenis_cuti) {
            $query->where('jenis_cuti', $request->jenis_cuti);
        }

        // Filter tahun — opsional
        if ($request->tahun) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Filter search pegawai
        if ($request->search) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(20);

        // Summary dari seluruh data
        $summary = [
            'total' => Cuti::count(),
            'disetujui' => Cuti::where('status', 'disetujui')->count(),
            'ditolak' => Cuti::where('status', 'ditolak')->count(),
            'pending' => Cuti::where('status', 'pending')->count(),
            'dibatalkan' => Cuti::where('status', 'dibatalkan')->count(),
            'total_hari' => Cuti::where('status', 'disetujui')->sum('lama_hari'),
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

        $tahun = $request->tahun;
        $status = $request->status;
        $jenis_cuti = $request->jenis_cuti;
        $search = $request->search;

        return view('admin.laporan.cuti', compact(
            'data', 'summary', 'tahun', 'tahunList', 'status', 'jenis_cuti', 'search'
        ));
    }

    /**
     * Download laporan cuti (PDF/Excel)
     */
    public function downloadCuti(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $status = $request->status;
        $jenis_cuti = $request->jenis_cuti;
        $format = $request->format ?? 'pdf';

        $query = Cuti::with(['pegawai', 'pegawai.jabatan', 'finalApprovedBy']);

        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($jenis_cuti) {
            $query->where('jenis_cuti', $jenis_cuti);
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

        // Margin
        $pdf->setOption('margin-top', 40);
        $pdf->setOption('margin-right', 40);
        $pdf->setOption('margin-bottom', 40);
        $pdf->setOption('margin-left', 40);

        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream("laporan_cuti_" . $tahun . ".pdf", ['Attachment' => false]);
    }
}