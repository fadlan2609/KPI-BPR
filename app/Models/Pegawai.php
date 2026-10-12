<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pegawai extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'pegawai';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan_id',
        'kantor_id',
        'atasan_langsung_id',
        'status',
        'tanggal_lahir',
        'tanggal_masuk',
        'jenis_kelamin',
        'status_pernikahan',
        'status_karyawan',
        'pendidikan_terakhir',
        'nama_pasangan',
        'jumlah_anak',
        'nik',
        'alamat',
        'no_hp',
        'email',
        'email_notif',
        'ttd_type',
        'ttd_image',
        'foto_profil',
        'foto_crop_x',
        'foto_crop_y',
        'foto_crop_zoom', 
        'foto_crop_rotate',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'jumlah_anak' => 'integer',
        'email_notif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================================
    // RELASI DASAR
    // ============================================================
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    public function atasanLangsung()
    {
        return $this->belongsTo(Pegawai::class, 'atasan_langsung_id');
    }

    public function bawahanLangsung()
    {
        return $this->hasMany(Pegawai::class, 'atasan_langsung_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'pegawai_id');
    }

    public function gajiPokok()
    {
        return $this->hasMany(GajiPokok::class);
    }

    public function gajiPokokAktif()
    {
        return $this->hasOne(GajiPokok::class)->where('is_active', true);
    }

    public function penilaianSebagaiPegawai()
    {
        return $this->hasMany(PenilaianKPI::class, 'pegawai_id');
    }

    public function penilaianSebagaiPenilai()
    {
        return $this->hasMany(PenilaianKPI::class, 'penilai_id');
    }

    public function hasilPenilaian()
    {
        return $this->hasMany(HasilPenilaian::class);
    }

    // ==================== RELASI CUTI ====================
    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'pegawai_id');
    }

    public function saldoCuti()
    {
        return $this->hasMany(SaldoCuti::class, 'pegawai_id');
    }

    public function cutiApprovals()
    {
        return $this->hasMany(CutiApproval::class, 'approver_id');
    }

    public function cutiApprovalsPending()
    {
        return $this->hasMany(CutiApproval::class, 'approver_id')
            ->where('status', 'pending');
    }

    public function cutiRequests()
    {
        return $this->hasMany(CutiApproval::class, 'requested_by');
    }

    public function cutiFinalApproved()
    {
        return $this->hasMany(Cuti::class, 'final_approved_by');
    }

    // ==================== RELASI RIWAYAT JABATAN ====================
    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class)->orderBy('tahun_mulai', 'asc');
    }

    public function permintaanEditRiwayat()
    {
        return $this->hasMany(PermintaanEditRiwayat::class)->orderBy('created_at', 'desc');
    }

    // ==================== RELASI REWARD PUNISHMENT ====================
    public function rewardPunishment()
    {
        return $this->hasMany(RewardPunishment::class)->orderBy('tanggal', 'desc');
    }

    public function rewards()
    {
        return $this->hasMany(RewardPunishment::class)
            ->where('kategori', 'reward')
            ->orderBy('tanggal', 'desc');
    }

    public function punishments()
    {
        return $this->hasMany(RewardPunishment::class)
            ->where('kategori', 'punishment')
            ->orderBy('tanggal', 'desc');
    }

    public function rewardPunishmentCreated()
    {
        return $this->hasMany(RewardPunishment::class, 'created_by');
    }

    // ============================================================
    // NOTIFIKASI EMAIL
    // ============================================================
    public function routeNotificationForMail($notification = null)
    {
        return $this->email;
    }

    // ============================================================
    // ACCESSOR
    // ============================================================
    public function getNamaLengkapAttribute()
    {
        return $this->nama . ($this->nip ? ' (' . $this->nip . ')' : '');
    }

    public function getInisialAttribute()
    {
        $nama = trim($this->nama ?? '');
        if (empty($nama)) return '?';

        $parts = explode(' ', $nama);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
        return strtoupper(substr($nama, 0, 2));
    }

    public function getNamaJabatanAttribute()
    {
        return $this->jabatan->nama ?? '-';
    }

    public function getNamaKantorAttribute()
    {
        return $this->kantor->nama ?? '-';
    }

    public function getTtdImageUrlAttribute()
    {
        if (!$this->ttd_image) return null;
        if (filter_var($this->ttd_image, FILTER_VALIDATE_URL)) return $this->ttd_image;
        return asset('storage/' . $this->ttd_image);
    }

    public function getTtdTypeLabelAttribute()
    {
        return match($this->ttd_type) {
            'gambar' => 'Tanda Tangan',
            'barcode' => 'Barcode',
            default => 'Belum ada',
        };
    }

    // ============================================================
    // SCOPE
    // ============================================================
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeJabatan($query, $namaJabatan)
    {
        return $query->whereHas('jabatan', function ($q) use ($namaJabatan) {
            $q->where('nama', 'like', '%' . $namaJabatan . '%');
        });
    }

    public function scopeCari($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama', 'like', '%' . $keyword . '%')
              ->orWhere('nip', 'like', '%' . $keyword . '%');
        });
    }

    public function scopeExclude($query, $pegawaiId)
    {
        return $query->where('id', '!=', $pegawaiId);
    }

    // ============================================================
    // HELPER
    // ============================================================
    public function isAtasan()
    {
        return $this->bawahanLangsung()->exists();
    }

    public function getBawahan()
    {
        return $this->bawahanLangsung()->aktif()->get();
    }

    public function hasUser()
    {
        return $this->user()->exists();
    }

    public function isAktif()
    {
        return $this->status === 'aktif';
    }

    public function getSaldoCuti($tahun = null)
    {
        $tahun = $tahun ?? date('Y');

        return SaldoCuti::firstOrCreate(
            [
                'pegawai_id' => $this->id,
                'tahun' => $tahun,
            ],
            [
                'total_hari' => 12,
                'digunakan' => 0,
                'sisa_hari' => 12,
                'keterangan' => "Saldo cuti tahun {$tahun}",
            ]
        );
    }

    public function getJumlahCutiPending()
    {
        return $this->cutiApprovalsPending()->count();
    }

    public function keluarga()
    {
        return $this->hasMany(PegawaiKeluarga::class);
    }

    public function anak()
    {
        return $this->hasMany(PegawaiAnak::class)->orderBy('urutan_anak');
    }

    public function pendidikan()
    {
        return $this->hasMany(PegawaiPendidikan::class)->orderBy('tahun_masuk');
    }
}