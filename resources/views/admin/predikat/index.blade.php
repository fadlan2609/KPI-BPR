@extends('layouts.admin')

@section('title', 'Master Predikat Kinerja')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Daftar Predikat Kinerja Pegawai</h1>
        <button onclick="openModal('tambahModal')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
            <i class="fas fa-plus mr-2"></i> Tambah Predikat
        </button>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-wrap gap-4">
            <input type="text" id="search" placeholder="Cari predikat..." 
                   class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1">
            <button onclick="filterData()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-search mr-2"></i> Cari
            </button>
            <button onclick="resetFilter()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                <i class="fas fa-undo mr-2"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Predikat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($predikat as $index => $p)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $p->nama }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $p->keterangan ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $p->batas_bawah }} - {{ $p->batas_atas }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" 
                              style="color: {{ $p->warna_text }}; background-color: {{ $p->warna_latar }}">
                            Preview
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.predikat.edit', $p->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        {{-- FORM DELETE dengan parameter --}}
                        <form action="{{ route('admin.predikat.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus predikat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data predikat</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3">
            {{ $predikat->links() }}
        </div>
    </div>
</div>

<!-- ====== MODAL TAMBAH PREDIKAT ====== -->
<div id="tambahModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Tambah Predikat Baru</h3>
            <button onclick="closeModal('tambahModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.predikat.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Predikat*</label>
                    <input type="text" name="nama" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" rows="2" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Batas Bawah*</label>
                        <input type="number" name="batas_bawah" required min="0" max="100" step="0.01"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Batas Atas*</label>
                        <input type="number" name="batas_atas" required min="0" max="100" step="0.01"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Warna Teks*</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="warna_text" value="#000000" 
                               class="h-10 w-10 border rounded cursor-pointer">
                        <input type="text" name="warna_text_text" placeholder="#000000"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Warna Latar*</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="warna_latar" value="#FFFFFF" 
                               class="h-10 w-10 border rounded cursor-pointer">
                        <input type="text" name="warna_latar_text" placeholder="#FFFFFF"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="p-4 border rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                    <span id="previewPredikat" class="px-4 py-2 rounded-full text-sm font-semibold inline-block">
                        Contoh Predikat
                    </span>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('tambahModal')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    function filterData() {
        const search = document.getElementById('search').value;
        window.location.href = '{{ route("admin.predikat.index") }}?search=' + search;
    }

    function resetFilter() {
        document.getElementById('search').value = '';
        window.location.href = '{{ route("admin.predikat.index") }}';
    }

    // Preview warna
    document.querySelector('input[name="warna_text"]').addEventListener('input', function() {
        document.querySelector('input[name="warna_text_text"]').value = this.value;
        updatePreview();
    });

    document.querySelector('input[name="warna_latar"]').addEventListener('input', function() {
        document.querySelector('input[name="warna_latar_text"]').value = this.value;
        updatePreview();
    });

    function updatePreview() {
        const textColor = document.querySelector('input[name="warna_text_text"]').value;
        const bgColor = document.querySelector('input[name="warna_latar_text"]').value;
        const preview = document.getElementById('previewPredikat');
        preview.style.color = textColor;
        preview.style.backgroundColor = bgColor;
        preview.textContent = document.querySelector('input[name="nama"]').value || 'Contoh Predikat';
    }

    document.querySelector('input[name="nama"]').addEventListener('input', updatePreview);
    document.querySelector('input[name="warna_text_text"]').addEventListener('input', function() {
        document.querySelector('input[name="warna_text"]').value = this.value;
        updatePreview();
    });
    document.querySelector('input[name="warna_latar_text"]').addEventListener('input', function() {
        document.querySelector('input[name="warna_latar"]').value = this.value;
        updatePreview();
    });

    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            event.target.classList.add('hidden');
            event.target.classList.remove('flex');
        }
    }
</script>
@endpush
@endsection