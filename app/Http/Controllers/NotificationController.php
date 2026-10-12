<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Daftar semua notifikasi
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $pegawai = $user->pegawai;

        // Cek apakah user punya relasi pegawai
        if (!$pegawai) {
            // Kalau admin tidak punya pegawai, ambil dari notifiable user
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $notifications = $pegawai->notifications()
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('notifications.index', compact('notifications', 'pegawai', 'user'));
    }

    /**
     * Ambil notifikasi terbaru (AJAX — untuk dropdown)
     */
    public function latest()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $pegawai = $user->pegawai;
        $notifiable = $pegawai ?? $user;

        $notifications = $notifiable->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? 'Notifikasi',
                    'message' => $n->data['message'] ?? '',
                    'url' => $n->data['url'] ?? '#',
                    'icon' => $n->data['icon'] ?? 'bell',
                    'type' => $n->data['type'] ?? 'info',
                    'is_read' => !is_null($n->read_at),
                    'time_ago' => $n->created_at->diffForHumans(),
                ];
            });

        $unreadCount = $notifiable->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false]);

        $pegawai = $user->pegawai;
        $notifiable = $pegawai ?? $user;

        $notification = $notifiable->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'success' => true,
                'url' => $notification->data['url'] ?? null,
            ]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Tandai semua sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false]);

        $pegawai = $user->pegawai;
        $notifiable = $pegawai ?? $user;

        $notifiable->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false]);

        $pegawai = $user->pegawai;
        $notifiable = $pegawai ?? $user;

        $notification = $notifiable->notifications()->find($id);
        if ($notification) {
            $notification->delete();
        }

        return response()->json(['success' => true]);
    }
}