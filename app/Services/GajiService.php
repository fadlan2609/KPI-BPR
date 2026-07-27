<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\HasilPenilaian;
use App\Models\KebijakanKenaikanGaji;
use App\Models\GajiPokok;
use App\Models\RiwayatKenaikanGaji;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GajiService
{
    /**
     * Hitung kenaikan gaji untuk semua pegawai di periode tertentu
     */
    public function hitungKenaikanGajiPeriode($periodeId)
    {
        $hasilPenilaian = HasilPenilaian::where('periode_id', $periodeId)
            ->where('status', 'final')
            ->get();

        $results = [];
        foreach ($hasilPenilaian as $penilaian) {
            $result = $this->hitungKenaikanGajiPegawai($penilaian->pegawai_id, $periodeId);
            if ($result) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Hitung kenaikan gaji untuk satu pegawai
     */
    public function hitungKenaikanGajiPegawai($pegawaiId, $periodeId)
    {
        // 1. Ambil hasil penilaian
        $penilaian = HasilPenilaian::where('pegawai_id', $pegawaiId)
            ->where('periode_id', $periodeId)
            ->first();

        if (!$penilaian) {
            return null;
        }

        // 2. Ambil predikat
        $predikat = $penilaian->predikat;
        if (!$predikat) {
            return null;
        }

        // 3. Ambil kebijakan kenaikan
        $kebijakan = KebijakanKenaikanGaji::where('predikat_id', $predikat->id)
            ->where('is_active', true)
            ->first();

        if (!$kebijakan) {
            return null;
        }

        // 4. Ambil gaji pokok terakhir
        $gajiTerakhir = GajiPokok::where('pegawai_id', $pegawaiId)
            ->where('is_active', true)
            ->first();

        if (!$gajiTerakhir) {
            return null;
        }

        $gajiSekarang = $gajiTerakhir->gaji_pokok;

        // 5. Hitung kenaikan
        $persentaseKenaikan = $kebijakan->persentase;
        $nominalKenaikan = $gajiSekarang * ($persentaseKenaikan / 100);
        $gajiBaru = $gajiSekarang + $nominalKenaikan;

        // 6. Validasi batas minimal/maksimal
        if ($kebijakan->minimal_gaji && $gajiBaru < $kebijakan->minimal_gaji) {
            $gajiBaru = $kebijakan->minimal_gaji;
            $nominalKenaikan = $gajiBaru - $gajiSekarang;
            $persentaseKenaikan = ($nominalKenaikan / $gajiSekarang) * 100;
        }

        if ($kebijakan->maksimal_gaji && $gajiBaru > $kebijakan->maksimal_gaji) {
            $gajiBaru = $kebijakan->maksimal_gaji;
            $nominalKenaikan = $gajiBaru - $gajiSekarang;
            $persentaseKenaikan = ($nominalKenaikan / $gajiSekarang) * 100;
        }

        return [
            'pegawai_id' => $pegawaiId,
            'pegawai_nama' => $penilaian->pegawai->nama,
            'pegawai_nip' => $penilaian->pegawai->nip,
            'gaji_sekarang' => $gajiSekarang,
            'gaji_baru' => $gajiBaru,
            'nominal_kenaikan' => $nominalKenaikan,
            'persentase_kenaikan' => round($persentaseKenaikan, 2),
            'predikat' => $predikat->nama,
            'nilai_akhir' => $penilaian->nilai_akhir,
            'tanggal_efektif' => Carbon::now()->addMonth(),
        ];
    }

    /**
     * Simpan riwayat kenaikan gaji
     */
    public function simpanRiwayatKenaikan($data, $periodeId)
    {
        DB::beginTransaction();

        try {
            // 1. Nonaktifkan gaji aktif sebelumnya
            GajiPokok::where('pegawai_id', $data['pegawai_id'])
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // 2. Simpan gaji baru
            GajiPokok::create([
                'pegawai_id' => $data['pegawai_id'],
                'gaji_pokok' => $data['gaji_baru'],
                'tunjangan_jabatan' => 0,
                'tunjangan_keluarga' => 0,
                'tunjangan_lainnya' => 0,
                'tanggal_berlaku' => $data['tanggal_efektif'],
                'is_active' => true,
            ]);

            // 3. Simpan riwayat
            RiwayatKenaikanGaji::create([
                'pegawai_id' => $data['pegawai_id'],
                'periode_id' => $periodeId,
                'gaji_lama' => $data['gaji_sekarang'],
                'gaji_baru' => $data['gaji_baru'],
                'persentase' => $data['persentase_kenaikan'],
                'predikat' => $data['predikat'],
                'tanggal_efektif' => $data['tanggal_efektif'],
                'approved_by' => auth()->user()->pegawai_id,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get riwayat kenaikan gaji pegawai
     */
    public function getRiwayatKenaikan($pegawaiId)
    {
        return RiwayatKenaikanGaji::with(['pegawai', 'periode'])
            ->where('pegawai_id', $pegawaiId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}