<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Illuminate\Database\Seeder;

class SaldoCutiSeeder extends Seeder
{
    public function run()
    {
        $tahun = date('Y');
        $pegawai = Pegawai::all();

        foreach ($pegawai as $p) {
            SaldoCuti::firstOrCreate(
                [
                    'pegawai_id' => $p->id,
                    'tahun' => $tahun,
                ],
                [
                    'total_hari' => 12,
                    'digunakan' => 0,
                    'sisa_hari' => 12,
                    'keterangan' => "Saldo cuti tahun {$tahun}",
                ]
            );
        }
    }
}