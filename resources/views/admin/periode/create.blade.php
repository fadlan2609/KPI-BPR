@extends('layouts.admin')

@section('title', 'Tambah Periode Penilaian')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Periode Penilaian</h1>
        <a href="{{ route('admin.periode.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- TAMPILKAN ERROR --}}
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.periode.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Periode -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nama Periode <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required 
                       value="{{ old('nama') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nama') border-red-500 @enderror"
                       placeholder="Contoh: Periode Penilaian Jan-Jun 2026">
                @error('nama')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_mulai" required 
                       value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tanggal_mulai') border-red-500 @enderror">
                @error('tanggal_mulai')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Selesai -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Selesai <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_selesai" required 
                       value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+6 months'))) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tanggal_selesai') border-red-500 @enderror">
                @error('tanggal_selesai')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Batas Self Assessment -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Batas Self Assessment <span class="text-red-500">*</span></label>
                <input type="date" name="batas_self_assessment" required 
                       value="{{ old('batas_self_assessment', date('Y-m-d', strtotime('+1 month'))) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('batas_self_assessment') border-red-500 @enderror">
                @error('batas_self_assessment')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Batas Penilaian Atasan -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Batas Penilaian Atasan <span class="text-red-500">*</span></label>
                <input type="date" name="batas_penilaian_atasan" required 
                       value="{{ old('batas_penilaian_atasan', date('Y-m-d', strtotime('+2 months'))) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('batas_penilaian_atasan') border-red-500 @enderror">
                @error('batas_penilaian_atasan')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Batas Finalisasi -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Batas Finalisasi <span class="text-red-500">*</span></label>
                <input type="date" name="batas_finalisasi" required 
                       value="{{ old('batas_finalisasi', date('Y-m-d', strtotime('+3 months'))) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('batas_finalisasi') border-red-500 @enderror">
                @error('batas_finalisasi')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <!-- Aktifkan -->
            <div class="flex items-center mt-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                       {{ old('is_active') ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Aktifkan periode ini sekarang
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('admin.periode.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-save mr-2"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection