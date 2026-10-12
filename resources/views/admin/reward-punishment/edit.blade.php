@extends('layouts.admin')

@section('title', 'Edit Reward/Punishment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Reward/Punishment</h1>
                <p class="text-sm text-gray-500">Update data</p>
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

        <form action="{{ route('admin.reward-punishment.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pegawai <span class="text-red-500">*</span></label>
                <select name="pegawai_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    @foreach($pegawai as $p)
                        <option value="{{ $p->id }}" {{ old('pegawai_id', $item->pegawai_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ $p->nip }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" id="kategori" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="reward" {{ old('kategori', $item->kategori) == 'reward' ? 'selected' : '' }}>🎁 Reward</option>
                        <option value="punishment" {{ old('kategori', $item->kategori) == 'punishment' ? 'selected' : '' }}>⚠️ Punishment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                    <select name="tipe" id="tipe" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        @foreach(\App\Models\RewardPunishment::getTipeOptions(old('kategori', $item->kategori)) as $k => $v)
                            <option value="{{ $k }}" {{ old('tipe', $item->tipe) == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $item->judul) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('keterangan', $item->keterangan) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $item->tanggal->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Opsional)</label>
                    <input type="number" name="nominal" value="{{ old('nominal', $item->nominal) }}" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Bukti (Opsional)</label>
                    @if($item->file_bukti)
                        <div class="mb-2">
                            <a href="{{ $item->file_url }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                                <i class="fas fa-file"></i> Lihat file saat ini
                            </a>
                        </div>
                    @endif
                    <input type="file" name="file_bukti" accept=".pdf,.jpg,.jpeg,.png,.webp"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <p class="text-xs text-gray-500 mt-1">Upload baru untuk replace</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="aktif" {{ old('status', $item->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ old('status', $item->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ old('status', $item->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-between pt-4 border-t">
                <form action="{{ route('admin.reward-punishment.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="padding: 10px 20px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
                <div class="flex gap-2">
                    <a href="{{ route('admin.reward-punishment.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">Batal</a>
                    <button type="submit" style="padding: 10px 24px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; font-weight: 700; border-radius: 6px; border: none; cursor: pointer;">
                        <i class="fas fa-save mr-2"></i> Update
                    </button>
                </div>
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
    const oldTipe = '{{ old('tipe', $item->tipe) }}';
    function update() {
        const v = k.value;
        let opts = v === 'reward' ? rewardOptions : punishmentOptions;
        t.innerHTML = '';
        Object.keys(opts).forEach(key => {
            const o = document.createElement('option');
            o.value = key; o.textContent = opts[key];
            if (key === oldTipe) o.selected = true;
            t.appendChild(o);
        });
    }
    k.addEventListener('change', update);
});
</script>
@endsection