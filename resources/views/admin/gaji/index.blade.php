@extends('layouts.admin')

@section('title', 'Manajemen Gaji')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Gaji</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.gaji.kenaikan') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                <i class="fas fa-arrow-up mr-2"></i> Kenaikan Gaji
            </a>
            <a href="{{ route('admin.bonus.index') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                <i class="fas fa-gift mr-2"></i> Bonus
            </a>
            <a href="{{ route('admin.gaji.setting') }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm">
                <i class="fas fa-cog mr-2"></i> Setting
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card Total Karyawan -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalPegawai ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Card Total Gaji -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Gaji</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalGaji ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Card Periode Aktif -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-calendar-alt text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Periode Aktif</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $periodeAktif ? 'Ada' : 'Tidak Ada' }}</p>
                    @if($periodeAktif)
                        <p class="text-xs text-gray-500">{{ $periodeAktif->nama }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Periode Closed -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Periode Penilaian Selesai</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periodeList as $index => $p)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $p->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $p->tanggal_mulai->format('d/m/Y') }} - {{ $p->tanggal_selesai->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs 
                                {{ $p->status == 'closed' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.gaji.kenaikan', ['periode_id' => $p->id]) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-2">
                                <i class="fas fa-arrow-up mr-1"></i> Kenaikan
                            </a>
                            <a href="{{ route('admin.bonus.setting', $p->id) }}" 
                               class="text-green-600 hover:text-green-900">
                                <i class="fas fa-gift mr-1"></i> Bonus
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Belum ada periode penilaian yang selesai
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection