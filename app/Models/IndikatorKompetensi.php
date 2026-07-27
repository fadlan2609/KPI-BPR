<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKompetensi extends Model
{
    use HasFactory;

    protected $table = 'indikator_kompetensi';

    protected $fillable = [
        'jabatan_id',
        'perspektif',
        'nama',
        'indikator',
        'skala_maksimal',
        'bobot'
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}