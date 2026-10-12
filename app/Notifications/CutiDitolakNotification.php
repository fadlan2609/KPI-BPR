<?php

namespace App\Notifications;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CutiDitolakNotification extends Notification
{
    use Queueable;

    protected $cuti;
    protected $alasan;

    public function __construct(Cuti $cuti, $alasan = null)
    {
        $this->cuti = $cuti;
        $this->alasan = $alasan;
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        if ($notifiable->email_notif ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase($notifiable)
    {
        return [
            'cuti_id' => $this->cuti->id,
            'title' => 'Cuti Anda Ditolak',
            'message' => 'Pengajuan ' . ucfirst($this->cuti->jenis_cuti) 
                       . ' Anda ditolak. ' . ($this->alasan ? 'Alasan: ' . $this->alasan : ''),
            'url' => route('pegawai.cuti.index'),
            'icon' => 'x-circle',
            'type' => 'cuti_ditolak',
        ];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Cuti Anda Ditolak')
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line('Mohon maaf, pengajuan cuti Anda **ditolak**.')
            ->line('**Detail Cuti:**')
            ->line('Jenis: ' . ucfirst($this->cuti->jenis_cuti))
            ->line('Tanggal: ' . $this->cuti->tanggal_mulai->format('d/m/Y') 
                 . ' s/d ' . $this->cuti->tanggal_selesai->format('d/m/Y'));

        if ($this->alasan) {
            $mail->line('**Alasan Penolakan:** ' . $this->alasan);
        }

        return $mail
            ->action('Lihat Detail', route('pegawai.cuti.index'))
            ->line('Silakan hubungi atasan Anda untuk info lebih lanjut.');
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}