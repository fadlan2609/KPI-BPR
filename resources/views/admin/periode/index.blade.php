@extends('layouts.admin')

@section('title', 'Periode Penilaian')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Periode Penilaian</h1>
        <a href="{{ route('admin.periode.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
            <i class="fas fa-plus mr-2"></i> Tambah Periode
        </a>
    </div>

    <!-- Info -->
    @php
        $periodeAktif = \App\Models\PeriodePenilaian::where('is_active', true)->first();
    @endphp
    @if($periodeAktif)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800">
                <i class="fas fa-check-circle mr-2"></i>
                Periode Aktif: <strong>{{ $periodeAktif->nama }}</strong>
                ({{ $periodeAktif->tanggal_mulai->format('d/m/Y') }} - {{ $periodeAktif->tanggal_selesai->format('d/m/Y') }})
            </p>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Belum ada periode penilaian yang aktif. Silakan buat dan aktifkan periode.
            </p>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($periode as $index => $p)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $p->nama }}</div>
                        @if($p->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $p->tanggal_mulai->format('d/m/Y') }} - {{ $p->tanggal_selesai->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <div>Self: {{ $p->batas_self_assessment->format('d/m/Y') }}</div>
                        <div>Atasan: {{ $p->batas_penilaian_atasan->format('d/m/Y') }}</div>
                        <div>Final: {{ $p->batas_finalisasi->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $p->status == 'active' ? 'bg-green-100 text-green-800' : 
                               ($p->status == 'closed' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if(!$p->is_active)
                            <a href="{{ route('admin.periode.activate', $p->id) }}" 
                               class="text-green-600 hover:text-green-900 mr-2"
                               onclick="return confirm('Aktifkan periode ini?')">
                                <i class="fas fa-check-circle"></i> Aktifkan
                            </a>
                        @endif
                        <a href="{{ route('admin.periode.edit', $p->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        {{-- PERBAIKI: Form Delete dengan parameter --}}
                        <form action="{{ route('admin.periode.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada periode penilaian</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3">
            {{ $periode->links() }}
        </div>
    </div>
</div>
@endsection