<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePenilaian;
use App\Models\KebijakanBonus;
use App\Models\PredikatKinerja;
use App\Services\BonusService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BonusExport;

class BonusController extends Controller
{
    protected $bonusService;

    public function __construct(BonusService $bonusService)
    {
        $this->bonusService = $bonusService;
    }

    public function index()
    {
        $periodeList = PeriodePenilaian::where('status', 'closed')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.bonus.index', compact('periodeList'));
    }

    public function setting($periodeId)
    {
        $periode = PeriodePenilaian::findOrFail($periodeId);
        $predikatList = PredikatKinerja::all();
        $kebijakan = KebijakanBonus::where('periode_id', $periodeId)->first();

        return view('admin.bonus.setting', compact('periode', 'predikatList', 'kebijakan'));
    }

    public function saveSetting(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'total_pool' => 'required|numeric|min:0',
            'bobot' => 'required|array',
            'bobot.*' => 'required|numeric|min:0|max:10',
        ]);

        KebijakanBonus::updateOrCreate(
            [
                'periode_id' => $request->periode_id,
            ],
            [
                'total_pool' => $request->total_pool,
                'bobot_predikat' => json_encode($request->bobot),
                'is_active' => true,
            ]
        );

        return redirect()->route('admin.bonus.setting', $request->periode_id)
            ->with('success', 'Kebijakan bonus berhasil disimpan!');
    }

    public function hitung(Request $request)
    {
        $periodeId = $request->periode_id;

        if (!$periodeId) {
            return redirect()->route('admin.bonus.index')
                ->with('error', 'Silakan pilih periode terlebih dahulu!');
        }

        try {
            $results = $this->bonusService->hitungBonusPeriode($periodeId);
            $this->bonusService->simpanPerhitunganBonus($periodeId, $results);

            return redirect()->route('admin.bonus.setting', $periodeId)
                ->with('success', 'Perhitungan bonus berhasil dilakukan! Total bonus: Rp ' . number_format(array_sum(array_column($results, 'bonus_akhir')), 0, ',', '.'));
        } catch (\Exception $e) {
            return redirect()->route('admin.bonus.setting', $periodeId)
                ->with('error', 'Gagal menghitung bonus: ' . $e->getMessage());
        }
    }

    public function approve(Request $request)
    {
        $periodeId = $request->periode_id;

        // Update status approval
        // Bisa ditambahkan field approved_at di perhitungan_bonus

        return redirect()->route('admin.bonus.index')
            ->with('success', 'Bonus berhasil disetujui!');
    }

    public function export(Request $request)
    {
        $periodeId = $request->periode_id;
        $periode = PeriodePenilaian::findOrFail($periodeId);

        return Excel::download(
            new BonusExport($periodeId),
            "bonus_{$periode->nama}_{date('Y-m-d')}.xlsx"
        );
    }
}