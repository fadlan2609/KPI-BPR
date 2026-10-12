@extends('layouts.pegawai')

@section('title', 'Upload Surat Dokter')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Upload Surat Dokter</h1>
                <p class="text-sm text-gray-500">Untuk cuti sakit yang sudah disetujui</p>
            </div>
            <a href="{{ route('pegawai.cuti.index') }}"
               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Info Cuti --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-1"></i> Detail Cuti
            </h3>
            <table class="text-sm text-blue-800">
                <tr>
                    <td class="pr-3 py-1">Jenis Cuti</td>
                    <td class="pr-2">:</td>
                    <td class="font-semibold">Cuti {{ ucfirst($cuti->jenis_cuti) }}</td>
                </tr>
                <tr>
                    <td class="pr-3 py-1">Tanggal Mulai</td>
                    <td class="pr-2">:</td>
                    <td>{{ $cuti->tanggal_mulai->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr>
                    <td class="pr-3 py-1">Tanggal Selesai</td>
                    <td class="pr-2">:</td>
                    <td>{{ $cuti->tanggal_selesai->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr>
                    <td class="pr-3 py-1">Lama Cuti</td>
                    <td class="pr-2">:</td>
                    <td>{{ $cuti->lama_hari }} hari kerja</td>
                </tr>
            </table>
        </div>

        {{-- Info Surat --}}
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded mb-6">
            <p class="text-sm">
                <i class="fas fa-info-circle mr-1"></i>
                Cuti sakit Anda sudah <strong>disetujui final</strong>.
                Silakan upload surat dokter sebagai bukti. Surat akan diverifikasi oleh admin.
            </p>
        </div>

        @if($cuti->surat_dokter_status === 'ditolak')
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded mb-6">
                <p class="text-sm">
                    <i class="fas fa-times-circle mr-1"></i>
                    <strong>Surat dokter sebelumnya ditolak.</strong>
                    Silakan upload ulang dengan surat yang lebih jelas.
                </p>
            </div>
        @endif

        {{-- Form Upload --}}
        <form action="{{ route('pegawai.cuti.upload-surat', $cuti->id) }}" method="POST"
              enctype="multipart/form-data" id="formUpload">
            @csrf

            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer"
                 id="drop-zone">
                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                <p class="text-sm text-gray-600 mb-2">
                    <strong>Klik untuk pilih file</strong> atau drag & drop
                </p>
                <p class="text-xs text-gray-500">
                    PDF, JPG, PNG, WEBP • Maks 5MB
                </p>

                <input type="file" name="surat_dokter" id="surat_dokter"
                       accept=".pdf,.jpg,.jpeg,.png,.webp" class="hidden" required>
            </div>

            {{-- Preview --}}
            <div id="preview-container" class="mt-4 hidden">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-2">File terpilih:</p>
                    <div class="flex items-center space-x-3">
                        <div id="preview-icon"
                             class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file text-red-600 text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p id="preview-nama" class="text-sm font-medium text-gray-800 truncate"></p>
                            <p id="preview-ukuran" class="text-xs text-gray-500"></p>
                        </div>
                        <button type="button" onclick="resetFile()"
                                class="text-red-500 hover:text-red-700 flex-shrink-0">
                            <i class="fas fa-times-circle text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <p id="error-file" class="text-sm text-red-600 mt-2 hidden">
                <i class="fas fa-exclamation-circle mr-1"></i>
                <span></span>
            </p>

            <div class="flex justify-end space-x-2 mt-6 pt-4 border-t">
                <a href="{{ route('pegawai.cuti.index') }}"
                   class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                    Batal
                </a>
                <button type="submit" id="btn-submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-upload mr-2"></i> Upload Surat Dokter
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('surat_dokter');
    const previewContainer = document.getElementById('preview-container');
    const errorFile = document.getElementById('error-file');
    const MAX_SIZE = 5 * 1024 * 1024;
    const ALLOWED = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            handleFile(fileInput.files[0]);
        }
    });

    function handleFile(file) {
        errorFile.classList.add('hidden');

        if (file.size > MAX_SIZE) {
            errorFile.querySelector('span').textContent =
                'File terlalu besar (' + (file.size / 1024 / 1024).toFixed(1) + ' MB). Maks 5MB.';
            errorFile.classList.remove('hidden');
            resetFile();
            return;
        }

        if (!ALLOWED.includes(file.type)) {
            errorFile.querySelector('span').textContent =
                'Format file tidak didukung. Gunakan PDF, JPG, PNG, atau WEBP.';
            errorFile.classList.remove('hidden');
            resetFile();
            return;
        }

        previewContainer.classList.remove('hidden');
        document.getElementById('preview-nama').textContent = file.name;
        document.getElementById('preview-ukuran').textContent =
            (file.size / 1024).toFixed(1) + ' KB';

        const icon = document.getElementById('preview-icon');
        if (file.type === 'application/pdf') {
            icon.innerHTML = '<i class="fas fa-file-pdf text-red-600 text-xl"></i>';
        } else {
            icon.innerHTML = '<i class="fas fa-file-image text-blue-600 text-xl"></i>';
            icon.className = 'w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0';
        }
    }

    function resetFile() {
        fileInput.value = '';
        previewContainer.classList.add('hidden');
    }
</script>
@endpush
@endsection