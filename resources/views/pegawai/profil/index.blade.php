@extends('layouts.pegawai')

@section('title', 'Profil Saya')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
@endpush

@section('content')
<div class="space-y-6">

    {{-- ==================== HEADER ==================== --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
            <p class="text-sm text-gray-500">Kelola data pribadi, keluarga, pendidikan & tanda tangan Anda</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pegawai.profil.cetak-cv') }}" target="_blank"
               class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-bold">
                Cetak CV
            </a>
            <a href="{{ route('pegawai.dashboard') }}"
               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm font-bold">
                Kembali
            </a>
        </div>
    </div>

    {{-- ==================== ALERT ==================== --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KARTU PROFIL ==================== --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                {{-- Foto Profil --}}
                @if($pegawai->foto_profil && file_exists(storage_path('app/public/' . $pegawai->foto_profil)))
                    @php
                        $cx = ($pegawai->foto_crop_x ?? 0.5) * 100;
                        $cy = ($pegawai->foto_crop_y ?? 0.5) * 100;
                        $cz = $pegawai->foto_crop_zoom ?? 1;
                        $cr = $pegawai->foto_crop_rotate ?? 0;
                    @endphp
                    <img src="{{ route('storage.file', ['path' => $pegawai->foto_profil]) }}?v={{ time() }}"
                         alt="{{ $pegawai->nama }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-blue-100 mx-auto mb-4"
                         style="object-position: {{ $cx }}% {{ $cy }}%; transform: scale({{ $cz }}) rotate({{ $cr }}deg);">
                @else
                    <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">
                        {{ strtoupper(substr($pegawai->nama, 0, 1)) }}
                    </div>
                @endif

                <h2 class="text-lg font-bold">{{ $pegawai->nama }}</h2>
                <p class="text-sm text-gray-500">{{ $pegawai->nip }}</p>
                <p class="text-sm text-gray-600 mt-2">{{ $pegawai->jabatan->nama ?? '-' }}</p>
                <p class="text-xs text-gray-500">{{ $pegawai->kantor->nama ?? '-' }}</p>

                <div class="mt-4 pt-4 border-t">
                    <p class="text-xs text-gray-500 mb-2">Status TTD</p>
                    @if($pegawai->ttd_image)
                        <p class="text-sm text-green-600 font-medium">TTD sudah tersedia</p>
                    @else
                        <p class="text-sm text-yellow-600 font-medium">Belum ada TTD</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ==================== FORM PROFIL ==================== --}}
        <div class="lg:col-span-2">
            <form action="{{ route('pegawai.profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ============ A. DATA PRIBADI ============ --}}
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="border-b px-6 py-4 bg-gray-50">
                        <h3 class="font-semibold text-gray-800">A. Data Pribadi</h3>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" name="nama" required value="{{ old('nama', $pegawai->nama) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                            <input type="text" value="{{ $pegawai->nip }}" disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $pegawai->nik) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $pegawai->no_hp) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $pegawai->email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                   value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                            <input type="text" name="status_pernikahan" value="{{ old('status_pernikahan', $pegawai->status_pernikahan) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasangan</label>
                            <input type="text" name="nama_pasangan" value="{{ old('nama_pasangan', $pegawai->nama_pasangan) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Anak</label>
                            <input type="number" name="jumlah_anak" min="0" value="{{ old('jumlah_anak', $pegawai->jumlah_anak ?? 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="alamat" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('alamat', $pegawai->alamat) }}</textarea>
                        </div>

                        {{-- FOTO PROFIL DENGAN CROP --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>

                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    @if($pegawai->foto_profil && file_exists(storage_path('app/public/' . $pegawai->foto_profil)))
                                        @php
                                            $cx = ($pegawai->foto_crop_x ?? 0.5) * 100;
                                            $cy = ($pegawai->foto_crop_y ?? 0.5) * 100;
                                            $cz = $pegawai->foto_crop_zoom ?? 1;
                                            $cr = $pegawai->foto_crop_rotate ?? 0;
                                        @endphp
                                        <img id="preview-foto"
                                             src="{{ route('storage.file', ['path' => $pegawai->foto_profil]) }}?v={{ time() }}"
                                             alt="Foto Profil"
                                             class="w-24 h-24 rounded-full object-cover border-2 border-gray-300"
                                             style="object-position: {{ $cx }}% {{ $cy }}%; transform: scale({{ $cz }}) rotate({{ $cr }}deg);">
                                    @else
                                        <div id="preview-placeholder" class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                            <span class="text-3xl font-bold text-gray-500">
                                                {{ strtoupper(substr($pegawai->nama, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <input type="file" id="input-foto-profil" name="foto_profil" accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WEBP. Max 2 MB.</p>

                                    {{-- Hidden input crop --}}
                                    <input type="hidden" name="foto_crop_x" id="foto_crop_x" value="{{ $pegawai->foto_crop_x ?? 0.5 }}">
                                    <input type="hidden" name="foto_crop_y" id="foto_crop_y" value="{{ $pegawai->foto_crop_y ?? 0.5 }}">
                                    <input type="hidden" name="foto_crop_zoom" id="foto_crop_zoom" value="{{ $pegawai->foto_crop_zoom ?? 1 }}">
                                    <input type="hidden" name="foto_crop_rotate" id="foto_crop_rotate" value="{{ $pegawai->foto_crop_rotate ?? 0 }}">

                                    @if($pegawai->foto_profil)
                                        <div class="mt-2 flex gap-2 flex-wrap items-center">
                                            <button type="button" onclick="bukaModalCrop()"
                                                    class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded font-bold">
                                                <i class="fas fa-crop"></i> Atur Posisi
                                            </button>
                                            <label class="inline-flex items-center text-xs text-red-600">
                                                <input type="checkbox" name="hapus_foto" value="1" class="mr-1">
                                                Hapus foto
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============ B. TANDA TANGAN ============ --}}
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="border-b px-6 py-4 bg-gray-50">
                        <h3 class="font-semibold text-gray-800">B. Tanda Tangan / Barcode</h3>
                    </div>

                    <div class="p-6">
                        @if($pegawai->ttd_image)
                            <div class="mb-4 p-4 border border-gray-300 rounded bg-gray-50 inline-block">
                                <p class="text-xs text-gray-600 mb-2"><strong>TTD Saat Ini:</strong></p>
                                <img src="{{ route('storage.file', ['path' => $pegawai->ttd_image]) }}"
                                     alt="TTD" class="h-20 object-contain">
                                <div class="mt-2">
                                    <label class="inline-flex items-center text-xs text-red-600">
                                        <input type="checkbox" name="hapus_ttd" value="1" class="mr-1">
                                        Hapus TTD ini
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jenis TTD</label>
                                <select name="ttd_type" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="gambar" {{ old('ttd_type', $pegawai->ttd_type) == 'gambar' ? 'selected' : '' }}>Gambar Tanda Tangan</option>
                                    <option value="barcode" {{ old('ttd_type', $pegawai->ttd_type) == 'barcode' ? 'selected' : '' }}>Barcode / QR Code</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                                <input type="file" name="ttd_image" accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <p class="text-xs text-gray-500 mt-1">PNG transparan, max 2 MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============ C. DATA ISTRI/SUAMI ============ --}}
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="border-b px-6 py-4 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">C. Data Istri/Suami</h3>
                        <button type="button" onclick="addKeluarga()"
                                class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-bold">+ Tambah</button>
                    </div>
                    <div class="p-6" id="keluarga-container">
                        @forelse($pegawai->keluarga as $i => $k)
                            <div class="border border-gray-200 rounded-lg p-4 mb-3 keluarga-item bg-gray-50">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-bold text-sm">Istri/Suami #{{ $i + 1 }}</h4>
                                    <button type="button" onclick="this.closest('.keluarga-item').remove()" class="text-red-600 text-sm">Hapus</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div><label class="block text-xs font-medium mb-1">Hubungan</label>
                                        <select name="keluarga[{{ $i }}][hubungan]" class="w-full px-2 py-1 border rounded text-sm">
                                            <option value="istri" {{ $k->hubungan == 'istri' ? 'selected' : '' }}>Istri</option>
                                            <option value="suami" {{ $k->hubungan == 'suami' ? 'selected' : '' }}>Suami</option>
                                        </select>
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Nama</label>
                                        <input type="text" name="keluarga[{{ $i }}][nama]" value="{{ $k->nama }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">NIK</label>
                                        <input type="text" name="keluarga[{{ $i }}][nik]" value="{{ $k->nik }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Tempat Lahir</label>
                                        <input type="text" name="keluarga[{{ $i }}][tempat_lahir]" value="{{ $k->tempat_lahir }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Tanggal Lahir</label>
                                        <input type="date" name="keluarga[{{ $i }}][tanggal_lahir]" value="{{ $k->tanggal_lahir?->format('Y-m-d') }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Jenis Kelamin</label>
                                        <select name="keluarga[{{ $i }}][jenis_kelamin]" class="w-full px-2 py-1 border rounded text-sm">
                                            <option value="L" {{ $k->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ $k->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Pendidikan Terakhir</label>
                                        <input type="text" name="keluarga[{{ $i }}][pendidikan_terakhir]" value="{{ $k->pendidikan_terakhir }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Pekerjaan</label>
                                        <input type="text" name="keluarga[{{ $i }}][pekerjaan]" value="{{ $k->pekerjaan }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">No. Telp</label>
                                        <input type="text" name="keluarga[{{ $i }}][no_telp]" value="{{ $k->no_telp }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                </div>

                                {{-- ✅ Riwayat Sekolah Istri/Suami --}}
                                <div class="ml-4 border-l-2 border-purple-300 pl-3 mt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-gray-700">Riwayat Sekolah Istri/Suami</span>
                                        <button type="button" onclick="addPendidikanKeluarga({{ $i }})" class="text-xs text-purple-600 hover:underline font-bold">+ Tambah Sekolah</button>
                                    </div>
                                    <div id="pendidikan-keluarga-{{ $i }}">
                                        @foreach($k->pendidikan as $j => $p)
                                            <div class="grid grid-cols-6 gap-2 mb-1 pendidikan-keluarga-item">
                                                <select name="keluarga[{{ $i }}][pendidikan][{{ $j }}][jenjang]" class="px-1 py-0.5 border rounded text-xs">
                                                    @foreach(['SD','SMP','SMA/SMK','D3','S1','S2','S3'] as $jgj)
                                                        <option value="{{ $jgj }}" {{ $p->jenjang == $jgj ? 'selected' : '' }}>{{ $jgj }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="keluarga[{{ $i }}][pendidikan][{{ $j }}][nama_sekolah]" value="{{ $p->nama_sekolah }}" placeholder="Nama Sekolah" class="px-1 py-0.5 border rounded text-xs">
                                                <input type="text" name="keluarga[{{ $i }}][pendidikan][{{ $j }}][jurusan]" value="{{ $p->jurusan }}" placeholder="Jurusan" class="px-1 py-0.5 border rounded text-xs">
                                                <input type="text" name="keluarga[{{ $i }}][pendidikan][{{ $j }}][kota]" value="{{ $p->kota }}" placeholder="Kota" class="px-1 py-0.5 border rounded text-xs">
                                                <input type="number" name="keluarga[{{ $i }}][pendidikan][{{ $j }}][tahun_masuk]" value="{{ $p->tahun_masuk }}" placeholder="Masuk" class="px-1 py-0.5 border rounded text-xs">
                                                <input type="number" name="keluarga[{{ $i }}][pendidikan][{{ $j }}][tahun_lulus]" value="{{ $p->tahun_lulus }}" placeholder="Lulus" class="px-1 py-0.5 border rounded text-xs">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-4 text-sm">Belum ada data istri/suami. Klik tombol "+ Tambah" di atas.</p>
                        @endforelse
                    </div>
                </div>

                {{-- ============ D. DATA ANAK ============ --}}
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="border-b px-6 py-4 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">D. Data Anak</h3>
                        <button type="button" onclick="addAnak()"
                                class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-bold">+ Tambah Anak</button>
                    </div>
                    <div class="p-6" id="anak-container">
                        @forelse($pegawai->anak as $i => $a)
                            <div class="border border-gray-200 rounded-lg p-4 mb-3 anak-item bg-gray-50">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-bold text-sm">Anak #{{ $i + 1 }}</h4>
                                    <button type="button" onclick="this.closest('.anak-item').remove()" class="text-red-600 text-sm">Hapus</button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                    <div><label class="block text-xs font-medium mb-1">Nama Anak</label>
                                        <input type="text" name="anak[{{ $i }}][nama]" value="{{ $a->nama }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">NIK</label>
                                        <input type="text" name="anak[{{ $i }}][nik]" value="{{ $a->nik }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Tanggal Lahir</label>
                                        <input type="date" name="anak[{{ $i }}][tanggal_lahir]" value="{{ $a->tanggal_lahir?->format('Y-m-d') }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Jenis Kelamin</label>
                                        <select name="anak[{{ $i }}][jenis_kelamin]" class="w-full px-2 py-1 border rounded text-sm">
                                            <option value="L" {{ $a->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ $a->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Anak Ke-</label>
                                        <input type="number" name="anak[{{ $i }}][urutan_anak]" value="{{ $a->urutan_anak }}" class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                    <div><label class="block text-xs font-medium mb-1">Status Anak</label>
                                        <select name="anak[{{ $i }}][status_anak]" class="w-full px-2 py-1 border rounded text-sm">
                                            <option value="kandung" {{ $a->status_anak == 'kandung' ? 'selected' : '' }}>Kandung</option>
                                            <option value="tiri" {{ $a->status_anak == 'tiri' ? 'selected' : '' }}>Tiri</option>
                                            <option value="angkat" {{ $a->status_anak == 'angkat' ? 'selected' : '' }}>Angkat</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- ✅ Riwayat Sekolah Anak --}}
                                <div class="ml-4 border-l-2 border-blue-300 pl-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold">Riwayat Sekolah Anak</span>
                                        <button type="button" onclick="addPendidikanAnak({{ $i }})" class="text-xs text-blue-600 hover:underline font-bold">+ Tambah Sekolah</button>
                                    </div>
                                    <div id="pendidikan-anak-{{ $i }}">
                                        @foreach($a->pendidikan as $j => $p)
                                            <div class="grid grid-cols-5 gap-2 mb-1 pendidikan-item">
                                                <select name="anak[{{ $i }}][pendidikan][{{ $j }}][jenjang]" class="px-1 py-0.5 border rounded text-xs">
                                                    @foreach(['TK','SD','SMP','SMA/SMK','D3','S1','S2','S3'] as $jgj)
                                                        <option value="{{ $jgj }}" {{ $p->jenjang == $jgj ? 'selected' : '' }}>{{ $jgj }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="anak[{{ $i }}][pendidikan][{{ $j }}][nama_sekolah]" value="{{ $p->nama_sekolah }}" placeholder="Nama Sekolah" class="px-1 py-0.5 border rounded text-xs">
                                                <input type="text" name="anak[{{ $i }}][pendidikan][{{ $j }}][kota]" value="{{ $p->kota }}" placeholder="Kota" class="px-1 py-0.5 border rounded text-xs">
                                                <input type="number" name="anak[{{ $i }}][pendidikan][{{ $j }}][tahun_masuk]" value="{{ $p->tahun_masuk }}" placeholder="Masuk" class="px-1 py-0.5 border rounded text-xs">
                                                <input type="number" name="anak[{{ $i }}][pendidikan][{{ $j }}][tahun_lulus]" value="{{ $p->tahun_lulus }}" placeholder="Lulus" class="px-1 py-0.5 border rounded text-xs">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-4 text-sm">Belum ada data anak. Klik tombol "+ Tambah Anak" di atas.</p>
                        @endforelse
                    </div>
                </div>

                {{-- ============ E. PENDIDIKAN PEGAWAI ============ --}}
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="border-b px-6 py-4 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">E. Riwayat Pendidikan Pegawai</h3>
                        <button type="button" onclick="addPendidikan()"
                                class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-bold">+ Tambah</button>
                    </div>
                    <div class="p-6" id="pendidikan-container">
                        @forelse($pegawai->pendidikan as $i => $p)
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-2 mb-2 pendidikan-pegawai-item">
                                <select name="pendidikan[{{ $i }}][jenjang]" class="px-2 py-1 border rounded text-sm">
                                    @foreach(['SD','SMP','SMA/SMK','D3','S1','S2','S3'] as $jgj)
                                        <option value="{{ $jgj }}" {{ $p->jenjang == $jgj ? 'selected' : '' }}>{{ $jgj }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="pendidikan[{{ $i }}][nama_sekolah]" value="{{ $p->nama_sekolah }}" placeholder="Nama Sekolah" class="px-2 py-1 border rounded text-sm">
                                <input type="text" name="pendidikan[{{ $i }}][jurusan]" value="{{ $p->jurusan }}" placeholder="Jurusan" class="px-2 py-1 border rounded text-sm">
                                <input type="text" name="pendidikan[{{ $i }}][kota]" value="{{ $p->kota }}" placeholder="Kota" class="px-2 py-1 border rounded text-sm">
                                <input type="number" name="pendidikan[{{ $i }}][tahun_masuk]" value="{{ $p->tahun_masuk }}" placeholder="Masuk" class="px-2 py-1 border rounded text-sm">
                                <input type="number" name="pendidikan[{{ $i }}][tahun_lulus]" value="{{ $p->tahun_lulus }}" placeholder="Lulus" class="px-2 py-1 border rounded text-sm">
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-4 text-sm">Belum ada riwayat pendidikan. Klik tombol "+ Tambah" di atas.</p>
                        @endforelse
                    </div>
                </div>

                {{-- ============ TOMBOL ============ --}}
                <div class="flex justify-end gap-2 mb-6">
                    <a href="{{ route('pegawai.profil.cetak-cv') }}" target="_blank"
                       class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow">
                        Cetak CV
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow">
                        Simpan Profil
                    </button>
                </div>
            </form>

            {{-- ============ FORM GANTI PASSWORD ============ --}}
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="border-b px-6 py-4 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Ganti Password</h3>
                </div>

                <form action="{{ route('pegawai.profil.update-password') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama *</label>
                        <input type="password" name="current_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        @error('current_password')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru *</label>
                        <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                        @error('password')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru *</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-bold">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- MODAL CROP --}}
<div id="modal-crop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:20px; max-width:600px; width:90%; max-height:90vh; overflow-y:auto;">
        <h3 style="font-weight:bold; font-size:18px; margin-bottom:12px; color:#1e293b;">
            <i class="fas fa-crop"></i> Atur Posisi Foto Profil
        </h3>

        <div style="max-height:400px; overflow:hidden; background:#f1f5f9; border-radius:8px;">
            <img id="img-crop" src="" style="max-width:100%; display:block;">
        </div>

        <p style="font-size:12px; color:#64748b; margin-top:8px;">
            <i class="fas fa-info-circle"></i> Drag untuk menggeser, scroll untuk zoom.
        </p>

        <div style="margin-top:16px; display:flex; gap:8px; justify-content:flex-end;">
            <button type="button" onclick="tutupModalCrop()"
                    style="padding:8px 16px; background:#94a3b8; color:white; border-radius:8px; font-weight:bold; border:none; cursor:pointer;">
                Batal
            </button>
            <button type="button" onclick="simpanCrop()"
                    style="padding:8px 16px; background:#3b82f6; color:white; border-radius:8px; font-weight:bold; border:none; cursor:pointer;">
                <i class="fas fa-save"></i> Simpan Posisi
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
// ==================== KELUARGA ====================
let keluargaCount = {{ $pegawai->keluarga->count() ?? 0 }};

function addKeluarga() {
    const i = keluargaCount++;
    const html = `
        <div class="border border-gray-200 rounded-lg p-4 mb-3 keluarga-item bg-gray-50">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-bold text-sm">Istri/Suami Baru</h4>
                <button type="button" onclick="this.closest('.keluarga-item').remove()" class="text-red-600 text-sm">Hapus</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium mb-1">Hubungan</label>
                    <select name="keluarga[${i}][hubungan]" class="w-full px-2 py-1 border rounded text-sm">
                        <option value="istri">Istri</option><option value="suami">Suami</option>
                    </select>
                </div>
                <div><label class="block text-xs font-medium mb-1">Nama</label><input type="text" name="keluarga[${i}][nama]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">NIK</label><input type="text" name="keluarga[${i}][nik]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">Tempat Lahir</label><input type="text" name="keluarga[${i}][tempat_lahir]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">Tanggal Lahir</label><input type="date" name="keluarga[${i}][tanggal_lahir]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">Jenis Kelamin</label>
                    <select name="keluarga[${i}][jenis_kelamin]" class="w-full px-2 py-1 border rounded text-sm">
                        <option value="L">Laki-laki</option><option value="P">Perempuan</option>
                    </select>
                </div>
                <div><label class="block text-xs font-medium mb-1">Pendidikan Terakhir</label><input type="text" name="keluarga[${i}][pendidikan_terakhir]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">Pekerjaan</label><input type="text" name="keluarga[${i}][pekerjaan]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">No. Telp</label><input type="text" name="keluarga[${i}][no_telp]" class="w-full px-2 py-1 border rounded text-sm"></div>
            </div>
            <div class="ml-4 border-l-2 border-purple-300 pl-3 mt-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-gray-700">Riwayat Sekolah Istri/Suami</span>
                    <button type="button" onclick="addPendidikanKeluarga(${i})" class="text-xs text-purple-600 hover:underline font-bold">+ Tambah Sekolah</button>
                </div>
                <div id="pendidikan-keluarga-${i}"></div>
            </div>
        </div>
    `;
    document.getElementById('keluarga-container').insertAdjacentHTML('beforeend', html);
}

// ==================== PENDIDIKAN KELUARGA ====================
let pendidikanKeluargaCount = {};
function addPendidikanKeluarga(keluargaIdx) {
    if (!pendidikanKeluargaCount[keluargaIdx]) pendidikanKeluargaCount[keluargaIdx] = 0;
    const j = pendidikanKeluargaCount[keluargaIdx]++;

    const html = `
        <div class="grid grid-cols-6 gap-2 mb-1 pendidikan-keluarga-item">
            <select name="keluarga[${keluargaIdx}][pendidikan][${j}][jenjang]" class="px-1 py-0.5 border rounded text-xs">
                ${['SD','SMP','SMA/SMK','D3','S1','S2','S3'].map(x => '<option value="' + x + '">' + x + '</option>').join('')}
            </select>
            <input type="text" name="keluarga[${keluargaIdx}][pendidikan][${j}][nama_sekolah]" placeholder="Nama Sekolah" class="px-1 py-0.5 border rounded text-xs">
            <input type="text" name="keluarga[${keluargaIdx}][pendidikan][${j}][jurusan]" placeholder="Jurusan" class="px-1 py-0.5 border rounded text-xs">
            <input type="text" name="keluarga[${keluargaIdx}][pendidikan][${j}][kota]" placeholder="Kota" class="px-1 py-0.5 border rounded text-xs">
            <input type="number" name="keluarga[${keluargaIdx}][pendidikan][${j}][tahun_masuk]" placeholder="Masuk" class="px-1 py-0.5 border rounded text-xs">
            <input type="number" name="keluarga[${keluargaIdx}][pendidikan][${j}][tahun_lulus]" placeholder="Lulus" class="px-1 py-0.5 border rounded text-xs">
        </div>
    `;
    document.getElementById('pendidikan-keluarga-' + keluargaIdx).insertAdjacentHTML('beforeend', html);
}

// ==================== ANAK ====================
let anakCount = {{ $pegawai->anak->count() ?? 0 }};

function addAnak() {
    const i = anakCount++;
    const html = `
        <div class="border border-gray-200 rounded-lg p-4 mb-3 anak-item bg-gray-50">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-bold text-sm">Anak Baru</h4>
                <button type="button" onclick="this.closest('.anak-item').remove()" class="text-red-600 text-sm">Hapus</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <div><label class="block text-xs font-medium mb-1">Nama Anak</label><input type="text" name="anak[${i}][nama]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">NIK</label><input type="text" name="anak[${i}][nik]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">Tanggal Lahir</label><input type="date" name="anak[${i}][tanggal_lahir]" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">Jenis Kelamin</label>
                    <select name="anak[${i}][jenis_kelamin]" class="w-full px-2 py-1 border rounded text-sm">
                        <option value="L">Laki-laki</option><option value="P">Perempuan</option>
                    </select>
                </div>
                <div><label class="block text-xs font-medium mb-1">Anak Ke-</label><input type="number" name="anak[${i}][urutan_anak]" value="1" class="w-full px-2 py-1 border rounded text-sm"></div>
                <div><label class="block text-xs font-medium mb-1">Status Anak</label>
                    <select name="anak[${i}][status_anak]" class="w-full px-2 py-1 border rounded text-sm">
                        <option value="kandung">Kandung</option><option value="tiri">Tiri</option><option value="angkat">Angkat</option>
                    </select>
                </div>
            </div>
            <div class="ml-4 border-l-2 border-blue-300 pl-3">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold">Riwayat Sekolah Anak</span>
                    <button type="button" onclick="addPendidikanAnak(${i})" class="text-xs text-blue-600 hover:underline font-bold">+ Tambah Sekolah</button>
                </div>
                <div id="pendidikan-anak-${i}"></div>
            </div>
        </div>
    `;
    document.getElementById('anak-container').insertAdjacentHTML('beforeend', html);
}

// ==================== PENDIDIKAN ANAK ====================
let pendidikanAnakCount = {};
function addPendidikanAnak(anakIdx) {
    if (!pendidikanAnakCount[anakIdx]) pendidikanAnakCount[anakIdx] = 0;
    const j = pendidikanAnakCount[anakIdx]++;
    const html = `
        <div class="grid grid-cols-5 gap-2 mb-1 pendidikan-item">
            <select name="anak[${anakIdx}][pendidikan][${j}][jenjang]" class="px-1 py-0.5 border rounded text-xs">
                ${['TK','SD','SMP','SMA/SMK','D3','S1','S2','S3'].map(x => '<option value="' + x + '">' + x + '</option>').join('')}
            </select>
            <input type="text" name="anak[${anakIdx}][pendidikan][${j}][nama_sekolah]" placeholder="Nama Sekolah" class="px-1 py-0.5 border rounded text-xs">
            <input type="text" name="anak[${anakIdx}][pendidikan][${j}][kota]" placeholder="Kota" class="px-1 py-0.5 border rounded text-xs">
            <input type="number" name="anak[${anakIdx}][pendidikan][${j}][tahun_masuk]" placeholder="Masuk" class="px-1 py-0.5 border rounded text-xs">
            <input type="number" name="anak[${anakIdx}][pendidikan][${j}][tahun_lulus]" placeholder="Lulus" class="px-1 py-0.5 border rounded text-xs">
        </div>
    `;
    document.getElementById('pendidikan-anak-' + anakIdx).insertAdjacentHTML('beforeend', html);
}

// ==================== PENDIDIKAN PEGAWAI ====================
let pendidikanCount = {{ $pegawai->pendidikan->count() ?? 0 }};

function addPendidikan() {
    const i = pendidikanCount++;
    const html = `
        <div class="grid grid-cols-1 md:grid-cols-6 gap-2 mb-2 pendidikan-pegawai-item">
            <select name="pendidikan[${i}][jenjang]" class="px-2 py-1 border rounded text-sm">
                ${['SD','SMP','SMA/SMK','D3','S1','S2','S3'].map(x => '<option value="' + x + '">' + x + '</option>').join('')}
            </select>
            <input type="text" name="pendidikan[${i}][nama_sekolah]" placeholder="Nama Sekolah" class="px-2 py-1 border rounded text-sm">
            <input type="text" name="pendidikan[${i}][jurusan]" placeholder="Jurusan" class="px-2 py-1 border rounded text-sm">
            <input type="text" name="pendidikan[${i}][kota]" placeholder="Kota" class="px-2 py-1 border rounded text-sm">
            <input type="number" name="pendidikan[${i}][tahun_masuk]" placeholder="Masuk" class="px-2 py-1 border rounded text-sm">
            <input type="number" name="pendidikan[${i}][tahun_lulus]" placeholder="Lulus" class="px-2 py-1 border rounded text-sm">
        </div>
    `;
    document.getElementById('pendidikan-container').insertAdjacentHTML('beforeend', html);
}

// ==================== CROPPER FOTO PROFIL ====================
let cropper = null;
let currentImageSrc = '';

function bukaModalCrop() {
    const input = document.getElementById('input-foto-profil');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            currentImageSrc = e.target.result;
            openCropper(e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    } @if($pegawai->foto_profil) else {
        currentImageSrc = "{{ route('storage.file', ['path' => $pegawai->foto_profil]) }}?v={{ time() }}";
        openCropper(currentImageSrc);
    } @endif
}

function openCropper(src) {
    const modal = document.getElementById('modal-crop');
    const img = document.getElementById('img-crop');
    img.src = src;
    modal.style.display = 'flex';

    if (cropper) cropper.destroy();

    cropper = new Cropper(img, {
        aspectRatio: 1,
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 1,
        background: false,
        responsive: true,
        ready: function() {
            const cr = parseInt(document.getElementById('foto_crop_rotate').value) || 0;
            if (cr != 0) cropper.rotate(cr);
        }
    });
}

function tutupModalCrop() {
    document.getElementById('modal-crop').style.display = 'none';
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

function simpanCrop() {
    if (!cropper) return;
    const data = cropper.getData();
    const img = document.getElementById('img-crop');

    const xPercent = (data.x / img.naturalWidth).toFixed(4);
    const yPercent = (data.y / img.naturalHeight).toFixed(4);
    const zoom = (img.naturalWidth / data.width).toFixed(4);

    document.getElementById('foto_crop_x').value = xPercent;
    document.getElementById('foto_crop_y').value = yPercent;
    document.getElementById('foto_crop_zoom').value = zoom;
    document.getElementById('foto_crop_rotate').value = data.rotate || 0;

    tutupModalCrop();

    const preview = document.getElementById('preview-foto');
    if (preview) {
        preview.style.objectPosition = `${xPercent * 100}% ${yPercent * 100}%`;
        preview.style.transform = `scale(${zoom}) rotate(${data.rotate || 0}deg)`;
    }

    alert('Posisi foto disimpan. Klik "Simpan Profil" untuk menyimpan ke database.');
}

document.getElementById('input-foto-profil')?.addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        setTimeout(() => bukaModalCrop(), 100);
    }
});
</script>
@endpush