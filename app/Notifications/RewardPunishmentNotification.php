<?php

namespace App\Notifications;

use App\Models\RewardPunishment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RewardPunishmentNotification extends Notification
{
    use Queueable;

    protected $item;
    protected $action; // 'created', 'updated', 'status_changed'

    public function __construct(RewardPunishment $item, $action = 'created')
    {
        $this->item = $item;
        $this->action = $action;
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
        $kategoriLabel = $this->item->kategori === 'reward' ? 'Reward' : 'Punishment';
        $icon = $this->item->kategori === 'reward' ? 'gift' : 'exclamation-triangle';
        $type = $this->item->kategori === 'reward' ? 'reward' : 'punishment';

        if ($this->action === 'status_changed') {
            $title = "Status {$kategoriLabel} Diubah";
            $message = "Status \"{$this->item->judul}\" telah diubah menjadi: " . $this->item->status_color['label'];
        } else {
            $title = "Anda Menerima {$kategoriLabel}";
            $message = "\"{$this->item->judul}\" - " . ($this->item->keterangan ?? '-');
        }

        return [
            'reward_punishment_id' => $this->item->id,
            'title' => $title,
            'message' => $message,
            'url' => route('pegawai.reward-punishment.show', $this->item->id),
            'icon' => $icon,
            'type' => $type,
            'action' => $this->action,
        ];
    }

    public function toMail($notifiable)
    {
        $kategoriLabel = $this->item->kategori === 'reward' ? 'Reward' : 'Punishment';

        $mail = (new MailMessage)
            ->subject("{$kategoriLabel} Baru - {$this->item->judul}")
            ->greeting('Halo, ' . $notifiable->nama . '!');

        if ($this->action === 'status_changed') {
            $mail->line("Status {$kategoriLabel} Anda telah diubah.")
                 ->line('**Judul:** ' . $this->item->judul)
                 ->line('**Status Baru:** ' . $this->item->status_color['label']);
        } else {
            $mail->line("Anda menerima **{$kategoriLabel}** baru.")
                 ->line('**Judul:** ' . $this->item->judul)
                 ->line('**Tipe:** ' . $this->item->tipe_label)
                 ->line('**Tanggal:** ' . $this->item->tanggal->translatedFormat('d F Y'))
                 ->line('**Keterangan:** ' . ($this->item->keterangan ?? '-'));

            if ($this->item->nominal) {
                $mail->line('**Nominal:** ' . $this->item->nominal_format);
            }
        }

        return $mail
            ->action('Lihat Detail', route('pegawai.reward-punishment.show', $this->item->id))
            ->line('Terima kasih.');
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}