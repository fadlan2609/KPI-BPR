<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnakPendidikan extends Model
{
    protected $table = 'anak_pendidikan';

    protected $fillable = [
        'anak_id', 'jenjang', 'nama_sekolah', 'kota',
        'tahun_masuk', 'tahun_lulus', 'keterangan'
    ];

    public function anak()
    {
        return $this->belongsTo(PegawaiAnak::class, 'anak_id');
    }
}