@extends('layouts.admin')

@section('title', 'Laporan Gaji')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Kenaikan Gaji</h1>
            @if(isset($periode) && $periode)
                <p class="text-sm text-gray-500">Periode: {{ $periode->nama ?? $periode->periode ?? '-' }}</p>
            @endif
        </div>
        <a href="{{ route('admin.laporan.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(isset($message))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                {{ $message }}
            </p>
        </div>
    @else
        <!-- Filter -->
        <div class="bg-white rounded-lg shadow p-4">
            <form action="{{ route('admin.laporan.gaji') }}" method="GET" class="flex flex-wrap gap-4">
                <select name="periode_id" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Periode</option>
                    @if(isset($periodeList))
                        @foreach($periodeList as $p)
                            <option value="{{ $p->id }}" {{ isset($periode) && $periode && $periode->id == $p->id ? 'selected' : '' }}>
                                {{ $p->nama ?? $p->periode ?? '-' }}
                                @if(isset($p->tanggal_mulai))
                                    ({{ $p->tanggal_mulai->format('d/m/Y') }} - {{ $p->tanggal_selesai->format('d/m/Y') }})
                                @endif
                            </option>
                        @endforeach
                    @endif
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-filter mr-2"></i> Tampilkan
                </button>
                <a href="{{ route('admin.laporan.gaji') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                    <i class="fas fa-undo mr-2"></i> Reset
                </a>
            </form>
        </div>

        @if(isset($periode) && $periode)
            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $summary['total'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Total Pegawai</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($summary['total_kenaikan'] ?? 0, 2) }}%</p>
                    <p class="text-sm text-gray-500">Total Kenaikan</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($summary['rata_rata'] ?? 0, 2) }}%</p>
                    <p class="text-sm text-gray-500">Rata-rata Kenaikan</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pegawai</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Kenaikan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data ?? [] as $index => $d)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $d->pegawai->nama ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $d->pegawai->nip ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-green-600">{{ $d->persentase ?? 0 }}%</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                      style="color: {{ $d->predikat == 'Sangat Baik' ? '#fff' : '#000' }}; 
                                             background-color: {{ $d->predikat == 'Sangat Baik' ? '#28A745' : ($d->predikat == 'Baik' ? '#FFC107' : ($d->predikat == 'Cukup' ? '#FD7E14' : '#DC3545')) }}">
                                    {{ $d->predikat ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                Belum ada data kenaikan gaji untuk periode ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif(!isset($message))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Silakan pilih periode penilaian untuk melihat laporan gaji
                </p>
            </div>
        @endif
    @endif
</div>
@endsection