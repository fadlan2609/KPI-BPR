@extends('layouts.pegawai')

@section('title', 'Edit Riwayat Jabatan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Riwayat Jabatan</h1>
                <p class="text-sm text-gray-500">Perbarui data riwayat jabatan</p>
            </div>
            <a href="{{ route('pegawai.riwayat-jabatan.index') }}"
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

        <form action="{{ route('pegawai.riwayat-jabatan.update', $riwayat->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan *</label>
                <select name="jabatan_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Pilih Jabatan</option>
                    @foreach($jabatan as $j)
                        <option value="{{ $j->id }}" {{ old('jabatan_id', $riwayat->jabatan_id) == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kantor</label>
                <select name="kantor_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Pilih Kantor</option>
                    @foreach($kantor as $k)
                        <option value="{{ $k->id }}" {{ old('kantor_id', $riwayat->kantor_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- STATUS KARYAWAN --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-semibold text-yellow-900 mb-3 flex items-center">
                    <i class="fas fa-id-badge mr-2"></i>
                    Status Karyawan
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status_karyawan" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500">
                            <option value="">-- Pilih Status --</option>
                            <option value="magang" {{ old('status_karyawan', $riwayat->status_karyawan) == 'magang' ? 'selected' : '' }}>🎓 Magang / Internship</option>
                            <option value="kontrak" {{ old('status_karyawan', $riwayat->status_karyawan) == 'kontrak' ? 'selected' : '' }}>📝 Kontrak (PKWT)</option>
                            <option value="tetap" {{ old('status_karyawan', $riwayat->status_karyawan) == 'tetap' ? 'selected' : '' }}>✅ Tetap (PKWTT)</option>
                            <option value="honorer" {{ old('status_karyawan', $riwayat->status_karyawan) == 'honorer' ? 'selected' : '' }}>🤝 Honorer</option>
                            <option value="outsourcing" {{ old('status_karyawan', $riwayat->status_karyawan) == 'outsourcing' ? 'selected' : '' }}>🏢 Outsourcing</option>
                            <option value="lainnya" {{ old('status_karyawan', $riwayat->status_karyawan) == 'lainnya' ? 'selected' : '' }}>📋 Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Kontrak (opsional)
                        </label>
                        <input type="text" name="jenis_kontrak"
                               value="{{ old('jenis_kontrak', $riwayat->jenis_kontrak) }}"
                               placeholder="Contoh: PKWT 1 Tahun"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Mulai *</label>
                    <input type="number" name="tahun_mulai" min="1980" max="{{ date('Y') }}" required
                           value="{{ old('tahun_mulai', $riwayat->tahun_mulai) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Selesai</label>
                    <input type="number" name="tahun_selesai" min="1980" max="{{ date('Y') + 5 }}"
                           value="{{ old('tahun_selesai', $riwayat->tahun_selesai) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan kalau masih menjabat</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('keterangan', $riwayat->keterangan) }}</textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t">
                <a href="{{ route('pegawai.riwayat-jabatan.index') }}"
                   class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">Batal</a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection