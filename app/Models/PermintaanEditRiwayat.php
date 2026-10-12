<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanEditRiwayat extends Model
{
    use HasFactory;

    protected $table = 'permintaan_edit_riwayat';

    protected $fillable = [
        'pegawai_id',
        'riwayat_jabatan_id',
        'jenis_permintaan',
        'alasan',
        'data_baru',
        'status',
        'catatan_admin',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'data_baru' => 'array',
        'approved_at' => 'datetime',
    ];

    // ==================== RELASI ====================
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function riwayatJabatan()
    {
        return $this->belongsTo(RiwayatJabatan::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Pegawai::class, 'approved_by');
    }

    // ==================== ACCESSOR ====================
    public function getJenisLabelAttribute()
    {
        return match($this->jenis_permintaan) {
            'tambah' => 'Tambah Riwayat',
            'edit' => 'Edit Riwayat',
            'hapus' => 'Hapus Riwayat',
            default => 'Permintaan',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Review',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => '-',
        };
    }

    // ==================== SCOPE ====================
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}