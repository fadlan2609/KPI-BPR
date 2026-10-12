<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\RewardPunishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RewardPunishmentController extends Controller
{
    /**
     * Ambil kategori dari route defaults atau request
     */
    private function getKategori(Request $request)
    {
        return $request->kategori
            ?? $request->route()->defaults['kategori']
            ?? null;
    }

    /**
     * Halaman reward / punishment pegawai
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        $kategori = $this->getKategori($request);

        // Ambil data sesuai kategori
        $query = RewardPunishment::where('pegawai_id', $pegawai->id);
        if ($kategori) $query->where('kategori', $kategori);
        $data = $query->orderBy('tanggal', 'desc')->get();

        // Untuk kompatibilitas dengan view lama (yang masih pakai $rewards & $punishments)
        $rewards = RewardPunishment::where('pegawai_id', $pegawai->id)
            ->reward()
            ->orderBy('tanggal', 'desc')
            ->get();

        $punishments = RewardPunishment::where('pegawai_id', $pegawai->id)
            ->punishment()
            ->orderBy('tanggal', 'desc')
            ->get();

        $stats = [
            'total_reward' => $rewards->count(),
            'total_punishment' => $punishments->count(),
            'total_nominal_reward' => $rewards->where('status', 'aktif')->sum('nominal'),
            'total_nominal_punishment' => $punishments->where('status', 'aktif')->sum('nominal'),
        ];

        return view('pegawai.reward-punishment.index', compact(
            'pegawai',
            'data',
            'rewards',
            'punishments',
            'stats',
            'kategori'
        ));
    }

    /**
     * Detail item
     */
    public function show($id)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $item = RewardPunishment::where('pegawai_id', $pegawai->id)
            ->with(['createdBy', 'updatedBy'])
            ->findOrFail($id);

        return view('pegawai.reward-punishment.show', compact('pegawai', 'item'));
    }

    /**
     * Cetak laporan sendiri
     */
    public function cetakLaporan(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Akun tidak terhubung dengan pegawai!');
        }

        $kategori = $this->getKategori($request);

        $query = RewardPunishment::where('pegawai_id', $pegawai->id);

        if ($kategori) $query->where('kategori', $kategori);
        if ($request->dari_tanggal && $request->sampai_tanggal) {
            $query->whereBetween('tanggal', [$request->dari_tanggal, $request->sampai_tanggal]);
        }

        $data = $query->orderBy('tanggal', 'asc')->get();
        $bpr = \App\Models\BPR::first();

        $stats = [
            'total_reward' => $data->where('kategori', 'reward')->count(),
            'total_punishment' => $data->where('kategori', 'punishment')->count(),
            'total_nominal_reward' => $data->where('kategori', 'reward')->where('status', 'aktif')->sum('nominal'),
            'total_nominal_punishment' => $data->where('kategori', 'punishment')->where('status', 'aktif')->sum('nominal'),
        ];

        $pdf = Pdf::loadView('pegawai.reward-punishment.laporan-pdf', compact('pegawai', 'data', 'bpr', 'stats', 'kategori'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times-Roman');

        $prefix = $kategori ? ucfirst($kategori) : 'Reward_Punishment';
        $namaFile = 'Laporan_' . $prefix . '_' . str_replace(' ', '_', $pegawai->nama) . '.pdf';

        return $pdf->stream($namaFile);
    }
}