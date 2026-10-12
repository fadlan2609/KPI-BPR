<?php

namespace App\Notifications;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SuratDokterUploadedNotification extends Notification
{
    use Queueable;

    protected $cuti;

    /**
     * Create a new notification instance.
     */
    public function __construct(Cuti $cuti)
    {
        $this->cuti = $cuti;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        $pegawai = $this->cuti->pegawai;
        $namaPegawai = $pegawai->nama ?? 'Pegawai';
        $tanggalMulai = $this->cuti->tanggal_mulai
            ? \Carbon\Carbon::parse($this->cuti->tanggal_mulai)->format('d M Y')
            : '-';

        return [
            'type' => 'surat_dokter_uploaded',
            'title' => '📎 Surat Dokter Baru Diupload',
            'message' => "{$namaPegawai} mengupload surat dokter untuk cuti sakit tanggal {$tanggalMulai}. Perlu verifikasi.",
            'url' => route('admin.cuti.show', $this->cuti->id),
            'cuti_id' => $this->cuti->id,
            'pegawai_id' => $pegawai->id ?? null,
            'pegawai_nama' => $namaPegawai,
            'icon' => 'file-medical',
            'color' => 'blue',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}