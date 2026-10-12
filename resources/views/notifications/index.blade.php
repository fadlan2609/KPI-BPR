@extends('layouts.pegawai')

@section('title', 'Notifikasi')

@section('content')

{{-- INLINE STYLE --}}
<style>
    .notif-header { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px; }
    .notif-header-left { display: flex; align-items: center; gap: 14px; }
    .notif-icon-wrap { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5); background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); }
    .notif-icon-wrap i { color: white; font-size: 22px; }
    .notif-title { font-size: 24px; font-weight: 800; color: #111827; margin: 0; line-height: 1.2; }
    .notif-subtitle { font-size: 13px; color: #6b7280; margin-top: 2px; }

    .notif-alert { position: relative; overflow: hidden; border-radius: 16px; border-left: 5px solid #ef4444; background: linear-gradient(135deg, #fef2f2 0%, #fed7aa 100%); padding: 20px; margin-bottom: 20px; box-shadow: 0 10px 30px -10px rgba(239, 68, 68, 0.3); }
    .notif-alert-inner { display: flex; align-items: center; gap: 16px; position: relative; z-index: 2; }
    .notif-alert-icon { width: 56px; height: 56px; border-radius: 50%; background: #ef4444; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px -4px rgba(239, 68, 68, 0.6); flex-shrink: 0; animation: notifBounce 1.5s ease-in-out infinite; }
    .notif-alert-icon i { color: white; font-size: 24px; }
    @keyframes notifBounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    .notif-alert-content { flex: 1; min-width: 0; }
    .notif-alert-title { font-size: 16px; font-weight: 800; color: #991b1b; margin: 0 0 4px 0; }
    .notif-alert-desc { font-size: 13px; color: #b91c1c; line-height: 1.5; margin: 0; }
    .notif-alert-btn { background: #dc2626; color: white; padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; box-shadow: 0 4px 12px -2px rgba(220, 38, 38, 0.5); transition: all 0.2s; white-space: nowrap; }
    .notif-alert-btn:hover { background: #b91c1c; transform: translateY(-1px); }

    .notif-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.08); overflow: hidden; border: 1px solid #f3f4f6; }
    .notif-card-header { padding: 18px 24px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(90deg, #f9fafb 0%, #ffffff 100%); }
    .notif-card-title { font-size: 15px; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 8px; margin: 0; }
    .notif-card-title i { color: #3b82f6; }
    .notif-card-count { font-size: 12px; color: #6b7280; background: #f3f4f6; padding: 4px 12px; border-radius: 9999px; font-weight: 600; }

    .notif-item { position: relative; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.2s; display: flex; gap: 16px; align-items: flex-start; }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: #f9fafb; }
    .notif-item.unread { background: #eff6ff; }
    .notif-item.unread:hover { background: #dbeafe; }
    .notif-item.unread::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #3b82f6; }

    .notif-item-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.15); position: relative; background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); }
    .notif-item-icon i { color: white; font-size: 18px; }
    .notif-item-icon.green { background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); }
    .notif-item-icon.red { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }
    .notif-item-icon.gray { background: linear-gradient(135deg, #6b7280 0%, #374151 100%); }
    .notif-item-dot { position: absolute; top: -3px; right: -3px; width: 14px; height: 14px; background: #ef4444; border-radius: 50%; border: 2px solid white; animation: notifPing 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; }
    @keyframes notifPing { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }

    .notif-item-content { flex: 1; min-width: 0; }
    .notif-item-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 6px; }
    .notif-item-title { font-size: 15px; font-weight: 700; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .notif-badge-new { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; background: #dbeafe; color: #1e40af; border-radius: 9999px; font-size: 11px; font-weight: 700; }
    .notif-badge-read { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; background: #f3f4f6; color: #6b7280; border-radius: 9999px; font-size: 11px; font-weight: 600; }
    .notif-time { font-size: 12px; color: #9ca3af; white-space: nowrap; flex-shrink: 0; }
    .notif-message { font-size: 14px; color: #4b5563; line-height: 1.55; margin: 0 0 12px 0; }
    .notif-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding-top: 12px; border-top: 1px solid #f3f4f6; }

    .notif-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 10px; font-size: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
    .notif-btn-primary { background: #2563eb; color: white; box-shadow: 0 4px 10px -2px rgba(37, 99, 235, 0.4); }
    .notif-btn-primary:hover { background: #1d4ed8; }
    .notif-btn-gray { background: #f3f4f6; color: #374151; }
    .notif-btn-gray:hover { background: #e5e7eb; }
    .notif-btn-red { background: #fef2f2; color: #dc2626; margin-left: auto; }
    .notif-btn-red:hover { background: #fee2e2; }

    .notif-empty { padding: 64px 20px; text-align: center; }
    .notif-empty-icon { width: 112px; height: 112px; border-radius: 50%; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .notif-empty-icon i { font-size: 44px; color: #d1d5db; }
    .notif-empty-title { font-size: 18px; font-weight: 700; color: #374151; margin: 0 0 8px 0; }
    .notif-empty-desc { font-size: 14px; color: #6b7280; max-width: 400px; margin: 0 auto; line-height: 1.6; }
</style>

<div style="max-width: 900px; margin: 0 auto;">

    {{-- HEADER --}}
    <div class="notif-header">
        <div class="notif-header-left">
            <div class="notif-icon-wrap">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h1 class="notif-title">Notifikasi</h1>
                <p class="notif-subtitle">Semua pemberitahuan untuk Anda</p>
            </div>
        </div>

        @php
            $userNotif = Auth::user();
            $notifiable = $userNotif->pegawai ?? $userNotif;
            $unreadCount = $notifiable->unreadNotifications()->count();
        @endphp

        @if($unreadCount > 0)
            <form action="{{ route('pegawai.notifications.read-all') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="notif-alert-btn" style="background: #2563eb;">
                    <i class="fas fa-check-double"></i>
                    Tandai Semua Dibaca
                    <span style="background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 9999px; font-size: 12px; margin-left: 6px;">
                        {{ $unreadCount }}
                    </span>
                </button>
            </form>
        @endif
    </div>

    {{-- ALERT MENONJOL --}}
    @if($unreadCount > 0)
        <div class="notif-alert">
            <div class="notif-alert-inner">
                <div class="notif-alert-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="notif-alert-content">
                    <h3 class="notif-alert-title">🔔 Anda punya {{ $unreadCount }} notifikasi belum dibaca!</h3>
                    <p class="notif-alert-desc">Segera periksa notifikasi di bawah ini agar tidak ada informasi penting yang terlewat.</p>
                </div>
                <form action="{{ route('pegawai.notifications.read-all') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="notif-alert-btn">
                        <i class="fas fa-check"></i> Tandai
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- CARD --}}
    <div class="notif-card">
        <div class="notif-card-header">
            <h3 class="notif-card-title">
                <i class="fas fa-list-ul"></i>
                Daftar Notifikasi
            </h3>
            <span class="notif-card-count">Total: {{ $notifications->total() }}</span>
        </div>

        @forelse($notifications as $notif)
            @php
                $data = $notif->data;
                $isRead = !is_null($notif->read_at);
                $type = $data['type'] ?? 'info';
                $iconClass = match($type) {
                    'cuti_disetujui' => 'green',
                    'cuti_ditolak' => 'red',
                    'cuti_baru' => '',
                    default => 'gray',
                };
            @endphp

            <div class="notif-item {{ $isRead ? '' : 'unread' }}"
                 onclick="openNotif('{{ $notif->id }}', '{{ $data['url'] ?? '' }}')">

                <div class="notif-item-icon {{ $iconClass }}">
                    <i class="fas fa-{{ $data['icon'] ?? 'bell' }}"></i>
                    @if(!$isRead)
                        <span class="notif-item-dot"></span>
                    @endif
                </div>

                <div class="notif-item-content">
                    <div class="notif-item-top">
                        <h4 class="notif-item-title">
                            {{ $data['title'] ?? 'Notifikasi' }}
                            @if(!$isRead)
                                <span class="notif-badge-new">
                                    <i class="fas fa-circle" style="font-size: 6px;"></i>
                                    Baru
                                </span>
                            @else
                                <span class="notif-badge-read">
                                    <i class="fas fa-check" style="font-size: 9px;"></i>
                                    Dibaca
                                </span>
                            @endif
                        </h4>
                        <span class="notif-time">
                            <i class="far fa-clock"></i>
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="notif-message">{{ $data['message'] ?? '' }}</p>

                    <div class="notif-actions">
                        @if(!empty($data['url']))
                            <a href="{{ $data['url'] }}"
                               onclick="event.stopPropagation()"
                               class="notif-btn notif-btn-primary">
                                <i class="fas fa-arrow-right"></i>
                                Lihat Detail
                            </a>
                        @endif

                        @if(!$isRead)
                            <button onclick="event.stopPropagation(); markRead('{{ $notif->id }}')"
                                    class="notif-btn notif-btn-gray">
                                <i class="fas fa-check"></i>
                                Tandai Dibaca
                            </button>
                        @endif

                        <button onclick="event.stopPropagation(); deleteNotif('{{ $notif->id }}')"
                                class="notif-btn notif-btn-red">
                            <i class="fas fa-trash"></i>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="notif-empty">
                <div class="notif-empty-icon">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3 class="notif-empty-title">Belum Ada Notifikasi</h3>
                <p class="notif-empty-desc">
                    Semua pemberitahuan terkait cuti, penilaian, dan aktivitas Anda akan muncul di sini.
                </p>
            </div>
        @endforelse

        @if($notifications->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #f3f4f6; background: #f9fafb;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function openNotif(id, url) {
        fetch(`{{ url('pegawai/notifications') }}/${id}/read`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.url) window.location.href = data.url;
            else if (url && url !== 'null' && url !== '#') window.location.href = url;
            else location.reload();
        })
        .catch(() => {
            if (url && url !== 'null' && url !== '#') window.location.href = url;
        });
    }
    function markRead(id) {
        fetch(`{{ url('pegawai/notifications') }}/${id}/read`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => location.reload()).catch(() => location.reload());
    }
    function deleteNotif(id) {
        if (!confirm('Hapus notifikasi ini?')) return;
        fetch(`{{ url('pegawai/notifications') }}/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => location.reload()).catch(() => location.reload());
    }
</script>

@endsection