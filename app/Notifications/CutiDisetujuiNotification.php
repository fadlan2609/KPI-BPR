<?php

namespace App\Notifications;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CutiDisetujuiNotification extends Notification
{
    use Queueable;

    protected $cuti;

    public function __construct(Cuti $cuti)
    {
        $this->cuti = $cuti;
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
            'title' => 'Cuti Anda Disetujui',
            'message' => 'Pengajuan ' . ucfirst($this->cuti->jenis_cuti) 
                       . ' Anda (' . $this->cuti->lama_hari . ' hari) telah disetujui final.',
            'url' => route('pegawai.cuti.index'),
            'icon' => 'check-circle',
            'type' => 'cuti_disetujui',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Cuti Anda Telah Disetujui')
            ->greeting('Halo, ' . $notifiable->nama . '!')
            ->line('Selamat! Pengajuan cuti Anda telah **disetujui final**.')
            ->line('**Detail Cuti:**')
            ->line('Jenis: ' . ucfirst($this->cuti->jenis_cuti))
            ->line('Tanggal: ' . $this->cuti->tanggal_mulai->format('d/m/Y') 
                 . ' s/d ' . $this->cuti->tanggal_selesai->format('d/m/Y'))
            ->line('Lama: ' . $this->cuti->lama_hari . ' hari kerja')
            ->line('Masuk Kerja: ' . ($this->cuti->tanggal_masuk_kerja 
                 ? $this->cuti->tanggal_masuk_kerja->format('d/m/Y') : '-'))
            ->action('Lihat Detail', route('pegawai.cuti.index'))
            ->line('Selamat beristirahat!');
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}