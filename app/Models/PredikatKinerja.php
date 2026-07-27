<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredikatKinerja extends Model
{
    use HasFactory;

    // TAMBAHKAN INI - spesifikasikan nama tabel
    protected $table = 'predikat_kinerja';

    protected $fillable = [
        'nama',
        'keterangan',
        'batas_atas',
        'batas_bawah',
        'warna_text',
        'warna_latar'
    ];

    public function hasilPenilaian()
    {
        return $this->hasMany(HasilPenilaian::class);
    }

    public function kebijakanKenaikanGaji()
    {
        return $this->hasMany(KebijakanKenaikanGaji::class);
    }
}