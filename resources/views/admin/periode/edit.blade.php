@extends('layouts.admin')

@section('title', 'Edit Periode Penilaian')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Periode Penilaian</h1>
            <p class="text-sm text-gray-500">Perbarui data periode penilaian kinerja</p>
        </div>
        <a href="{{ route('admin.periode.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.periode.update', $periode->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Periode -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Periode <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $periode->nama) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nama') border-red-500 @enderror"
                           placeholder="Contoh: Periode Penilaian Semester 1 2026">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $periode->tanggal_mulai->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('tanggal_mulai') border-red-500 @enderror">
                    @error('tanggal_mulai')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $periode->tanggal_selesai->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('tanggal_selesai') border-red-500 @enderror">
                    @error('tanggal_selesai')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Self Assessment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Self Assessment</label>
                    <input type="date" name="batas_self_assessment" value="{{ old('batas_self_assessment', $periode->batas_self_assessment?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('batas_self_assessment') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Batas akhir pegawai mengisi self assessment</p>
                    @error('batas_self_assessment')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Penilaian Atasan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Penilaian Atasan</label>
                    <input type="date" name="batas_penilaian_atasan" value="{{ old('batas_penilaian_atasan', $periode->batas_penilaian_atasan?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('batas_penilaian_atasan') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Batas akhir atasan menilai bawahan</p>
                    @error('batas_penilaian_atasan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Finalisasi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Finalisasi</label>
                    <input type="date" name="batas_finalisasi" value="{{ old('batas_finalisasi', $periode->batas_finalisasi?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('batas_finalisasi') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Batas akhir finalisasi penilaian</p>
                    @error('batas_finalisasi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="draft" {{ old('status', $periode->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $periode->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="closed" {{ old('status', $periode->status) == 'closed' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Status periode penilaian</p>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $periode->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                        Aktifkan Periode Ini
                    </label>
                    <p class="ml-4 text-xs text-gray-500">Hanya satu periode yang bisa aktif</p>
                </div>
            </div>

            <!-- Info Periode -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                    <div>
                        <p class="text-sm text-blue-700">
                            <strong>Durasi:</strong> 
                            {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($periode->tanggal_selesai)) + 1 }} hari
                        </p>
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Status Saat Ini:</strong> 
                            @if($periode->status == 'active')
                                <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                            @elseif($periode->status == 'closed')
                                <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs">Ditutup</span>
                            @else
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs">Draft</span>
                            @endif
                        </p>
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Dibuat:</strong> {{ $periode->created_at->format('d M Y H:i') }}
                        </p>
                        @if($periode->updated_at)
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Terakhir Diperbarui:</strong> {{ $periode->updated_at->format('d M Y H:i') }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Validasi Tanggal -->
            <div id="date-warning" class="mt-3 hidden p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span id="date-message">Tanggal selesai harus setelah tanggal mulai</span>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.periode.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Update Periode
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Validasi tanggal
    const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
    const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]');
    const dateWarning = document.getElementById('date-warning');
    const dateMessage = document.getElementById('date-message');

    tanggalMulai.addEventListener('change', validateDate);
    tanggalSelesai.addEventListener('change', validateDate);

    function validateDate() {
        const mulai = tanggalMulai.value;
        const selesai = tanggalSelesai.value;

        if (mulai && selesai) {
            if (selesai < mulai) {
                dateWarning.classList.remove('hidden');
                dateMessage.textContent = 'Tanggal selesai (' + formatDate(selesai) + ') harus setelah tanggal mulai (' + formatDate(mulai) + ')';
                tanggalSelesai.classList.add('border-red-500');
            } else {
                dateWarning.classList.add('hidden');
                tanggalSelesai.classList.remove('border-red-500');
            }
        }
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr + 'T00:00:00');
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return days[date.getDay()] + ', ' + date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
    }

    // Validasi sebelum submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const mulai = tanggalMulai.value;
        const selesai = tanggalSelesai.value;

        if (mulai && selesai && selesai < mulai) {
            e.preventDefault();
            alert('Tanggal selesai harus setelah tanggal mulai!');
            return false;
        }
    });

    // Preview durasi otomatis
    tanggalMulai.addEventListener('change', updateDuration);
    tanggalSelesai.addEventListener('change', updateDuration);

    function updateDuration() {
        const mulai = tanggalMulai.value;
        const selesai = tanggalSelesai.value;

        if (mulai && selesai && selesai >= mulai) {
            const start = new Date(mulai + 'T00:00:00');
            const end = new Date(selesai + 'T00:00:00');
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            const durasiSpan = document.querySelector('.text-blue-700 strong');
            if (durasiSpan && durasiSpan.textContent.includes('Durasi:')) {
                durasiSpan.parentElement.textContent = 'Durasi: ' + diffDays + ' hari';
            }
        }
    }

    // Initial validation
    validateDate();
    updateDuration();
</script>
@endpush
@endsection