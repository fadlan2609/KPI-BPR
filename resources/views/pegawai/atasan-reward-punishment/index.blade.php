@extends('layouts.pegawai')

@section('title', 'Input R/P Bawahan')

@php
    // Tentukan prefix route berdasarkan kategori dari controller
    $kategori = $kategori ?? 'reward';
    $routePrefix = 'pegawai.atasan-' . $kategori; // pegawai.atasan-reward atau pegawai.atasan-punishment
@endphp

@section('content')
<div class="space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg"
                 style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                <i class="fas fa-user-shield text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Input Reward/Punishment Bawahan</h1>
                <p class="text-sm text-gray-500">Anda dapat menginput untuk bawahan langsung Anda</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.create') }}"
           style="display: inline-flex; align-items: center; padding: 10px 20px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 8px; text-decoration: none;">
            <i class="fas fa-plus mr-2"></i> Tambah
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <p class="text-sm text-green-700 font-medium">Total Reward Bawahan</p>
            <p class="text-2xl font-bold text-green-700">{{ $stats['total_reward'] }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <p class="text-sm text-red-700 font-medium">Total Punishment Bawahan</p>
            <p class="text-2xl font-bold text-red-700">{{ $stats['total_punishment'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-list mr-2 text-indigo-600"></i> Daftar</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bawahan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $item)
                        @php $kc = $item->kategori_color; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $item->pegawai->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span style="display:inline-block; padding: 3px 10px; background: {{ $kc['bg'] }}; color: {{ $kc['text'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">{{ $kc['label'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm"><i class="fas {{ $item->tipe_icon }} mr-1 text-gray-500"></i>{{ $item->tipe_label }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->judul }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($item->created_by == Auth::user()->pegawai->id)
                                    <form action="{{ route($routePrefix . '.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="padding: 5px 10px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; font-size: 11px; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $data->links() }}</div>
    </div>
</div>
@endsection