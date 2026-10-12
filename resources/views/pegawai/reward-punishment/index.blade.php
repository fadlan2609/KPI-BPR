@extends('layouts.pegawai')

@section('title', $kategori === 'punishment' ? 'Punishment Saya' : 'Reward Saya')

@section('content')
<div class="space-y-6">

    @php
        $isReward = $kategori === 'reward';
        $theme = $isReward
            ? [
                'title' => 'Reward Saya',
                'subtitle' => 'Riwayat penghargaan Anda',
                'icon' => 'fa-gift',
                'gradient' => 'linear-gradient(135deg, #22c55e 0%, #15803d 100%)',
                'shadow' => 'rgba(34, 197, 94, 0.4)',
                'items' => $rewards,
                'total' => $stats['total_reward'],
                'nominal' => $stats['total_nominal_reward'],
              ]
            : [
                'title' => 'Punishment Saya',
                'subtitle' => 'Riwayat sanksi Anda',
                'icon' => 'fa-exclamation-triangle',
                'gradient' => 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)',
                'shadow' => 'rgba(239, 68, 68, 0.4)',
                'items' => $punishments,
                'total' => $stats['total_punishment'],
                'nominal' => $stats['total_nominal_punishment'],
              ];
    @endphp

    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg"
                 style="background: {{ $theme['gradient'] }}; box-shadow: 0 10px 25px -5px {{ $theme['shadow'] }};">
                <i class="fas {{ $theme['icon'] }} text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $theme['title'] }}</h1>
                <p class="text-sm text-gray-500">{{ $theme['subtitle'] }}</p>
            </div>
        </div>
        <a href="{{ $isReward ? route('pegawai.reward.cetak-laporan') : route('pegawai.punishment.cetak-laporan') }}"
           target="_blank"
           style="display: inline-flex; align-items: center; padding: 10px 20px; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 8px; text-decoration: none;">
            <i class="fas fa-file-pdf mr-2"></i> Cetak Laporan
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-lg p-4 text-center border"
             style="background: {{ $isReward ? '#f0fdf4' : '#fef2f2' }}; border-color: {{ $isReward ? '#bbf7d0' : '#fecaca' }};">
            <p class="text-sm font-medium" style="color: {{ $isReward ? '#166534' : '#991b1b' }};">Total {{ $isReward ? 'Reward' : 'Punishment' }}</p>
            <p class="text-2xl font-bold" style="color: {{ $isReward ? '#166534' : '#991b1b' }};">{{ $theme['total'] }}</p>
        </div>
        <div class="rounded-lg p-4 text-center border border-blue-200 bg-blue-50">
            <p class="text-sm text-blue-700 font-medium">Total Nominal</p>
            <p class="text-lg font-bold text-blue-700">Rp {{ number_format($theme['nominal'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-bold text-gray-800">
                <i class="fas fa-list mr-2" style="color: {{ $isReward ? '#22c55e' : '#ef4444' }};"></i>
                Daftar {{ $isReward ? 'Reward' : 'Punishment' }}
                <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-700 rounded-full text-xs font-bold">{{ $theme['items']->count() }}</span>
            </h3>
        </div>

        <div class="p-6 space-y-3">
            @forelse($theme['items'] as $item)
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex items-start gap-3 flex-1">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: {{ $theme['gradient'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px -2px {{ $theme['shadow'] }};">
                                <i class="fas {{ $item->tipe_icon }} text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900">{{ $item->judul }}</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-tag mr-1"></i>{{ $item->tipe_label }}
                                    @if($item->nominal_format) — <strong>{{ $item->nominal_format }}</strong>@endif
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="far fa-calendar mr-1"></i>{{ $item->tanggal->translatedFormat('d F Y') }}
                                </p>
                                @if($item->keterangan)
                                    <p class="text-sm text-gray-600 mt-2 italic">{{ $item->keterangan }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            @php $sc = $item->status_color; @endphp
                            <span style="display:inline-block; padding: 3px 10px; background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">{{ $sc['label'] }}</span>
                            <div class="mt-2">
                                <a href="{{ $isReward ? route('pegawai.reward.show', $item->id) : route('pegawai.punishment.show', $item->id) }}"
                                   class="text-blue-600 hover:underline text-xs">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas {{ $theme['icon'] }} text-5xl mb-3 block text-gray-300"></i>
                    <p class="font-semibold">Belum ada {{ $isReward ? 'reward' : 'punishment' }}</p>
                    <p class="text-sm">Data akan muncul di sini ketika ada</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection