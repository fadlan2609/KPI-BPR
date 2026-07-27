@extends('layouts.admin')

@section('title', 'Detail Pengajuan Cuti')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pengajuan Cuti</h1>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap pengajuan cuti</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.cuti.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            @if($cuti->status == 'pending')
                <a href="{{ route('admin.cuti.edit', $cuti->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    <div class="mb-6 p-4 rounded-lg 
        @if($cuti->status == 'pending') bg-yellow-50 border border-yellow-200
        @elseif($cuti->status == 'disetujui') bg-green-50 border border-green-200
        @else bg-red-50 border border-red-200
        @endif">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($cuti->status == 'pending')
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                @elseif($cuti->status == 'disetujui')
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                @else
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                @endif
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium 
                    @if($cuti->status == 'pending') text-yellow-800
                    @elseif($cuti->status == 'disetujui') text-green-800
                    @else text-red-800
                    @endif">
                    Status: {{ ucfirst($cuti->status) }}
                </h3>
                @if($cuti->status != 'pending')
                    <p class="text-xs text-gray-600 mt-1">
                        Diproses oleh: {{ $cuti->approvedBy->nama ?? '-' }} 
                        pada {{ $cuti->approved_at ? Carbon\Carbon::parse($cuti->approved_at)->format('d/m/Y H:i') : '-' }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Pegawai -->
        <div class="border rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user text-blue-600 mr-2"></i>
                Informasi Pegawai
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Nama</span>
                    <span class="font-medium">{{ $cuti->pegawai->nama }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">NIP</span>
                    <span class="font-medium">{{ $cuti->pegawai->nip }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Jabatan</span>
                    <span class="font-medium">{{ $cuti->pegawai->jabatan->nama ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kantor</span>
                    <span class="font-medium">{{ $cuti->pegawai->kantor->nama ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Informasi Cuti -->
        <div class="border rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                Informasi Cuti
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Jenis Cuti</span>
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($cuti->jenis_cuti == 'tahunan') bg-blue-100 text-blue-800
                        @elseif($cuti->jenis_cuti == 'sakit') bg-red-100 text-red-800
                        @elseif($cuti->jenis_cuti == 'melahirkan') bg-pink-100 text-pink-800
                        @elseif($cuti->jenis_cuti == 'khusus') bg-purple-100 text-purple-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($cuti->jenis_cuti) }}
                    </span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Tanggal Mulai</span>
                    <span class="font-medium">{{ Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Tanggal Selesai</span>
                    <span class="font-medium">{{ Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Lama Cuti</span>
                    <span class="font-medium">{{ $cuti->lama_hari }} hari kerja</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Keterangan</span>
                    <span class="font-medium">{{ $cuti->keterangan ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Hari Cuti -->
    <div class="mt-6 border rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-list-ul text-blue-600 mr-2"></i>
            Detail Hari Cuti
        </h3>
        
        <!-- Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div class="bg-blue-50 rounded-lg p-3 text-center">
                <p class="text-sm text-gray-600">Total Hari Kalender</p>
                <p class="text-xl font-bold text-blue-600">{{ $totalHariKalender }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3 text-center">
                <p class="text-sm text-gray-600">Hari Kerja</p>
                <p class="text-xl font-bold text-green-600">{{ $hariKerja }}</p>
            </div>
            <div class="bg-red-50 rounded-lg p-3 text-center">
                <p class="text-sm text-gray-600">Hari Libur (Sabtu/Minggu)</p>
                <p class="text-xl font-bold text-red-600">{{ $hariLibur }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3 text-center">
                <p class="text-sm text-gray-600">Status</p>
                <p class="text-xl font-bold 
                    @if($cuti->status == 'pending') text-yellow-600
                    @elseif($cuti->status == 'disetujui') text-green-600
                    @else text-red-600
                    @endif">
                    {{ ucfirst($cuti->status) }}
                </p>
            </div>
        </div>

        <!-- Table Hari -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hari</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($detailHari as $index => $hari)
                    <tr class="{{ $hari['is_weekend'] ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $hari['format'] }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $hari['hari'] }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($hari['is_weekend'])
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i> Libur
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Hari Kerja
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Aksi untuk Admin -->
    <div class="mt-6 flex flex-wrap gap-2">
        @if($cuti->status == 'pending')
            <a href="{{ route('admin.cuti.approve', $cuti->id) }}" 
               onclick="return confirm('Setujui pengajuan cuti ini?')"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                <i class="fas fa-check mr-2"></i> Setujui
            </a>
            <a href="{{ route('admin.cuti.reject', $cuti->id) }}" 
               onclick="return confirm('Tolak pengajuan cuti ini?')"
               class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                <i class="fas fa-times mr-2"></i> Tolak
            </a>
            <a href="{{ route('admin.cuti.edit', $cuti->id) }}" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        @elseif($cuti->status == 'disetujui')
            <a href="#" 
               onclick="confirmCancelApproval('{{ route('admin.cuti.cancel-approval', $cuti->id) }}', '{{ $cuti->pegawai->nama }}')"
               class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-md">
                <i class="fas fa-undo mr-2"></i> Batalkan Persetujuan
            </a>
        @elseif($cuti->status == 'ditolak')
            <a href="#" 
               onclick="confirmReopen('{{ route('admin.cuti.reopen', $cuti->id) }}', '{{ $cuti->pegawai->nama }}')"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                <i class="fas fa-redo mr-2"></i> Buka Kembali
            </a>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Buka Kembali (Reopen) -->
<div id="reopenModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="fas fa-redo text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Konfirmasi Buka Kembali</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin membuka kembali pengajuan cuti <strong id="reopenNama"></strong>?
                </p>
                <p class="text-xs text-green-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Status akan kembali menjadi <strong>Pending</strong> dan dapat diproses ulang.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="reopenForm" method="GET" class="inline">
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-300 mr-2">
                        <i class="fas fa-redo mr-1"></i> Buka Kembali
                    </button>
                    <button type="button" onclick="closeReopenModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batalkan Persetujuan -->
<div id="cancelApprovalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                <i class="fas fa-undo text-orange-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Konfirmasi Batalkan Persetujuan</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin membatalkan persetujuan cuti <strong id="cancelApprovalNama"></strong>?
                </p>
                <p class="text-xs text-orange-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Status akan kembali menjadi <strong>Pending</strong> dan saldo cuti akan dikembalikan.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="cancelApprovalForm" method="GET" class="inline">
                    <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 mr-2">
                        <i class="fas fa-undo mr-1"></i> Batalkan
                    </button>
                    <button type="button" onclick="closeCancelApprovalModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ==================== REOPEN MODAL ====================
    function confirmReopen(url, nama) {
        event.preventDefault();
        const modal = document.getElementById('reopenModal');
        const form = document.getElementById('reopenForm');
        const namaSpan = document.getElementById('reopenNama');
        
        namaSpan.textContent = nama;
        form.action = url;
        
        modal.classList.remove('hidden');
    }

    function closeReopenModal() {
        document.getElementById('reopenModal').classList.add('hidden');
    }

    // ==================== CANCEL APPROVAL MODAL ====================
    function confirmCancelApproval(url, nama) {
        event.preventDefault();
        const modal = document.getElementById('cancelApprovalModal');
        const form = document.getElementById('cancelApprovalForm');
        const namaSpan = document.getElementById('cancelApprovalNama');
        
        namaSpan.textContent = nama;
        form.action = url;
        
        modal.classList.remove('hidden');
    }

    function closeCancelApprovalModal() {
        document.getElementById('cancelApprovalModal').classList.add('hidden');
    }

    // ==================== CLOSE MODALS ON OUTSIDE CLICK ====================
    window.onclick = function(event) {
        const reopenModal = document.getElementById('reopenModal');
        const cancelApprovalModal = document.getElementById('cancelApprovalModal');
        
        if (event.target == reopenModal) {
            reopenModal.classList.add('hidden');
        }
        if (event.target == cancelApprovalModal) {
            cancelApprovalModal.classList.add('hidden');
        }
    }

    // ==================== AUTO CLOSE ON SUBMIT ====================
    document.addEventListener('DOMContentLoaded', function() {
        // Reopen form
        const reopenForm = document.getElementById('reopenForm');
        if (reopenForm) {
            reopenForm.addEventListener('submit', function() {
                closeReopenModal();
            });
        }
        
        // Cancel approval form
        const cancelApprovalForm = document.getElementById('cancelApprovalForm');
        if (cancelApprovalForm) {
            cancelApprovalForm.addEventListener('submit', function() {
                closeCancelApprovalModal();
            });
        }
    });
</script>
@endpush
@endsection