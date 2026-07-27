@extends('layouts.admin')

@section('title', 'Manajemen Bonus')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Bonus</h1>
        <a href="{{ route('admin.gaji.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Info -->
    @if($periodeList->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                Belum ada periode penilaian yang selesai. Bonus hanya bisa dihitung setelah periode penilaian closed.
            </p>
        </div>
    @endif

    <!-- Daftar Periode -->
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Bonus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Bonus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periodeList as $index => $p)
                    @php
                        $bonus = \App\Models\PerhitunganBonus::where('periode_id', $p->id)->first();
                        $totalBonus = \App\Models\PerhitunganBonus::where('periode_id', $p->id)->sum('bonus_akhir');
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $p->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $p->tanggal_mulai->format('d/m/Y') }} - {{ $p->tanggal_selesai->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($bonus)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Sudah Dihitung</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Belum Dihitung</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($totalBonus > 0)
                                Rp {{ number_format($totalBonus, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.bonus.setting', $p->id) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-2">
                                <i class="fas fa-cog mr-1"></i> Setting
                            </a>
                            @if($bonus)
                                <a href="{{ route('admin.bonus.export', ['periode_id' => $p->id]) }}" 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-file-excel mr-1"></i> Export
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
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