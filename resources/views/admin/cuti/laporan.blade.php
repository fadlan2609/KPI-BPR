@extends('layouts.admin')

@section('title', 'Laporan Cuti')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Laporan Cuti</h1>
            <p class="text-sm text-gray-500">Cuti yang sudah disetujui final</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.cuti.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('admin.cuti.laporan.cetak', request()->query()) }}" target="_blank"
               class="px-4 py-2 bg-red-600 text-white rounded-md">
                <i class="fas fa-file-pdf mr-2"></i> Cetak PDF
            </a>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
               class="px-3 py-2 border rounded-md text-sm">
        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
               class="px-3 py-2 border rounded-md text-sm">
        <select name="pegawai_id" class="px-3 py-2 border rounded-md text-sm">
            <option value="">Semua Pegawai</option>
            @foreach($pegawai as $p)
                <option value="{{ $p->id }}" {{ request('pegawai_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->nama }}
                </option>
            @endforeach
        </select>
        <select name="jenis_cuti" class="px-3 py-2 border rounded-md text-sm">
            <option value="">Semua Jenis</option>
            <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
            <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Sakit</option>
            <option value="melahirkan" {{ request('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Melahirkan</option>
            <option value="khusus" {{ request('jenis_cuti') == 'khusus' ? 'selected' : '' }}>Khusus</option>
            <option value="lainnya" {{ request('jenis_cuti') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
        <a href="{{ route('admin.cuti.laporan') }}" class="px-4 py-2 bg-gray-400 text-white rounded-md text-sm">Reset</a>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pegawai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disetujui Final Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($cuti as $i => $c)
                <tr>
                    <td class="px-4 py-3 text-sm">{{ $cuti->firstItem() + $i }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium">{{ $c->pegawai->nama }}</div>
                        <div class="text-xs text-gray-500">{{ $c->pegawai->nip }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $c->pegawai->jabatan->nama ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ ucfirst($c->jenis_cuti) }}</td>
                    <td class="px-4 py-3 text-sm">
                        {{ $c->tanggal_mulai->format('d/m/Y') }} - {{ $c->tanggal_selesai->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $c->lama_hari }} hari</td>
                    <td class="px-4 py-3 text-sm">
                        {{ $c->finalApprovedBy->nama ?? '-' }}
                        <div class="text-xs text-gray-500">
                            {{ $c->final_approved_at ? $c->final_approved_at->format('d/m/Y H:i') : '' }}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                        Tidak ada cuti yang disetujui final
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $cuti->withQueryString()->links() }}</div>
</div>
@endsection