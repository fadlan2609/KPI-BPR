@extends('layouts.admin')

@section('title', 'Ajukan Cuti')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Ajukan Cuti (Admin)</h1>

    <form action="{{ route('admin.cuti.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Pegawai *</label>
            <select name="pegawai_id" required class="w-full px-3 py-2 border rounded-md">
                <option value="">Pilih Pegawai</option>
                @foreach($pegawai as $p)
                    <option value="{{ $p->id }}" {{ old('pegawai_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nama }} ({{ $p->nip }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Jenis Cuti *</label>
            <select name="jenis_cuti" required class="w-full px-3 py-2 border rounded-md">
                <option value="tahunan">Tahunan</option>
                <option value="sakit">Sakit</option>
                <option value="melahirkan">Melahirkan</option>
                <option value="khusus">Khusus</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal Mulai *</label>
                <input type="date" name="tanggal_mulai" required class="w-full px-3 py-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Lama (Hari Kerja) *</label>
                <input type="number" name="lama_hari" min="1" value="1" required class="w-full px-3 py-2 border rounded-md">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Approver Pertama *</label>
            <select name="approver_id" required class="w-full px-3 py-2 border rounded-md">
                <option value="">Pilih Pegawai</option>
                @foreach($pegawai as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border rounded-md"></textarea>
        </div>

        <div class="flex justify-end space-x-2 pt-4 border-t">
            <a href="{{ route('admin.cuti.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                <i class="fas fa-paper-plane mr-1"></i> Kirim
            </button>
        </div>
    </form>
</div>
@endsection