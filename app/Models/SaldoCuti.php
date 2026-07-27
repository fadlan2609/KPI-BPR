<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoCuti extends Model
{
    use HasFactory;

    protected $table = 'saldo_cuti';

    protected $fillable = [
        'pegawai_id',
        'tahun',
        'total_hari',
        'digunakan',
        'sisa_hari'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}