@extends('layouts.admin')

@section('title', 'Edit Pengajuan Cuti')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Pengajuan Cuti</h1>
            <p class="text-sm text-gray-500 mt-1">Edit pengajuan cuti pegawai</p>
        </div>
        <a href="{{ route('admin.cuti.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.cuti.update', $cuti->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pegawai -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pegawai <span class="text-red-500">*</span></label>
                <select name="pegawai_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('pegawai_id') border-red-500 @enderror">
                    <option value="">Pilih Pegawai</option>
                    @foreach($pegawai as $p)
                        <option value="{{ $p->id }}" {{ old('pegawai_id', $cuti->pegawai_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ $p->nip }}) - {{ $p->jabatan->nama ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('pegawai_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Cuti -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti <span class="text-red-500">*</span></label>
                <select name="jenis_cuti" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('jenis_cuti') border-red-500 @enderror">
                    <option value="">Pilih Jenis Cuti</option>
                    <option value="tahunan" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                    <option value="sakit" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'sakit' ? 'selected' : '' }}>Cuti Sakit</option>
                    <option value="melahirkan" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'melahirkan' ? 'selected' : '' }}>Cuti Melahirkan</option>
                    <option value="khusus" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'khusus' ? 'selected' : '' }}>Cuti Khusus</option>
                    <option value="lainnya" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('jenis_cuti')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Cuti <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_mulai" required
                       value="{{ old('tanggal_mulai', $cuti->tanggal_mulai->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('tanggal_mulai') border-red-500 @enderror">
                @error('tanggal_mulai')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lama Hari -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lama Cuti (Hari Kerja) <span class="text-red-500">*</span></label>
                <input type="number" name="lama_hari" required
                       value="{{ old('lama_hari', $cuti->lama_hari) }}"
                       min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('lama_hari') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Masukkan jumlah hari kerja (Senin-Jumat)</p>
                @error('lama_hari')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('keterangan') border-red-500 @enderror"
                          placeholder="Alasan cuti (opsional)">{{ old('keterangan', $cuti->keterangan) }}</textarea>
                @error('keterangan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Perhitungan Cuti -->
            <div class="md:col-span-2">
                <div id="info-cuti" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                    <div class="text-sm text-blue-800 space-y-1">
                        <p><i class="fas fa-calendar-check mr-2"></i> <strong>Tanggal Selesai Cuti:</strong> <span id="tanggal-selesai"></span></p>
                        <p><i class="fas fa-calendar-alt mr-2"></i> <strong>Total Hari Kalender:</strong> <span id="total-hari"></span> hari</p>
                        <p><i class="fas fa-briefcase mr-2"></i> <strong>Hari Kerja:</strong> <span id="hari-kerja"></span> hari</p>
                        <p><i class="fas fa-calendar-week mr-2"></i> <strong>Hari Libur (Sabtu/Minggu):</strong> <span id="hari-libur"></span> hari</p>
                    </div>
                </div>
            </div>

            <!-- Info Saldo Cuti -->
            <div class="md:col-span-2">
                <div id="info-saldo" class="bg-green-50 border border-green-200 rounded-lg p-4 hidden">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="saldo-cuti-text">Sisa cuti: 0 hari</span>
                    </p>
                </div>
            </div>

            <!-- Info Status -->
            <div class="md:col-span-2">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Status Saat Ini:</strong> 
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($cuti->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($cuti->status == 'disetujui') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($cuti->status) }}
                        </span>
                        <span class="text-xs text-gray-500 ml-2">
                            (Hanya cuti dengan status <strong>Pending</strong> yang dapat diedit)
                        </span>
                    </p>
                </div>
            </div>

            <!-- Informasi Hari Kerja -->
            <div class="md:col-span-2">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Informasi:</strong> Perhitungan cuti hanya menghitung hari kerja (Senin-Jumat). 
                        Sabtu dan Minggu tidak dihitung sebagai hari cuti.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('admin.cuti.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-save mr-2"></i> Update Cuti
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let currentPegawaiId = {{ $cuti->pegawai_id }};
    
    // Auto calculate when fields change
    document.querySelector('input[name="tanggal_mulai"]').addEventListener('change', calculateCuti);
    document.querySelector('input[name="lama_hari"]').addEventListener('input', calculateCuti);
    document.querySelector('select[name="pegawai_id"]').addEventListener('change', function() {
        currentPegawaiId = this.value;
        getSaldo(this.value);
        calculateCuti();
    });

    function calculateCuti() {
        const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]').value;
        const lamaHari = document.querySelector('input[name="lama_hari"]').value;
        
        if (tanggalMulai && lamaHari && parseInt(lamaHari) > 0) {
            // Fetch calculation via AJAX
            fetch('/admin/cuti/calculate-date-range', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    tanggal_mulai: tanggalMulai,
                    lama_hari: lamaHari
                })
            })
            .then(response => response.json())
            .then(data => {
                const info = document.getElementById('info-cuti');
                info.classList.remove('hidden');
                
                document.getElementById('tanggal-selesai').textContent = data.tanggal_selesai_format;
                document.getElementById('total-hari').textContent = data.total_hari_kalender;
                document.getElementById('hari-kerja').textContent = data.hari_kerja;
                document.getElementById('hari-libur').textContent = data.hari_libur;
            })
            .catch(() => {
                document.getElementById('info-cuti').classList.add('hidden');
            });
        } else {
            document.getElementById('info-cuti').classList.add('hidden');
        }
    }

    function getSaldo(pegawaiId) {
        const info = document.getElementById('info-saldo');
        
        if (pegawaiId) {
            // Fetch saldo cuti via AJAX
            fetch('/admin/cuti/saldo-json/' + pegawaiId)
                .then(response => response.json())
                .then(data => {
                    info.classList.remove('hidden');
                    if (data.saldo) {
                        document.getElementById('saldo-cuti-text').innerHTML = 
                            '<strong>Sisa cuti:</strong> ' + data.saldo.sisa_hari + ' hari' +
                            '<br><span class="text-xs text-gray-600">' +
                            '(Total: ' + data.saldo.total_hari + ' hari, Digunakan: ' + data.saldo.digunakan + ' hari)' +
                            '</span>';
                    } else {
                        document.getElementById('saldo-cuti-text').textContent = 'Belum ada data saldo cuti untuk tahun ini';
                    }
                })
                .catch(() => {
                    info.classList.add('hidden');
                });
        } else {
            info.classList.add('hidden');
        }
    }

    // Check on page load
    document.addEventListener('DOMContentLoaded', function() {
        const pegawaiSelect = document.querySelector('select[name="pegawai_id"]');
        if (pegawaiSelect.value) {
            getSaldo(pegawaiSelect.value);
        }
        
        const mulai = document.querySelector('input[name="tanggal_mulai"]').value;
        const lamaHari = document.querySelector('input[name="lama_hari"]').value;
        if (mulai && lamaHari) {
            calculateCuti();
        }
    });
</script>
@endpush
@endsection