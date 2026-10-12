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
        'tanggal_masuk_kerja',
        'lama_hari',
        'keterangan',
        'surat_dokter',
        'surat_dokter_status',
        'surat_dokter_uploaded_at',
        'status',
        'current_approval_level',
        'final_approved_by',
        'final_approved_at',
    ];

    protected $casts = [
        'tanggal_mulai'            => 'date',
        'tanggal_selesai'          => 'date',
        'tanggal_masuk_kerja'      => 'date',
        'surat_dokter_uploaded_at' => 'datetime',
        'final_approved_at'        => 'datetime',
    ];

    const STATUS_PENDING    = 'pending';
    const STATUS_PROSES     = 'proses';
    const STATUS_DISETUJUI  = 'disetujui';
    const STATUS_DITOLAK    = 'ditolak';
    const STATUS_DIBATALKAN = 'dibatalkan';

    // ==================== RELASI ====================
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function approvals()
    {
        return $this->hasMany(CutiApproval::class)->orderBy('level');
    }

    public function finalApprovedBy()
    {
        return $this->belongsTo(Pegawai::class, 'final_approved_by');
    }

    // ==================== ACCESSOR ====================
    public function getSuratDokterUrlAttribute()
    {
        if (!$this->surat_dokter) return null;
        return asset('storage/' . $this->surat_dokter);
    }

    public function getSuratDokterIsPdfAttribute()
    {
        if (!$this->surat_dokter) return false;
        return strtolower(pathinfo($this->surat_dokter, PATHINFO_EXTENSION)) === 'pdf';
    }

    public function getSuratDokterIsImageAttribute()
    {
        if (!$this->surat_dokter) return false;
        $ext = strtolower(pathinfo($this->surat_dokter, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
    }

    public function getPerluSuratDokterAttribute()
    {
        return $this->jenis_cuti === 'sakit';
    }

    public function getSudahUploadSuratDokterAttribute()
    {
        return !empty($this->surat_dokter);
    }

    public function getSuratDokterStatusLabelAttribute()
    {
        return match($this->surat_dokter_status) {
            'menunggu'  => 'Menunggu Verifikasi',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => 'Belum Upload',
        };
    }

    public function getSuratDokterStatusColorAttribute()
    {
        return match($this->surat_dokter_status) {
            'menunggu'  => 'yellow',
            'disetujui' => 'green',
            'ditolak'   => 'red',
            default     => 'gray',
        };
    }

    public function getDurasiKalenderAttribute()
    {
        return Carbon::parse($this->tanggal_mulai)
            ->diffInDays(Carbon::parse($this->tanggal_selesai)) + 1;
    }

    public function getHariLiburAttribute()
    {
        $mulai = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);
        $libur = 0;
        $current = $mulai->copy();

        while ($current <= $selesai) {
            if ($current->isWeekend()) $libur++;
            $current->addDay();
        }

        return $libur;
    }

    // ==================== HELPER STATIC ====================
    public static function hitungHariKerja($tanggalMulai, $tanggalSelesai)
    {
        $mulai = Carbon::parse($tanggalMulai);
        $selesai = Carbon::parse($tanggalSelesai);
        $hariKerja = 0;

        while ($mulai <= $selesai) {
            if ($mulai->dayOfWeek >= Carbon::MONDAY && $mulai->dayOfWeek <= Carbon::FRIDAY) {
                $hariKerja++;
            }
            $mulai->addDay();
        }

        return $hariKerja;
    }

    public static function hitungTanggalSelesai($tanggalMulai, $jumlahHariKerja)
    {
        $start = Carbon::parse($tanggalMulai);
        $current = $start->copy();
        $hariKerjaDihitung = 0;

        if ($jumlahHariKerja <= 0) {
            return $start;
        }

        while ($hariKerjaDihitung < $jumlahHariKerja) {
            if ($current->dayOfWeek >= Carbon::MONDAY && $current->dayOfWeek <= Carbon::FRIDAY) {
                $hariKerjaDihitung++;
                if ($hariKerjaDihitung == $jumlahHariKerja) {
                    return $current;
                }
            }
            $current->addDay();
        }

        return $current;
    }

    public static function hitungTanggalMasukKerja($tanggalSelesai)
    {
        $tanggal = Carbon::parse($tanggalSelesai);
        $tanggal->addDay();

        while ($tanggal->dayOfWeek === Carbon::SATURDAY ||
               $tanggal->dayOfWeek === Carbon::SUNDAY) {
            $tanggal->addDay();
        }

        return $tanggal;
    }

    // ==================== AUTO-CALCULATE ====================
    protected static function booted()
    {
        static::creating(function ($cuti) {
            if ($cuti->tanggal_mulai && $cuti->lama_hari) {
                $tanggalSelesai = self::hitungTanggalSelesai(
                    $cuti->tanggal_mulai,
                    $cuti->lama_hari
                );
                $cuti->tanggal_selesai = $tanggalSelesai->format('Y-m-d');
                $cuti->tanggal_masuk_kerja = self::hitungTanggalMasukKerja($tanggalSelesai)->format('Y-m-d');
            }
        });

        static::updating(function ($cuti) {
            if ($cuti->isDirty(['tanggal_mulai', 'lama_hari'])) {
                $tanggalSelesai = self::hitungTanggalSelesai(
                    $cuti->tanggal_mulai,
                    $cuti->lama_hari
                );
                $cuti->tanggal_selesai = $tanggalSelesai->format('Y-m-d');
                $cuti->tanggal_masuk_kerja = self::hitungTanggalMasukKerja($tanggalSelesai)->format('Y-m-d');
            }
        });
    }

    // ==================== SCOPE ====================
    public function scopePendingApproval($query, $pegawaiId)
    {
        return $query->where('status', self::STATUS_PROSES)
            ->whereHas('approvals', function ($q) use ($pegawaiId) {
                $q->where('approver_id', $pegawaiId)->where('status', 'pending');
            });
    }

    public function scopeFinalApproved($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }
}