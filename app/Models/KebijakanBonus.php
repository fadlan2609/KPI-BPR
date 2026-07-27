<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KebijakanBonus extends Model
{
    use HasFactory;

    protected $table = 'kebijakan_bonus';

    protected $fillable = [
        'periode_id',
        'total_pool',
        'bobot_predikat',
        'is_active'
    ];

    protected $casts = [
        'bobot_predikat' => 'array',
        'is_active' => 'boolean'
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodePenilaian::class);
    }
}