<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\PenilaianKPI;
use App\Models\HasilPenilaian;
use App\Models\PredikatKinerja;
use App\Models\BobotPenilaian;
use App\Models\PeriodePenilaian;
use App\Models\IndikatorKPI;
use App\Models\IndikatorKompetensi;
use App\Models\IndikatorCoreValue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenilaianService
{
    /**
     * Get all penilai for a pegawai
     */
    public function getPenilaiForPegawai($pegawaiId)
    {
        $pegawai = Pegawai::with(['jabatan.atasan', 'atasanLangsung'])->findOrFail($pegawaiId);
        
        $penilai = [];
        
        // 1. Self Assessment (pegawai sendiri)
        $penilai['self'] = $pegawai;
        
        // 2. Atasan Langsung
        if ($pegawai->atasanLangsung) {
            $penilai['atasan_langsung'] = $pegawai->atasanLangsung;
        }
        
        // 3. Atasan Penilai
        if ($pegawai->atasanLangsung && $pegawai->atasanLangsung->atasanLangsung) {
            $penilai['atasan_penilai'] = $pegawai->atasanLangsung->atasanLangsung;
        }
        
        return $penilai;
    }

    /**
     * Check if pegawai has atasan
     */
    public function hasAtasan($pegawaiId)
    {
        $pegawai = Pegawai::find($pegawaiId);
        return $pegawai && $pegawai->atasan_langsung_id ? true : false;
    }

    /**
     * Check if pegawai has atasan penilai
     */
    public function hasAtasanPenilai($pegawaiId)
    {
        $pegawai = Pegawai::with('atasanLangsung.atasanLangsung')->find($pegawaiId);
        return $pegawai && $pegawai->atasanLangsung && $pegawai->atasanLangsung->atasanLangsung ? true : false;
    }

    /**
     * Get required penilai levels
     */
    public function getRequiredLevels($pegawaiId)
    {
        $pegawai = Pegawai::find($pegawaiId);
        $levels = ['self'];
        
        if ($pegawai && $pegawai->atasan_langsung_id) {
            $levels[] = 'atasan_langsung';
            
            $atasanLangsung = $pegawai->atasanLangsung;
            if ($atasanLangsung && $atasanLangsung->atasan_langsung_id) {
                $levels[] = 'atasan_penilai';
            }
        }
        
        return $levels;
    }

    /**
     * Get indikator untuk jabatan
     */
    public function getIndikatorForJabatan($jabatanId)
    {
        return [
            'kpi' => IndikatorKPI::where('jabatan_id', $jabatanId)->get(),
            'kompetensi' => IndikatorKompetensi::where('jabatan_id', $jabatanId)->get(),
            'core_values' => IndikatorCoreValue::where('jabatan_id', $jabatanId)->get(),
        ];
    }

    /**
     * Calculate nilai total dengan konversi skala
     * KPI: 0-100, Kompetensi: 1-5 (dikonversi ke 0-100), Core Values: 1-5 (dikonversi ke 0-100)
     */
    public function calculateNilaiTotal($nilaiKPI, $nilaiKompetensi, $nilaiCoreValues, $bobot)
    {
        // Konversi kompetensi dari skala 1-5 ke 0-100
        $nilaiKompetensiKonversi = ($nilaiKompetensi / 5) * 100;
        
        // Konversi core values dari skala 1-5 ke 0-100
        $nilaiCoreKonversi = ($nilaiCoreValues / 5) * 100;
        
        $nilaiTotal = ($nilaiKPI * $bobot->bobot_kpi / 100) +
                      ($nilaiKompetensiKonversi * $bobot->bobot_kompetensi / 100) +
                      ($nilaiCoreKonversi * $bobot->bobot_core_values / 100);
        
        return round($nilaiTotal, 2);
    }

    /**
     * Save penilaian
     */
    public function savePenilaian($data)
    {
        $periode = PeriodePenilaian::where('is_active', true)->first();
        
        if (!$periode) {
            throw new \Exception('Tidak ada periode penilaian aktif');
        }
        
        // Hitung rata-rata nilai
        $avgKPI = array_sum($data['nilai_kpi']) / count($data['nilai_kpi']);
        $avgKompetensi = array_sum($data['nilai_kompetensi']) / count($data['nilai_kompetensi']);
        $avgCoreValues = array_sum($data['nilai_core_values']) / count($data['nilai_core_values']);
        
        // Ambil bobot
        $pegawai = Pegawai::find($data['pegawai_id']);
        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $periode->id)
            ->first();
        
        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
            ];
        }
        
        // Hitung nilai total dengan konversi
        $nilaiTotal = $this->calculateNilaiTotal($avgKPI, $avgKompetensi, $avgCoreValues, $bobot);
        
        // Simpan penilaian
        $penilaian = PenilaianKPI::create([
            'pegawai_id' => $data['pegawai_id'],
            'penilai_id' => $data['penilai_id'],
            'periode_id' => $periode->id,
            'level_penilai' => $data['level_penilai'],
            'nilai_kpi' => round($avgKPI, 2),
            'nilai_kompetensi' => round($avgKompetensi, 2), // Nilai asli 1-5
            'nilai_core_values' => round($avgCoreValues, 2), // Nilai asli 1-5
            'nilai_total' => $nilaiTotal,
            'detail_indikator' => json_encode([
                'kpi' => $data['nilai_kpi'],
                'kompetensi' => $data['nilai_kompetensi'],
                'core_values' => $data['nilai_core_values'],
            ]),
            'status' => 'submitted',
            'submitted_at' => Carbon::now(),
        ]);
        
        $this->checkAndFinalize($data['pegawai_id'], $periode->id);
        
        return $penilaian;
    }

    /**
     * Save penilaian with detail (including comments)
     */
    public function savePenilaianWithDetail($data, $detail)
    {
        $periode = PeriodePenilaian::where('is_active', true)->first();
        
        if (!$periode) {
            throw new \Exception('Tidak ada periode penilaian aktif');
        }
        
        // Hitung rata-rata nilai
        $avgKPI = array_sum($data['nilai_kpi']) / count($data['nilai_kpi']);
        $avgKompetensi = array_sum($data['nilai_kompetensi']) / count($data['nilai_kompetensi']);
        $avgCoreValues = array_sum($data['nilai_core_values']) / count($data['nilai_core_values']);
        
        // Ambil bobot
        $pegawai = Pegawai::find($data['pegawai_id']);
        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $periode->id)
            ->first();
        
        if (!$bobot) {
            $bobot = (object) [
                'bobot_kpi' => 70,
                'bobot_kompetensi' => 15,
                'bobot_core_values' => 15,
            ];
        }
        
        // Hitung nilai total dengan konversi
        $nilaiTotal = $this->calculateNilaiTotal($avgKPI, $avgKompetensi, $avgCoreValues, $bobot);
        
        // Simpan penilaian
        $penilaian = PenilaianKPI::create([
            'pegawai_id' => $data['pegawai_id'],
            'penilai_id' => $data['penilai_id'],
            'periode_id' => $periode->id,
            'level_penilai' => $data['level_penilai'],
            'nilai_kpi' => round($avgKPI, 2),
            'nilai_kompetensi' => round($avgKompetensi, 2), // Nilai asli 1-5
            'nilai_core_values' => round($avgCoreValues, 2), // Nilai asli 1-5
            'nilai_total' => $nilaiTotal,
            'detail_indikator' => json_encode($detail),
            'status' => 'submitted',
            'submitted_at' => Carbon::now(),
        ]);
        
        $this->checkAndFinalize($data['pegawai_id'], $periode->id);
        
        return $penilaian;
    }

    /**
     * Check and finalize
     */
    public function checkAndFinalize($pegawaiId, $periodeId)
    {
        $requiredLevels = $this->getRequiredLevels($pegawaiId);
        
        $completedLevels = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->pluck('level_penilai')
            ->toArray();
        
        $completed = array_intersect($requiredLevels, $completedLevels);
        
        if (count($completed) === count($requiredLevels)) {
            $this->finalizePenilaian($pegawaiId, $periodeId);
        }
    }

    /**
     * Finalize penilaian
     */
    public function finalizePenilaian($pegawaiId, $periodeId)
    {
        $pegawai = Pegawai::find($pegawaiId);
        $periode = PeriodePenilaian::find($periodeId);
        
        $penilaian = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->get()
            ->keyBy('level_penilai');
        
        $bobot = BobotPenilaian::where('jabatan_id', $pegawai->jabatan_id)
            ->where('periode_id', $periodeId)
            ->first();
        
        if (!$bobot) {
            $bobot = (object) [
                'bobot_self' => 20,
                'bobot_atasan_langsung' => 50,
                'bobot_atasan_penilai' => 30,
            ];
        }
        
        // Ambil nilai total dari masing-masing level (sudah dihitung dengan konversi)
        $nilaiSelf = $penilaian['self']->nilai_total ?? 0;
        $nilaiAtasan = $penilaian['atasan_langsung']->nilai_total ?? 0;
        $nilaiPenilai = $penilaian['atasan_penilai']->nilai_total ?? 0;
        
        $hasAtasan = $this->hasAtasan($pegawaiId);
        $hasAtasanPenilai = $this->hasAtasanPenilai($pegawaiId);
        
        // Calculate final score
        if (!$hasAtasan) {
            $nilaiAkhir = $nilaiSelf;
        } elseif (!$hasAtasanPenilai) {
            $nilaiAkhir = ($nilaiSelf * 0.2) + ($nilaiAtasan * 0.8);
        } else {
            $nilaiAkhir = ($nilaiSelf * $bobot->bobot_self / 100) +
                          ($nilaiAtasan * $bobot->bobot_atasan_langsung / 100) +
                          ($nilaiPenilai * $bobot->bobot_atasan_penilai / 100);
        }
        
        $nilaiAkhir = round($nilaiAkhir, 2);
        
        $predikat = PredikatKinerja::where('batas_bawah', '<=', $nilaiAkhir)
            ->where('batas_atas', '>=', $nilaiAkhir)
            ->first();
        
        $hasil = HasilPenilaian::updateOrCreate(
            [
                'pegawai_id' => $pegawaiId,
                'periode_id' => $periodeId,
            ],
            [
                'nilai_self' => $nilaiSelf,
                'nilai_atasan_langsung' => $nilaiAtasan,
                'nilai_atasan_penilai' => $nilaiPenilai,
                'nilai_akhir' => $nilaiAkhir,
                'predikat_id' => $predikat?->id,
                'status' => 'final',
            ]
        );
        
        return $hasil;
    }

    /**
     * Get progress penilaian
     */
    public function getProgress($pegawaiId, $periodeId)
    {
        $pegawai = Pegawai::find($pegawaiId);
        $penilaian = PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->get()
            ->keyBy('level_penilai');
        
        $requiredLevels = $this->getRequiredLevels($pegawaiId);
        
        $levels = [];
        
        // Self Assessment
        $levels['self'] = [
            'label' => 'Self Assessment',
            'required' => in_array('self', $requiredLevels),
            'completed' => isset($penilaian['self']),
            'nilai' => $penilaian['self']->nilai_total ?? null,
        ];
        
        // Atasan Langsung
        $hasAtasan = $this->hasAtasan($pegawaiId);
        if ($hasAtasan) {
            $levels['atasan_langsung'] = [
                'label' => 'Atasan Langsung',
                'required' => in_array('atasan_langsung', $requiredLevels),
                'completed' => isset($penilaian['atasan_langsung']),
                'nilai' => $penilaian['atasan_langsung']->nilai_total ?? null,
            ];
        }
        
        // Atasan Penilai
        $hasAtasanPenilai = $this->hasAtasanPenilai($pegawaiId);
        if ($hasAtasanPenilai) {
            $levels['atasan_penilai'] = [
                'label' => 'Atasan Penilai',
                'required' => in_array('atasan_penilai', $requiredLevels),
                'completed' => isset($penilaian['atasan_penilai']),
                'nilai' => $penilaian['atasan_penilai']->nilai_total ?? null,
            ];
        }
        
        $hasil = HasilPenilaian::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->first();
        
        return [
            'levels' => $levels,
            'finalized' => $hasil && $hasil->status === 'final',
            'nilai_akhir' => $hasil->nilai_akhir ?? null,
            'predikat' => $hasil->predikat->nama ?? null,
            'predikat_warna' => $hasil->predikat->warna_latar ?? null,
        ];
    }

    /**
     * Get penilaian by pegawai
     */
    public function getPenilaianByPegawai($pegawaiId, $periodeId)
    {
        return PenilaianKPI::with(['penilai'])
            ->where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->get()
            ->keyBy('level_penilai');
    }

    /**
     * Check if pegawai has self assessment
     */
    public function hasSelfAssessment($pegawaiId, $periodeId)
    {
        return PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->where('level_penilai', 'self')
            ->exists();
    }

    /**
     * Check if pegawai has atasan langsung assessment
     */
    public function hasAtasanLangsungAssessment($pegawaiId, $periodeId)
    {
        return PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->where('level_penilai', 'atasan_langsung')
            ->exists();
    }

    /**
     * Check if pegawai has atasan penilai assessment
     */
    public function hasAtasanPenilaiAssessment($pegawaiId, $periodeId)
    {
        return PenilaianKPI::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->where('level_penilai', 'atasan_penilai')
            ->exists();
    }

    /**
     * Get total self assessment
     */
    public function getTotalSelfAssessment($periodeId)
    {
        return PenilaianKPI::where('periode_id', $periodeId)
            ->where('level_penilai', 'self')
            ->distinct('pegawai_id')
            ->count('pegawai_id');
    }

    /**
     * Get total final
     */
    public function getTotalFinal($periodeId)
    {
        return HasilPenilaian::where('periode_id', $periodeId)
            ->where('status', 'final')
            ->count();
    }

    /**
     * Get statistik penilaian
     */
    public function getStatistik($periodeId)
    {
        $data = HasilPenilaian::with(['predikat'])
            ->where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        return [
            'total' => $data->count(),
            'sangat_baik' => $data->where('predikat.nama', 'Sangat Baik')->count(),
            'baik' => $data->where('predikat.nama', 'Baik')->count(),
            'cukup' => $data->where('predikat.nama', 'Cukup')->count(),
            'kurang' => $data->where('predikat.nama', 'Kurang')->count(),
            'rata_rata' => $data->avg('nilai_akhir') ?? 0,
            'nilai_tertinggi' => $data->max('nilai_akhir') ?? 0,
            'nilai_terendah' => $data->min('nilai_akhir') ?? 0,
        ];
    }
}