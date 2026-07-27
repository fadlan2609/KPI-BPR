@extends('layouts.admin')

@section('title', 'Daftar Penilaian KPI')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Penilaian KPI</h1>
            @if($periodeAktif)
                <p class="text-sm text-gray-500">Periode: {{ $periodeAktif->nama }}</p>
            @endif
        </div>
        <div class="flex space-x-2">
            @if($periodeAktif)
                <a href="{{ route('admin.penilaian.finalize', $periodeAktif->id) }}" 
                   class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-sm"
                   onclick="return confirm('Finalisasi semua penilaian?')">
                    <i class="fas fa-check-double mr-2"></i> Finalisasi Semua
                </a>
            @endif
            <a href="{{ route('admin.penilaian.progress') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                <i class="fas fa-tasks mr-2"></i> Progress
            </a>
        </div>
    </div>

    @if(!$periodeAktif)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ $message ?? 'Belum ada periode penilaian aktif' }}
            </p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pegawai as $index => $p)
                @php
                    $progress = $progressData[$p->id] ?? null;
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $p->nama }}</div>
                        <div class="text-xs text-gray-500">NIP: {{ $p->nip }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->jabatan->nama ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($progress)
                            <div class="space-y-1">
                                @foreach($progress['levels'] as $key => $level)
                                    <div class="flex items-center text-xs">
                                        @if($level['completed'])
                                            <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                                        @else
                                            <span class="text-gray-400"><i class="fas fa-circle"></i></span>
                                        @endif
                                        <span class="ml-1">{{ $level['label'] }}</span>
                                        @if($level['nilai'])
                                            <span class="ml-1 font-semibold">{{ $level['nilai'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">Belum ada penilaian</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($progress && $progress['finalized'])
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Final</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data pegawai</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($periodeAktif && count($pegawai) > 0 && method_exists($pegawai, 'links'))
            <div class="px-6 py-3">
                {{ $pegawai->links() }}
            </div>
        @endif
    </div>
</div>
@endsection