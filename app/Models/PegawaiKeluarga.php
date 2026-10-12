<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiKeluarga extends Model
{
    protected $table = 'pegawai_keluarga';

    protected $fillable = [
        'pegawai_id', 'hubungan', 'nama', 'nik', 'tempat_lahir',
        'tanggal_lahir', 'jenis_kelamin', 'pendidikan_terakhir',
        'pekerjaan', 'alamat', 'no_telp', 'foto'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    public function pendidikan()
    {
        return $this->hasMany(KeluargaPendidikan::class, 'keluarga_id')->orderBy('tahun_masuk');
    }
}