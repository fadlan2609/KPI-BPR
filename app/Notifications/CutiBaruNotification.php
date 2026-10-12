<?php

namespace App\Notifications;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CutiBaruNotification extends Notification
{
    use Queueable;

    protected $cuti;
    protected $level;

    public function __construct(Cuti $cuti, $level)
    {
        $this->cuti = $cuti;
        $this->level = $level;
    }

    /**
     * Channel: database + mail
     */
    public function via($notifiable)
    {
        $channels = ['database'];

        // Kirim email hanya kalau pegawai aktifkan email notif
        if ($notifiable->email_notif ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Notifikasi database
     */
    public function toDatabase($notifiable)
    {
        return [
            'cuti_id' => $this->cuti->id,
            'title' => 'Pengajuan Cuti Baru',
            'message' => $this->cuti->pegawai->nama . ' mengajukan ' 
                       . ucfirst($this->cuti->jenis_cuti) 
                       . ' selama ' . $this->cuti->lama_hari . ' hari kerja.',
            'url' => route('pegawai.cuti.approval.show', $this->cuti->id),
            'icon' => 'calendar-plus',
            'type' => 'cuti_baru',
            'level' => $this->level,
        ];
    }

    /**
     * Notifikasi email
     */
    public function toMail($notifiable)
    {
        $url = route('pegawai.cuti.approval.show', $this->cuti->id);

        return (new MailMessage)
            ->subject('Pengajuan Cuti Baru Menunggu Persetujuan Anda')
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line('Ada pengajuan cuti baru yang membutuhkan persetujuan Anda.')
            ->line('**Detail Pengajuan:**')
            ->line('Nama: ' . $this->cuti->pegawai->nama)
            ->line('NIP: ' . $this->cuti->pegawai->nip)
            ->line('Jenis Cuti: ' . ucfirst($this->cuti->jenis_cuti))
            ->line('Tanggal: ' . $this->cuti->tanggal_mulai->format('d/m/Y') 
                 . ' s/d ' . $this->cuti->tanggal_selesai->format('d/m/Y'))
            ->line('Lama: ' . $this->cuti->lama_hari . ' hari kerja')
            ->line('Level Persetujuan: ' . $this->level)
            ->action('Lihat & Setujui', $url)
            ->line('Terima kasih.');
    }

    /**
     * Data untuk array (opsional)
     */
    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}