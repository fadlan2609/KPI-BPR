@extends('layouts.pegawai')

@section('title', 'Detail Reward/Punishment')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @php $kc = $item->kategori_color; $sc = $item->status_color; @endphp

    <div class="rounded-xl shadow-lg overflow-hidden" style="background: {{ $kc['gradient'] }};">
        <div class="p-6 text-white">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div class="flex items-center gap-4">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 28px;">
                        <i class="fas {{ $item->tipe_icon }}"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $item->judul }}</h1>
                        <p class="text-sm opacity-90">{{ $kc['label'] }} — {{ $item->tipe_label }}</p>
                    </div>
                </div>
                <a href="{{ route('pegawai.reward-punishment.index') }}"
                   style="padding: 8px 16px; background: rgba(255,255,255,0.25); color: white; font-weight: 700; border-radius: 6px; text-decoration: none;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-info-circle text-blue-600 mr-2"></i> Detail</h3>
        <table class="w-full text-sm">
            <tr class="border-b"><td class="py-2 text-gray-500 w-40">Kategori</td><td class="py-2"><span style="display:inline-block; padding: 3px 10px; background: {{ $kc['bg'] }}; color: {{ $kc['text'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">{{ $kc['label'] }}</span></td></tr>
            <tr class="border-b"><td class="py-2 text-gray-500">Tipe</td><td class="py-2 font-medium">{{ $item->tipe_label }}</td></tr>
            <tr class="border-b"><td class="py-2 text-gray-500">Tanggal</td><td class="py-2">{{ $item->tanggal->translatedFormat('d F Y') }}</td></tr>
            @if($item->nominal_format)
                <tr class="border-b"><td class="py-2 text-gray-500">Nominal</td><td class="py-2 font-bold">{{ $item->nominal_format }}</td></tr>
            @endif
            <tr class="border-b"><td class="py-2 text-gray-500">Status</td><td class="py-2"><span style="display:inline-block; padding: 3px 10px; background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">{{ $sc['label'] }}</span></td></tr>
            @if($item->createdBy)
                <tr class="border-b"><td class="py-2 text-gray-500">Diinput oleh</td><td class="py-2">{{ $item->createdBy->nama }}</td></tr>
            @endif
            @if($item->updatedBy)
                <tr><td class="py-2 text-gray-500">Diupdate oleh</td><td class="py-2">{{ $item->updatedBy->nama }}</td></tr>
            @endif
        </table>
    </div>

    @if($item->keterangan)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-align-left text-blue-600 mr-2"></i> Keterangan</h3>
            <p class="text-gray-700 leading-relaxed">{{ $item->keterangan }}</p>
        </div>
    @endif

    @if($item->has_file)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-file text-blue-600 mr-2"></i> File Bukti</h3>
            @if($item->file_is_image)
                <img src="{{ $item->file_url }}" alt="Bukti" class="max-w-full h-auto max-h-96 rounded border border-gray-300 mx-auto block">
            @elseif($item->file_is_pdf)
                <iframe src="{{ $item->file_url }}" class="w-full h-96 border border-gray-300 rounded"></iframe>
            @endif
            <div class="text-center mt-3">
                <a href="{{ $item->file_url }}" target="_blank" style="display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-weight: 700; border-radius: 6px; text-decoration: none;">
                    <i class="fas fa-external-link-alt mr-1"></i> Buka File
                </a>
            </div>
        </div>
    @endif
</div>
@endsection