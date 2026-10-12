<?php

namespace App\Notifications;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class CutiBaruAdminNotification extends Notification
{
    use Queueable;

    protected $cuti;

    public function __construct(Cuti $cuti)
    {
        $this->cuti = $cuti;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $pegawai = $this->cuti->pegawai;
        $namaPegawai = $pegawai->nama ?? 'Pegawai';
        $jenisCuti = ucfirst($this->cuti->jenis_cuti ?? '-');
        $tanggalMulai = $this->cuti->tanggal_mulai
            ? \Carbon\Carbon::parse($this->cuti->tanggal_mulai)->format('d M Y')
            : '-';
        $lamaHari = $this->cuti->lama_hari ?? 0;

        // Icon & warna berdasarkan jenis cuti
        $icon = 'calendar-plus';
        $color = 'green';
        if ($this->cuti->jenis_cuti === 'sakit') {
            $icon = 'file-medical';
            $color = 'red';
        } elseif ($this->cuti->jenis_cuti === 'melahirkan') {
            $icon = 'baby';
            $color = 'pink';
        } elseif ($this->cuti->jenis_cuti === 'khusus') {
            $icon = 'star';
            $color = 'purple';
        }

        return [
            'type' => 'cuti_baru',
            'title' => "📋 Pengajuan {$jenisCuti} Baru",
            'message' => "{$namaPegawai} mengajukan {$jenisCuti} selama {$lamaHari} hari mulai {$tanggalMulai}.",
            'url' => route('admin.cuti.show', $this->cuti->id),
            'cuti_id' => $this->cuti->id,
            'pegawai_id' => $pegawai->id ?? null,
            'pegawai_nama' => $namaPegawai,
            'jenis_cuti' => $this->cuti->jenis_cuti,
            'lama_hari' => $lamaHari,
            'icon' => $icon,
            'color' => $color,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}