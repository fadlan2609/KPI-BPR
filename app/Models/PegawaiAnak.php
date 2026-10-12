<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiAnak extends Model
{
    protected $table = 'pegawai_anak';

    protected $fillable = [
        'pegawai_id', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'urutan_anak', 'status_anak', 'pendidikan_terakhir',
        'sekolah_sekarang', 'pekerjaan', 'alamat', 'foto'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pendidikan()
    {
        return $this->hasMany(AnakPendidikan::class, 'anak_id')->orderBy('tahun_masuk');
    }

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }
}