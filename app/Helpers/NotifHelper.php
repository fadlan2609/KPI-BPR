<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Pegawai;

class NotifHelper
{
    public static function notifyAdmins($notification)
    {
        $admins = User::where('role', 'admin')
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify($notification);
            } catch (\Exception $e) {
                \Log::error('Gagal kirim notif ke admin ' . $admin->id . ': ' . $e->getMessage());
            }
        }

        return $admins->count();
    }

    public static function notifyPegawai($pegawaiId, $notification)
    {
        $pegawai = Pegawai::find($pegawaiId);
        if (!$pegawai) return false;

        $user = User::where('pegawai_id', $pegawaiId)->first();
        if (!$user) return false;

        try {
            $user->notify($notification);
            return true;
        } catch (\Exception $e) {
            \Log::error('Gagal kirim notif ke pegawai ' . $pegawaiId . ': ' . $e->getMessage());
            return false;
        }
    }
}