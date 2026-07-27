<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PredikatKinerja;
use App\Models\KebijakanKenaikanGaji;

class KebijakanSeeder extends Seeder
{
    public function run(): void
    {
        $predikat = PredikatKinerja::all();
        
        $kebijakan = [
            'Sangat Baik' => 10,
            'Baik' => 7,
            'Cukup' => 4,
            'Kurang' => 0
        ];

        foreach ($predikat as $p) {
            KebijakanKenaikanGaji::create([
                'predikat_id' => $p->id,
                'persentase' => $kebijakan[$p->nama] ?? 0,
                'is_active' => true
            ]);
        }
    }
}