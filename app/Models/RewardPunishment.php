<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RewardPunishment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'reward_punishment';

    protected $fillable = [
        'pegawai_id',
        'kategori',
        'tipe',
        'judul',
        'keterangan',
        'tanggal',
        'nominal',
        'file_bukti',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    // ==================== ACTIVITY LOG ====================
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'pegawai_id',
                'kategori',
                'tipe',
                'judul',
                'keterangan',
                'tanggal',
                'nominal',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Input reward/punishment baru',
                'updated' => 'Edit reward/punishment',
                'deleted' => 'Hapus reward/punishment',
                'restored' => 'Restore reward/punishment',
                default => $eventName,
            });
    }

    // ==================== RELASI ====================
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Pegawai::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Pegawai::class, 'updated_by');
    }

    // ==================== ACCESSOR ====================

    /**
     * Label tipe (yang sudah termasuk tipe baru)
     */
    public function getTipeLabelAttribute()
    {
        return match($this->tipe) {
            // Reward
            'bonus' => 'Bonus',
            'kenaikan_gaji' => 'Kenaikan Gaji',
            'promosi' => 'Promosi Jabatan',
            'piagam' => 'Piagam Penghargaan',
            'pegawai_teladan' => 'Pegawai Teladan',
            'beasiswa' => 'Beasiswa',
            'pensiun_dini' => 'Pensiun Dini',
            // Punishment
            'sp1' => 'Surat Peringatan 1',
            'sp2' => 'Surat Peringatan 2',
            'sp3' => 'Surat Peringatan 3',
            'teguran_lisan' => 'Teguran Lisan',
            'pemotongan_gaji' => 'Pemotongan Gaji',
            'penurunan_jabatan' => 'Penurunan Jabatan',
            'skorsing' => 'Skorsing',
            'pemberhentian' => 'Pemberhentian',
            // Umum
            'lainnya' => 'Lainnya',
            default => '-',
        };
    }

    /**
     * Icon tipe
     */
    public function getTipeIconAttribute()
    {
        return match($this->tipe) {
            'bonus' => 'fa-gift',
            'kenaikan_gaji' => 'fa-arrow-up',
            'promosi' => 'fa-arrow-trend-up',
            'piagam' => 'fa-award',
            'pegawai_teladan' => 'fa-star',
            'beasiswa' => 'fa-graduation-cap',
            'pensiun_dini' => 'fa-umbrella-beach',
            'sp1', 'sp2', 'sp3' => 'fa-exclamation-triangle',
            'teguran_lisan' => 'fa-comment-dots',
            'pemotongan_gaji' => 'fa-money-bill-wave',
            'penurunan_jabatan' => 'fa-arrow-down',
            'skorsing' => 'fa-ban',
            'pemberhentian' => 'fa-times-circle',
            default => 'fa-circle-info',
        };
    }

    /**
     * Warna badge kategori
     */
    public function getKategoriColorAttribute()
    {
        if ($this->kategori === 'reward') {
            return [
                'bg' => '#dcfce7',
                'text' => '#166534',
                'border' => '#22c55e',
                'gradient' => 'linear-gradient(135deg, #22c55e 0%, #15803d 100%)',
                'label' => 'Reward',
            ];
        }

        return [
            'bg' => '#fee2e2',
            'text' => '#991b1b',
            'border' => '#ef4444',
            'gradient' => 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)',
            'label' => 'Punishment',
        ];
    }

    /**
     * Warna badge status
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'aktif' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'Aktif'],
            'selesai' => ['bg' => '#dcfce7', 'text' => '#166534', 'label' => 'Selesai'],
            'dibatalkan' => ['bg' => '#f3f4f6', 'text' => '#6b7280', 'label' => 'Dibatalkan'],
            default => ['bg' => '#f3f4f6', 'text' => '#374151', 'label' => '-'],
        };
    }

    /**
     * Nominal format rupiah
     */
    public function getNominalFormatAttribute()
    {
        if (!$this->nominal) return null;
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Apakah punya file bukti
     */
    public function getHasFileAttribute()
    {
        return !empty($this->file_bukti);
    }

    /**
     * URL file bukti
     */
    public function getFileUrlAttribute()
    {
        if (!$this->file_bukti) return null;
        return asset('storage/' . $this->file_bukti);
    }

    /**
     * Apakah file ini PDF
     */
    public function getFileIsPdfAttribute()
    {
        if (!$this->file_bukti) return false;
        return strtolower(pathinfo($this->file_bukti, PATHINFO_EXTENSION)) === 'pdf';
    }

    /**
     * Apakah file ini gambar
     */
    public function getFileIsImageAttribute()
    {
        if (!$this->file_bukti) return false;
        $ext = strtolower(pathinfo($this->file_bukti, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
    }

    // ==================== SCOPE ====================
    public function scopeReward($query)
    {
        return $query->where('kategori', 'reward');
    }

    public function scopePunishment($query)
    {
        return $query->where('kategori', 'punishment');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'dibatalkan');
    }

    // ==================== STATIC ====================
    public static function getTipeOptions($kategori = null)
    {
        $rewardOptions = [
            'bonus' => 'Bonus',
            'kenaikan_gaji' => 'Kenaikan Gaji',
            'promosi' => 'Promosi Jabatan',
            'piagam' => 'Piagam Penghargaan',
            'pegawai_teladan' => 'Pegawai Teladan',
            'beasiswa' => 'Beasiswa',
            'pensiun_dini' => 'Pensiun Dini',
            'lainnya' => 'Lainnya',
        ];

        $punishmentOptions = [
            'sp1' => 'Surat Peringatan 1 (SP1)',
            'sp2' => 'Surat Peringatan 2 (SP2)',
            'sp3' => 'Surat Peringatan 3 (SP3)',
            'teguran_lisan' => 'Teguran Lisan',
            'pemotongan_gaji' => 'Pemotongan Gaji',
            'penurunan_jabatan' => 'Penurunan Jabatan',
            'skorsing' => 'Skorsing',
            'pemberhentian' => 'Pemberhentian',
            'lainnya' => 'Lainnya',
        ];

        if ($kategori === 'reward') return $rewardOptions;
        if ($kategori === 'punishment') return $punishmentOptions;

        return array_merge($rewardOptions, $punishmentOptions);
    }

    public static function getKategoriOptions()
    {
        return [
            'reward' => 'Reward (Penghargaan)',
            'punishment' => 'Punishment (Sanksi)',
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'aktif' => 'Aktif',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
    }
}