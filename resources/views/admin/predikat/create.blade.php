@extends('layouts.admin')

@section('title', 'Tambah Predikat Kinerja')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Predikat Baru</h1>
            <p class="text-sm text-gray-500">Buat predikat kinerja pegawai baru</p>
        </div>
        <a href="{{ route('admin.predikat.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.predikat.store') }}" method="POST" id="formPredikat">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Predikat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Predikat <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama_predikat" value="{{ old('nama') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nama') border-red-500 @enderror"
                           placeholder="Contoh: Sangat Baik, Baik, Cukup, Kurang">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Deskripsi singkat predikat">
                </div>

                <!-- Batas Bawah -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Batas Bawah <span class="text-red-500">*</span></label>
                    <input type="number" name="batas_bawah" id="batas_bawah" value="{{ old('batas_bawah') }}" required
                           step="0.01" min="0" max="100"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('batas_bawah') border-red-500 @enderror"
                           placeholder="0">
                    @error('batas_bawah')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Atas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Batas Atas <span class="text-red-500">*</span></label>
                    <input type="number" name="batas_atas" id="batas_atas" value="{{ old('batas_atas') }}" required
                           step="0.01" min="0" max="100"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('batas_atas') border-red-500 @enderror"
                           placeholder="100">
                    @error('batas_atas')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warna Teks -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna Teks <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="warna_text" id="warna_text" 
                               value="{{ old('warna_text', '#000000') }}"
                               class="w-12 h-12 p-1 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" name="warna_text_hex" id="warna_text_hex"
                               value="{{ old('warna_text', '#000000') }}"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="#000000">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Pilih warna teks untuk predikat</p>
                    @error('warna_text')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warna Latar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna Latar <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="warna_latar" id="warna_latar"
                               value="{{ old('warna_latar', '#e5e7eb') }}"
                               class="w-12 h-12 p-1 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" name="warna_latar_hex" id="warna_latar_hex"
                               value="{{ old('warna_latar', '#e5e7eb') }}"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="#e5e7eb">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Pilih warna latar untuk predikat</p>
                    @error('warna_latar')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Preview -->
            <div class="mt-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Preview</h4>
                <div class="flex items-center gap-4">
                    <span id="preview-badge" 
                          class="px-4 py-2 rounded-full text-sm font-semibold"
                          style="color: {{ old('warna_text', '#000000') }}; background-color: {{ old('warna_latar', '#e5e7eb') }}">
                        {{ old('nama', 'Predikat') }}
                    </span>
                    <span class="text-sm text-gray-500">
                        Nilai: {{ old('batas_bawah', '0') }} - {{ old('batas_atas', '100') }}
                    </span>
                </div>
            </div>

            <!-- Validasi Range -->
            <div id="range-warning" class="mt-3 hidden p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span id="range-message">Batas bawah harus lebih kecil dari batas atas</span>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.predikat.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Simpan Predikat
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Sync color picker with text input
    document.getElementById('warna_text').addEventListener('input', function() {
        document.getElementById('warna_text_hex').value = this.value;
        updatePreview();
    });

    document.getElementById('warna_text_hex').addEventListener('input', function() {
        document.getElementById('warna_text').value = this.value;
        updatePreview();
    });

    document.getElementById('warna_latar').addEventListener('input', function() {
        document.getElementById('warna_latar_hex').value = this.value;
        updatePreview();
    });

    document.getElementById('warna_latar_hex').addEventListener('input', function() {
        document.getElementById('warna_latar').value = this.value;
        updatePreview();
    });

    // Update preview on input changes
    document.getElementById('nama_predikat').addEventListener('input', updatePreview);
    document.getElementById('batas_bawah').addEventListener('input', function() {
        updatePreview();
        validateRange();
    });
    document.getElementById('batas_atas').addEventListener('input', function() {
        updatePreview();
        validateRange();
    });

    function updatePreview() {
        const nama = document.getElementById('nama_predikat').value || 'Predikat';
        const warnaText = document.getElementById('warna_text_hex').value || '#000000';
        const warnaLatar = document.getElementById('warna_latar_hex').value || '#e5e7eb';
        const batasBawah = document.getElementById('batas_bawah').value || '0';
        const batasAtas = document.getElementById('batas_atas').value || '100';
        
        const preview = document.getElementById('preview-badge');
        preview.textContent = nama;
        preview.style.color = warnaText;
        preview.style.backgroundColor = warnaLatar;
        
        // Update nilai di preview
        const nilaiSpan = document.querySelector('.text-gray-500');
        if (nilaiSpan) {
            nilaiSpan.textContent = `Nilai: ${batasBawah} - ${batasAtas}`;
        }
    }

    function validateRange() {
        const bawah = parseFloat(document.getElementById('batas_bawah').value);
        const atas = parseFloat(document.getElementById('batas_atas').value);
        const warning = document.getElementById('range-warning');
        const message = document.getElementById('range-message');
        
        if (!isNaN(bawah) && !isNaN(atas)) {
            if (bawah >= atas) {
                warning.classList.remove('hidden');
                message.textContent = 'Batas bawah (' + bawah + ') harus lebih kecil dari batas atas (' + atas + ')';
                document.getElementById('batas_bawah').classList.add('border-red-500');
                document.getElementById('batas_atas').classList.add('border-red-500');
            } else {
                warning.classList.add('hidden');
                document.getElementById('batas_bawah').classList.remove('border-red-500');
                document.getElementById('batas_atas').classList.remove('border-red-500');
            }
        }
    }

    // Validasi sebelum submit
    document.getElementById('formPredikat').addEventListener('submit', function(e) {
        const bawah = parseFloat(document.getElementById('batas_bawah').value);
        const atas = parseFloat(document.getElementById('batas_atas').value);
        
        if (bawah >= atas) {
            e.preventDefault();
            alert('Batas bawah harus lebih kecil dari batas atas!');
            return false;
        }
    });

    // Initial preview
    updatePreview();
</script>
@endpush
@endsection