@extends('layouts.admin')

@section('title', 'Laporan Cuti')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Cuti</h1>
            <p class="text-sm text-gray-500">Rekapitulasi pengajuan cuti pegawai</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.laporan.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                <i class="fas fa-print mr-2"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.laporan.cuti') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti</label>
                <select name="jenis_cuti" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                    <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                    <option value="melahirkan" {{ request('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                    <option value="khusus" {{ request('jenis_cuti') == 'khusus' ? 'selected' : '' }}>Cuti Khusus</option>
                    <option value="lainnya" {{ request('jenis_cuti') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-sm text-gray-600">Total Pengajuan</p>
            <p class="text-2xl font-bold text-blue-600">{{ $totalPengajuan ?? 0 }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <p class="text-sm text-gray-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $totalPending ?? 0 }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <p class="text-sm text-gray-600">Disetujui</p>
            <p class="text-2xl font-bold text-green-600">{{ $totalDisetujui ?? 0 }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
            <p class="text-sm text-gray-600">Ditolak</p>
            <p class="text-2xl font-bold text-red-600">{{ $totalDitolak ?? 0 }}</p>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pegawai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Cuti</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lama Hari</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pengajuan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cuti ?? [] as $index => $c)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ ($cuti->currentPage() - 1) * $cuti->perPage() + $index + 1 }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $c->pegawai->nama ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $c->pegawai->nip ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($c->jenis_cuti == 'tahunan') bg-blue-100 text-blue-800
                            @elseif($c->jenis_cuti == 'sakit') bg-red-100 text-red-800
                            @elseif($c->jenis_cuti == 'melahirkan') bg-pink-100 text-pink-800
                            @elseif($c->jenis_cuti == 'khusus') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($c->jenis_cuti) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ isset($c->tanggal_mulai) ? Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') : '-' }}</div>
                        <div class="text-xs">s.d {{ isset($c->tanggal_selesai) ? Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') : '-' }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        <span class="font-medium">{{ $c->lama_hari ?? 0 }}</span> hari kerja
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($c->status == 'pending')
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @elseif($c->status == 'disetujui')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i> Disetujui
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times mr-1"></i> Ditolak
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ isset($c->created_at) ? Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        <p>Tidak ada data cuti</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($cuti) && $cuti->count() > 0)
    <div class="mt-4">
        {{ $cuti->withQueryString()->links() }}
    </div>
    @endif
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    .bg-white {
        background: white !important;
        box-shadow: none !important;
    }
    .shadow {
        box-shadow: none !important;
    }
    .rounded-lg {
        border-radius: 0 !important;
    }
    .border {
        border: 1px solid #ddd !important;
    }
}
</style>
@endsection