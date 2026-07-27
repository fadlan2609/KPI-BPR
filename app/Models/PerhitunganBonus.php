<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerhitunganBonus extends Model
{
    use HasFactory;

    protected $table = 'perhitungan_bonus';

    protected $fillable = [
        'pegawai_id',
        'periode_id',
        'gaji_pokok',
        'nilai_kinerja',
        'predikat',
        'bobot',
        'bonus_dasar',
        'bonus_akhir'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class);
    }
}