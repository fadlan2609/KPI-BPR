<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePenilaian;
use App\Models\KebijakanKenaikanGaji;
use App\Models\PredikatKinerja;
use App\Models\Pegawai;
use App\Models\GajiPokok;
use App\Services\GajiService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KenaikanGajiExport;

class GajiController extends Controller
{
    protected $gajiService;

    public function __construct(GajiService $gajiService)
    {
        $this->gajiService = $gajiService;
    }

    public function index()
    {
        $periodeList = PeriodePenilaian::where('status', 'closed')
            ->orderBy('created_at', 'desc')
            ->get();

        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        $totalPegawai = Pegawai::where('status', 'aktif')->count();
        $totalGaji = GajiPokok::where('is_active', true)->sum('gaji_pokok');

        return view('admin.gaji.index', compact('periodeList', 'periodeAktif', 'totalPegawai', 'totalGaji'));
    }

    public function kenaikan(Request $request)
    {
        $periodeId = $request->periode_id;
        
        if (!$periodeId) {
            return redirect()->route('admin.gaji.index')
                ->with('error', 'Silakan pilih periode terlebih dahulu!');
        }

        $periode = PeriodePenilaian::findOrFail($periodeId);
        $results = $this->gajiService->hitungKenaikanGajiPeriode($periodeId);

        // Summary
        $summary = [
            'total_pegawai' => count($results),
            'total_kenaikan' => array_sum(array_column($results, 'nominal_kenaikan')),
            'rata_rata_persentase' => count($results) > 0 
                ? array_sum(array_column($results, 'persentase_kenaikan')) / count($results) 
                : 0,
        ];

        return view('admin.gaji.kenaikan', compact('periode', 'results', 'summary'));
    }

    public function approveKenaikan(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'pegawai_ids' => 'required|array',
        ]);

        $periodeId = $request->periode_id;

        foreach ($request->pegawai_ids as $pegawaiId) {
            $data = $this->gajiService->hitungKenaikanGajiPegawai($pegawaiId, $periodeId);
            if ($data) {
                $this->gajiService->simpanRiwayatKenaikan($data, $periodeId);
            }
        }

        return redirect()->route('admin.gaji.kenaikan', ['periode_id' => $periodeId])
            ->with('success', 'Kenaikan gaji berhasil disetujui!');
    }

    public function exportKenaikan(Request $request)
    {
        $periodeId = $request->periode_id;
        $periode = PeriodePenilaian::findOrFail($periodeId);

        return Excel::download(
            new KenaikanGajiExport($periodeId), 
            "kenaikan_gaji_{$periode->nama}_{date('Y-m-d')}.xlsx"
        );
    }

    public function setting()
    {
        $predikat = PredikatKinerja::all();
        $kebijakan = KebijakanKenaikanGaji::where('is_active', true)
            ->get()
            ->keyBy('predikat_id');

        return view('admin.gaji.setting', compact('predikat', 'kebijakan'));
    }

    public function saveSetting(Request $request)
    {
        $request->validate([
            'persentase' => 'required|array',
            'persentase.*' => 'required|numeric|min:0|max:100',
            'minimal_gaji' => 'nullable|array',
            'minimal_gaji.*' => 'nullable|numeric|min:0',
            'maksimal_gaji' => 'nullable|array',
            'maksimal_gaji.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->persentase as $predikatId => $persentase) {
            KebijakanKenaikanGaji::updateOrCreate(
                [
                    'predikat_id' => $predikatId,
                    'is_active' => true,
                ],
                [
                    'persentase' => $persentase,
                    'minimal_gaji' => $request->minimal_gaji[$predikatId] ?? null,
                    'maksimal_gaji' => $request->maksimal_gaji[$predikatId] ?? null,
                ]
            );
        }

        return redirect()->route('admin.gaji.setting')
            ->with('success', 'Kebijakan kenaikan gaji berhasil disimpan!');
    }
}