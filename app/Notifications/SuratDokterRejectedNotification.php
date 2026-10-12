<?php

namespace App\Notifications;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SuratDokterRejectedNotification extends Notification
{
    use Queueable;

    protected $cuti;
    protected $catatan;

    public function __construct(Cuti $cuti, $catatan = null)
    {
        $this->cuti = $cuti;
        $this->catatan = $catatan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $tanggalMulai = $this->cuti->tanggal_mulai
            ? \Carbon\Carbon::parse($this->cuti->tanggal_mulai)->format('d M Y')
            : '-';

        return [
            'type' => 'surat_dokter_rejected',
            'title' => '❌ Surat Dokter Ditolak',
            'message' => "Surat dokter untuk cuti sakit tanggal {$tanggalMulai} ditolak. Alasan: " . ($this->catatan ?? '-'),
            'url' => route('pegawai.cuti.upload-surat.form', $this->cuti->id),
            'cuti_id' => $this->cuti->id,
            'catatan' => $this->catatan,
            'icon' => 'times-circle',
            'color' => 'red',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}