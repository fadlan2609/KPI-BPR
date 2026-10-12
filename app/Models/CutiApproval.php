<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiApproval extends Model
{
    use HasFactory;

    protected $table = 'cuti_approvals';

    protected $fillable = [
        'cuti_id',
        'level',
        'approver_id',
        'requested_by',
        'status',
        'is_final',
        'is_planned',
        'catatan',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_final' => 'boolean',
        'is_planned' => 'boolean',
    ];

    // ==================== RELASI ====================
    public function cuti()
    {
        return $this->belongsTo(Cuti::class);
    }

    public function approver()
    {
        return $this->belongsTo(Pegawai::class, 'approver_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(Pegawai::class, 'requested_by');
    }

    // ==================== HELPER ====================
    public function isPending()
    {
        return $this->status === 'pending';
    }
}