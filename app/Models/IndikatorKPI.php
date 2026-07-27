<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKPI extends Model
{
    use HasFactory;

    protected $table = 'indikator_kpi';

    protected $fillable = [
        'jabatan_id',
        'perspektif',
        'nama',
        'indikator',
        'target',
        'satuan',
        'bobot',
        'periode'
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}