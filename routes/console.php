<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==================== REMINDER CUTI SAKIT TANPA SURAT ====================
Schedule::call(function () {
    try {
        $cutiSakitTanpaSurat = \App\Models\Cuti::with('pegawai')
            ->where('jenis_cuti', 'sakit')
            ->where(function ($q) {
                $q->whereNull('surat_dokter')->orWhere('surat_dokter', '');
            })
            ->whereIn('status', ['pending', 'proses'])
            ->where('tanggal_mulai', '>=', now()->subDays(7))
            ->get();

        if ($cutiSakitTanpaSurat->count() > 0) {
            $customNotif = new class($cutiSakitTanpaSurat->count()) extends \Illuminate\Notifications\Notification {
                protected $count;

                public function __construct($count)
                {
                    $this->count = $count;
                }

                public function via($n)
                {
                    return ['database'];
                }

                public function toArray($n)
                {
                    return [
                        'type' => 'reminder_surat_dokter',
                        'title' => '⚠️ Reminder: Cuti Sakit Tanpa Surat',
                        'message' => "Ada {$this->count} cuti sakit yang belum upload surat dokter. Perlu tindak lanjut.",
                        'url' => route('admin.cuti.index'),
                        'icon' => 'exclamation-triangle',
                        'color' => 'yellow',
                        'created_at' => now()->toDateTimeString(),
                    ];
                }
            };

            \App\Helpers\NotifHelper::notifyAdmins($customNotif);
        }
    } catch (\Exception $e) {
        \Log::error('Scheduler reminder surat dokter error: ' . $e->getMessage());
    }
})->dailyAt('08:00')->name('reminder-cuti-sakit-tanpa-surat')->withoutOverlapping();