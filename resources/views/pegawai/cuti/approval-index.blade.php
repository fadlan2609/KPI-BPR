@extends('layouts.pegawai')

@section('title', 'Approval Cuti')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Approval Cuti</h1>
        <p class="text-sm text-gray-500">Cuti yang menunggu persetujuan Anda</p>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 flex">
            <button onclick="switchTab('menunggu')" id="btn-menunggu"
                    class="px-6 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                <i class="fas fa-clock mr-2"></i> Menunggu ({{ $menunggu->total() }})
            </button>
            <button onclick="switchTab('riwayat')" id="btn-riwayat"
                    class="px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500">
                <i class="fas fa-history mr-2"></i> Riwayat ({{ $riwayat->total() }})
            </button>
        </div>
    </div>

    <!-- Tab Menunggu -->
    <div id="tab-menunggu" class="bg-white rounded-lg shadow">
        <div class="p-4">
            @forelse($menunggu as $c)
            <div class="border rounded-lg p-4 mb-3 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <h3 class="font-semibold text-gray-900">{{ $c->pegawai->nama }}</h3>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                Level {{ $c->current_approval_level }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $c->tanggal_mulai->format('d/m/Y') }} - {{ $c->tanggal_selesai->format('d/m/Y') }}
                            ({{ $c->lama_hari }} hari kerja)
                        </p>
                        <p class="text-sm text-gray-600">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($c->jenis_cuti) }}
                            </span>
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('pegawai.cuti.approval.show', $c->id) }}"
                           class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                            <i class="fas fa-eye mr-1"></i> Review
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                <p>Tidak ada cuti yang menunggu persetujuan</p>
            </div>
            @endforelse

            {{ $menunggu->links() }}
        </div>
    </div>

    <!-- Tab Riwayat -->
    <div id="tab-riwayat" class="bg-white rounded-lg shadow hidden">
        <div class="p-4">
            @forelse($riwayat as $c)
            <div class="border rounded-lg p-4 mb-3">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $c->pegawai->nama }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ $c->tanggal_mulai->format('d/m/Y') }} - {{ $c->tanggal_selesai->format('d/m/Y') }}
                        </p>
                    </div>
                    <a href="{{ route('pegawai.cuti.approval.show', $c->id) }}"
                       class="text-blue-600 hover:underline text-sm">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-gray-500">Belum ada riwayat</div>
            @endforelse

            {{ $riwayat->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        document.getElementById('tab-menunggu').classList.toggle('hidden', tab !== 'menunggu');
        document.getElementById('tab-riwayat').classList.toggle('hidden', tab !== 'riwayat');

        document.getElementById('btn-menunggu').className = tab === 'menunggu'
            ? 'px-6 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600'
            : 'px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500';
        document.getElementById('btn-riwayat').className = tab === 'riwayat'
            ? 'px-6 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600'
            : 'px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500';
    }
</script>
@endpush
@endsection