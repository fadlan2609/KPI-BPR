<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Kantor;
use App\Models\HasilPenilaian;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPegawai = Pegawai::where('status', 'aktif')->count();
        $totalJabatan = Jabatan::count();
        $totalKantor = Kantor::count();
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        // Data untuk grafik
        $chartData = $this->getChartData();
        
        // Progress checklist
        $progress = $this->getProgress();
        
        // Aktivitas terbaru
        $activities = $this->getActivities();
        
        return view('admin.dashboard.index', compact(
            'totalPegawai',
            'totalJabatan',
            'totalKantor',
            'periodeAktif',
            'chartData',
            'progress',
            'activities'
        ));
    }
    
    public function chartData()
    {
        return response()->json($this->getChartData());
    }
    
    private function getChartData()
    {
        // Distribusi pegawai per jabatan
        $jabatan = Jabatan::withCount('pegawai')->get();
        $labels = $jabatan->pluck('nama')->toArray();
        $data = $jabatan->pluck('pegawai_count')->toArray();
        
        // Distribusi pegawai per kantor
        $kantor = Kantor::withCount('pegawai')->get();
        $kantorLabels = $kantor->pluck('nama')->toArray();
        $kantorData = $kantor->pluck('pegawai_count')->toArray();
        
        return [
            'perJabatan' => [
                'labels' => $labels,
                'data' => $data
            ],
            'perKantor' => [
                'labels' => $kantorLabels,
                'data' => $kantorData
            ]
        ];
    }
    
    private function getProgress()
    {
        $checklist = [
            'bpr' => false,
            'jabatan' => false,
            'pegawai' => false,
            'predikat' => false,
            'indikator' => false,
            'self_assessment' => false,
            'laporan' => false
        ];
        
        // Cek BPR
        $bpr = \App\Models\BPR::first();
        $checklist['bpr'] = $bpr && $bpr->nama_bpr;
        
        // Cek Jabatan
        $checklist['jabatan'] = Jabatan::count() > 0;
        
        // Cek Pegawai
        $checklist['pegawai'] = Pegawai::count() > 0;
        
        // Cek Predikat
        $checklist['predikat'] = \App\Models\PredikatKinerja::count() > 0;
        
        // Cek Indikator
        $checklist['indikator'] = \App\Models\IndikatorKPI::count() > 0;
        
        // Cek Self Assessment
        $periode = PeriodePenilaian::where('is_active', true)->first();
        if ($periode) {
            $checklist['self_assessment'] = \App\Models\PenilaianKPI::where('periode_id', $periode->id)
                ->where('level_penilai', 'self')
                ->count() > 0;
        }
        
        // Cek Laporan
        $checklist['laporan'] = \App\Models\HasilPenilaian::where('status', 'final')->count() > 0;
        
        return $checklist;
    }
    
    private function getActivities()
    {
        return \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}