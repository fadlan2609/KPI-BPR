<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiPendidikan extends Model
{
    protected $table = 'pegawai_pendidikan';

    protected $fillable = [
        'pegawai_id', 'jenjang', 'nama_sekolah', 'jurusan', 'kota',
        'tahun_masuk', 'tahun_lulus', 'no_ijazah', 'file_ijazah', 'keterangan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}