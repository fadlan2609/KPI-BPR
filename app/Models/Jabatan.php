<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = [
        'nama',
        'jabatan_atasan_id',
        'level',
        'keterangan'
    ];

    public function atasan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_atasan_id');
    }

    public function bawahan()
    {
        return $this->hasMany(Jabatan::class, 'jabatan_atasan_id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function indikatorKPI()
    {
        return $this->hasMany(IndikatorKPI::class);
    }

    public function indikatorKompetensi()
    {
        return $this->hasMany(IndikatorKompetensi::class);
    }

    public function indikatorCoreValues()
    {
        return $this->hasMany(IndikatorCoreValue::class);
    }

    public function bobotPenilaian()
    {
        return $this->hasMany(BobotPenilaian::class);
    }
}