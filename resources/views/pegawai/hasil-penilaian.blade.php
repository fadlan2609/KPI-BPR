@extends('layouts.pegawai')

@section('title', 'Hasil Penilaian')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Hasil Penilaian</h1>
        <a href="{{ route('pegawai.dashboard') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if($hasil->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                Belum ada hasil penilaian yang final
            </p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Self</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Atasan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Penilai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Akhir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predikat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($hasil as $index => $h)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $h->periode->nama ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $h->nilai_self ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $h->nilai_atasan_langsung ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $h->nilai_atasan_penilai ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold">{{ $h->nilai_akhir }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                  style="color: {{ $h->predikat->warna_text ?? '#000' }}; 
                                         background-color: {{ $h->predikat->warna_latar ?? '#ccc' }}">
                                {{ $h->predikat->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Final</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('pegawai.cetak-laporan', $h->id) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye mr-1"></i> Lihat Laporan
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $hasil->links() }}
        </div>
    @endif
</div>
@endsection