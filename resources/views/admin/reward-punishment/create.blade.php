@extends('layouts.admin')

@section('title', 'Tambah Reward/Punishment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Reward/Punishment</h1>
                <p class="text-sm text-gray-500">Input penghargaan atau sanksi pegawai</p>
            </div>
            <a href="{{ route('admin.reward-punishment.index') }}"
               style="display: inline-flex; align-items: center; padding: 8px 16px; background: #6b7280; color: white; font-size: 14px; font-weight: 700; border-radius: 6px; text-decoration: none;">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.reward-punishment.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pegawai <span class="text-red-500">*</span></label>
                <select name="pegawai_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($pegawai as $p)
                        <option value="{{ $p->id }}" {{ old('pegawai_id', $selectedPegawai) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ $p->nip }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" id="kategori" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="reward" {{ old('kategori', $selectedKategori) == 'reward' ? 'selected' : '' }}>🎁 Reward (Penghargaan)</option>
                        <option value="punishment" {{ old('kategori', $selectedKategori) == 'punishment' ? 'selected' : '' }}>⚠️ Punishment (Sanksi)</option>
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
                       placeholder="Contoh: Bonus Kinerja Q1 2026"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md"
                          placeholder="Detail keterangan (opsional)">{{ old('keterangan') }}</textarea>
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
                           placeholder="Contoh: 2000000"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada nominal</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Bukti (Opsional)</label>
                    <input type="file" name="file_bukti" accept=".pdf,.jpg,.jpeg,.png,.webp"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <p class="text-xs text-gray-500 mt-1">PDF / JPG / PNG / WEBP. Max 5MB</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ old('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('admin.reward-punishment.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">Batal</a>
                <button type="submit" style="padding: 10px 24px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: white; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rewardOptions = {
        'bonus': 'Bonus', 'kenaikan_gaji': 'Kenaikan Gaji', 'promosi': 'Promosi Jabatan',
        'piagam': 'Piagam Penghargaan', 'pegawai_teladan': 'Pegawai Teladan',
        'beasiswa': 'Beasiswa', 'pensiun_dini': 'Pensiun Dini', 'lainnya': 'Lainnya'
    };
    const punishmentOptions = {
        'sp1': 'Surat Peringatan 1 (SP1)', 'sp2': 'Surat Peringatan 2 (SP2)', 'sp3': 'Surat Peringatan 3 (SP3)',
        'teguran_lisan': 'Teguran Lisan', 'pemotongan_gaji': 'Pemotongan Gaji',
        'penurunan_jabatan': 'Penurunan Jabatan', 'skorsing': 'Skorsing',
        'pemberhentian': 'Pemberhentian', 'lainnya': 'Lainnya'
    };
    const kategoriSel = document.getElementById('kategori');
    const tipeSel = document.getElementById('tipe');
    const oldTipe = '{{ old('tipe') }}';

    function updateTipe() {
        const val = kategoriSel.value;
        let opts = {};
        if (val === 'reward') opts = rewardOptions;
        else if (val === 'punishment') opts = punishmentOptions;

        tipeSel.innerHTML = '<option value="">-- Pilih Tipe --</option>';
        Object.keys(opts).forEach(k => {
            const o = document.createElement('option');
            o.value = k; o.textContent = opts[k];
            if (k === oldTipe) o.selected = true;
            tipeSel.appendChild(o);
        });
    }
    kategoriSel.addEventListener('change', updateTipe);
    if (kategoriSel.value) updateTipe();
});
</script>
@endsection