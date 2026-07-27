@extends('layouts.admin')

@section('title', 'Edit Pegawai')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Pegawai</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $pegawai->nama }} ({{ $pegawai->nip }})</p>
        </div>
        <a href="{{ route('admin.pegawai.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Data Wajib -->
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-8 w-1 bg-blue-600 rounded"></div>
                    <h3 class="text-lg font-semibold text-gray-800">Data Wajib</h3>
                    <span class="text-xs text-red-500">* Wajib diisi</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $pegawai->nama) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nama') border-red-500 @enderror">
                @error('nama')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIP <span class="text-red-500">*</span></label>
                <input type="text" name="nip" value="{{ old('nip', $pegawai->nip) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nip') border-red-500 @enderror">
                @error('nip')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                <select name="jabatan_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('jabatan_id') border-red-500 @enderror">
                    <option value="">Pilih Jabatan</option>
                    @foreach($jabatanOptions as $j)
                        <option value="{{ $j->id }}" {{ old('jabatan_id', $pegawai->jabatan_id) == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
                @error('jabatan_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kantor Penempatan <span class="text-red-500">*</span></label>
                <select name="kantor_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('kantor_id') border-red-500 @enderror">
                    <option value="">Pilih Kantor</option>
                    @foreach($kantorOptions as $k)
                        <option value="{{ $k->id }}" {{ old('kantor_id', $pegawai->kantor_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kantor_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('status') border-red-500 @enderror">
                    <option value="aktif" {{ old('status', $pegawai->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="keluar" {{ old('status', $pegawai->status) == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="mengundurkan_diri" {{ old('status', $pegawai->status) == 'mengundurkan_diri' ? 'selected' : '' }}>Mengundurkan Diri</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Atasan Langsung</label>
                <select name="atasan_langsung_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">- Tidak Ada -</option>
                    @foreach($pegawaiOptions as $p)
                        <option value="{{ $p->id }}" {{ old('atasan_langsung_id', $pegawai->atasan_langsung_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ $p->nip }})
                        </option>
                    @endforeach
                </select>
                @error('atasan_langsung_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Data Tambahan -->
            <div class="lg:col-span-2 mt-4">
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-8 w-1 bg-green-600 rounded"></div>
                    <h3 class="text-lg font-semibold text-gray-800">Data Tambahan</h3>
                    <span class="text-xs text-gray-500">(Opsional)</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $pegawai->tanggal_masuk) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="jenis_kelamin"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Pilih</option>
                    <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                <input type="text" name="status_pernikahan" value="{{ old('status_pernikahan', $pegawai->status_pernikahan) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Menikah / Belum Menikah / Duda / Janda">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Karyawan</label>
                <input type="text" name="status_karyawan" value="{{ old('status_karyawan', $pegawai->status_karyawan) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Tetap / Kontrak / Magang">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                <input type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="S1 / S2 / SMA / D3">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasangan</label>
                <input type="text" name="nama_pasangan" value="{{ old('nama_pasangan', $pegawai->nama_pasangan) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Nama suami/istri">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Anak</label>
                <input type="number" name="jumlah_anak" value="{{ old('jumlah_anak', $pegawai->jumlah_anak ?? 0) }}" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" name="nik" value="{{ old('nik', $pegawai->nik) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="Nomor Induk Kependudukan">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Tempat Tinggal</label>
                <textarea name="alamat" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                          placeholder="Alamat lengkap">{{ old('alamat', $pegawai->alamat) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $pegawai->no_hp) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="0812-3456-7890">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $pegawai->email) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="email@domain.com">
            </div>
        </div>

        <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('admin.pegawai.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i> Update Pegawai
            </button>
        </div>
    </form>
</div>
@endsection