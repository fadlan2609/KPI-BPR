<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKenaikanGaji extends Model
{
    use HasFactory;

    protected $table = 'riwayat_kenaikan_gaji';

    protected $fillable = [
        'pegawai_id',
        'periode_id',
        'gaji_lama',
        'gaji_baru',
        'persentase',
        'predikat',
        'tanggal_efektif',
        'approved_by'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Pegawai::class, 'approved_by');
    }
}