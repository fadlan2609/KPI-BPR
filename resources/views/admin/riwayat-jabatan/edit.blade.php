@extends('layouts.admin')

@section('title', 'Edit Riwayat Jabatan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Riwayat Jabatan</h1>
                <p class="text-sm text-gray-500">Untuk: {{ $pegawai->nama }} ({{ $pegawai->nip }})</p>
            </div>
            <a href="{{ route('admin.riwayat-jabatan.show', $pegawai->id) }}"
               style="display: inline-flex; align-items: center; padding: 8px 16px; background: #6b7280; color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                <i class="fas fa-arrow-left" style="margin-right: 6px;"></i> Kembali
            </a>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-yellow-500 text-white rounded-full flex items-center justify-center font-bold text-lg">
                    {{ strtoupper(substr($pegawai->nama, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-yellow-900">{{ $pegawai->nama }}</p>
                    <p class="text-xs text-yellow-700">Edit riwayat: {{ $riwayat->jabatan->nama ?? '-' }}</p>
                </div>
            </div>
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

        <form action="{{ route('admin.riwayat-jabatan.update', $riwayat->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Jabatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Jabatan <span class="text-red-500">*</span>
                </label>
                <select name="jabatan_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    @foreach($jabatan as $j)
                        <option value="{{ $j->id }}" {{ old('jabatan_id', $riwayat->jabatan_id) == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kantor --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kantor</label>
                <select name="kantor_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kantor --</option>
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

            {{-- Periode --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tahun Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tahun_mulai" min="1980" max="{{ date('Y') }}"
                           value="{{ old('tahun_mulai', $riwayat->tahun_mulai) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Selesai</label>
                    <input type="number" name="tahun_selesai" min="1980" max="{{ date('Y') + 5 }}"
                           value="{{ old('tahun_selesai', $riwayat->tahun_selesai) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                          placeholder="Opsional">{{ old('keterangan', $riwayat->keterangan) }}</textarea>
            </div>

            {{-- Info Periode --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                <p class="text-xs text-indigo-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Periode saat ini:</strong> {{ $riwayat->periode_label }}
                    ({{ $riwayat->durasi }})
                </p>
            </div>

            <div class="flex justify-between gap-2 pt-4 border-t">
                <form action="{{ route('admin.riwayat-jabatan.destroy', $riwayat->id) }}" method="POST"
                      onsubmit="return confirm('Hapus riwayat ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            style="padding: 10px 20px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>

                <div class="flex gap-2">
                    <a href="{{ route('admin.riwayat-jabatan.show', $pegawai->id) }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">Batal</a>
                    <button type="submit"
                            style="padding: 10px 24px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                        <i class="fas fa-save mr-2"></i> Update Riwayat
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection