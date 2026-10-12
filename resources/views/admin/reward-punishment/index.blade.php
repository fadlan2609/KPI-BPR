@extends('layouts.admin')

@section('title', 'Reward & Punishment')

@php
    // Tentukan prefix route berdasarkan kategori dari controller
    // Controller mengirim $kategori via ->defaults('kategori', 'reward'|'punishment')
    $kategori = $kategori ?? 'reward';
    $routePrefix = 'admin.' . $kategori; // hasil: admin.reward atau admin.punishment
    $judul = ucfirst($kategori);
@endphp

@section('content')
<div class="bg-white rounded-lg shadow p-6">

    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reward & Punishment</h1>
            <p class="text-sm text-gray-500">Kelola penghargaan & sanksi pegawai</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route($routePrefix . '.laporan.cetak', request()->query()) }}" target="_blank"
               style="display: inline-flex; align-items: center; padding: 8px 16px; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 8px; text-decoration: none;">
                <i class="fas fa-file-pdf mr-2"></i> Cetak Laporan
            </a>
            <a href="{{ route($routePrefix . '.create') }}"
               style="display: inline-flex; align-items: center; padding: 8px 16px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 8px; text-decoration: none;">
                <i class="fas fa-plus mr-2"></i> Tambah
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-700 font-medium">Total Reward</p>
            <p class="text-2xl font-bold text-green-700">{{ $stats['total_reward'] }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-700 font-medium">Total Punishment</p>
            <p class="text-2xl font-bold text-red-700">{{ $stats['total_punishment'] }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-700 font-medium">Total Nominal Reward</p>
            <p class="text-lg font-bold text-blue-700">Rp {{ number_format($stats['total_nominal_reward'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <p class="text-sm text-orange-700 font-medium">Total Nominal Punishment</p>
            <p class="text-lg font-bold text-orange-700">Rp {{ number_format($stats['total_nominal_punishment'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select name="kategori" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">Semua Kategori</option>
            <option value="reward" {{ request('kategori') == 'reward' ? 'selected' : '' }}>Reward</option>
            <option value="punishment" {{ request('kategori') == 'punishment' ? 'selected' : '' }}>Punishment</option>
        </select>
        <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <select name="pegawai_id" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">Semua Pegawai</option>
            @foreach($pegawai as $p)
                <option value="{{ $p->id }}" {{ request('pegawai_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
               class="px-3 py-2 border border-gray-300 rounded-md text-sm">
        <div class="flex gap-2 md:col-span-4">
            <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="px-3 py-2 border rounded-md text-sm">
            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="px-3 py-2 border rounded-md text-sm">
            <button type="submit" style="padding: 8px 20px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            <a href="{{ route($routePrefix . '.index') }}" style="padding: 8px 20px; background: #9ca3af; color: white; font-weight: 700; border-radius: 6px; text-decoration: none;">
                <i class="fas fa-undo"></i> Reset
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pegawai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $i => $item)
                @php $kc = $item->kategori_color; $sc = $item->status_color; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">{{ $data->firstItem() + $i }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-900">{{ $item->pegawai->nama ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $item->pegawai->nip ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span style="display:inline-block; padding: 3px 10px; background: {{ $kc['bg'] }}; color: {{ $kc['text'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">
                            {{ $kc['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <i class="fas {{ $item->tipe_icon }} mr-1 text-gray-500"></i>
                        {{ $item->tipe_label }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium">{{ $item->judul }}</div>
                        @if($item->nominal_format)
                            <div class="text-xs text-gray-500">{{ $item->nominal_format }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span style="display:inline-block; padding: 3px 10px; background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">
                            {{ $sc['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex flex-wrap gap-1">
                            <a href="{{ route($routePrefix . '.show', $item->id) }}"
                               style="display: inline-flex; align-items: center; padding: 5px 10px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route($routePrefix . '.edit', $item->id) }}"
                               style="display: inline-flex; align-items: center; padding: 5px 10px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; font-size: 11px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route($routePrefix . '.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="display: inline-flex; align-items: center; padding: 5px 10px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; font-size: 11px; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $data->withQueryString()->links() }}</div>
</div>
@endsection