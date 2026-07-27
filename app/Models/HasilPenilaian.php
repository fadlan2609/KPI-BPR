<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPenilaian extends Model
{
    use HasFactory;

    protected $table = 'hasil_penilaian';

    protected $fillable = [
        'pegawai_id',
        'periode_id',
        'predikat_id',
        'nilai_self',
        'nilai_atasan_langsung',
        'nilai_atasan_penilai',
        'nilai_akhir',
        'status',
        'approved_by',
        'approved_at'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class);
    }

    public function predikat()
    {
        return $this->belongsTo(PredikatKinerja::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Pegawai::class, 'approved_by');
    }
}