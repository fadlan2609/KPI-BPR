@extends('layouts.pegawai')

@section('title', 'Tambah Reward/Punishment Bawahan')

@php
    // Tentukan prefix route berdasarkan kategori dari controller
    $kategori = $kategori ?? 'reward';
    $routePrefix = 'pegawai.atasan-' . $kategori; // pegawai.atasan-reward atau pegawai.atasan-punishment
@endphp

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Reward/Punishment</h1>
                <p class="text-sm text-gray-500">Input untuk bawahan langsung Anda</p>
            </div>
            <a href="{{ route($routePrefix . '.index') }}"
               style="display: inline-flex; align-items: center; padding: 8px 16px; background: #6b7280; color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Anda hanya bisa input untuk <strong>bawahan langsung</strong> Anda.
            </p>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- FIX: route pakai $routePrefix --}}
        <form action="{{ route($routePrefix . '.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bawahan <span class="text-red-500">*</span></label>
                <select name="pegawai_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">-- Pilih Bawahan --</option>
                    @foreach($bawahan as $p)
                        <option value="{{ $p->id }}" {{ old('pegawai_id', $selectedPegawai ?? '') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ $p->nip }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" id="kategori" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">-- Pilih --</option>
                        <option value="reward" {{ old('kategori', $kategori) == 'reward' ? 'selected' : '' }}>🎁 Reward</option>
                        <option value="punishment" {{ old('kategori', $kategori) == 'punishment' ? 'selected' : '' }}>⚠️ Punishment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                    <select name="tipe" id="tipe" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">-- Pilih Kategori Dulu --</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul') }}" required maxlength="255"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('keterangan') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Opsional)</label>
                    <input type="number" name="nominal" value="{{ old('nominal') }}" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File Bukti (Opsional)</label>
                <input type="file" name="file_bukti" accept=".pdf,.jpg,.jpeg,.png,.webp"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                {{-- FIX: route pakai $routePrefix --}}
                <a href="{{ route($routePrefix . '.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">Batal</a>
                <button type="submit" style="padding: 10px 24px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: white; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rewardOptions = {'bonus': 'Bonus','kenaikan_gaji': 'Kenaikan Gaji','promosi': 'Promosi Jabatan','piagam': 'Piagam Penghargaan','pegawai_teladan': 'Pegawai Teladan','beasiswa': 'Beasiswa','pensiun_dini': 'Pensiun Dini','lainnya': 'Lainnya'};
    const punishmentOptions = {'sp1': 'SP1','sp2': 'SP2','sp3': 'SP3','teguran_lisan': 'Teguran Lisan','pemotongan_gaji': 'Pemotongan Gaji','penurunan_jabatan': 'Penurunan Jabatan','skorsing': 'Skorsing','pemberhentian': 'Pemberhentian','lainnya': 'Lainnya'};
    const k = document.getElementById('kategori');
    const t = document.getElementById('tipe');
    const oldTipe = '{{ old('tipe') }}';

    function update() {
        const v = k.value;
        let opts = v === 'reward' ? rewardOptions : v === 'punishment' ? punishmentOptions : {};
        t.innerHTML = '<option value="">-- Pilih --</option>';
        Object.keys(opts).forEach(key => {
            const o = document.createElement('option');
            o.value = key; o.textContent = opts[key];
            if (key === oldTipe) o.selected = true;
            t.appendChild(o);
        });
    }
    k.addEventListener('change', update);
    if (k.value) update();
});
</script>
@endsection