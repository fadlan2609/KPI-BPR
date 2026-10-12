<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatJabatan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_jabatan';

    protected $fillable = [
        'pegawai_id',
        'jabatan_id',
        'kantor_id',
        'status_karyawan',
        'jenis_kontrak',
        'tahun_mulai',
        'tahun_selesai',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tahun_mulai' => 'integer',
        'tahun_selesai' => 'integer',
    ];

    // ==================== RELASI ====================
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    // ==================== ACCESSOR ====================
    public function getPeriodeLabelAttribute()
    {
        $mulai = $this->tahun_mulai;
        $selesai = $this->tahun_selesai ?? 'Sekarang';

        return "{$mulai} - {$selesai}";
    }

    public function getDurasiAttribute()
    {
        $selesai = $this->tahun_selesai ?? date('Y');
        $durasi = $selesai - $this->tahun_mulai;

        return $durasi > 0 ? $durasi . ' tahun' : 'Kurang dari 1 tahun';
    }

    public function getIsCurrentAttribute()
    {
        return $this->tahun_selesai === null;
    }

    /**
     * Label status karyawan
     */
    public function getStatusKaryawanLabelAttribute()
    {
        return match($this->status_karyawan) {
            'magang' => 'Magang',
            'kontrak' => 'Kontrak',
            'tetap' => 'Tetap',
            'honorer' => 'Honorer',
            'outsourcing' => 'Outsourcing',
            'lainnya' => 'Lainnya',
            default => '-',
        };
    }

    /**
     * Warna badge status karyawan
     */
    public function getStatusKaryawanColorAttribute()
    {
        return match($this->status_karyawan) {
            'magang' => ['bg' => '#fef3c7', 'text' => '#92400e', 'border' => '#f59e0b'],
            'kontrak' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'border' => '#3b82f6'],
            'tetap' => ['bg' => '#dcfce7', 'text' => '#166534', 'border' => '#22c55e'],
            'honorer' => ['bg' => '#f3e8ff', 'text' => '#6b21a8', 'border' => '#a855f7'],
            'outsourcing' => ['bg' => '#fed7aa', 'text' => '#9a3412', 'border' => '#f97316'],
            'lainnya' => ['bg' => '#f3f4f6', 'text' => '#374151', 'border' => '#9ca3af'],
            default => ['bg' => '#f3f4f6', 'text' => '#374151', 'border' => '#9ca3af'],
        };
    }

    /**
     * Daftar opsi status karyawan (untuk dropdown)
     */
    public static function getStatusKaryawanOptions()
    {
        return [
            'magang' => 'Magang',
            'kontrak' => 'Kontrak',
            'tetap' => 'Tetap',
            'honorer' => 'Honorer',
            'outsourcing' => 'Outsourcing',
            'lainnya' => 'Lainnya',
        ];
    }

    // ==================== SCOPE ====================
    public function scopeAktif($query)
    {
        return $query->whereNull('tahun_selesai');
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('tahun_mulai', 'asc');
    }

    public function scopeTerbaru($query)
    {
        return $query->orderBy('tahun_mulai', 'desc');
    }
}