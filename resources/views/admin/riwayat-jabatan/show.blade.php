@extends('layouts.admin')

@section('title', 'Riwayat Jabatan Pegawai')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $pegawai->nama }}</h1>
                <p class="text-sm text-gray-500">
                    {{ $pegawai->nip }} — {{ $pegawai->jabatan->nama ?? '-' }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.riwayat-jabatan.cetak-cv', $pegawai->id) }}"
                   target="_blank"
                   style="display: inline-flex; align-items: center; padding: 8px 16px; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none; box-shadow: 0 4px 10px -2px rgba(139, 92, 246, 0.4);">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Cetak CV
                </a>

                <a href="{{ route('admin.riwayat-jabatan.download-cv', $pegawai->id) }}"
                   style="display: inline-flex; align-items: center; padding: 8px 16px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none; box-shadow: 0 4px 10px -2px rgba(34, 197, 94, 0.4);">
                    <i class="fas fa-download mr-2"></i>
                    Download CV
                </a>

                <a href="{{ route('admin.riwayat-jabatan.create', ['pegawai_id' => $pegawai->id]) }}"
                   style="display: inline-flex; align-items: center; padding: 8px 16px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Riwayat
                </a>

                <a href="{{ route('admin.riwayat-jabatan.index') }}"
                   style="display: inline-flex; align-items: center; padding: 8px 16px; background: #6b7280; color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Total Riwayat</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $riwayat->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Jabatan Sekarang</p>
            <p class="text-lg font-bold text-blue-600">{{ $pegawai->jabatan->nama ?? '-' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Total Pengalaman</p>
            @php
                $totalTahun = 0;
                foreach($riwayat as $r) {
                    $selesai = $r->tahun_selesai ?? date('Y');
                    $totalTahun += ($selesai - $r->tahun_mulai);
                }
            @endphp
            <p class="text-2xl font-bold text-green-600">{{ $totalTahun }} tahun</p>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-history text-indigo-600 mr-2"></i>
            Timeline Riwayat Jabatan
            <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xs font-bold">
                {{ $riwayat->count() }}
            </span>
        </h3>

        @forelse($riwayat as $index => $r)
            <div class="relative pl-14 pb-10 {{ $loop->last ? '' : 'border-l-2 border-indigo-200' }}">

                <div class="absolute left-0 top-0 -ml-4 w-8 h-8 rounded-full border-4 border-white shadow-md flex items-center justify-center text-white font-bold text-xs
                    {{ $r->is_current ? 'bg-green-500' : 'bg-indigo-500' }}">
                    {{ $index + 1 }}
                </div>

                <div class="bg-gray-50 rounded-xl p-5 hover:bg-gray-100 transition border
                    {{ $r->is_current ? 'border-green-400 shadow-md' : 'border-gray-200' }}">

                    <div class="flex justify-between items-start flex-wrap gap-3 mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h4 class="text-lg font-bold text-gray-900">
                                    {{ $r->jabatan->nama ?? '-' }}
                                </h4>
                                @if($r->is_current)
                                    <span class="px-2 py-0.5 text-xs bg-green-500 text-white rounded-full font-bold">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        AKTIF
                                    </span>
                                @endif
                            </div>

                            @if($r->status_karyawan)
                                @php $sk = $r->status_karyawan_color; @endphp
                                <div class="mb-2">
                                    <span style="display: inline-flex; align-items: center; padding: 3px 10px; background: {{ $sk['bg'] }}; color: {{ $sk['text'] }}; border: 1px solid {{ $sk['border'] }}; border-radius: 9999px; font-size: 11px; font-weight: 700;">
                                        <i class="fas fa-id-badge" style="margin-right: 5px;"></i>
                                        {{ $r->status_karyawan_label }}
                                        @if($r->jenis_kontrak)
                                            — {{ $r->jenis_kontrak }}
                                        @endif
                                    </span>
                                </div>
                            @endif

                            <p class="text-sm text-gray-600">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                {{ $r->kantor->nama ?? '-' }}
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="px-3 py-1.5 bg-indigo-100 text-indigo-800 rounded-lg">
                                <p class="text-sm font-bold">{{ $r->periode_label }}</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $r->durasi }}</p>
                        </div>
                    </div>

                    @if($r->keterangan)
                        <div class="bg-white rounded-lg p-3 mb-3 border-l-4 border-indigo-400">
                            <p class="text-sm text-gray-700 italic">
                                <i class="fas fa-quote-left text-indigo-400 mr-1"></i>
                                {{ $r->keterangan }}
                            </p>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-200">
                        <a href="{{ route('admin.riwayat-jabatan.edit', $r->id) }}"
                           style="display: inline-flex; align-items: center; padding: 6px 12px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; font-size: 12px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>

                        <form action="{{ route('admin.riwayat-jabatan.destroy', $r->id) }}" method="POST"
                              class="inline" onsubmit="return confirm('Hapus riwayat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="display: inline-flex; align-items: center; padding: 6px 12px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; font-size: 12px; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-gray-50 rounded-xl">
                <i class="fas fa-briefcase text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-600 mb-1">Belum Ada Riwayat Jabatan</h3>
                <p class="text-sm text-gray-500 mb-4">Tambahkan riwayat jabatan pegawai ini.</p>
                <a href="{{ route('admin.riwayat-jabatan.create', ['pegawai_id' => $pegawai->id]) }}"
                   style="display: inline-flex; align-items: center; padding: 10px 20px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Riwayat Pertama
                </a>
            </div>
        @endforelse
    </div>

</div>
@endsection