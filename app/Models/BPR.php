<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BPR extends Model
{
    use HasFactory;

    protected $table = 'bpr';

    protected $fillable = [
        'sandi_bpr',
        'nama_bpr',
        'jenis_bpr',
        'jenis_lembaga',
        'kategori',
        'no_telp',
        'alamat',
        'email',
        'website',
        'logo'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Accessor: URL logo lengkap
     * Pemakaian: $bpr->logo_url
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }

        // Jika logo sudah berupa URL lengkap
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        // Jika logo disimpan di storage
        return asset('storage/' . $this->logo);
    }

    /**
     * Accessor: Nama BPR dengan fallback
     * Pemakaian: $bpr->nama_lengkap
     */
    public function getNamaLengkapAttribute()
    {
        return $this->nama_bpr ?? 'BPRS Amanah Bangsa';
    }

    /**
     * Helper static: Ambil data BPR pertama (untuk digunakan di layout)
     * Pemakaian: BPR::getFirst()
     */
    public static function getFirst()
    {
        return self::first() ?? new self([
            'nama_bpr' => 'BPRS Amanah Bangsa',
        ]);
    }

    /**
     * Helper static: Ambil nama BPR
     */
    public static function getNamaBpr()
    {
        return self::first()->nama_bpr ?? 'BPRS Amanah Bangsa';
    }

    /**
     * Helper static: Ambil URL logo
     */
    public static function getLogoUrl()
    {
        $bpr = self::first();
        if (!$bpr || !$bpr->logo) {
            return null;
        }
        return asset('storage/' . $bpr->logo);
    }
}