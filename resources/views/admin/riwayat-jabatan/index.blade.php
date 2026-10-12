@extends('layouts.admin')

@section('title', 'Riwayat Jabatan Pegawai')

@section('content')
<div class="bg-white rounded-lg shadow p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Jabatan (Mutasi)</h1>
            <p class="text-sm text-gray-500">Kelola riwayat jabatan semua pegawai</p>
        </div>
        <a href="{{ route('admin.riwayat-jabatan.permintaan') }}"
           class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-sm font-semibold">
            <i class="fas fa-clock mr-2"></i>
            Permintaan Edit
            @php
                $pendingCount = \App\Models\PermintaanEditRiwayat::where('status', 'pending')->count();
            @endphp
            @if($pendingCount > 0)
                <span class="ml-2 px-2 py-0.5 bg-white text-yellow-600 rounded-full text-xs font-bold">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>
    </div>

    {{-- Alert pending --}}
    @if($permintaanPending->count() > 0)
        <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
            <h3 class="font-semibold text-yellow-800 mb-2">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ $permintaanPending->count() }} permintaan edit menunggu review
            </h3>
            <ul class="text-sm text-yellow-800 space-y-1">
                @foreach($permintaanPending->take(3) as $p)
                    <li>
                        <i class="fas fa-user mr-1"></i>
                        <strong>{{ $p->pegawai->nama ?? '-' }}</strong> —
                        {{ $p->alasan }}
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('admin.riwayat-jabatan.permintaan') }}"
               class="inline-block mt-2 text-sm text-yellow-700 hover:underline font-medium">
                Lihat Semua →
            </a>
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NIP..."
               class="px-3 py-2 border border-gray-300 rounded-md text-sm flex-1">
        <button type="submit"
                style="display: inline-flex; align-items: center; padding: 8px 16px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; box-shadow: 0 4px 10px -2px rgba(59, 130, 246, 0.4);">
            <i class="fas fa-search" style="margin-right: 6px;"></i> Cari
        </button>
        <a href="{{ route('admin.riwayat-jabatan.index') }}"
           style="display: inline-flex; align-items: center; padding: 8px 16px; background: #9ca3af; color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none;">
            Reset
        </a>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pegawai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan Sekarang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kantor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Riwayat</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pegawai as $i => $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm">{{ $pegawai->firstItem() + $i }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-900">{{ $p->nama }}</div>
                        <div class="text-xs text-gray-500">{{ $p->nip }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 font-medium">
                            {{ $p->jabatan->nama ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <i class="fas fa-building text-gray-400 mr-1"></i>
                        {{ $p->kantor->nama ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($p->total_riwayat > 0)
                            <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800 font-medium">
                                <i class="fas fa-history mr-1"></i>
                                {{ $p->total_riwayat }} riwayat
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-500 font-medium">
                                Belum ada
                            </span>
                        @endif
                    </td>

                    {{-- KOLOM AKSI --}}
                    <td class="px-4 py-3 text-sm">
                        <div class="flex flex-wrap gap-1">

                            {{-- Tombol Lihat --}}
                            <a href="{{ route('admin.riwayat-jabatan.show', $p->id) }}"
                               style="display: inline-flex; align-items: center; padding: 6px 12px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #ffffff !important; font-size: 12px; font-weight: 700; border-radius: 6px; text-decoration: none; box-shadow: 0 4px 10px -2px rgba(99, 102, 241, 0.4); transition: all 0.2s;"
                               onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 14px -2px rgba(99, 102, 241, 0.5)';"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px -2px rgba(99, 102, 241, 0.4)';"
                               title="Lihat Riwayat">
                                <i class="fas fa-eye" style="margin-right: 5px;"></i> Lihat
                            </a>

                            {{-- Tombol Tambah --}}
                            <a href="{{ route('admin.riwayat-jabatan.create', ['pegawai_id' => $p->id]) }}"
                               style="display: inline-flex; align-items: center; padding: 6px 12px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: #ffffff !important; font-size: 12px; font-weight: 700; border-radius: 6px; text-decoration: none; box-shadow: 0 4px 10px -2px rgba(34, 197, 94, 0.4); transition: all 0.2s;"
                               onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 14px -2px rgba(34, 197, 94, 0.5)';"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px -2px rgba(34, 197, 94, 0.4)';"
                               title="Tambah Riwayat">
                                <i class="fas fa-plus" style="margin-right: 5px;"></i> Tambah
                            </a>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        Tidak ada data pegawai
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $pegawai->withQueryString()->links() }}</div>
</div>
@endsection