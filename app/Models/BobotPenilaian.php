<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BobotPenilaian extends Model
{
    use HasFactory;

    protected $table = 'bobot_penilaian';

    protected $fillable = [
        'jabatan_id',
        'periode_id',
        'bobot_kpi',
        'bobot_kompetensi',
        'bobot_core_values',
        'bobot_self',
        'bobot_atasan_langsung',
        'bobot_atasan_penilai'
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class);
    }
}