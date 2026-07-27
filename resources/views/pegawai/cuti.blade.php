@extends('layouts.pegawai')

@section('title', 'Manajemen Cuti')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Cuti</h1>
            <p class="text-sm text-gray-500">Kelola pengajuan cuti Anda</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="openModal('tambahModal')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                <i class="fas fa-plus mr-2"></i> Ajukan Cuti
            </button>
            <a href="{{ route('pegawai.dashboard') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Saldo Cuti -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Total Cuti</p>
            <p class="text-2xl font-bold text-blue-600">{{ $saldoCuti->total_hari ?? 12 }} hari</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Digunakan</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $saldoCuti->digunakan ?? 0 }} hari</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm text-gray-500">Sisa Cuti</p>
            <p class="text-2xl font-bold text-green-600">{{ $saldoCuti->sisa_hari ?? 12 }} hari</p>
        </div>
    </div>

    <!-- Daftar Cuti -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Cuti</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cuti as $index => $c)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 rounded-full text-xs 
                            {{ $c->jenis_cuti == 'tahunan' ? 'bg-blue-100 text-blue-800' : 
                               ($c->jenis_cuti == 'sakit' ? 'bg-red-100 text-red-800' : 
                                ($c->jenis_cuti == 'melahirkan' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($c->jenis_cuti) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        {{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') }} - 
                        {{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $c->lama_hari }} hari</td>
                    <td class="px-4 py-3 text-sm">{{ $c->keterangan ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 rounded-full text-xs
                            {{ $c->status == 'disetujui' ? 'bg-green-100 text-green-800' : 
                               ($c->status == 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($c->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                        Belum ada riwayat cuti
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">
            {{ $cuti->links() }}
        </div>
    </div>
</div>

<!-- ==================== MODAL TAMBAH CUTI ==================== -->
<div id="tambahModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-lg">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-plus text-blue-600 mr-2"></i>
                    Ajukan Cuti
                </h3>
                <button onclick="closeModal('tambahModal')" class="text-gray-400 hover:text-gray-600 text-2xl transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <form action="{{ route('pegawai.cuti.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti <span class="text-red-500">*</span></label>
                            <select name="jenis_cuti" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">Pilih Jenis Cuti</option>
                                <option value="tahunan">Cuti Tahunan</option>
                                <option value="sakit">Cuti Sakit</option>
                                <option value="melahirkan">Cuti Melahirkan</option>
                                <option value="khusus">Cuti Khusus</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" required
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" required
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                      placeholder="Alasan cuti (opsional)"></textarea>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Sisa cuti Anda: <strong>{{ $saldoCuti->sisa_hari ?? 12 }} hari</strong>
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('tambahModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition">
                            <i class="fas fa-times mr-2"></i> Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            document.querySelectorAll('.fixed').forEach(function(modal) {
                modal.classList.add('hidden');
            });
            document.body.style.overflow = '';
        }
    }

    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.fixed').forEach(function(modal) {
                modal.classList.add('hidden');
            });
            document.body.style.overflow = '';
        }
    });
</script>
@endpush
@endsection