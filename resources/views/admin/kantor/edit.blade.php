@extends('layouts.admin')

@section('title', 'Edit Kantor')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Kantor</h1>
            <p class="text-sm text-gray-500">Perbarui data kantor/cabang</p>
        </div>
        <a href="{{ route('admin.kantor.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.kantor.update', $kantor->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Nama Kantor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kantor <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $kantor->nama) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nama') border-red-500 @enderror"
                           placeholder="Contoh: Kantor Pusat, Kantor Cabang Kisaran">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Kantor</label>
                    <textarea name="alamat" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('alamat') border-red-500 @enderror"
                              placeholder="Alamat lengkap kantor">{{ old('alamat', $kantor->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('keterangan') border-red-500 @enderror"
                              placeholder="Keterangan tambahan tentang kantor">{{ old('keterangan', $kantor->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah Pegawai (Readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pegawai</label>
                    <input type="text" value="{{ $kantor->pegawai_count ?? 0 }} pegawai" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                    <p class="mt-1 text-xs text-gray-500">Jumlah pegawai yang ditempatkan di kantor ini (otomatis)</p>
                </div>
            </div>

            <!-- Info Kantor -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                    <div>
                        <p class="text-sm text-blue-700">
                            <strong>ID Kantor:</strong> #{{ $kantor->id }}
                        </p>
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Dibuat:</strong> {{ $kantor->created_at->format('d M Y H:i') }}
                        </p>
                        @if($kantor->updated_at)
                        <p class="text-sm text-blue-700 mt-1">
                            <strong>Terakhir Diperbarui:</strong> {{ $kantor->updated_at->format('d M Y H:i') }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.kantor.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Update Kantor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection