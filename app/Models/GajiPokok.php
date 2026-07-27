<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiPokok extends Model
{
    use HasFactory;

    protected $table = 'gaji_pokok';

    protected $fillable = [
        'pegawai_id',
        'gaji_pokok',
        'tunjangan_jabatan',
        'tunjangan_keluarga',
        'tunjangan_lainnya',
        'tanggal_berlaku',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_berlaku' => 'date'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}