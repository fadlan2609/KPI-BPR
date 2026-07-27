<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';

    protected $fillable = [
        'pegawai_id',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_hari',
        'keterangan',
        'status',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Pegawai::class, 'approved_by');
    }

    // Accessor untuk mendapatkan durasi dalam hari kalender
    public function getDurasiKalenderAttribute()
    {
        $mulai = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);
        return $mulai->diffInDays($selesai) + 1;
    }

    // Accessor untuk mendapatkan jumlah hari libur
    public function getHariLiburAttribute()
    {
        $mulai = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);
        $libur = 0;
        $current = $mulai->copy();
        
        while ($current <= $selesai) {
            if ($current->isWeekend()) {
                $libur++;
            }
            $current->addDay();
        }
        
        return $libur;
    }
}