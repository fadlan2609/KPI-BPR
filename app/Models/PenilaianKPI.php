<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianKPI extends Model
{
    use HasFactory;

    protected $table = 'penilaian_kpi';

    protected $fillable = [
        'pegawai_id',
        'penilai_id',
        'periode_id',
        'level_penilai',
        'nilai_kpi',
        'nilai_kompetensi',
        'nilai_core_values',
        'nilai_total',
        'detail_indikator',
        'status',
        'submitted_at'
    ];

    protected $casts = [
        'detail_indikator' => 'array'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function penilai()
    {
        return $this->belongsTo(Pegawai::class, 'penilai_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class);
    }
}