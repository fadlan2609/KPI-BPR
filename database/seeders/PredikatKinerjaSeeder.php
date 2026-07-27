<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PredikatKinerja;

class PredikatKinerjaSeeder extends Seeder
{
    public function run(): void
    {
        $predikat = [
            [
                'nama' => 'Sangat Baik',
                'keterangan' => 'Kinerja melebihi ekspektasi',
                'batas_atas' => 100,
                'batas_bawah' => 85,
                'warna_text' => '#FFFFFF',
                'warna_latar' => '#28A745'
            ],
            [
                'nama' => 'Baik',
                'keterangan' => 'Kinerja sesuai ekspektasi',
                'batas_atas' => 84.99,
                'batas_bawah' => 70,
                'warna_text' => '#000000',
                'warna_latar' => '#FFC107'
            ],
            [
                'nama' => 'Cukup',
                'keterangan' => 'Kinerja perlu peningkatan',
                'batas_atas' => 69.99,
                'batas_bawah' => 60,
                'warna_text' => '#FFFFFF',
                'warna_latar' => '#FD7E14'
            ],
            [
                'nama' => 'Kurang',
                'keterangan' => 'Kinerja di bawah target',
                'batas_atas' => 59.99,
                'batas_bawah' => 0,
                'warna_text' => '#FFFFFF',
                'warna_latar' => '#DC3545'
            ]
        ];

        foreach ($predikat as $p) {
            PredikatKinerja::create($p);
        }
    }
}