<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\HasilPenilaian;
use App\Models\KebijakanBonus;
use App\Models\PerhitunganBonus;
use App\Models\PeriodePenilaian;
use Illuminate\Support\Facades\DB;

class BonusService
{
    /**
     * Hitung bonus untuk semua pegawai di periode tertentu
     */
    public function hitungBonusPeriode($periodeId)
    {
        // 1. Ambil kebijakan bonus
        $kebijakan = KebijakanBonus::where('periode_id', $periodeId)
            ->where('is_active', true)
            ->first();

        if (!$kebijakan) {
            throw new \Exception('Kebijakan bonus untuk periode ini belum di-set!');
        }

        $bobotPredikat = $kebijakan->bobot_predikat;

        // 2. Ambil hasil penilaian final
        $hasilPenilaian = HasilPenilaian::where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        if ($hasilPenilaian->isEmpty()) {
            return [];
        }

        // 3. Hitung bonus dasar
        $bonusDasarList = [];
        foreach ($hasilPenilaian as $penilaian) {
            $pegawai = $penilaian->pegawai;
            $gajiPokok = $pegawai->gajiPokokAktif->gaji_pokok ?? 0;
            $predikatNama = $penilaian->predikat->nama ?? 'Kurang';
            
            $bobot = $bobotPredikat[$predikatNama] ?? 0;
            $bonusDasar = $gajiPokok * $bobot;

            $bonusDasarList[] = [
                'pegawai_id' => $penilaian->pegawai_id,
                'pegawai_nama' => $pegawai->nama,
                'pegawai_nip' => $pegawai->nip,
                'gaji_pokok' => $gajiPokok,
                'nilai_kinerja' => $penilaian->nilai_akhir,
                'predikat' => $predikatNama,
                'bobot' => $bobot,
                'bonus_dasar' => $bonusDasar,
                'bonus_akhir' => 0,
            ];
        }

        // 4. Proporsikan dengan total bonus pool
        $totalBonusDasar = array_sum(array_column($bonusDasarList, 'bonus_dasar'));

        if ($totalBonusDasar > 0) {
            foreach ($bonusDasarList as &$item) {
                $proporsi = $item['bonus_dasar'] / $totalBonusDasar;
                $item['bonus_akhir'] = round($kebijakan->total_pool * $proporsi, 2);
            }
        }

        return $bonusDasarList;
    }

    /**
     * Simpan perhitungan bonus
     */
    public function simpanPerhitunganBonus($periodeId, $results)
    {
        DB::beginTransaction();

        try {
            // Hapus perhitungan lama
            PerhitunganBonus::where('periode_id', $periodeId)->delete();

            foreach ($results as $data) {
                PerhitunganBonus::create([
                    'pegawai_id' => $data['pegawai_id'],
                    'periode_id' => $periodeId,
                    'gaji_pokok' => $data['gaji_pokok'],
                    'nilai_kinerja' => $data['nilai_kinerja'],
                    'predikat' => $data['predikat'],
                    'bobot' => $data['bobot'],
                    'bonus_dasar' => $data['bonus_dasar'],
                    'bonus_akhir' => $data['bonus_akhir'],
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get perhitungan bonus
     */
    public function getPerhitunganBonus($periodeId)
    {
        return PerhitunganBonus::with(['pegawai'])
            ->where('periode_id', $periodeId)
            ->get();
    }
}