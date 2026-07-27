@extends('layouts.admin')

@section('title', 'Informasi BPR')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Informasi BPR</h1>
        <span class="text-sm text-gray-500">
            Terakhir diperbarui: {{ $bpr ? $bpr->updated_at->format('d M Y H:i') : 'Belum ada data' }}
        </span>
    </div>

    <form action="{{ route('admin.bpr.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Logo -->
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Logo BPR</label>
                <div class="flex items-center space-x-4">
                    @if($bpr && $bpr->logo)
                        <img src="{{ asset('storage/' . $bpr->logo) }}" alt="Logo BPR" class="h-24 w-24 object-cover rounded border">
                    @else
                        <div class="h-24 w-24 bg-gray-200 rounded border flex items-center justify-center">
                            <span class="text-gray-400 text-xs">No Logo</span>
                        </div>
                    @endif
                    <div class="flex-1">
                        <input type="file" name="logo" accept="image/*" 
                               class="block w-full text-sm text-gray-500 
                               file:mr-4 file:py-2 file:px-4 
                               file:rounded-full file:border-0 
                               file:text-sm file:font-semibold 
                               file:bg-blue-50 file:text-blue-700 
                               hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, SVG | Max: 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Sandi BPR -->
            <div>
                <label for="sandi_bpr" class="block text-sm font-medium text-gray-700">Sandi BPR</label>
                <input type="text" name="sandi_bpr" id="sandi_bpr" 
                       value="{{ old('sandi_bpr', $bpr->sandi_bpr ?? '') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Nama BPR -->
            <div>
                <label for="nama_bpr" class="block text-sm font-medium text-gray-700">Nama BPR</label>
                <input type="text" name="nama_bpr" id="nama_bpr" 
                       value="{{ old('nama_bpr', $bpr->nama_bpr ?? '') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Jenis BPR -->
            <div>
                <label for="jenis_bpr" class="block text-sm font-medium text-gray-700">Jenis BPR</label>
                <input type="text" name="jenis_bpr" id="jenis_bpr" 
                       value="{{ old('jenis_bpr', $bpr->jenis_bpr ?? '') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Jenis Lembaga -->
            <div>
                <label for="jenis_lembaga" class="block text-sm font-medium text-gray-700">Jenis Lembaga</label>
                <select name="jenis_lembaga" id="jenis_lembaga" 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Jenis Lembaga</option>
                    <option value="BPRS" {{ (old('jenis_lembaga', $bpr->jenis_lembaga ?? '') == 'BPRS') ? 'selected' : '' }}>BPRS</option>
                    <option value="Bank Syariah" {{ (old('jenis_lembaga', $bpr->jenis_lembaga ?? '') == 'Bank Syariah') ? 'selected' : '' }}>Bank Syariah</option>
                    <option value="Bank Konvensional" {{ (old('jenis_lembaga', $bpr->jenis_lembaga ?? '') == 'Bank Konvensional') ? 'selected' : '' }}>Bank Konvensional</option>
                </select>
            </div>

            <!-- Kategori -->
            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="kategori" id="kategori" 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kategori</option>
                    <option value="Kategori A" {{ (old('kategori', $bpr->kategori ?? '') == 'Kategori A') ? 'selected' : '' }}>Kategori A</option>
                    <option value="Kategori B" {{ (old('kategori', $bpr->kategori ?? '') == 'Kategori B') ? 'selected' : '' }}>Kategori B</option>
                    <option value="Kategori C" {{ (old('kategori', $bpr->kategori ?? '') == 'Kategori C') ? 'selected' : '' }}>Kategori C</option>
                </select>
            </div>

            <!-- No. Telp -->
            <div>
                <label for="no_telp" class="block text-sm font-medium text-gray-700">No. Telp</label>
                <input type="text" name="no_telp" id="no_telp" 
                       value="{{ old('no_telp', $bpr->no_telp ?? '') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Alamat -->
            <div class="col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat BPR</label>
                <textarea name="alamat" id="alamat" rows="3" 
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('alamat', $bpr->alamat ?? '') }}</textarea>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email (Opsional)</label>
                <input type="email" name="email" id="email" 
                       value="{{ old('email', $bpr->email ?? '') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Website -->
            <div>
                <label for="website" class="block text-sm font-medium text-gray-700">Website (Opsional)</label>
                <input type="url" name="website" id="website" 
                       value="{{ old('website', $bpr->website ?? '') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition">
                <i class="fas fa-save mr-2"></i> Simpan Informasi BPR
            </button>
        </div>
    </form>
</div>
@endsection