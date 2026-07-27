<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BPR;

class BPRSeeder extends Seeder
{
    public function run(): void
    {
        BPR::create([
            'sandi_bpr' => 'BPRS001',
            'nama_bpr' => 'BPRS Amanah Bangsa',
            'jenis_bpr' => 'Bank Syariah',
            'jenis_lembaga' => 'BPRS',
            'kategori' => 'Kategori A',
            'no_telp' => '(061) 1234567',
            'alamat' => 'Jl. Sudirman No. 123, Medan',
            'email' => 'info@bprsamanahbangsa.co.id',
            'website' => 'www.bprsamanahbangsa.co.id'
        ]);
    }
}