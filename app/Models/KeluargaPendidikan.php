<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeluargaPendidikan extends Model
{
    protected $table = 'keluarga_pendidikan';

    protected $fillable = [
        'keluarga_id', 'jenjang', 'nama_sekolah', 'jurusan',
        'kota', 'tahun_masuk', 'tahun_lulus', 'keterangan'
    ];

    public function keluarga()
    {
        return $this->belongsTo(PegawaiKeluarga::class, 'keluarga_id');
    }
}