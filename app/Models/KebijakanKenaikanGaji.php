<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KebijakanKenaikanGaji extends Model
{
    use HasFactory;

    // TAMBAHKAN INI - spesifikasikan nama tabel
    protected $table = 'kebijakan_kenaikan_gaji';

    protected $fillable = [
        'predikat_id',
        'persentase',
        'minimal_gaji',
        'maksimal_gaji',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function predikat()
    {
        return $this->belongsTo(PredikatKinerja::class);
    }
}