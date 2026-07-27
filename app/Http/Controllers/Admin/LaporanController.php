<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePenilaian;
use App\Models\HasilPenilaian;
use App\Models\PerhitunganBonus;
use App\Models\RiwayatKenaikanGaji;
use App\Models\Cuti;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPenilaianExport;

class LaporanController extends Controller
{
    public function index()
    {
        return view('admin.laporan.index');
    }

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
        $data = HasilPenilaian::with(['pegawai', 'predikat'])
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

    public function downloadPenilaian(Request $request)
    {
        $periodeId = $request->periode_id;
        $format = $request->format ?? 'pdf';

        if ($format == 'excel') {
            return Excel::download(
                new LaporanPenilaianExport($periodeId),
                "laporan_penilaian_{$periodeId}.xlsx"
            );
        }

        $periode = PeriodePenilaian::findOrFail($periodeId);
        $data = HasilPenilaian::with(['pegawai', 'predikat'])
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        $pdf = Pdf::loadView('admin.laporan.pdf-penilaian', compact('periode', 'data'));
        return $pdf->download("laporan_penilaian_{$periode->nama}.pdf");
    }

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
        $data = RiwayatKenaikanGaji::with(['pegawai'])
            ->where('periode_id', $periodeId)
            ->get();

        $summary = [
            'total' => $data->count(),
            'total_kenaikan' => $data->sum('persentase'),
            'rata_rata' => $data->avg('persentase') ?? 0,
        ];

        return view('admin.laporan.gaji', compact('periodeList', 'periode', 'data', 'summary'));
    }

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
        $data = PerhitunganBonus::with(['pegawai'])
            ->where('periode_id', $periodeId)
            ->get();

        $summary = [
            'total' => $data->count(),
            'total_bonus' => $data->sum('bonus_akhir'),
            'rata_rata' => $data->avg('bonus_akhir') ?? 0,
        ];

        return view('admin.laporan.bonus', compact('periodeList', 'periode', 'data', 'summary'));
    }

    public function cuti(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        
        $data = Cuti::with(['pegawai'])
            ->whereYear('created_at', $tahun)
            ->get();

        $summary = [
            'total' => $data->count(),
            'disetujui' => $data->where('status', 'disetujui')->count(),
            'ditolak' => $data->where('status', 'ditolak')->count(),
            'pending' => $data->where('status', 'pending')->count(),
        ];

        return view('admin.laporan.cuti', compact('data', 'summary', 'tahun'));
    }
}