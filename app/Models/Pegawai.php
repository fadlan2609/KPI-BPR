<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan_id',
        'kantor_id',
        'atasan_langsung_id',
        'status',
        'tanggal_lahir',
        'tanggal_masuk',
        'jenis_kelamin',
        'status_pernikahan',
        'status_karyawan',
        'pendidikan_terakhir',
        'nama_pasangan',
        'jumlah_anak',
        'nik',
        'alamat',
        'no_hp',
        'email'
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    public function atasanLangsung()
    {
        return $this->belongsTo(Pegawai::class, 'atasan_langsung_id');
    }

    public function bawahanLangsung()
    {
        return $this->hasMany(Pegawai::class, 'atasan_langsung_id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function gajiPokok()
    {
        return $this->hasMany(GajiPokok::class);
    }

    public function gajiPokokAktif()
    {
        return $this->hasOne(GajiPokok::class)->where('is_active', true);
    }

    public function penilaianSebagaiPegawai()
    {
        return $this->hasMany(PenilaianKPI::class, 'pegawai_id');
    }

    public function penilaianSebagaiPenilai()
    {
        return $this->hasMany(PenilaianKPI::class, 'penilai_id');
    }

    public function hasilPenilaian()
    {
        return $this->hasMany(HasilPenilaian::class);
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    public function saldoCuti()
    {
        return $this->hasMany(SaldoCuti::class);
    }
}