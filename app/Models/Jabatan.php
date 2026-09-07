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

    /**
     * Cek apakah jabatan memiliki indikator
     */
    public function hasIndikator()
    {
        return $this->indikatorKPI()->count() > 0 || 
               $this->indikatorKompetensi()->count() > 0 || 
               $this->indikatorCoreValues()->count() > 0;
    }

    /**
     * Get total indikator
     */
    public function getTotalIndikatorAttribute()
    {
        return $this->indikatorKPI()->count() + 
               $this->indikatorKompetensi()->count() + 
               $this->indikatorCoreValues()->count();
    }

    /**
     * Get status indikator
     */
    public function getStatusIndikatorAttribute()
    {
        return $this->total_indikator > 0 ? 'lengkap' : 'kosong';
    }

    /**
     * Get status indikator text
     */
    public function getStatusIndikatorTextAttribute()
    {
        return $this->status_indikator == 'lengkap' ? '✅ Ada Indikator' : '❌ Belum Ada';
    }
}