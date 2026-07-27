<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodePenilaian extends Model
{
    use HasFactory;

    protected $table = 'periode_penilaian';

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'batas_self_assessment',
        'batas_penilaian_atasan',
        'batas_finalisasi',
        'status',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'batas_self_assessment' => 'date',
        'batas_penilaian_atasan' => 'date',
        'batas_finalisasi' => 'date',
    ];

    public function penilaian()
    {
        return $this->hasMany(PenilaianKPI::class);
    }

    public function hasilPenilaian()
    {
        return $this->hasMany(HasilPenilaian::class);
    }

    public function kebijakanBonus()
    {
        return $this->hasMany(KebijakanBonus::class);
    }

    public function bobotPenilaian()
    {
        return $this->hasMany(BobotPenilaian::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}