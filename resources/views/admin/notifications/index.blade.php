@extends('layouts.admin')

@section('title', 'Notifikasi')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 24px 16px;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h1 style="font-size:20px; font-weight:700; color:#1e293b; margin:0 0 4px 0;">🔔 Notifikasi</h1>
            <p style="font-size:13px; color:#64748b; margin:0;">Semua notifikasi sistem</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-emerald-700 text-white text-xs font-bold rounded-lg">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
        @forelse($notifications as $notif)
            @php $data = $notif->data; @endphp
            <a href="{{ $data['url'] ?? '#' }}" 
               style="display:block; padding:16px 20px; border-bottom:1px solid #f1f5f9; text-decoration:none; {{ $notif->read_at ? '' : 'background:#eff6ff;' }}">
                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="font-size:24px;">
                        @if(($data['type'] ?? '') == 'cuti_baru') 📋
                        @elseif(($data['type'] ?? '') == 'surat_dokter_uploaded') 📎
                        @elseif(($data['type'] ?? '') == 'surat_dokter_rejected') ❌
                        @else 🔔
                        @endif
                    </div>
                    <div style="flex:1;">
                        <p style="font-size:14px; font-weight:700; color:#1e293b; margin:0 0 4px 0;">
                            {{ $data['title'] ?? 'Notifikasi' }}
                        </p>
                        <p style="font-size:13px; color:#64748b; margin:0 0 4px 0;">
                            {{ $data['message'] ?? '' }}
                        </p>
                        <p style="font-size:11px; color:#94a3b8; margin:0;">
                            {{ $notif->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @if(!$notif->read_at)
                        <div style="width:8px; height:8px; background:#2563eb; border-radius:50%;"></div>
                    @endif
                </div>
            </a>
        @empty
            <div style="padding:60px 20px; text-align:center;">
                <p style="font-size:48px; margin-bottom:12px;">🔕</p>
                <p style="font-size:14px; color:#64748b;">Belum ada notifikasi</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top:16px;">
        {{ $notifications->links() }}
    </div>

</div>
@endsection