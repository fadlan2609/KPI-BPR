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
                <form action="{{ route('admin.penilaian.finalize', $periodeAktif->id) }}" method="POST" class="inline" id="formFinalize">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-sm" 
                            onclick="return confirm('Finalisasi semua penilaian untuk periode {{ $periodeAktif->nama }}?')">
                        <i class="fas fa-check-double mr-2"></i> Finalisasi Semua
                    </button>
                </form>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pegawai as $index => $p)
                @php
                    $progress = $progressData[$p->id] ?? null;
                    $isFinalized = $progress && $progress['finalized'];
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
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
                        @if($isFinalized)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Final</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex space-x-2">
                            @if($periodeAktif)
                                @if(!$isFinalized)
                                    <a href="{{ route('admin.penilaian.create', [$p->id, $periodeAktif->id]) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    
                                    {{-- TOMBOL HAPUS DENGAN KODE KEAMANAN --}}
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-900" 
                                            onclick="confirmDelete({{ $p->id }}, '{{ $p->nama }}', false)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    {{-- Jika sudah final, tetap tampilkan tombol hapus dengan verifikasi --}}
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-900" 
                                            onclick="confirmDelete({{ $p->id }}, '{{ $p->nama }}', true)">
                                        <i class="fas fa-trash"></i>
                                        <span class="text-xs text-gray-400 ml-1">(Final)</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data pegawai</td>
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

<!-- ====== MODAL KONFIRMASI HAPUS DENGAN KODE KEAMANAN ====== -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-red-600">
                <i class="fas fa-exclamation-triangle mr-2"></i> Konfirmasi Hapus Penilaian
            </h3>
            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mb-4">
            <p class="text-gray-700">Anda akan menghapus penilaian untuk:</p>
            <p class="font-bold text-gray-900 text-lg mt-1" id="deletePegawaiName">-</p>
            <p class="text-sm text-red-500 mt-2" id="deleteFinalWarning" style="display: none;">
                <i class="fas fa-exclamation-circle mr-1"></i> 
                <strong>PERHATIAN:</strong> Penilaian ini sudah FINAL. Penghapusan akan menghapus semua data penilaian dan hasilnya.
            </p>
            <p class="text-sm text-gray-500 mt-2">Masukkan kode keamanan untuk melanjutkan:</p>
        </div>

        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            
            <div class="mb-4">
                <div class="flex items-center gap-3">
                    <input type="password" 
                           id="securityCode" 
                           
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <button type="button" onclick="togglePassword()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
                

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let deletePegawaiId = null;
    let deletePegawaiName = '';
    let deleteIsFinal = false;

    function confirmDelete(id, name, isFinal) {
        deletePegawaiId = id;
        deletePegawaiName = name;
        deleteIsFinal = isFinal;

        // Set nama pegawai
        document.getElementById('deletePegawaiName').textContent = name;

        // Tampilkan peringatan jika final
        const warning = document.getElementById('deleteFinalWarning');
        if (isFinal) {
            warning.style.display = 'block';
        } else {
            warning.style.display = 'none';
        }

        // Set action form
        const form = document.getElementById('deleteForm');
        form.action = '/admin/penilaian/' + id;

        // Reset input
        document.getElementById('securityCode').value = '';
        document.getElementById('securityCode').type = 'password';

        // Tampilkan modal
        document.getElementById('deleteConfirmModal').classList.remove('hidden');
        document.getElementById('deleteConfirmModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').classList.add('hidden');
        document.getElementById('deleteConfirmModal').classList.remove('flex');
    }

    function togglePassword() {
        const input = document.getElementById('securityCode');
        const icon = document.getElementById('togglePasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Validasi kode sebelum submit
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        const code = document.getElementById('securityCode').value;
        if (code !== '123321') {
            e.preventDefault();
            alert('❌ Kode keamanan salah! Silakan masukkan kode yang benar.');
            document.getElementById('securityCode').focus();
            document.getElementById('securityCode').value = '';
            return false;
        }
        // Jika kode benar, form akan submit
        return true;
    });

    // Tutup modal jika klik di luar
    window.onclick = function(event) {
        const modal = document.getElementById('deleteConfirmModal');
        if (event.target === modal) {
            closeDeleteModal();
        }
    }
</script>
@endpush
@endsection