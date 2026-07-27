@extends('layouts.admin')

@section('title', 'Manajemen Cuti')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Cuti</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pengajuan cuti pegawai</p>
        </div>
        <a href="{{ route('admin.cuti.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
            <i class="fas fa-plus mr-2"></i> Ajukan Cuti
        </a>
    </div>

    <!-- Filter / Pencarian -->
    <div class="mb-4 flex flex-wrap gap-2">
        <form action="{{ route('admin.cuti.index') }}" method="GET" class="flex flex-wrap gap-2">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="jenis_cuti" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Jenis</option>
                <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                <option value="melahirkan" {{ request('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                <option value="khusus" {{ request('jenis_cuti') == 'khusus' ? 'selected' : '' }}>Cuti Khusus</option>
                <option value="lainnya" {{ request('jenis_cuti') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            <input type="text" name="search" placeholder="Cari pegawai..." value="{{ request('search') }}" 
                   class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            <a href="{{ route('admin.cuti.index') }}" class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-md text-sm">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </form>
    </div>

    <!-- Tabel Cuti -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Cuti</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lama Hari</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cuti as $index => $c)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ ($cuti->currentPage() - 1) * $cuti->perPage() + $index + 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $c->pegawai->nama }}</div>
                        <div class="text-xs text-gray-500">{{ $c->pegawai->nip }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') }}</div>
                        <div class="text-xs">s.d {{ Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="font-medium">{{ $c->lama_hari }}</span> hari kerja
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex flex-wrap gap-1">
                            <!-- Tombol Detail/Review -->
                            <a href="{{ route('admin.cuti.show', $c->id) }}" 
                               class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded text-xs"
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($c->status == 'pending')
                                <!-- Tombol untuk Pending -->
                                <a href="{{ route('admin.cuti.approve', $c->id) }}" 
                                   onclick="return confirm('Setujui pengajuan cuti ini?')"
                                   class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-2 py-1 rounded text-xs"
                                   title="Setujui">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="{{ route('admin.cuti.reject', $c->id) }}" 
                                   onclick="return confirm('Tolak pengajuan cuti ini?')"
                                   class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded text-xs"
                                   title="Tolak">
                                    <i class="fas fa-times"></i>
                                </a>
                                <a href="{{ route('admin.cuti.edit', $c->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded text-xs"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" 
                                   onclick="confirmDelete('{{ route('admin.cuti.destroy', $c->id) }}', '{{ $c->pegawai->nama }}', '{{ $c->jenis_cuti }}')"
                                   class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded text-xs"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            
                            @elseif($c->status == 'disetujui')
                                <!-- Tombol untuk Disetujui -->
                                <a href="#" 
                                   onclick="confirmCancelApproval('{{ route('admin.cuti.cancel-approval', $c->id) }}', '{{ $c->pegawai->nama }}')"
                                   class="text-orange-600 hover:text-orange-900 bg-orange-100 hover:bg-orange-200 px-2 py-1 rounded text-xs"
                                   title="Batalkan Persetujuan">
                                    <i class="fas fa-undo"></i>
                                </a>
                                <a href="#" 
                                   onclick="confirmDelete('{{ route('admin.cuti.destroy', $c->id) }}', '{{ $c->pegawai->nama }}', '{{ $c->jenis_cuti }}')"
                                   class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded text-xs"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            
                            @elseif($c->status == 'ditolak')
                                <!-- Tombol untuk Ditolak -->
                                <a href="#" 
                                   onclick="confirmReopen('{{ route('admin.cuti.reopen', $c->id) }}', '{{ $c->pegawai->nama }}')"
                                   class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-2 py-1 rounded text-xs"
                                   title="Buka Kembali">
                                    <i class="fas fa-redo"></i>
                                </a>
                                <a href="#" 
                                   onclick="confirmDelete('{{ route('admin.cuti.destroy', $c->id) }}', '{{ $c->pegawai->nama }}', '{{ $c->jenis_cuti }}')"
                                   class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded text-xs"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        <p>Tidak ada data cuti</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $cuti->withQueryString()->links() }}
    </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus pengajuan cuti <strong id="deleteNama"></strong> 
                    (<span id="deleteJenis"></span>)?
                </p>
                <p class="text-xs text-red-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Hanya cuti dengan status <strong>Pending</strong> yang dapat dihapus!
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-300 mr-2">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                </form>
            </div>
        </div>
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
    // ==================== DELETE MODAL ====================
    function confirmDelete(url, nama, jenis) {
        event.preventDefault();
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const namaSpan = document.getElementById('deleteNama');
        const jenisSpan = document.getElementById('deleteJenis');
        
        namaSpan.textContent = nama;
        jenisSpan.textContent = jenis.charAt(0).toUpperCase() + jenis.slice(1);
        form.action = url;
        
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

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
        const deleteModal = document.getElementById('deleteModal');
        const reopenModal = document.getElementById('reopenModal');
        const cancelApprovalModal = document.getElementById('cancelApprovalModal');
        
        if (event.target == deleteModal) {
            deleteModal.classList.add('hidden');
        }
        if (event.target == reopenModal) {
            reopenModal.classList.add('hidden');
        }
        if (event.target == cancelApprovalModal) {
            cancelApprovalModal.classList.add('hidden');
        }
    }

    // ==================== AUTO CLOSE ON SUBMIT ====================
    document.addEventListener('DOMContentLoaded', function() {
        // Delete form
        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function() {
                closeDeleteModal();
            });
        }
        
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