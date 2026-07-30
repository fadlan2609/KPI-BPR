@extends('layouts.admin')

@section('title', 'Edit Jabatan')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Jabatan</h1>
            <p class="text-sm text-gray-500">Perbarui data jabatan pegawai</p>
        </div>
        <a href="{{ route('admin.jabatan.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.jabatan.update', $jabatan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Nama Jabatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $jabatan->nama) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nama') border-red-500 @enderror"
                           placeholder="Contoh: Direktur Utama, Manager, Staff">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jabatan Atasan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Atasan</label>
                    <select name="jabatan_atasan_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('jabatan_atasan_id') border-red-500 @enderror">
                        <option value="">- Tidak Ada -</option>
                        @foreach($jabatanOptions as $j)
                            <option value="{{ $j->id }}" 
                                {{ old('jabatan_atasan_id', $jabatan->jabatan_atasan_id) == $j->id ? 'selected' : '' }}>
                                {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Pilih jabatan atasan langsung dari jabatan ini</p>
                    @error('jabatan_atasan_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('keterangan') border-red-500 @enderror"
                              placeholder="Deskripsi jabatan, tugas dan tanggung jawab">{{ old('keterangan', $jabatan->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Jabatan -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                    <div>
                        <p class="text-sm text-blue-700">
                            <strong>ID Jabatan:</strong> #{{ $jabatan->id }}
                        </p>
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Dibuat:</strong> {{ $jabatan->created_at->format('d M Y H:i') }}
                        </p>
                        @if($jabatan->updated_at)
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Terakhir Diperbarui:</strong> {{ $jabatan->updated_at->format('d M Y H:i') }}
                        </p>
                        @endif
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Jumlah Pegawai:</strong> 
                            <span class="font-bold text-blue-800">{{ $jabatan->pegawai_count ?? 0 }}</span> orang
                        </p>
                        @if($jabatan->atasan)
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Atasan:</strong> {{ $jabatan->atasan->nama }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.jabatan.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Update Jabatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection