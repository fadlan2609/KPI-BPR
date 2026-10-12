@extends('layouts.admin')

@section('title', 'Detail Reward/Punishment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @php $kc = $item->kategori_color; $sc = $item->status_color; @endphp

    {{-- Header Card --}}
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
                <a href="{{ route('admin.reward-punishment.index') }}"
                   style="padding: 8px 16px; background: rgba(255,255,255,0.25); color: white; font-weight: 700; border-radius: 6px; text-decoration: none;">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user text-blue-600 mr-2"></i> Informasi Pegawai
            </h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2 text-gray-500 w-32">Nama</td><td class="py-2 font-medium">{{ $item->pegawai->nama ?? '-' }}</td></tr>
                <tr class="border-b"><td class="py-2 text-gray-500">NIP</td><td class="py-2">{{ $item->pegawai->nip ?? '-' }}</td></tr>
                <tr class="border-b"><td class="py-2 text-gray-500">Jabatan</td><td class="py-2">{{ $item->pegawai->jabatan->nama ?? '-' }}</td></tr>
                <tr><td class="py-2 text-gray-500">Kantor</td><td class="py-2">{{ $item->pegawai->kantor->nama ?? '-' }}</td></tr>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i> Informasi {{ $kc['label'] }}
            </h3>
            <table class="w-full text-sm">
                <tr class="border-b"><td class="py-2 text-gray-500 w-32">Kategori</td><td class="py-2"><span style="display:inline-block; padding: 3px 10px; background: {{ $kc['bg'] }}; color: {{ $kc['text'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">{{ $kc['label'] }}</span></td></tr>
                <tr class="border-b"><td class="py-2 text-gray-500">Tipe</td><td class="py-2 font-medium">{{ $item->tipe_label }}</td></tr>
                <tr class="border-b"><td class="py-2 text-gray-500">Tanggal</td><td class="py-2">{{ $item->tanggal->translatedFormat('d F Y') }}</td></tr>
                @if($item->nominal_format)
                <tr class="border-b"><td class="py-2 text-gray-500">Nominal</td><td class="py-2 font-bold">{{ $item->nominal_format }}</td></tr>
                @endif
                <tr><td class="py-2 text-gray-500">Status</td><td class="py-2"><span style="display:inline-block; padding: 3px 10px; background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">{{ $sc['label'] }}</span></td></tr>
            </table>
        </div>
    </div>

    {{-- Keterangan --}}
    @if($item->keterangan)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-align-left text-blue-600 mr-2"></i> Keterangan</h3>
        <p class="text-gray-700 leading-relaxed">{{ $item->keterangan }}</p>
    </div>
    @endif

    {{-- File Bukti --}}
    @if($item->has_file)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-file text-blue-600 mr-2"></i> File Bukti</h3>
        @if($item->file_is_image)
            <div class="mb-3">
                <img src="{{ $item->file_url }}" alt="Bukti" class="max-w-full h-auto max-h-96 rounded border border-gray-300 mx-auto block">
            </div>
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

    {{-- Update Status --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-exchange-alt text-blue-600 mr-2"></i> Ubah Status</h3>
        <form action="{{ route('admin.reward-punishment.update-status', $item->id) }}" method="POST" class="flex gap-2 flex-wrap">
            @csrf @method('PATCH')
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="aktif" {{ $item->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="selesai" {{ $item->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan" {{ $item->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" style="padding: 8px 20px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                <i class="fas fa-save mr-1"></i> Simpan Status
            </button>
        </form>
    </div>

    {{-- History --}}
    @if($activities->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-history text-blue-600 mr-2"></i> History Perubahan</h3>
        <div class="space-y-3">
            @foreach($activities as $act)
                <div class="border-l-4 border-blue-400 pl-4 py-2 bg-gray-50 rounded">
                    <p class="text-sm font-medium">{{ $act->description }}</p>
                    <p class="text-xs text-gray-500">
                        Oleh: {{ $act->causer->nama ?? 'System' }} — {{ $act->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if($act->properties->has('old') && $act->properties->has('attributes'))
                        <div class="mt-2 text-xs">
                            @foreach($act->properties['attributes'] as $key => $new)
                                @if(isset($act->properties['old'][$key]) && $act->properties['old'][$key] != $new)
                                    <div class="text-gray-600">
                                        <strong>{{ $key }}:</strong>
                                        <span class="line-through text-red-500">{{ $act->properties['old'][$key] }}</span>
                                        →
                                        <span class="text-green-600">{{ $new }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection