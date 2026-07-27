@extends('layouts.pegawai')

@section('title', 'Penilaian Bawahan')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Penilaian Bawahan</h1>
            <p class="text-sm text-gray-500">Nilai kinerja bawahan Anda (langsung dan tidak langsung)</p>
        </div>
        <a href="{{ route('pegawai.dashboard') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(!$periodeAktif)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ $message ?? 'Belum ada periode penilaian aktif' }}
            </p>
        </div>
    @endif

    <!-- Table Bawahan -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pegawai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level Penilai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Self</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Penilaian</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bawahan as $index => $b)
                @php
                    $progress = $progressData[$b->id] ?? null;
                    $selfCompleted = $progress && isset($progress['levels']['self']) && $progress['levels']['self']['completed'];
                    $levelKey = $b->level_penilai ?? 'atasan_langsung';
                    $atasanCompleted = $progress && isset($progress['levels'][$levelKey]) && $progress['levels'][$levelKey]['completed'];
                @endphp
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $b->nama }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $b->nip }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $b->jabatan->nama ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $b->level_penilai == 'atasan_langsung' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $b->level_label ?? ($b->level_penilai == 'atasan_langsung' ? 'Atasan Langsung' : 'Atasan Penilai') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($selfCompleted)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            </span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                <i class="fas fa-clock mr-1"></i> Belum
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($atasanCompleted)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                <i class="fas fa-check-circle mr-1"></i> Sudah Dinilai
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                <i class="fas fa-exclamation-circle mr-1"></i> Belum Dinilai
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        @if($periodeAktif)
                            @if($atasanCompleted)
                                <a href="{{ route('pegawai.penilaian-bawahan.show', $b->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            @else
                                <a href="{{ route('pegawai.penilaian-bawahan.create', $b->id) }}" 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-pen mr-1"></i> Nilai
                                </a>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-3 text-center text-gray-500">
                        Anda tidak memiliki bawahan yang perlu dinilai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($periodeAktif)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Keterangan Level Penilai:</strong>
            </p>
            <ul class="text-sm text-blue-700 ml-6 mt-1 list-disc">
                <li><strong>Atasan Langsung</strong> = Pegawai yang langsung berada di bawah Anda</li>
                <li><strong>Atasan Penilai</strong> = Pegawai yang atasan langsungnya adalah bawahan langsung Anda</li>
            </ul>
        </div>
    @endif
</div>
@endsection